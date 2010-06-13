{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Search by category
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 * @ListChild (class="XLite_Module_AdvancedSearch_View_AdvancedSearch", weight="40")
 *}
<tr class="search-category form-field">
  <td colspan="3">
    <label for="search-category">Category:</label>
    <widget template="modules/AdvancedSearch/select_category.tpl" class="XLite_View_CategorySelect" fieldName="search[category]" allOption selectedCategoryId="{search.category}" />
  </td>
</tr>
