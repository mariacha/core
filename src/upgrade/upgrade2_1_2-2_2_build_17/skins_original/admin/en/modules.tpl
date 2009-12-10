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

Use this section to manage add-on components of your online store.
<span id="notes_url" style="display:"><a href="javascript:ShowNotes();" class="NavigationPath" onClick="this.blur()"><b>How to use this section &gt;&gt;&gt;</b></a></span>
<span id="notes_body" style="display: none"><p align="justify">Activate a module and click on its title or <img src="images/go.gif" width="13" height="13" border="0" align="absmiddle"> icon to configure it</p>
</span>
<hr>
<span class="ErrorMessage" IF="xlite.mm.error">{xlite.mm.error:h}<br></span>

<span IF="xlite.mm.errorBrokenDependencies">
<p class="ErrorMessage">&gt;&gt; Unable to {action} module {xlite.mm.moduleName} &lt;&lt;</p>
<p>There are depending modules found</p>
<li FOREACH="xlite.mm.errorDependencies,dep">{dep:h}</li>
<p>Please {action} the depending modules first</p>
<br>
</span>

<span IF="xlite.mm.brokenDependencies">
<p class="ErrorMessage">&gt;&gt; Cannot initialize some module(s): dependency modules are not available &lt;&lt;</p>
</span>

{if:xlite.mm.safeMode}
<p>
<font class="ErrorMessage">&gt;&gt; Modules information is not available in safe mode &lt;&lt;</font>
<br><br>
<a IF="xlite.session.safe_mode" href="{url}&safe_mode=off"><u><b>Turn OFF safe mode</b></u></a>
</p>
{else:}
<p IF="!xlite.mm.modules"><b>&gt;&gt;&nbsp;You have no modules installed&nbsp;&lt;&lt;</b></p>
<p IF="xlite.mm.modules">You have <b>{xlite.mm.modulesNumber}</b> module{if:!xlite.mm.modulesNumber=#1#}s{end:} installed and <b>{xlite.mm.activeModulesNumber}</b> module{if:!xlite.mm.activeModulesNumber=#1#}s{end:} activated.</p>
{end:}

<table cellpadding="0" cellspacing="0" border="0" width="100%">

{* Display payment modules *}

<tbody IF="getSortModules(#8#)">
<widget template="modules_body.tpl" caption="Commercial payment modules" key="8">
</tbody>


{* Display shipping modules *}

<tbody IF="getSortModules(#4#)">
<widget template="modules_body.tpl" caption="Commercial shipping modules" key="4">
</tbody>


{* Display commercial modules *}

<tbody IF="getSortModules(#2#)">
{if:getSortModules(#8#)|getSortModules(#4#)}
<widget template="modules_body.tpl" caption="Other commercial modules" key="2">
{else:}
<widget template="modules_body.tpl" caption="Commercial modules" key="2">
{end:}
</tbody>


{* Display free modules *}

<tbody IF="getSortModules(#1#)">
<widget template="modules_body.tpl" caption="Free modules" key="1">
</tbody>


{* Display 3rd party modules *}

<tbody IF="getSortModules(#4096#)">
<widget template="modules_body.tpl" caption="3rd party modules" key="4096">
</tbody>

</table>

<br>

<table border=0 cellpadding=5 width=100%>
<form action="admin.php" method="POST" enctype="multipart/form-data">
<tr>
    <td class="AdminTitle"><hr>Install new module</td>
</tr>    
<tr>
    <td>Select module .tar file: <input type="file" name="module_file"></td>
</tr>
<tr>
    <td>
    <input type="hidden" name="target" value="modules">
    <input type="hidden" name="action" value="install">
    <input type="submit" value=" Install ">
    </td>
</tr>
</form>
</table>
