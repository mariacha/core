<widget class="CPager" data="{users}" name="searchResults" itemsPerPage="{config.General.users_per_page}">

<form action="admin.php" method="get" name="user_profile">
<table border="0" width="100%">
<tr class="TableHead">
    <td width=10>&nbsp;</td>
    <td nowrap align=left>Login</td>
    <td nowrap align=left>Username</td>
    <td nowrap align=left width=110>First login</td>
    <td nowrap align=left width=110>Last login</td>
</tr>
<tr FOREACH="searchResults.pageData,id,user">
    <td align="center" width="10"><input type="radio" name="profile_id" value="{user.profile_id}" checked="{isSelected(id,#0#)}"></td>
    <td nowrap><a href="admin.php?target=profile&profile_id={user.profile_id}&backUrl={url:u}"><u>{user.login:h}</u></a></td>
    <td nowrap><a href="admin.php?target=profile&profile_id={user.profile_id}&backUrl={url:u}">{user.billing_firstname:h}&nbsp;{user.billing_lastname:h}</a></td>
    <td nowrap align=left width=110>{if:user.first_login}{time_format(user.first_login):h}{else:}Never{end:}</td>
    <td nowrap align=left width=110>{if:user.last_login}{time_format(user.last_login):h}{else:}Never{end:}</td>
</tr>
</table>

<br>
<input type="hidden" name="target" value="profile">
<input type="hidden" name="mode" value="modify">
<input type="hidden" name="backUrl" value="{url:r}">
<p align="left">
<input type="submit" value="Modify">
&nbsp;&nbsp;
<input type="button" name="Delete" value="Delete" onclick="document.user_profile.mode.value='delete'; document.user_profile.submit();">
</p>
</form>
