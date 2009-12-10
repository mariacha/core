<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003 Creative Development <info@creativedevelopment.biz>       |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URL:                                                        |
| http://www.litecommerce.com/software_license_agreement.html                  |
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
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Ruslan R. Fazliev              |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2003 Creative        |
| Development. All Rights Reserved.                                            |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* @package Module_ProtxForm
* @access public
* @version $Id: protxform.php,v 1.3 2007/07/12 12:52:41 osipov Exp $
*/
class PaymentMethod_protxform_cc extends PaymentMethod_credit_card
{
    var $processorName = "ProtxForm";
	var $hasConfigurationForm = true;
    var $configurationTemplate = "modules/ProtxForm/config.tpl";

    function handleRequest(&$cart)
    {
        require_once "modules/ProtxForm/encoded.php";
        PaymentMethod_ProtxForm_handleRequest($this, $cart);
    }

	function getFormTemplate()
	{
		return "modules/ProtxForm/checkout.tpl";
	}

	function getSuccessUrl($order_id)
	{
		return $this->xlite->shopURL("cart.php?target=protxform_checkout&action=return", $this->get("config.Security.customer_security"));
	}

	function getFailureUrl($order_id)
	{
		return $this->xlite->shopURL("cart.php?target=protxform_checkout&action=return&failed=1", $this->get("config.Security.customer_security"));
	}

	function getSucessedStatus()
	{
		return "P";
	}

	function getFailedStatus()
	{
		return "F";
	}

	function getQueuedStatus()
	{
		return "Q";
	}


//////////// Fill "Protx VSP Form" form methods ////////////
	function getVendorName()
	{
		return $this->get("params.vendor");
	}

	function getFormPostUrl($is_simulator=false)
	{
		if ($is_simulatror) {
			return "https://ukvpstest.protx.com/VSPSimulator/VSPFormGateway.asp";
		}

		return (($this->get("params.testmode") == "N") ? "https://ukvps.protx.com/vps2form/submit.asp" : "https://ukvpstest.protx.com/vps2form/submit.asp");
	}

	function getCryptedInfo($cart)
	{
		include_once "modules/ProtxForm/encoded.php";

		return func_ProtxForm_compileInfoCrypt($this, $cart);
	}

	function getPaymentType()
	{
		if (in_array($this->get("params.trans_type"), array("PAYMENT", "DEFERRED")))
			return $this->get("params.trans_type");

		return "DEFERRED";
	}

}
// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
