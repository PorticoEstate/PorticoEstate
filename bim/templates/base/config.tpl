<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered">

		<tr class="th">
			<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr>
			<td>{lang_baseurl}:</td>
			<td><input name="newsettings[bimserver_baseurl]" value="{value_bimserver_baseurl}"></td>
		</tr>
		<tr>
			<td>{lang_username}:</td>
			<td><input name="newsettings[bimserver_username]" value="{value_bimserver_username}"></td>
		</tr>
		<tr>
			<td>{lang_password}:</td>
			<td><input name="newsettings[bimserver_password]" value="{value_bimserver_password}"></td>
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
