<!-- BEGIN header -->
<form method="post" action="{action_url}">
	<table class="pure-table pure-table-bordered">
		<th>
		<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</th>
		<tr bgcolor="{th_err}">
			<td colspan="2">&nbsp;<b>{error}</b></font></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr class="row_on">
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="row_off">
			<td colspan="2">&nbsp;<b>{lang_Messenger}/{lang_Settings}</b></font></td>
		</tr>
		<tr class="row_off">
			<td>{lang_Select_where_you_want_to_store}/{lang_retrieve_messages}.</td>
			<td>
				<select name="newsettings[message_repository]">
					<option value="sql" {selected_message_repository_sql}>SQL</option>
					<option value="imap" {selected_message_repository_imap}>SMTP/IMAP</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_IMAP_host_for_messages}:</td>
			<td><input name="newsettings[imap_message_host]" value="{value_imap_message_host}" size="40" /></td>
		</tr>
		<tr class="row_off">
			<td>{lang_Restrict_users_to_only_sending_to_the_follow_group}:</td>
			<td>
				<table>
					{hook_restrict_to_group}
				</table>
			</td>
		</tr>
		<!-- END body -->
		<!-- BEGIN footer -->
		<tr class="{th}">
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" name="submit" value="{lang_submit}" />
				<input type="submit" name="cancel" value="{lang_cancel}" />
			</td>
		</tr>
	</table>
</form>
<!-- END footer -->
