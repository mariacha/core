<select name="{field}Month">
	<option value="1" selected="{month=1}">January</option>
	<option value="2" selected="{month=2}">February</option>
	<option value="3" selected="{month=3}">March</option>
	<option value="4" selected="{month=4}">April</option>
	<option value="5" selected="{month=5}">May</option>
	<option value="6" selected="{month=6}">June</option>
	<option value="7" selected="{month=7}">July</option>
	<option value="8" selected="{month=8}">August</option>
	<option value="9" selected="{month=9}">September</option>
	<option value="10" selected="{month=10}">October</option>
	<option value="11" selected="{month=11}">November</option>
	<option value="12" selected="{month=12}">December</option>
</select>

<select name="{field}Day">
	<option FOREACH="days,v" value="{v}" selected="{day=v}">{v}</option>
</select>

<select name="{field}Year">
	<option FOREACH="years,v" value="{v}" selected="{year=v}">{v}</option>
</select>
