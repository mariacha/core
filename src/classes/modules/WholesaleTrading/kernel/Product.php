<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
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
| The Initial Developer of the Original Code is Creative Development LCC       |
| Portions created by Creative Development LCC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 foldmethod=marker shiftwidth=4: */

/**
* Class description.
*
* @package WholesaleTrading
* @version $Id: Product.php,v 1.24 2008/10/23 12:04:25 sheriff Exp $
*/
class Module_WholesaleTrading_Product extends Product
{
	var $_checkExistanceRequired = false; // perform direct sale check if the product does not exist

	function constructor($id = null)
	{
		parent::constructor($id);
		$this->fields["selling_membership"] = "";
		$this->fields["validaty_period"] = "";
	}
	
	function getShowExpandedOptions()
	{
		if (!$this->xlite->get("ProductOptionsEnabled") || !$this->hasOptions()) {
			return false;
		}	
		if (!$this->get("expansion_limit")) { 
			return false;
		}
		return true;
	}

	function hasExpandedOptions()
	{
		return (count($this->get("expandedItems")) > 0);
	}
	
	function getExpandedOptionsNames()
	{
		if (isset($this->_expandedOptionsNames)) {
			return $this->_expandedOptionsNames;
		}
		
		$this->_expandedOptionsNames = array();
		$options = $this->get("productOptions");
		foreach ($options as $opt) {
			$type = strtolower($opt->get("opttype"));
			if ($type == "radio button" || $type == "selectbox") {
				$this->_expandedOptionsNames []= $opt->get("opttext");
			}
		}
		return $this->_expandedOptionsNames;
	}

	function getFlatOptions()
	{
		if (isset($this->_flatOptions)) {
			return $this->_flatOptions;
		}
		
		$this->_flatOptions = array();
		$options = $this->get("productOptions");
		foreach ($options as $opt) {
			$type = strtolower($opt->get("opttype"));
			if ($type != "radio button" && $type != "selectbox") {
				$this->_flatOptions []= $opt;
			}
		}
		return $this->_flatOptions;
	}
	
	function getExpandedItems()
	{
		if (isset($this->expandedProductOptions)) {
			return $this->expandedProductOptions;
		}
		
		$found_options = array();
		
		if (!$this->xlite->get("ProductOptionsEnabled") || !$this->hasOptions()) {
			$this->expandedProductOptions = $found_options;
			return $found_options;
		}
		$options = $this->get("productOptions");
		$dst = array();
		foreach($options as $option) {
			$type = strtolower($option->get("opttype"));
			if ($type == "radio button" || $type == "selectbox") {
				$dst []= $option->get("productOptions");
			}	
		}

		if (empty($dst)) {
			$this->expandedProductOptions = $found_options;
			return $found_options;
		}	

		require_once "modules/WholesaleTrading/encoded.php";
		func_wholesaleTrading_selections($dst, $found_options);
		
		// remove options marked as exceptions {{{ 
		$exceptions_list = $this->get("optionExceptions");
		if (!empty($exceptions_list)) {
			foreach ($exceptions_list as $k => $v) {
				$exceptions = array();
				$exception = $v->get("exception");
				$columns = explode(";", $exception);
				// Trim exceptions
				foreach ($columns as $subvalue) {
					$exception = explode ("=", $subvalue);
					$exception_optclass = trim($exception[0]);
					$exception_option = trim($exception[1]);
					$exceptions[$exception_optclass] = $exception_option;
				}
			}

			$found = false;
			do {
				$found = false;
				for ($i = 0; $i < count($found_options); $i++) {
					$opt_array = array();
					foreach ($found_options[$i] as $_opt) {
						$opt_array[$_opt->class] = $_opt->option;	
					}
					$ex_size = sizeof($exceptions);
					$ex_found = 0;
					foreach ($exceptions as $subkey => $subvalue) {
						if ($opt_array[$subkey] == $subvalue) {
							$ex_found ++;
						}
					}
					if ($ex_found == $ex_size) {
						array_splice($found_options, $i, 1);
						$found = true;
						break;
					}
				}
			} while ($found != false);
		}
// }}}

		$this->expandedProductOptions = $found_options;
		return $this->expandedProductOptions;
	}

	function getFullPrice($amount, $optionIndex = null, $use_wholesale_price = true)
	{
        if (!$this->is("priceAvailable") && !$this->xlite->is("adminZone")) {
            return $this->get("config.WholesaleTrading.price_denied_message");
        }

		$wholesale_price = false;
		if ($use_wholesale_price) {
    	    $wp =& func_new("WholesalePricing");
	        $profile = $this->auth->get("profile");
        	$membership = (is_object($profile)) ? " OR membership='" . $profile->get("membership") . "'" : "";
	        $wholesale_prices = $wp->getProductPrices($this->get("product_id"), $amount, $membership);
    	    if (count($wholesale_prices) != 0) {
        	    $wholesale_price = $wholesale_prices[count($wholesale_prices) - 1]->get("price");
				$this->set("price", $wholesale_price);
	        }
		}
		$price = $this->get("listPrice");
		if (!is_null($optionIndex)) {
			$surcharge = 0;
			$originalPrice = $price;

			$opts = $this->get("expandedItems");
			foreach ($opts[$optionIndex] as $option) {
				$po =& func_new("ProductOption");
				$po->set("product_id", $this->get("product_id"));

				$modifiedPrice = ($wholesale_price === false)?($po->_modifiedPrice($option)):($po->_modifiedPrice($option, false, $wholesale_price));
				$surcharge += $modifiedPrice - $originalPrice;
			}

			$price = $originalPrice + $surcharge;
		}
		return $price;
	}

	function getAmountByOptions($optionsIndex)
	{
		if (!isset($optionsIndex)) {
			return -1; // -1 means infinity
		}
		$options_arr = $this->get("expandedItems");
		if (!(isset($options_arr[$optionsIndex]) && is_array($options_arr[$optionsIndex]))) {
			return -1; // -1 means infinity
		}
		foreach ($options_arr[$optionsIndex] as $_opt) {
			$option_keys[] = sprintf("%s:%s", $_opt->class, $_opt->option);
		}
		$key = $this->get('key')."|".implode("|", $option_keys);
		$inventory =& func_new("Inventory");
		$inventories = $inventory->findAll("inventory_id LIKE '".$this->get("product_id")."|%' AND enabled=1", "order_by");
		foreach ($inventories as $i) {
			if ($i->keyMatch($key)) {
				return $i->get('amount');
			}    
		}
		return -1; // -1 means infinity
	}

	function _available_action($action)
	{
		if (!isset($this->product_access) || is_null($this->product_access)) {
			$this->product_access =& func_new("ProductAccess");
			$this->product_access->set("product_id", $this->get("product_id"));
		}
		return $this->product_access->groupInAccessList($this->auth->get("profile.membership"), $action . "_group");
	}
	
	function isShowAvailable()
	{
		if ($this->checkDirectSaleAvailable()) {
			return true;
		}

		return $this->_available_action("show");
	}
	
	function isPriceAvailable()
	{
		if ($this->checkDirectSaleAvailable()) {
			return true;
		}

		if (!$this->is("showAvailable")) {
			return false;
		}
		return $this->_available_action("show_price");
	}

	function isSaleAvailable()
	{
		if ($this->checkDirectSaleAvailable()) {
			return true;
		}

		if (!$this->is("priceAvailable")) {
			return false;
		}
		return $this->_available_action("sell");
	}

	function assignDirectSaleAvailable($assign=true)
	{
		$access = $this->session->get("DirectSaleAvailable");
		if (!is_array($access)) {
			$access = array();
		}
		$access[$this->get("product_id")] = $assign;
		$this->session->set("DirectSaleAvailable", $access);
	}

	function checkDirectSaleAvailable()
	{
		$access = $this->session->get("DirectSaleAvailable");
		if (!is_array($access)) {
			$access = array();
		}
		return (isset($access[$this->get("product_id")]) ? $access[$this->get("product_id")] : false);
	}

	function isDirectSaleAvailable()
	{
	    if ($this->config->get("WholesaleTrading.direct_addition")) {
			$this->assignDirectSaleAvailable($this->_available_action("sell"));
			return $this->_available_action("sell");
		} else {
			return $this->isSaleAvailable();
		}
	}

	function filter()
	{
		if ($this->xlite->is("adminZone")) {
			return parent::filter();
		}
		if (parent::filter()) {
			return $this->is("showAvailable");
		} else {
    		if ($this->checkDirectSaleAvailable()) {
    			return true;
    		}
			return false;
		}	
	}

	function isExists()
	{
		$exists = parent::isExists();
		if ((!$exists) && $this->_checkExistanceRequired && $this->_available_action("sell")) return true;
		return $exists;
	}

	function &get($name)
	{
		if ($name == "price" && !$this->is("priceAvailable") && !$this->xlite->is("adminZone")) {
			return $this->get("config.WholesaleTrading.price_denied_message");
		}
		return parent::get($name);
	}

	function getListPrice()
	{
		if (!$this->is("priceAvailable") && !$this->xlite->is("adminZone")) {
			return $this->get("config.WholesaleTrading.price_denied_message");
		}
		return parent::getListPrice();
	}

	function hasWholesalePricing()
	{
		$this->_avail_wholesale_pricing = $this->get("wholesalePricing");
		if (count($this->_avail_wholesale_pricing) > 0) {
			return true;
		}	
		return false;
	}

	function getWholesalePricing()
	{
		if (is_null($this->wholesale_pricing)) {
			$wp =& func_new("WholesalePricing");
			$sqlStr = "product_id=" . $this->get('product_id');
			$sqlStr .= ( $this->auth->is("logged") ) ? " AND (membership='all' OR membership='" . $this->auth->get("profile.membership") . "')" : " AND membership='all'";
			$wholesale_pricing = $wp->findAll($sqlStr);
			$wholesale_pricing_hash = array();
			foreach ($wholesale_pricing as $wpIdx => $wp) {
				if (!isset($wholesale_pricing_hash[$wp->get("amount")])) {
					$wholesale_pricing_hash[$wp->get("amount")] = $wpIdx;
				} else {
					if ($this->auth->is("logged") && $this->auth->get("profile.membership") == $wp->get("membership") && $wholesale_pricing[$wholesale_pricing_hash[$wp->get("amount")]]->get("membership") == "all") {
						$wholesale_pricing_hash[$wp->get("amount")] = $wpIdx;
					}
				}
			}

			$this->wholesale_pricing = array();
			foreach ($wholesale_pricing_hash as $wp => $wpIdx) {
				$this->wholesale_pricing[] = $wholesale_pricing[$wpIdx];
			}
		}

		if ($this->config->get("Taxes.prices_include_tax")) {
			$oldPrice = $this->get("price");

			foreach($this->wholesale_pricing as $wp_idx => $wp) {
				$this->set("price", $wp->get("price"));
				$this->wholesale_pricing[$wp_idx]->set("price", $this->get("listPrice"));
			}

			$this->set("price", $oldPrice);
		}
		return $this->wholesale_pricing;
	}

	function isSellingMembership()
	{
		return strlen($this->get('selling_membership'));
	}

	function getPurchaseLimit()
	{
		$purchase_limit = &func_new("PurchaseLimit");
		$found = $purchase_limit->find("product_id =" . $this->get("product_id"));
		return $found ? $purchase_limit : false;
	}

    function delete()
    {
        // delete product accesses, purchase limits and wholesale prices
        $pa =& func_new("ProductAccess");
        $pl =& func_new("PurchaseLimit");
        $wp =& func_new("WholesalePricing");
        $this->db->query("DELETE FROM ".$pa->getTable(). " WHERE product_id=".$this->get("product_id"));
        $this->db->query("DELETE FROM ".$pl->getTable(). " WHERE product_id=".$this->get("product_id"));
        $this->db->query("DELETE FROM ".$wp->getTable(). " WHERE product_id=".$this->get("product_id"));

        // delete product
        parent::delete();
    }

	/**
	* Remove all unused Wholesale records
	*/
	function collectGarbage()
	{
		parent::collectGarbage();

		$products_table = $this->db->getTableByAlias("products");
		$classes = array("ProductAccess", "PurchaseLimit", "WholesalePricing");
		foreach ($classes as $class) {
			$obj =& func_new($class);

			$class_table = $obj->getTable();
			$sql = "SELECT DISTINCT(o.product_id) AS product_id FROM ".$class_table." o LEFT OUTER JOIN $products_table p ON o.product_id=p.product_id WHERE p.product_id IS NULL";
			$result = $this->db->getAll($sql);

			if (is_array($result) && count($result) > 0) {
				foreach ($result as $info) {
					$this->db->query("DELETE FROM ".$class_table. " WHERE product_id='".$info["product_id"]."'");
				}
			}
		}
	}
    
    function clone() {
		if ( function_exists("func_is_clone_deprecated") && func_is_clone_deprecated() ) {
			$p =& parent::cloneObject();
		} else {
			$p = parent::clone();
		}

        $originalId = $this->get("product_id");
        $newId = $p->get("product_id");        
        
		if ($this->config->get("WholesaleTrading.clone_wholesale_productaccess")) {            
            $productAccess = & func_new("ProductAccess");
            foreach ($productAccess->findAll("product_id=$originalId") as $access) {
                $foo = & func_new("ProductAccess");
                $foo->set("product_id", $newId);
                $foo->set("show_group", $access->get("show_group"));
                $foo->set("show_price_group", $access->get("show_price_group"));
                $foo->set("sell_group", $access->get("sell_group"));
                $foo->create();
            }
        }
        
        if ($this->config->get("WholesaleTrading.clone_wholesale_purchaselimit")) {            
            $purchaseLimit = & func_new("PurchaseLimit");
            foreach ($purchaseLimit->findAll("product_id=$originalId") as $limit) {
                $foo = & func_new("PurchaseLimit");
                $foo->set("product_id", $newId);
                $foo->set("min", $limit->get("min"));
                $foo->set("max", $limit->get("max"));
                $foo->create();
            }
        }
            
        if ($this->config->get("WholesaleTrading.clone_wholesale_pricing")) {
            $wholesalePricing = & func_new("WholesalePricing");
            foreach ($wholesalePricing->findAll("product_id=$originalId") as $pricing) {
                $foo = & func_new("WholesalePricing");
                $foo->set("product_id", $newId);
                $foo->set("amount", $pricing->get("amount"));
                $foo->set("price", $pricing->get("price"));
                $foo->set("membership", $pricing->get("membership"));
                $foo->create();
            }
		}
        return $p;
	}


}
// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
