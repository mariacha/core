<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 * 
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage Model
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

define('MM_OK', 0);
define('MM_ARCHIVE_CORRUPTED', 1);
define('MM_BROKEN_DEPENDENCIES', 2);

/**
 * Modules manager
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class XLite_Model_ModulesManager extends XLite_Base implements XLite_Base_ISingleton
{
    /**
     * GET params to enable safe mode
     */

    const PARAM_SAFE_MODE = 'safe_mode';
    const PARAM_AUTH_CODE = 'auth_code';

    /**
     * Session variable to determine current mode
     */
    const SESSION_VAR_SAFE_MODE = 'safe_mode';


    /**
     * Determines if we need to initialize modules or not 
     * 
     * @var    bool
     * @access protected
     * @since  1.0
     */
    protected $safeMode = false;

    /**
     * Module object
     * 
     * @var    XLite_Model_Module
     * @access protected
     * @since  1.0
     */
    protected $module = null;

    /**
     * List of active modules (array with module names as the keys) 
     * 
     * @var    array
     * @access protected
     * @since  3.0
     */
    protected $activeModules = null;


    /**
     * Instantiate moduel object
     * 
     * @return XLite_Model_Module
     * @access protected
     * @since  1.0
     */
    protected function getModule()
    {
        if (is_null($this->module)) {
            $this->module = new XLite_Model_Module();
        }

        return $this->module;
    }

    /**
     * Determines current mode 
     * 
     * @return bool
     * @access protected
     * @since  1.0
     */
    protected function isInSafeMode()
    {
        $result = false;

        if (XLite::getInstance()->is('adminZone') && isset($_GET[self::PARAM_SAFE_MODE])) {
            $authCode = XLite::getInstance()->getOptions(array('installer_details', 'auth_code'));
            $result = empty($authCode) xor (isset($_GET[self::PARAM_AUTH_CODE]) && ($authCode == $_GET[self::PARAM_AUTH_CODE]));
        }

        return $result;
    }
    
    /**
     * Run the "init" function for all active modules
     * 
     * @return void
     * @access protected
     * @since  1.0
     */
    protected function initModules()
    {
        foreach ($this->getModule()->findAll('enabled = \'1\'') as $module) {
            $className = 'XLite_Module_' . $module->get('name') . '_Main';
            if (class_exists($className)) {
                $moduleObject = new $className();
                $moduleObject->init();
                $moduleObject = null;

            } elseif ($this->getModule()->find('name = \'' . $module->get('name') . '\'')) {
                $this->getModule()->delete();
            
            }
        }
    }


    /**
     * Attempts to initialize the ModulesManager and all active modules
     * 
     * @return void
     * @access public
     * @since  1.0
     */
    public function init()
    {
        if ($this->isInSafeMode()) {

            if ('on' == $_GET[self::PARAM_SAFE_MODE]) {
                XLite_Model_Session::getInstance()->setComplex(self::SESSION_VAR_SAFE_MODE, true);
                $this->set('safeMode', true);
            } elseif ('off' == $_GET[self::PARAM_SAFE_MODE]) {
                XLite_Model_Session::getInstance()->setComplex(self::SESSION_VAR_SAFE_MODE, null);
            }
        }

        if ($this->config->General->safe_mode || XLite_Model_Session::getInstance()->isRegistered('safe_mode')) {
            XLite::getInstance()->setCleanUpCacheFlag(true);
            $this->set('safeMode', true);
        }

        $this->initModules();
    }

    public function getModules($type = null)
    {
        return $this->getModule()->findAll(is_null($type) ? '' : 'type = \'' . $type . '\'');
    }

    /**
     * Update modules list 
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function updateModulesList()
    {
        $names = array();
        foreach ($this->getModule()->findAll() as $module) {
            $names[$module->get('name')] = true;
        }

        foreach (glob(LC_MODULES_DIR . '*' . LC_DS . 'Main.php') as $f) {
            $parts = explode(LC_DS, $f);
            $name = $parts[count($parts) - 2];
            if (!isset($names[$name])) {
                $this->registerModule($name);

            } else {
                unset($names[$name]);
            }
        }

        foreach ($names as $name => $tmp) {
            if ($this->getModule()->find('name = \'' . $name . '\'')) {
                $this->delete();
            }
        }

    }

    /**
     * Register new module 
     * 
     * @param string $name Module name
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function registerModule($name)
    {

        require_once LC_MODULES_DIR . $name . LC_DS . 'Main.php';

        $className = 'XLite_Module_' . $name . '_Main';

        $module = new XLite_Model_Module();

        $module->set('name', $name);
        $module->set('mutual_modules', implode(',', $className::getMutualModules()));
        $module->set('type', $className::getType());

        $module->create();

        // Install SQL dump
        $installSQLPath = LC_MODULES_DIR . $name . LC_DS . 'install.sql';

        if (file_exists($installSQLPath)) {
            $error = query_upload($installSQLPath, $this->db->connection, true, true);

            if ($error) {
                // TODO - display error
            }
        }
    }

    public function getActiveModules($moduleName = null)
    {
        if (is_null($this->activeModules)) {
            $this->activeModules = array();
            foreach ($this->getModule()->findAll('enabled = \'1\'') as $module) {
                $this->activeModules[$module->get('name')] = true;
            }
        }

        return is_null($moduleName) ? $this->activeModules : isset($this->activeModules[$moduleName]);
    }

    public function getActiveModulesNumber()
    {
        return count($this->getActiveModules());
    }

    public function rebuildCache()
    {
        $decorator = new Decorator();
        $decorator->rebuildCache(true);
        $decorator = null;
    }

    public function cleanupCache()
    {
        $decorator = new Decorator();
        $decorator->cleanupCache();
        $decorator = null;
    }

    public function changeModuleStatus($module, $status, $cleanupCache = false)
    {
        if (!($module instanceof XLite_Model_Module)) {
            $module = new XLite_Model_Module($module);
        }

        $status ? $module->enable() : $module->disable();
        $result = $module->update();

        if ($cleanupCache) {
            XLite::getInstance()->setCleanUpCacheFlag(true);
        }

        return $result;
    }

    public function updateModules(array $moduleIDs, $type = null)
    {
        foreach ($this->getModules($type) as $module) {
            $this->changeModuleStatus($module, in_array($module->get('module_id'), $moduleIDs));
        }

        $this->rebuildCache();

        return true;
    }

    public function isActiveModule($moduleName)
    {
        return $this->getActiveModules($moduleName);
    }
}
