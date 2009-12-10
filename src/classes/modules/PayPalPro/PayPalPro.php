<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URL: http://www.litecommerce.com/license.php                |
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
| The Initial Developer of the Original Code is Ruslan R. Fazliev              |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2003 Creative        |
| Development. All Rights Reserved.                                            |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* 
*
* @package 
* @access public
* @version $Id: PayPalPro.php,v 1.9 2008/10/23 11:58:02 sheriff Exp $
*/
class Module_PayPalPro extends Module // {{{ 
{
	var $minVer = "2.0";
	var $showSettingsForm = true;

	function &getSettingsForm() // {{{ 
	{
		return "admin.php?target=payment_method&payment_method=paypalpro";

	} // }}} 
	
	function init() // {{{
    {
		if(!check_module_license("PayPalPro", true)) return;
		   
        parent::init();

        $pm =& func_new("PaymentMethod","paypalpro");
		
		switch($pm->get("params.solution")) {
			case "standard":
				$pm->checkServiceURL();
			    $pm->registerMethod("paypalpro"); 
			break;
			case "pro": 	
		        $pm->registerMethod("paypalpro");
		        $pm->registerMethod("paypalpro_express"); 
		    break;
			case "express":				
		        $pm->registerMethod("paypalpro_express"); 
		    break;
		}

		$this->addDecorator("Order", "Order_PayPalPro");

		$this->addDecorator("Dialog_profile","Module_PayPalPro_Dialog_profile");
        $this->addDecorator("Dialog_checkout","Dialog_standard_checkout");

		if($this->xlite->is("adminZone")) {
			$this->addDecorator("Admin_Dialog_payment_method","Module_PayPalPro_Admin_Dialog_payment_method");
			$this->addDecorator("Admin_Dialog_modules", "Module_PayPalPro_Admin_Dialog_modules");
		}	

		if ($this->xlite->mm->get("activeModules.PayPal")) {
			$modules = $this->xlite->mm->get("modules");
			$ids = array();
        	foreach ($modules as $module) {
        		if ($module->get("name") != "PayPal" && $module->get("enabled") ) {
        			$ids[] = $module->get("module_id");
        		}
			}
			$this->xlite->mm->updateModules($ids);
			$this->session->set("PayPalOff", true);
		}

    	$this->xlite->set("PayPalProEnabled", true);
		$this->xlite->set("PayPalProSolution",$pm->get("params.solution"));

        if ($pm->get("params.solution") != "standard") {
			$pm_express =& func_new("PaymentMethod", "paypalpro_express");
            $this->xlite->set("PayPalProExpressEnabled", $pm_express->get("enabled"));
        }
    } // }}} 

    function uninstall() // {{{ 
    {
        func_cleanup_cache("classes");
        func_cleanup_cache("skins");

        parent::uninstall();

    } // }}}

} // }}}

// WARNING:
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
