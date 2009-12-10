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
* Tax management dialog.
*
* @package Dialog
* @access public
* @version $Id: taxes.php,v 1.1 2004/11/22 09:19:48 sheriff Exp $
*
*/
class Admin_Dialog_taxes extends Admin_Dialog
{
    var $taxes;
    var $_rates;
    var $_levels;
    
    function init()
    {
        parent::init();
        $this->taxes = func_new("TaxRates");
        $this->getRates();
        if ($this->get("mode") == "add") {
            $this->initRuleParams();
        } elseif ($this->get("mode") == "edit") {
            $this->action_edit();
        }
    }
    
    function action_update_options()
    {
        $taxes = array();
        if (isset($_POST["pos"])) {
            foreach ($_POST["pos"] as $ind => $pos) {
                $tax = array("name" => $_POST["name"][$ind], "display_label" => $_POST["display_label"][$ind]);
                $taxes[] = $tax;
            }
        }
        if ($_POST["new_name"] != "") {
            $taxes[] = array("name" => $_POST["new_name"], "display_label" => $_POST["new_display_label"]);
            if (!isset($_POST["pos"])) {
                $_POST["pos"] = array();
            }    
            $_POST["pos"][] = $_POST["new_pos"];
        }
        if (isset($_POST["pos"])) {
            array_multisort($_POST["pos"], $taxes);
        }

        $schema = array("taxes" => $taxes, "use_billing_info" => $_POST["use_billing_info"], "prices_include_tax" => $_POST["prices_include_tax"],"include_tax_message" => $_POST["include_tax_message"]);
        $this->taxes->setSchema($schema);
    }

    function action_delete_tax()
    {
        
        $taxes = unserialize($this->config->get("Taxes.taxes"));
        array_splice($taxes, $_REQUEST["ind"], 1);
        $c = func_new("Config");
        $c->set("category", "Taxes");
        $c->set("name", "taxes");
        $c->set("value", serialize($taxes)); 
        $c->update();
    }

    function action_reset()
    {
        // reset to a pre-defined schema
        $schemaName = $_POST["schema"];
        $this->taxes->setPredefinedSchema($schemaName);
    }

    function action_delete_schema()
    {
        $name = $_POST["schema"];
        $tax = func_new("TaxRates");
        $tax->saveSchema($name, null);
    }
    
    function action_update_rates()
    {
        // update rates
        if (isset($_POST["varvalue"])) {
            foreach ($_POST["varvalue"] as $ind => $value) {
                // find the corresponding cell in the rates tree
                $ptr =& $this->locateNode($this->taxes->_rates, $this->_levels[$ind]);
                if (is_array($ptr)) {
                    // conditional
                    $ptr["action"] = $this->_insertValue($ptr["action"], $value);
                } else {
                    $ptr = $this->_insertValue($ptr, $value);
                }
            }
        }
        // sort rates
        if (isset($_POST["pos"])) {
            // build a pos tree
            $posTree = array();
            foreach ($_POST["pos"] as $ind => $pos) {
                $levels = $this->_levels[$ind];
                array_pop($levels);
                // locate the corresponding pos array in the pos tree
                $ptr =& $this->locateNode($posTree, $levels);
                if (!isset($ptr["orderbys"])) {
                    $ptr["orderbys"] = array();
                }
                $ptr["orderbys"][$ind] = $pos;
            }
            // sort all lists recursively
            $this->_sortRates($this->taxes->_rates, $posTree);
        }
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
    }

    function action_open()
    {
        $ind = $_REQUEST["ind"];
        $node =& $this->locateNode($this->taxes->_rates, $this->_levels[$ind]);
        $node["open"] = true;
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
        $this->getRates(); // re-build the tree
    }

    function action_all()
    {
        $this->changeAll($this->taxes->_rates, $_REQUEST["open"]);
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
        $this->getRates(); // re-build the tree
    }
    
    function changeAll(&$tree, $open)
    {
        for ($i=0; $i<count($tree); $i++) {
            if ($this->isCondition($tree[$i])) {
                if ($open) {
                    $tree[$i]["open"] = true;
                } else {
                    if (isset($tree[$i]["open"])) {
                        unset($tree[$i]["open"]);
                    }
                }
                $this->changeAll($tree[$i]["action"], $open);
            }
        }
    }
   
    function action_close()
    {
        $ind = $_REQUEST["ind"];
        $node =& $this->locateNode($this->taxes->_rates, $this->_levels[$ind]);
        if (isset($node["open"])) {
            unset($node["open"]);
        }    
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
        $this->getRates(); // re-build the tree
    }

    function action_edit()
    {
        $this->taxes = func_new("TaxRates");
        if (isset($_REQUEST["ind"]) && $_REQUEST["ind"] !== '') {
            $this->ind = $ind = $_REQUEST["ind"];
            $this->tax = $this->locateNode($this->taxes->_rates, explode(',',$ind));
        } else {
            $this->tax = '';
        }
        $this->initRuleParams();
        $this->edit = 1;
    }
   
    function action_add()
    {
        $this->action_edit();
        $this->edit = 0;
        $this->tax = '';
    }
    
    function _readTaxForm()
    {
        if (isset($_REQUEST["ind"])) {
            $ind = $_REQUEST["ind"];
            $this->ind = $ind;
            $this->taxes = func_new("TaxRates");
            if ($ind === '') {
                $ind = array();
            } else {    
                $ind = explode(',',$ind);
            }    
            $this->indexes = $ind;
        }

        $this->initRuleParams();
        $conjuncts = array();
        foreach ($this->taxParams as $param) {
            if (!isset($_POST[$param->var])) {
                continue;
            }
            if (trim($_POST[$param->var]) !== '') {
                $conjuncts[] = "$param->cond=".$_POST[$param->var];
            }
        }
        $condition = join(' AND ', $conjuncts);
        $action = '';
        $taxValue = trim($_POST["taxValue"]);
        $taxName = trim($_POST["taxName"]);
        if ($taxName !== '' && $taxValue !== '') {
            if (is_numeric($taxValue) || $taxValue{0} == '=') {
                $action = "$taxName:=$taxValue";
            } else {
                $this->error = "Tax value must be a number or contain '=' at its start: '$taxValue'";
                return null;
            }
        }
        if ($action !== '' && $condition === '') {
            return $action;
        }
        if ($action !== '' && $condition !== '') {
            return array("condition" => $condition, "action" => $action);
        }
        if ($action === '' && $condition !== '') {
            return array("condition" => $condition, "action" => array(), "open" => true);
        }
        $this->error = "Form is empty";
        return null;
    }
    
    function action_add_submit()
    {
        $node = $this->_readTaxForm();
        
        if (is_null($node)) {
            $this->action_add(); // show errors and the form again
        } else {
            $subTree =& $this->locateNode($this->taxes->_rates, $this->indexes);
            if (isset($subTree["action"])) {
                $subTree["action"][] = $node;
            } else {
                $subTree[] = $node;
            }
            // store
            $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
            if ($_POST["add_another"]) {
                $this->set("mode", "add");
            } else {
                $this->set("mode", "");
            }
        }
    }

    function action_edit_submit()
    {
        $node = $this->_readTaxForm();
        if (is_null($node)) {
            $this->action_edit(); // show errors and the form again
        } else {
            $subTree =& $this->locateNode($this->taxes->_rates, $this->indexes);
            $subTree = $node;
            // store
            $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));
            $this->set("mode", "");
        }
    }

    function action_delete_rate()
    {
        $ind = $this->_levels[$_REQUEST["ind"]];
        $subTreeIndex = $ind;
        $lastIndex = array_pop($subTreeIndex); // remove last
        $subTree =& $this->locateNode($this->taxes->_rates, $subTreeIndex);
        if (isset($subTree[$lastIndex])) {
            unset($subTree[$lastIndex]);
        }
        if (isset($subTree["action"][$lastIndex])) {
            unset($subTree["action"][$lastIndex]);
        }
        // store
        $this->taxes->setSchema(array("tax_rates" => $this->taxes->_rates));

        $this->set("mode", "");
    }

    function getTaxCondParam($node, $param)
    {
        if (is_array($node)) {
            $cond = $node["condition"];
            $taxParams = $this->taxes->_parseCondition($cond);
            if (isset($taxParams[$param])) {
                return $taxParams[$param];
            }    
        }
        return null;
    }
    
    function initRuleParams()
    {
        $countries = func_new("Object");
        $countries->name = 'Countries';
        $countries->var  = 'country';
        $countries->cond  = 'country';
        $countries->values = array("EU country");
        $c = func_new("Country");
        foreach ($c->findAll() as $country) {
            $countries->values[] = $country->get("country");
        }
        $states = func_new("Object");
        $states->name = 'States';
        $states->var  = 'state';
        $states->cond  = 'state';
        $states->values = array();
        $c = func_new("State");
        foreach ($c->findAll() as $state) {
            $states->values[] = $state->get("state");
        }
        
        $cities = func_new("Object");
        $cities->name = "Cities";
        $cities->var = "city";
        $cities->cond = "city";
        $cities->values = array();
        $pr = func_new("Profile");
        foreach ($pr->findAll() as $p) {
            $cities->values[] = $p->get("shipping_city");
        }
        array_unique($cities->values);
        if (isset($cities->values[''])) {
            unset($cities->values['']);
        }

        $pm = func_new("Object");
        $pm->name = "Payment method";
        $pm->var = "pm";
        $pm->cond = "payment method";
        $pm->values = array();
        $pmethod = func_new("PaymentMethod");
        $methods = $pmethod->getActiveMethods();
        foreach ($methods as $method) {
            $pm->values[] = $method->get("name");
        }
        
        $classes = func_new("Object");
        $classes->name = "Product class, either new or existing";
        $classes->var = "pclass";
        $classes->cond = "product class";
        $classes->values = array_unique(array_merge(array("shipping service"), $this->taxes->getProductClasses()));
        array_multisort($classes->values);

        $memberships = func_new("Object");
        $memberships->name = "User membership level";
        $memberships->var = "membership";
        $memberships->cond = "membership";
        $memberships->values = array("No membership");
        
        $memberships->values = array_merge($memberships->values, $this->config->Memberships->memberships);

        $zips = func_new("Object");
        $zips->name = "Zip codes/ranges (e.g. 43200-43300,55555)";
        $zips->var = "zip";
        $zips->cond = "zip";
        $zips->values = array();
        
        $this->taxParams = array($countries,$states,$cities,$pm,$classes,$memberships, $zips);
        $this->taxNames = $this->taxes->getTaxNames();
    }

    function _sortRates(&$rateTree, &$pos)
    {
        for ($i=0; $i<count($rateTree); $i++) {
            // sort children
            if (is_array($rateTree[$i]) && is_array($rateTree[$i]["action"])) {
                if (!isset($pos[$i])) {
                    continue;
                }    
                $this->_sortRates($rateTree[$i]["action"], $pos[$i]);
            }  
        }
        if (!is_array($pos["orderbys"])) {
            print "pos = "; print_r($pos);
            $this->_die("pos['orderbys'] must be an array");
        }
        $ratesToSort = $rateTree;
        array_multisort($pos["orderbys"], $ratesToSort);
        $rateTree = $ratesToSort;
    }
    
    function &locateNode(&$tree, $path)
    {
        $ptr =& $tree;
        foreach ($path as $index) {
            if (isset($ptr["action"])) {
                $ptr =& $ptr["action"];
            }
            if (!isset($ptr[$index])) {
                // create a node 
                $ptr[$index] = array();
            }
            $ptr =& $ptr[$index];
        }
        return $ptr;
    }
    
    function _insertValue($expr, $value)
    {
        list($name,$oldval) = explode(':=', $expr);
        if (!isset($oldval)) {
            $this->_die("expr=$expr - wrong format");
        }
        return "$name:=$value";
    }
    
    function getIndex($ind)
    {
        return ($ind+1)*10;
    }
    function getPath($ind)
    {
        return join(',', $this->_levels[$ind]);
    }
    function getTaxName($tax)
    {
        return $tax["name"];
    }
    function getNoteTaxName($node)
    {
        if (is_array($node)) {
            $node = $node["action"];
            if (is_array($node)) {
                return '';
            }
        }
        return $this->getVarName($node);
    }
    function getNoteTaxValue($node)
    {
        if (is_array($node)) {
            $node = $node["action"];
            if (is_array($node)) {
                return '';
            }
        }
        return $this->getVarValue($node);
    }

    function getVarName($expr)
    {
        list($name) = explode(':=', $expr);
        return $name;
    }
    function getCondVarName($expr)
    {
        $expr = $expr["action"];
        list($name) = explode(':=', $expr);
        return $name;
    }
    function getVarValue($expr)
    {
        list($name,$value) = explode(':=', $expr);
        return $value;
    }
    function getCondVarValue($expr)
    {
        $expr = $expr["action"];
        list($name,$value) = explode(':=', $expr);
        return $value;
    }
    function getCondParam($node, $param)
    {
        if (is_array($node)) {
            $cond = $this->taxes->_parseCondition($node["condition"]);
            if (isset($cond[$param])) {
                return join(',', $cond[$param]);
            }
        }
        return '';
    }

    function getDisplayName($tax)
    {
        return $tax["display_label"];
    }
    function getRates()
    {
        $ind = 0;
        $this->_rates = array();
        $this->_levels = array();
        $this->_maxLevel = 0;
        $this->_initRates($this->taxes->_rates, array(), $ind);
    }
    function _initRates($rates, $levels, &$ind)
    {
        if ($this->_maxLevel < count($levels)) {
            $this->_maxLevel = count($levels);
        }
        if (!is_array($rates)) {
        
            $this->_die ("rates='$rates' must be array");
        }
        foreach ($rates as $ind_rate => $rate) {
            $this->_rates[$ind] = $rate;
            $this->_levels[$ind] = $levels;
            $this->_levels[$ind][] = $ind_rate;
            $ind++;
            if (is_array($rate)) {
                if (is_array($rate["action"]) && isset($rate["open"])) {
                    $levels1 = $levels;
                    $levels1[] = $ind_rate;
                    $this->_initRates($rate["action"], $levels1, $ind);
                }    
            }
        }
    }
    function isOpen($row)
    {
        return isset($row["open"]);
    }
    function getLevels($ind)
    {
        $result = "";
        $count = count($this->_levels[$ind])-1;
        for ($i=0; $i<$count; $i++) {
            $result .= "<td width=\"35\"></td>";
        }
        return $result;
    }
    function getCondition($cond)
    {
        return $cond["condition"];
    }
    function getAction($action)
    {
        if (is_array($action)) {
            // conditional tax
            return $action["action"];
        } else {
            // action itself
            return $action;
        }
    }    
    function isAction($a) 
    {
        return is_scalar($a);
    }
    function isCondition($a)
    {
        return is_array($a) && is_array($a["action"]);
    }
    function isConditionalAction($a)
    {
        return is_array($a) && is_scalar($a["action"]);
    }    
    function getColspan($ind, $additional=1)
    {
        return $this->_maxLevel-count($this->_levels[$ind])+$additional+1;
    }
    function getMaxColspan($additional=0)
    {
        return $this->_maxLevel+$additional;
    }
    function getTreePos($ind)
    {
        $levels = $this->_levels[$ind];
        return $levels[count($levels)-1]*10 + 10;
    }
    function getHeaderMargin()
    {
        $result = '';
        for ($i=0; $i<$this->_maxLevel; $i++) {
            $result .= "<th></th>";
        }    
        return $result;
    }
    function isLast($ind)
    {
        return !isset($this->_levels[$ind+1]) || count($this->_levels[$ind+1]) < count($this->_levels[$ind]);
    }

	function action_calculator()
	{
		$this->set("properties", $_POST);
		// default values
		$profile =& $this->auth->get("profile");
		if (!isset($this->country)) {
			if ($profile->get("billing_country")) {
				$this->country = $profile->get("billing_country");
			} else {
				$this->country = "US";
			}
		}
		if (!isset($this->state)) {
			if ($profile->get("billing_state")) {
				$this->state = $profile->get("billing_state");
			} else {
				$state = func_new("State");
				$state->find("code='OK'");
				$this->state = $state->get("state_id");
			}
		}
		$tax = func_new("TaxRates");
		// setup tax rate calculator
		foreach($_POST as $name => $value) {
			$name1 = str_replace("_", " ", $name);
			$tax->_conditionValues[$name1] = $this->$name;
		}
		if (isset($this->country)) {
			$country = func_new("Country",$this->country);
			$tax->_conditionValues["country"] = $country->get("country");
		}
		if (isset($this->state)) {
			$state = func_new("State",$this->state);
			$tax->_conditionValues["state"] = $state->get("state");
		}

		// calculate taxes
		$tax->calculateTaxes();
		$this->item_taxes = $tax->_taxValues;
        foreach ($this->item_taxes as $taxkey => $taxvalue) {
            $this->item_taxes[$taxkey] = $tax->_calcFormula($taxvalue);
        }
		$tax->_conditionValues['product class'] = 'shipping service';
		$tax->calculateTaxes();
		$this->shipping_taxes = $tax->_taxValues;
        foreach ($this->shipping_taxes as $taxkey => $taxvalue) {
            $this->shipping_taxes[$taxkey] = $tax->_calcFormula($taxvalue);
        }

        // show tax calculator
        $w = func_new("Widget");
        $w->component =& $this;
        $w->set("template", "tax/calculator.tpl");
        $w->init();
        $w->display();
        // do not output anything
        $this->set("silent", true);
	}

	function isDoubleValue($value)
	{
		if (strcmp(strval(doubleval($value)), strval($value)) == 0)
		{
			return true;
		}
		return false;
	}

    function action_save()
    {
        $name = $this->get("save_schema");
        if ($name == "") {
            $name = $this->get("new_name");
        }

        $tax = func_new("TaxRates");
        $tax->saveSchema($name);
    }

    function action_export()
    {
        $name = $this->get("export_schema");
        $tax = func_new("TaxRates");
        $schema = $tax->get("predefinedSchemas.$name");
        if (!is_null($schema)) {
            $this->set("silent", true);
            $this->startDownload("$name.tax");
            print serialize($schema);
        }
    }

    function action_import()
    {
        $file = $this->get("uploadedFile");
        if (is_null($file)) {
            return;
        }    
        $name = basename($_FILES['userfile']['name'], ".tax");
        $schema = unserialize(file_get_contents($file));
        $tax = func_new("TaxRates");
        $tax->saveSchema($name, $schema);
    }

    function &getEdit()
    {
        return $this->get("mode") == "edit";
    }
}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
