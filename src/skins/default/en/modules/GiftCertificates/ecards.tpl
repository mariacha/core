{* e-Card list *}
<table border="0" width="100%">
<form action="cart.php" method="POST" name="ecard_form">
<input type="hidden" name="target" value="gift_certificate_ecards">
<input type="hidden" name="action" value="update">
<input type="hidden" name="gcid" value="{gcid}">
<input type="hidden" name="ecard_id" value="">
<tr FOREACH="split3(ecards),row">
    <td FOREACH="row,ecard" align="center" width="33%">
        <span IF="ecard">
        <a href="{ecard.image.url}" target="_blank"><img src="{ecard.thumbnail.url}" border="0"></a><br>
        <widget class="CButton" label="Select" href="javascript: document.ecard_form.ecard_id.value='{ecard.ecard_id}';document.ecard_form.submit()">
        </span>
        <br>&nbsp;
    </td>
</tr>
</form>
</table>
<widget class="CSubmit" href="cart.php?target=add_gift_certificate&gcid={gcid}" label="Cancel">
