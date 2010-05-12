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
 * @subpackage Controller
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

/**
 * Preview giftcertificate e-card
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class XLite_Module_GiftCertificates_Controller_Customer_PreviewEcard extends XLite_Controller_Abstract
{
    /**
     * Controller parameters
     * 
     * @var    array
     * @access public
     * @see    ____var_see____
     * @since  3.0.0
     */
    public $params = array('target', 'gcid');

    /**
     * Gift certificate (cache)
     * 
     * @var    XLite_Module_GiftCertificates_Model_GiftCertificate
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $gc = null;
    
    /**
     * Get current gift certificate
     * 
     * @return XLite_Module_GiftCertificates_Model_GiftCertificate
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getGc()
    {
        if (is_null(XLite_Core_Request::getInstance()->gc)) {
            $this->gc = new XLite_Module_GiftCertificates_Model_GiftCertificate(
                XLite_Core_Request::getInstance()->gcid
            );
        }

        return $this->gc;
    }

    /**
     * Get regular template 
     * 
     * @return string
     * @access protected
     * @since  3.0.0
     */
    protected function getRegularTemplate()
    {
        return 'modules/GiftCertificates/preview.tpl';
    }
}
