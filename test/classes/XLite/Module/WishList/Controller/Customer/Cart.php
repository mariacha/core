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
 * Cart controller
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class XLite_Module_WishList_Controller_Customer_Cart extends XLite_Controller_Customer_Cart implements XLite_Base_IDecorator
{
    /**
     * Add item to cart (from wishlist)
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function action_add()
    {
        $wishlistId = XLite_Core_Request::getInstance()->wishlist_id;
        $itemId = XLite_Core_Request::getInstance()->item_id;
        $amount = intval(XLite_Core_Request::getInstance()->amount);

        if (!is_null($wishlistId) && !is_null($itemId)) {

            // process this wishlist
            parent::getCurrentItem();

            $wishlist_product = new XLite_Module_WishList_Model_WishListProduct($itemId, $wishlistId);
            
            if (!$wishlist_product->isOptionsExist()) {

                // TODO - add top message
                $this->set(
                    'returnUrl',
                    $this->buildUrl(
                        'wishlist',
                        '',
                        array('absentOptions' => 1, 'invalidProductName' => $wishlist_product->getProduct()->get('name'))
                    )
                );

                return;

            } elseif ($wishlist_product->isOptionsInvalid()) {

                // TODO - add top message
                $this->set(
                    'returnUrl',
                    $this->buildUrl(
                        'wishlist',
                        '',
                        array('invalidOptions' => 1, 'invalidProductName' => $wishlist_product->getProduct()->get('name'))
                    )
                );

                return;

            }

            if (0 > $amount) {
                $amount = $wishlist_product->get('amount');
            }

            $this->currentItem->set('options', $wishlist_product->get('options'));
            $this->currentItem->set('amount', XLite_Core_Request::getInstance()->amount);

            $this->session->set('wishlist_products', $wishlist_products);
        }

        parent::action_add();

        if (isset($wishlist_product)) {

            // TODO - add adding operation status - product must be remove only after SUCCESSFULL adding operation

            if ($wishlist_product->get('amount') <= $amount) {
                $wishlist_product->delete();

            } else {
                $wishlist_product->set('amount', $wishlist_product->get('amount') - $amount);
                $wishlist_product->update();
            }
        }
    }

}
