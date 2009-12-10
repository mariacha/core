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
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 foldmethod=marker shiftwidth=4: */

/**
* Module_DetailedImages_Product description.
*
* @package Module_DetailedImage
* @access public
* @version $Id: Product.php,v 1.14 2008/10/23 11:53:16 sheriff Exp $
*/
class Module_DetailedImages_Product extends Product
{
    function &getDetailedImages()
    {
        $image =& func_new("DetailedImage");
        $images = $image->findImages($this->get("product_id"));
        return $images;
    }
    
    function delete()
    {
		$images =& $this->get("detailedImages");
		foreach ($images as $image) {
			$image->delete();
		}
        parent::delete();
    }

	function clone()
	{
		if ( function_exists("func_is_clone_deprecated") && func_is_clone_deprecated() ) {
			$product =& parent::cloneObject();
		} else {
			$product =& parent::clone();
		}

		$images = $this->get("detailedImages");
		foreach ($images as $image) {
			$newImage =& func_new("DetailedImage");
			$newImage->set("alt", $image->get("alt"));
			$newImage->set("enabled", $image->get("enabled"));
			$newImage->set("order_by", $image->get("order_by"));
			$newImage->set("product_id", $product->get("product_id"));
			$newImage->create();

			$obj = $this->get("image");
			if (!method_exists($obj, " copyImageFile")) {
				// use correct image copy routine for LC version lower than 2.2
				$image->deepCopyTo($newImage->get("image_id"));
			} else {
				$obj->copyTo($newImage->get("image_id"));
			}
		}

		return $product;
	}

	/**
	* Remove all unused detailed images
	*/
	function collectGarbage()
	{
		parent::collectGarbage();

		$products_table = $this->db->getTableByAlias("products");
		$detailed_images_table = $this->db->getTableByAlias("images");
		$sql = "SELECT i.image_id FROM $detailed_images_table i LEFT OUTER JOIN $products_table p ON i.product_id=p.product_id WHERE p.product_id IS NULL";
		$result = $this->db->getAll($sql);

		if (is_array($result) && count($result) > 0) {
			foreach ($result as $info) {
				$di =& func_new("DetailedImage", $info["image_id"]);
				$di->delete();
			}
		}
	}
}
// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
