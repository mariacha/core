This page allows you to import product access from CSV file.<hr>

<p IF="!valid">
    <font class="ErrorMessage">&gt;&gt; Error occured &lt;&lt;<br></font>
</p>

<p>
<form action="admin.php" method=POST name=data_form enctype="multipart/form-data" >
<input FOREACH="dialog.allparams,param,val" type="hidden" name="{param}" value="{val:r}"/>
<input type="hidden" name="action" value="import_product_access">

<table border=0>
<tr>
    <td colspan=2><widget template="modules/WholesaleTrading/field_order.tpl"></td>
</tr>
<tr FOREACH="xlite.factory.ProductAccess.getImportFields(#product_access_layout#),id,fields">
    <td width=1>{id}:</td>
    <td width=99%>
        <select name="product_access_layout[{id}]">
            <option FOREACH="fields,field,value" value="{field}" selected="{isOrderFieldSelected(id,field,value)}">{field}</option>
        </select>
    </td>
</tr>
</table>
<br>
Text qualifier:<br><widget template="common/qualifier.tpl"><br>
<br>
Field delimiter:<br><widget template="common/delimiter.tpl"><br>
<br>
<widget template="modules/WholesaleTrading/unique_identifier.tpl">
<br>
File (CSV) local:<br><input type=text size=32 name=localfile value="{localfile}"><widget IF="invalid_localfile" template="common/uploaded_file_validator.tpl" state="{invalid_localfile_state}" /><br>
<br>
File (CSV) for upload:<br><input type=file size=32 name=userfile><widget IF="invalid_userfile"
template="common/uploaded_file_validator.tpl" state="{invalid_userfile_state}" /><br>
<br>
<input type=submit value=" Import ">

</form>
