{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Switcher checkbox
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.15
 *}

<span class="input-field-wrapper {getWrapperClass()}">
  {displayCommentedData(getCommentedData())}
  <input type="hidden" name="{getName()}" value="" />
  <input{getAttributesCode():h} />
  <div class="widget" title="{t(getWidgetTitle())}" data-enable-label="{t(getEnableLabel())}" data-disable-label="{t(getDisableLabel())}"></div>
</span>
