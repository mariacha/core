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
 * PHP version 5.3.0
 * 
 * @category  LiteCommerce
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\Upgrade\Entry\Module;

/**
 * Marketplace 
 * 
 * @see   ____class_see____
 * @since 1.0.0
 */
class Marketplace extends \XLite\Upgrade\Entry\Module\AModule
{
    /**
     * Module ID in database
     * 
     * @var   integer
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $moduleIDInstalled;

    /**
     * Module ID in database
     *
     * @var   integer
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $moduleIDForUpgrade;

    /**
     * Return entry readable name
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getName()
    {
        return $this->getModuleForUpgrade()->getModuleName();
    }

    /**
     * Return entry major version
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getMajorVersion()
    {
        return $this->getModuleForUpgrade()->getMajorVersion();
    }

    /**
     * Return entry minor version
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getMinorVersion()
    {
        return $this->getModuleForUpgrade()->getMinorVersion();
    }

    /**
     * Return entry revision date
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getRevisionDate()
    {
        $this->getModuleForUpgrade()->getRevisionDate();
    }

    /**
     * Return module author readable name
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getAuthor()
    {
        return $this->getModuleForUpgrade()->getAuthorName();
    }

    /**
     * Check if module is enabled
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isEnabled()
    {
        return (bool) $this->getModuleInstalled()->getEnabled();
    }

    /**
     * Constructor
     *
     * @param \XLite\Model\Module $moduleInstalled  Module model object
     * @param \XLite\Model\Module $moduleForUpgrade Module model object
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function __construct(\XLite\Model\Module $moduleInstalled, \XLite\Model\Module $moduleForUpgrade)
    {
        $this->moduleIDInstalled  = $moduleInstalled->getModuleID();
        $this->moduleIDForUpgrade = $moduleForUpgrade->getModuleID();

        if (is_null($this->getModuleInstalled())) {
            \Includes\ErrorHandler::fireError(
                'Module with ID "' . $this->moduleIDInstalled . '" is not found in DB'
            );
        }

        if (is_null($this->getModuleForUpgrade()) || !$this->getModuleForUpgrade()->getMarketplaceID()) {
            \Includes\ErrorHandler::fireError(
                'Module with ID "' . $this->moduleIDInstalled . '" is not found in DB'
                . ' or has an invaid markeplace identifier'
            );
        }
    }

    /**
     * Search for module in DB
     * 
     * @param integer $moduleID ID to search by
     *  
     * @return \XLite\Model\Module
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getModule($moduleID)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Module')->find($moduleID);
    }

    /**
     * Alias
     * 
     * @return \XLite\Model\Module
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getModuleInstalled()
    {
        return $this->getModule($this->moduleIDInstalled);
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Module
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getModuleForUpgrade()
    {
        return $this->getModule($this->moduleIDForUpgrade);
    }
}
