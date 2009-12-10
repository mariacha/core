<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003 Creative Development <info@creativedevelopment.biz>       |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URLs:                                                       |
|                                                                              |
| FOR LITECOMMERCE                                                             |
| http://www.litecommerce.com/software_license_agreement.html                  |
|                                                                              |
| FOR LITECOMMERCE ASP EDITION                                                 |
| http://www.litecommerce.com/software_license_agreement_asp.html              |
|                                                                              |
| THIS  AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
| SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT CREATIVE DEVELOPMENT, LLC |
| REGISTERED IN ULYANOVSK, RUSSIAN FEDERATION (hereinafter referred to as "THE |
| AUTHOR")  IS  FURNISHING  OR MAKING AVAILABLE TO  YOU  WITH  THIS  AGREEMENT |
| (COLLECTIVELY,  THE "SOFTWARE"). PLEASE REVIEW THE TERMS AND  CONDITIONS  OF |
| THIS LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY |
| INSTALLING,  COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE ACCEPTING AND AGREEING  TO  THE  TERMS  OF  THIS |
| LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT,  DO |
| NOT  INSTALL  OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL |
| PROPERTY  RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
| THAT  GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT  FOR |
| SALE  OR  FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY |
|                                                                              |
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* Something customer can put into its cart
*
*/
class OrderItem extends Base
{
    /**
    * Reference to the order/cart object
    */
    var $order = null;

    /**
    * A reference to the product object or null if there is no product
    */
    var $product = null;

    var $fields = array(
        'order_id'    => '',
        'item_id'     => '',
        'orderby'     => 0,
        'product_id'  => '',
        'price'       => '0',
        'amount'      => '1');

    var $primaryKey = array('order_id', 'item_id');
    var $alias = 'order_items';
    var $defaultOrder = "orderby";

    function formatCurrency($price)
    {
        return round($price, 2);
    }

    function setProduct($product)
    {
        $this->product = $product;
        if (is_null($product)) {
            $this->set("product_id", 0);
        } else {
        	if ($this->config->get("Taxes.prices_include_tax")) {
        		$this->set("price", $this->formatCurrency($product->get("taxedPrice")));
        	} else {
            	$this->set("price", $product->get("price"));
        	}
            $this->set("product_id", $product->get("product_id"));
        }
    }

    function &getProduct()
    {
        if (is_null($this->product) && $this->get("product_id")) {
            $this->product = func_new("Product", $this->get("product_id"));
        }
        return $this->product;
    }

    function create()
    {
        $this->set("item_id", $this->get("key"));
        parent::create();
    }
    
    /**
    * Returns a scalar key value used to identify items in shopping cart
    */
    function getKey()
    {
        return $this->get("product_id"); // . product_options
    }

    function updateAmount($amount)
    {
        $amount = (int)$amount;
        if ($amount <= 0) {
            $this->order->deleteItem($this);
        } else {
            $this->set("amount", $amount);
            $this->update();
        }
    }

    function getOrderby()
    {
        $sql = "SELECT MAX(orderby)+1 FROM %s WHERE order_id=%d";
        $sql = sprintf($sql, $this->get("table"), $this->get("order_id"));
        return $this->db->getOne($sql);
    }

    function getDiscountablePrice()
    {
        return $this->get("price");
    }

    function getTaxableTotal()
    {
        return $this->get("total");
    }

    function getTotal()
    {
        return $this->get("price") * $this->get("amount");
    }

    function getWeight()
    {
        return $this->get("product.weight") * $this->get("amount");
    }

    function &get($name)
    {
		if ($name == 'name' || $name == 'brief_description' || 
            $name == 'description' || $name == 'sku') 
            return $this->get("product.$name");
        return parent::get($name);
    }
	
	function hasThumbnail()
	{
		return $this->call("product.hasThumbnail");
	}
	function getThumbnailURL()
	{
		return $this->call("product.getThumbnailURL");
	}
    
	/**
	* This method is used in payment methods to briefly describe
	* the identity of the item.
	*/
    function getDescription()
    {
        return $this->get("name").' ('.$this->get("amount").')';
    }

    function getShortDescription($limit = 30)
    {
        if (strlen($this->get("sku"))) {
            $desc = $this->get("sku");
        } else {
            $desc = substr($this->get("name"), 0, $limit);
        }
        if ($this->get("amount") == 1) {
            return $desc;
        } else {
            return $desc . ' (' . $this->get("amount") . ')';
        }
    }

	/**
	* Validates the order item (e.g. the product_id supplied is an existing
	* product id, the amount is greater than zero etc.).
	* You cannot add an invalid item to a cart (prevented in Order::addItem()).
	* This procedure disabled possible work-arounds of standard dialog 
	* restrictions and is not intended to, say, restrict product options
	* and other cases when the cart must show an error/explanation message
	* to customer.
	*/
	function isValid()
	{
		$res = $this->is("product.exists") && $this->get("amount")>0;
        return $res;
	}

    /**
    * Decide whether to use shopping_cart/item.tpl widget to display
    * this item. Must be false if you want to use an alternative template.
    */
    function isUseStandardTemplate()
    {
        return true;
    }

    /**
    * Is this item needs to be shipped ?
    */
    function isShipped()
    {
        return true; 
    }

    /**
    * Returns the item descriptiopn URL in the shopping cart.
    */
    function getURL()
    {
        return CART_SELF . "?target=product&product_id=" . $this->get("product_id");
    }

}
// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
