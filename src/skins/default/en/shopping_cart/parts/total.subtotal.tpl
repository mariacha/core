{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Shopping cart subtotal
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="cart.totals", weight="10")
 * @ListChild (list="cart.panel.totals", weight="10")
 *}
<li class="subtotal">
  <strong>{t(#Subtotal#)}:</strong>
  <span class="cart-subtotal{if:cart.getIncludeSurcharges()} modified-subtotal{end:}">{formatPrice(cart.getSubtotal(),cart.getCurrency())}</span>
  <div IF="cart.getIncludeSurcharges()" class="including-modifiers" style="display: none;">
    <table class="including-modifiers" cellspacing="0">
      <tr FOREACH="cart.getIncludeSurcharges(),surcharge">
        <td class="name">{t(#Including X#,_ARRAY_(#name#^surcharge.getName()))}:</td>
        <td class="value">{formatPrice(surcharge.getValue(),cart.getCurrency())}</td>
      </tr>
    </table>
  </div>
</li>
