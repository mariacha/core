<table border="0" cellpadding=0 cellspacing="0" width="100%">
{if:absentOptions}
	Sorry, but some options of "{invalidProductName:h}" do not exist anymore and you can not add this product to the cart. 
	<br> </br>
	<tr>
		<td><img src="skins/default/en/images/but_left.gif" width="8" height="22" border="0" alt=""></td>
		<td class="CommonButtonBG" nowrap>&nbsp;&nbsp;<a href="javascript: history.go(-1)" target="" class="ButtonLink"><font  class="Button">Go back</font></a>&nbsp;&nbsp;</td>
		<td><img src="skins/default/en/images/but_right.gif" width="8" height="22" border="0" alt=""></td>
		<td width="100%"></td>
	</tr>
{else:}
	{if:invalidOptions}
		Sorry, but options of "{invalidProductName:h}" are invalid. You coudn't add product to cart.
		<br> </br>
		<tr>
			<td><img src="skins/default/en/images/but_left.gif" width="8" height="22" border="0" alt=""></td>
			<td class="CommonButtonBG" nowrap>&nbsp;&nbsp;<a href="javascript: history.go(-1)" target="" class="ButtonLink"><font  class="Button">Go back</font></a>&nbsp;&nbsp;</td>
			<td><img src="skins/default/en/images/but_right.gif" width="8" height="22" border="0" alt=""></td>
			<td width="100%"></td>
		</tr>
	{else:}
		{if:getItems()}
			<tr FOREACH="getItems(),key,item">
				<td><widget template="modules/WishList/item.tpl" key="{key}" item="{item}"></td>
			</tr>
			<tr>
				<td><widget template="common/hr.tpl"></td>	
			</tr>	
			<tr>
				<td><widget template="modules/WishList/send_wishlist.tpl"></td>
			</tr>
		{else:}
			<tr>
				<td>
					Your Wish List is empty.
				</td>	
			</tr>	
		{end:}
	{end:}
{end:}
</table>


