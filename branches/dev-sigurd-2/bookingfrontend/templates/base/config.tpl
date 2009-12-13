<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table border="0" align="center" width="85%">
		<tr class="th">
			<td colspan="2">&nbsp;<b>{title}</b></td>
		</tr>
<!-- END header -->
<!-- BEGIN body -->
		<tr class="row_on">
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="row_off">
			<td colspan="2">&nbsp;<b>{lang_bookingfrontend_settings}</b></td>
		</tr>
		<tr class="row_on">
			<td>{lang_custom_login_parameter}:</td>
			<td><input name="newsettings[login_parameter]" value="{value_login_parameter}"></td>
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
