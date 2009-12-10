<table cellpadding="0" cellspacing="0" border="0" width="100%">
<form action="{shopURL(#cart.php#)}" method=POST name="update{key}_form">
<tr>
    <input type=hidden name=target value="wishlist">
    <input type=hidden name=action value="update">
    <input type=hidden name=item_id value="{item.item_id}">
	<input type=hidden name=wishlist_id value="{item.wishlist_id}">
	<input type=hidden name=product_id value="{item.product_id}">
	<td valign=top width="100px">
        <widget visible="{item.hasImage()}" template="common/product_thumbnail.tpl" href="{item.url:h}" thumbnail="{item.imageURL}" margin="0px 5px 0px 0px">
	</td>
	<td>
		<table id="productDetailsTable" cellpadding=0 cellspacing=0 border=0 width="100%">
        <tr id="descriptionTitle">
        	<td colspan=3 class="ProductTitle"><a href="{item.url:h}">{item.name:h}</a></td>
        </tr>
		<tr id="descriptionTitle">
			<td colspan=3 class="ProductDetailsTitle"><br>Description</td>
		</tr>
		<tr>
			<td colspan=2><widget template="common/hr.tpl"></td>
			<td></td>
		</tr>
		<tr>
			<td colspan=3>&nbsp;</td>
		</tr>
		<tr id="description">
			<td colspan=3>{truncate(item.brief_description,#300#):h}
			</td>
		</tr>
	    <tr id="description">
		    <td colspan=3>
				<widget module="ProductOptions" template="modules/ProductOptions/selected_options.tpl" visible="{item.hasOptions()}" item="{item}">
		    </td>
		</tr>
		<tr>
			<td colspan=3>&nbsp;</td>
		</tr>
		<tr id="detailsTitle">
			<td colspan=3 class="ProductDetailsTitle">Details</td>
		</tr>
		<tr>
        <td colspan=2><widget template="common/hr.tpl"></td>
			<td></td>
		</tr>
		<tr IF="{item.sku}">
			<td width="20%" class="ProductDetails">SKU:</td>
			<td class="ProductDetails" colspan=2 nowrap>{item.sku}</td>
		</tr>
		<tr IF="{!item.weight=0}">
			<td width="20%" class="ProductDetails">Weight:
			</td>
			<td class="ProductDetails" colspan=2 nowrap>
				{item.weight} {config.General.weight_symbol}
			</td>
		</tr>
        <tr>
            <td colspan=3>&nbsp;</td>
        </tr>
		<tr>
			<td colspan=3>
			<table cellspacing=10 cellpadding=0 border=0>
			<tr>
				<td nowrap>
	        		<font class="ProductPriceTitle">Price:</font> <font class="ProductPriceConverting">{price_format(item,#price#):h}&nbsp;x&nbsp;</font>
	        		<input type="text" name="wishlist_amount" value="{item.amount}" size="3" maxlength="6">
	        		<font class="ProductPriceConverting">&nbsp;=&nbsp;</font>
	        		<font class="ProductPrice">{price_format(item,#total#):h}</font>
				</td>
				<td nowrap>
            		<widget class="CButton" template="modules/WishList/common/button.tpl" label="Update amount" href="javascript: document.update{key}_form.submit();" font="FormButton">
				</td>
				<td width="100%">
            		<widget class="CButton" template="modules/WishList/common/button.tpl" label="Remove" href="javascript: document.update{key}_form.action.value='delete'; document.update{key}_form.submit();" font="FormButton">
				</td>														
			</tr>
			</table>
			</td>
		</tr>
		 <tr>
		 	<td colspan=3>
				<table cellpadding=0 cellspacing=0 border=0>
					<tr>
				        <td colspan="3">
							<widget class="CButton" label="Add to cart" href="javascript: document.update{key}_form.action.value='add'; document.update{key}_form.target.value='cart'; document.update{key}_form.submit();" img="cart4button.gif" font="FormButton">
					    </td>
					</tr>	
				</table>	
			</td>
		</tr>	
</table>
</td>
</tr>
</form>
</table>
<br><br><br>
