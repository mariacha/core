
Use this section to export product extra fields into a CSV file.

<hr>

<p>
<form action="admin.php" method=POST name=data_form>
<input type="hidden" name="target" value="export_catalog">
<input type="hidden" name="action" value="export_fields">
<input type="hidden" name="page" value="{page}">

<widget template="product/fields_layout.tpl">

<table border="0">
<tr>
    <td colspan=2>
    <br>
    Delimiter:<br><widget template="common/delimiter.tpl">
    </td>
</tr>
<tr>
    <td colspan=2> <input type=submit value="Export extra fields" class="DialogMainButton"> </td>
</tr>
</table>
</form>

