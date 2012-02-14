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
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to Backup/Restore section
 *
 * @see   ____class_see____
 * @since 1.0.0
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class BackupRestore extends \XLite\View\Tabs\ATabs
{
    /**
     * Description of tabs related to Backup/Restore section and their targets
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $tabs = array(
        'db_backup' => array(
            'title'    => 'Back database up',
            'template' => 'db/backup.tpl',
        ),
        'db_restore' => array(
            'title'    => 'Restore database',
            'template' => 'db/restore.tpl',
        ),
    );


    /**
     * File size limit
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getUploadMaxFilesize()
    {
        return ini_get('upload_max_filesize');
    }
}
