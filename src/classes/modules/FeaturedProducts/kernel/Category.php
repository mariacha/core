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
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 foldmethod=marker shiftwidth=4: */

/**
* @package Module_FeaturedProducts
* @access public
* @version $Id: Category.php,v 1.12 2008/10/23 11:54:01 sheriff Exp $
*/
class Module_FeaturedProducts_Category extends Category
{
    function getFeaturedProducts()
    {
        if (is_null($this->featuredProducts)) {
            $fp = func_new("FeaturedProduct");
            $id = $this->get("category_id");
            $products = $fp->findAll("category_id='$id'");
            for ($i = 0; $i < count($products); $i++) {
                $categories = $products[$i]->product->get("categories");
                if (!empty($categories)) {
                    $products[$i]->product->category_id = $categories[0]->get("category_id");
                }    
            }
            $this->featuredProducts = $products;
        }
        return $this->featuredProducts;
    }

	function addFeaturedProducts($products)
	{
		for ($i=0; $i<count($products); $i++) {
			$fp = func_new("FeaturedProduct");
			$fp->set("category_id", $this->get("category_id"));
			$fp->set("product_id", $products[$i]->get("product_id"));
			if (!$fp->isExists()) {
				$fp->create();
			}
		}
	}

	function deleteFeaturedProducts($products)
	{
		for ($i=0; $i<count($products); $i++) {
			$fp = func_new("FeaturedProduct");
			$fp->set("category_id", $this->get("category_id"));
			$fp->set("product_id", $products[$i]->get("product_id"));
			$fp->delete();
		}	
	}

	function delete()
	{
		$this->deleteFeaturedProducts($this->getFeaturedProducts());
		parent::delete();
	}

}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
