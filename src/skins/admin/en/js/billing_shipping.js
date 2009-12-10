function copyBillingInfo(work_form)
{
	var fields = new Array("title","firstname","lastname","phone","fax","company","address","city","zipcode");
	for (var i = 0; i < fields.length; i++) {
		b_element = work_form["billing_" + fields[i]];
		s_element = work_form["shipping_" + fields[i]];
		s_element.value = b_element.value;
	}   

	work_form.elements["shipping_country"].selectedIndex = work_form.elements["billing_country"].selectedIndex;
	populateStates(work_form.elements["shipping_country"],'shipping_state');
	work_form.elements["shipping_state"].selectedIndex = work_form.elements["billing_state"].selectedIndex;

	if (work_form.elements["billing_state"].value == -1)
	{
		work_form["shipping_custom_state"].value = work_form["billing_custom_state"].value;
	}

	initStates();
}

function IsBillingShippingEqual(work_form)
{
	var fields = new Array("title","firstname","lastname","phone","fax","company","address","city","zipcode");
	for (var i = 0; i < fields.length; i++) {
		b_element = work_form["billing_" + fields[i]];
		s_element = work_form["shipping_" + fields[i]];
		if (s_element.value != b_element.value)
			return false;
	}

	if (work_form.elements["shipping_country"].selectedIndex != work_form.elements["billing_country"].selectedIndex)
		return false;

	if (work_form.elements["shipping_state"].selectedIndex != work_form.elements["billing_state"].selectedIndex)
		return false;

	if (work_form.elements["shipping_state"].value == -1 && work_form.elements["billing_state"].value == -1)
	{
		if (work_form["shipping_custom_state"].value != work_form["billing_custom_state"].value)
			return false;
	}

	return true;
}

function OnModifyShippingAddress(work_form)
{
	document.getElementById("shipping_body").style.display = '';
	document.getElementById("btn_copy_billing").style.display = '';
	document.getElementById("btn_modify_shipping").style.display = 'none';
}

function CheckBillingShipping()
{
	if (IsBillingShippingEqual(document.profile_form)) {
		document.getElementById('shipping_body').style.display = 'none';
		document.getElementById('btn_copy_billing').style.display = 'none';
		document.getElementById('btn_modify_shipping').style.display = '';
	}
}
