<!-- BEGIN form -->
<center>{errors}</center>

<!-- BEGIN input -->
<form action="{form_action}" method="POST" id="form">
	<table border="0" width="40%" align="center">

		<tr>
			{domain_select}
		</tr>
		<tr>
			<td>{lang_username}</td>
			<td>{domain_from_host}<input name="r_reg[loginid]" value="{value_username}" data-validation="length alphanumeric" data-validation-length="3-10"/>
			</td>
		</tr>

		<tr>
			<td colspan="2"><input type="submit" name="submit" value="{lang_submit}"></td>
		</tr>
	</table>
</form>
<!-- END input -->
<!-- END form -->
