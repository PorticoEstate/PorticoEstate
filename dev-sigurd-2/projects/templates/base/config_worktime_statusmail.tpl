<br /><br />
<center>
<table border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="2" align="center" valign="top">{message}</td>
	<tr>
<form method="POST" action="{action_url}">
	<tr>
		<td valign="top"><font color="{th_text}"><b>{worktime_statusmail_desc}:</b></font></td>
		<td>
			<select name="mail_type">
				<option value="off"{selected_off}>{opt_off_desc}</option>
				<option value="weekly"{selected_weekly}>{opt_weekly_desc}</option>
				<option value="monthly"{selected_monthly}>{opt_monthly_desc}</option>
			</select>
		</td>
	</tr>
    <tr height="50" valign="bottom">
		<td><input type="submit" name="save" value="{lang_save}"></td>
		<td align="right"><input type="submit" name="done" value="{lang_done}"></td>
	</tr>
</form>
</table>
</center>