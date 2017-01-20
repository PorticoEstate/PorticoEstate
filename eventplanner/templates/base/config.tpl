<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered">
		<tr class="th">
			<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr>
			<td>{lang_default_application_category}:</td>
			<td>
				<select name="newsettings[default_application_category]">
					{hook_default_application_category}
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
