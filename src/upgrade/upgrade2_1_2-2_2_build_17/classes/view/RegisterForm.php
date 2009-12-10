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
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 foldmethod=marker shiftwidth=4: */

/**
* Registration form component.
*
* @package $Package$
* @version $Id: RegisterForm.php,v 1.1 2006/07/11 06:38:30 sheriff Exp $
*/
class CRegisterForm extends Component
{
    var $params = array("success");
    // profile data
    var $profile = null;
    // whether to login user after successful registration or not
    var $autoLogin = true;
    // true if the user already exists in the register form
    var $userExists = false;
    var $allowAnonymous = false;

    function init()
    {
        parent::init();
        $this->mapRequest();
    }
    
    function isShowMembership()
    {
        return count($this->config->get("Memberships.memberships")) > 0;
    }

    function isFromCheckout()
    {
        return (strpos($this->returnUrl, "target=checkout") !== false) ? true : false;
    }

    function fillForm()
    {
        if ($this->get("mode") == "register") {
            // default registration form values
            $this->billing_country = $this->config->get("General.default_country");
            $this->billing_zipcode = $this->config->get("General.default_zipcode");
            $this->shipping_country = "";
            $this->billing_state = $this->shipping_state = "";
        }
        if ($this->get("mode") == "modify" && !is_null($this->get("profile"))) {
            $this->set("properties", $this->get("profile.properties"));
            // don't show passwords
            $this->password = $this->confirm_password = "";
        }
        parent::fillForm();
    }

    function getSuccess()
    {
        return $this->is("valid") && $this->success;
    }
    
    function action_register()
    {
        $this->profile =& func_new("Profile");
        if ($this->xlite->is("adminZone")) {
            $this->profile->set("properties", $_REQUEST);
        } else {
            $this->profile->modifyProperties($_REQUEST);
        }
        if (!$this->isFromCheckout()) {
            $result = $this->auth->register($this->profile);
            if ($result == USER_EXISTS) {
                $this->set("userExists", true);
                $this->set("valid", false); // can't go thru
            } else {
                $this->set("mode", "success"); // go to success page
            }
        } else {
            $this->profile->update();
			$this->set("success", true);
        }
    }

    function action_modify()
    {
        if ($this->xlite->is("adminZone")) {
            $this->profile->set("properties", $_REQUEST);
        } else {
            $this->profile->modifyProperties($_REQUEST);
        }
        if (!$this->isFromCheckout()) {
            $result = $this->auth->modify($this->profile);
            if ($result == USER_EXISTS) {
                // user already exists
                $this->set("userExists", true);
                $this->set("valid", false);
            } else {
                $this->set("success", true);
            }
        } else {
            $this->profile->update();
			$this->set("success", true);
        }
    }
    
}
// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
