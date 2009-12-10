<?php

$source = strReplace('<tr><td>E-mail:</td><td>{cart.profile.login}</td></tr>'."\n".'<tr><td colspan="2">&nbsp;</td></tr>', '<tr><td>E-mail:</td><td>{cart.profile.login}</td></tr>'."\n".'<tr><td colspan="2">&nbsp;</td></tr>'."\n</table>\n<table border=0 width=80%>\n<tr><td valign=top>\n<table border=0>", $source, __FILE__, __LINE__);
$search =<<<EOT
<tr><td>First Name:</td><td>{cart.profile.billing_firstname}</td></tr>
<tr><td>Last Name:</td><td>{cart.profile.billing_lastname}</td></tr>
<tr><td>Phone:</td><td>{cart.profile.billing_phone}</td></tr>
<tr><td>Fax:</td><td>{cart.profile.billing_fax}</td></tr>
<tr><td>Address:</td><td>{cart.profile.billing_address}</td></tr>
<tr><td>City:</td><td>{cart.profile.billing_city}</td></tr>
<tr><td>State:</td><td>{cart.profile.billingState.state}</td></tr>
<tr><td>Country:</td><td>{cart.profile.billingCountry.country}</td></tr>
<tr><td>Zip code:</td><td>{cart.profile.billing_zipcode}</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>

<tr><td colspan="2"><b>Shipping Information:</b><hr></td></tr>
EOT;

$replace =<<<EOT
<tr><td nowrap>First Name:</td><td>{cart.profile.billing_firstname}</td></tr>
<tr><td nowrap>Last Name:</td><td>{cart.profile.billing_lastname}</td></tr>
<tr><td nowrap>Phone:</td><td>{cart.profile.billing_phone}</td></tr>
<tr><td nowrap>Fax:</td><td>{cart.profile.billing_fax}</td></tr>
<tr><td nowrap>Address:</td><td>{cart.profile.billing_address}</td></tr>
<tr><td nowrap>City:</td><td>{cart.profile.billing_city}</td></tr>
<tr><td nowrap>State:</td><td>{cart.profile.billingState.state}</td></tr>
<tr><td nowrap>Country:</td><td>{cart.profile.billingCountry.country}</td></tr>
<tr><td nowrap>Zip code:</td><td>{cart.profile.billing_zipcode}</td></tr>
</table></td>
<td width=10></td><td valign=top>
<table border=0>
<tr> <td colspan="2"><b>Shipping Information:</b><hr></td></tr>
EOT;

$source = strReplace($search, $replace, $source, __FILE__, __LINE__);

$search =<<<EOT
<tr><td>First Name:</td><td>{cart.profile.shipping_firstname}</td></tr>
<tr><td>Last Name:</td><td>{cart.profile.shipping_lastname}</td></tr>
<tr><td>Phone:</td><td>{cart.profile.shipping_phone}</td></tr>
<tr><td>Fax:</td><td>{cart.profile.shipping_fax}</td></tr>
<tr><td>Address:</td><td>{cart.profile.shipping_address}</td></tr>
<tr><td>City:</td><td>{cart.profile.shipping_city}</td></tr>
<tr><td>State:</td><td>{cart.profile.shippingState.state}</td></tr>
<tr><td>Country:</td><td>{cart.profile.shippingCountry.country}</td></tr>
<tr><td>Zip code:</td><td>{cart.profile.shipping_zipcode}</td></tr>
</table>
EOT;

$replace =<<<EOT
<tr><td nowrap>First Name:</td><td>{cart.profile.shipping_firstname}</td></tr>
<tr><td nowrap>Last Name:</td><td>{cart.profile.shipping_lastname}</td></tr>
<tr><td nowrap>Phone:</td><td>{cart.profile.shipping_phone}</td></tr>
<tr><td nowrap>Fax:</td><td>{cart.profile.shipping_fax}</td></tr>
<tr><td nowrap>Address:</td><td>{cart.profile.shipping_address}</td></tr>
<tr><td nowrap>City:</td><td>{cart.profile.shipping_city}</td></tr>
<tr><td nowrap>State:</td><td>{cart.profile.shippingState.state}</td></tr>
<tr><td nowrap>Country:</td><td>{cart.profile.shippingCountry.country}</td></tr>
<tr><td nowrap>Zip code:</td><td>{cart.profile.shipping_zipcode}</td></tr>
</table></td>
</tr>
</table>

<p><b><a href="cart.php?target=profile&mode=modify&returnUrl={url:u}"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"><font class="FormButton"> Modify address information</font></a></b>
EOT;

$source = strReplace($search, $replace, $source, __FILE__, __LINE__);

$search =<<<EOT
<b><a href="cart.php?target=checkout&action=change_payment"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"><font class="FormButton"> Change payment method</font></a></b>
EOT;

$replace =<<<EOT
<b><a href="cart.php?target=checkout&mode=paymentMethod"><img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"><font class="FormButton"> Change payment method</font></a></b>

<widget template="checkout/credit_card.tpl" visible="{cart.paymentMethod.formTemplate=#checkout/credit_card.tpl#}">
<widget template="checkout/echeck.tpl" visible="{cart.paymentMethod.formTemplate=#checkout/echeck.tpl#}">
<widget template="checkout/offline.tpl" visible="{cart.paymentMethod.formTemplate=#checkout/offline.tpl#}">
<widget module="PaySystems" template="modules/PaySystems/checkout.tpl" visible="{cart.paymentMethod.formTemplate=#modules/PaySystems/checkout.tpl#}">
<widget module="WellsFargo" template="modules/WellsFargo/checkout.tpl" visible="{cart.paymentMethod.formTemplate=#modules/WellsFargo/checkout.tpl#}">
<widget module="HSBC" template="modules/HSBC/checkout.tpl" visible="{cart.paymentMethod.formTemplate=#modules/HSBC/checkout.tpl#}">
<widget module="ePDQ" template="modules/ePDQ/checkout.tpl" visible="{cart.paymentMethod.formTemplate=#modules/ePDQ/checkout.tpl#}">
<widget module="WorldPay" template="modules/WorldPay/checkout.tpl" visible="{cart.paymentMethod.formTemplate=#modules/WorldPay/checkout.tpl#}">
<widget module="GiftCertificates" template="modules/GiftCertificates/checkout.tpl" visible="{cart.paymentMethod.formTemplate=#modules/GiftCertificates/checkout.tpl#}">
<widget module="Promotion" template="modules/Promotion/checkout.tpl" visible="{cart.paymentMethod.formTemplate=#modules/Promotion/checkout.tpl#}">
<widget module="2CheckoutCom" template="modules/2CheckoutCom/checkout.tpl" visible="{cart.paymentMethod.formTemplate=#modules/2CheckoutCom/checkout.tpl#}">
<widget module="PayPal" template="modules/PayPal/checkout.tpl" visible="{cart.paymentMethod.formTemplate=#modules/PayPal/checkout.tpl#}">
<!-- PAYMENT METHOD FORM -->
EOT;

$source = strReplace($search, $replace, $source, __FILE__, __LINE__);

?>
