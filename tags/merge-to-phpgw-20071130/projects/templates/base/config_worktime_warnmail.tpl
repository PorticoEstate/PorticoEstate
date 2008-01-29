<br /><br />
<center>
<table border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="2" align="center" valign="top">{message}</td>
	<tr>
<form method="POST" action="{action_url}">
	<tr>
		<td colspan="2" align="center" valign="top"><font color="{th_text}"><b>{worktime_warnmail_desc}:</b></font></td>
	</tr>
    <tr height="50" valign="bottom">

		<td colspan="2" align="center" valign="top">
			{warnmail_type_selectbox}
		</td>
	</tr>
	<tr>
		<td><strong>{cc_receiver}:</strong></td>
		<td><input type="text" name="email_warnmail_address" value="{warnmail_email_address}" /></td>
	</tr>
    <tr height="50" valign="bottom">
		<td><input type="submit" name="save" value="{lang_save}"></td>
		<td align="right"><input type="submit" name="done" value="{lang_done}"></td>
	</tr>
</form>
</table>
</center>