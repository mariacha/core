<p class="TabHeader">Modules</p>
<p>This section is used to install and uninstall modules for the default shop.

<p class="ErrorMessage">{xlite.mm.error:h}</p>

<span IF="xlite.mm.errorBrokenDependencies">
<p class="ErrorMessage">&gt;&gt; Unable to {action} module {xlite.mm.moduleName} &lt;&lt;</p>
<p>There are depending modules found</p>
<li FOREACH="xlite.mm.errorDependencies,dep">{dep:h}</li>
<p>Please {action} the depending modules first</p>
<br>
</span>

<script language="Javascript">
<!--
function visibleBox(id, status)
{
	var Element = document.getElementById(id);
	if (Element) {
		Element.style.display = ((status) ? "" : "none");
	}
}

function ShowNotes()
{
	visibleBox("notes_url", false);
	visibleBox("notes_body", true);
}

function setChecked(form, input, check, key)
{
	var elements = document.forms[form].elements[input];

	if ( elements.length > 0 ) {
		for (var i = 0; i < elements.length; i++) {
			elements[i].checked = check;
		}
	} else {
		elements.checked = check;
	}
	if (key) {
		checkUpdated(key);
	}
}

function checkUpdated(key)
{
	var Element = document.getElementById("update_button_"+key);
	if (Element) {
		Element.className = "DialogMainButton";
	}
}

function setHeaderChecked(key)
{
	var Element = document.getElementById("activate_modules_"+key);
	if (Element && !Element.checked) {
		Element.checked = true;
	}
}

function uninstallModule(moduleForm, moduleID, moduleName)
{
	if (confirm('Are you sure you want to uninstall ' + moduleName + ' add-on?')) {
		moduleForm.module_id.value = moduleID;
		moduleForm.module_name.value = moduleName;
		moduleForm.action.value = 'uninstall';
		moduleForm.submit();
	}
}
// -->
</script>

<p IF="!xlite.mm.modules"><b>&gt;&gt;&nbsp;You have no modules installed&nbsp;&lt;&lt;</b></p>

<table cellpadding="0" cellspacing="0" border="0" width="100%">

{* Display payment modules *}

<tbody IF="getSortModules(#8#)">
<widget template="modules/asp/modules_body.tpl" caption="Commercial payment modules" key="8">
</tbody>


{* Display shipping modules *}

<tbody IF="getSortModules(#4#)">
<widget template="modules/asp/modules_body.tpl" caption="Commercial shipping modules" key="4">
</tbody>

{* Display commercial modules *}

<tbody IF="getSortModules(#2#)">
{if:getSortModules(#8#)|getSortModules(#4#)}
<widget template="modules/asp/modules_body.tpl" caption="Other commercial modules" key="2">
{else:}
<widget template="modules/asp/modules_body.tpl" caption="Commercial modules" key="2">
{end:}
</tbody>


{* Display commercial skin modules *}

<tbody IF="getSortModules(#16#)">
<widget template="modules/asp/modules_body.tpl" caption="Commercial skin modules" key="16">
</tbody>

{* Display free modules *}

<tbody IF="getSortModules(#1#)">
<widget template="modules/asp/modules_body.tpl" caption="Free modules" key="1">
</tbody>


{* Display 3rd party modules *}

<tbody IF="getSortModules(#4096#)">
<widget template="modules/asp/modules_body.tpl" caption="3rd party modules" key="4096">
</tbody>

</table>

<hr>

<table border=0 cellpadding=5>
<form action="cpanel.php" method="POST" enctype="multipart/form-data">
<tr>
    <td colspan=2 class="AdminTitle">Install new module</td>
</tr>    
<tr>
    <td>Select module .tar file: </td>
	<td><input type="file" name="module_file"></td>
</tr>
<tr>
	<td>&nbsp;</td>
    <td>
    <input type="hidden" name="target" value="modules">
    <input type="hidden" name="action" value="install">
    <input type="submit" value=" Install " class="DialogMainButton" onClick="this.blur();">
    </td>
</tr>
</form>
</table>
