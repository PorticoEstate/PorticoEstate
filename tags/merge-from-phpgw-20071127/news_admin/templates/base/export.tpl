<h1>{title}</h1>
<table border="0" width="100%">
	<tr>
		{left}
		<td>{lang_showing}</td>
		{right}
	</tr>
</table>
<form method="POST"><input type="text" name="query" value="{query}"><input type="submit" name="btnSearch" value="{lang_search}"></form>
<form method="POST">
	<!-- BEGIN cat_list -->
	<fieldset>
		<legend>{catname}</legend>
		<!-- BEGIN config -->
		<label for="inputconfig_{setting}_{catid}">{label}</label>
		<input type="text" id="inputconfig_{setting}_{catid}" name="inputconfig[{catid}][{setting}]" value="{config_value}"><br>
		<!-- END config -->
		<label for="inputconfig_type{cat_id}">{lang_type}</label>
		<select id="inputconfig_type{cat_id}" name="inputconfig[{catid}][type]">{typeselectlist}</select>
		<input type="hidden" name="catids[]" value="{catid}">
	</fieldset>
	<!-- END cat_list -->
	<input type="submit" name="btnSave" value="{lang_save}"> 
	<input type="submit" name="btnDone" value="{lang_done}">
</form>
</center>
