<?php

$source = strReplace('<td id="open{key}" style="display: none; cursor: hand;" onClick="visibleBox(\'{key}\')"><b><a href="cart.php?target=product&product_id={item.product.product_id}">{truncate(item,#name#,#30#):h}</a></b><span IF="{item.hasOptions()}"><table border=0 cellpadding=0 cellspacing=0><tr><td>&nbsp;&nbsp;<img src="images/modules/ProductOptions/close.gif" width="13" height="13" border="0" align="absmiddle" alt="hide options list"></td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td><widget module="ProductOptions" template="modules/ProductOptions/selected_options.tpl" visible="{item.hasOptions()}"></td></tr></table></span></td>', '<td id="open{key}" style="display: none; cursor: hand;" onClick="visibleBox(\'{key}\')"><b><span IF="item.product.product_id"><a href="cart.php?target=product&product_id={item.product.product_id}">{truncate(item,#name#,#30#):h}</a></span><span IF="!item.product.product_id">{truncate(item,#name#,#30#):h}</span></b><span IF="{item.hasOptions()}"><table border=0 cellpadding=0 cellspacing=0><tr><td>&nbsp;&nbsp;<img src="images/modules/ProductOptions/close.gif" width="13" height="13" border="0" align="absmiddle" alt="hide options list"></td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td><widget module="ProductOptions" template="modules/ProductOptions/selected_options.tpl" visible="{item.hasOptions()}"></td></tr></table></span></td>', $source, __FILE__, __LINE__);

?>
