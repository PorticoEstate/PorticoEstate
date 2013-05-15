<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table border="0" align="center" width="85%">
		<tr class="th">
			<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr class="row_on">
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="row_off">
			<td colspan="2">&nbsp;<b>{lang_mobilefrontend}</b></td>
		</tr>
		<tr class="row_on">
			<td>{lang_auth_type}:</td>
			<td>
				<select name="newsettings[auth_type]">
					<option value="0" {selected_auth_type_0}>Same as framework</option>
					<option value="sql" {selected_auth_type_sql}>SQL</option>
					<option value="customsso" {selected_auth_type_customsso}>Custom SSO</option>
				</select>
			</td>
		</tr>
		<!-- END body -->
		<!-- BEGIN footer -->
		<tr class="th">
			<td colspan="2">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" name="submit" value="{lang_submit}">
				<input type="submit" name="cancel" value="{lang_cancel}">
			</td>
		</tr>
	</table>
</form>
<!-- END footer -->
