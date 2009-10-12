<center>
<form method="POST" action="{form_action}">
<table>
	<tr class="row_on">
		<td align=right>{lang_recipient}:</td>
		<td><input type="text" name="recipient" value="{val_recipient}" style="width: 200px"></td>
	</tr>
	<tr class="row_off">
		<td align=right>{lang_subject}:</td>
		<td><input type="text" name="subject" value="{val_subject}" style="width: 200px"></td>
	</tr>
	<tr class="row_on">
		<td align=right>{lang_reply}:</td>
		<td><input type="text" name="reply" value="{val_reply}" style="width: 200px"></td>
	</tr>
	<tr class="row_off">
		<td valign=top align=right>{lang_message}:</td>
		<td><textarea name="txt_message" value="{val_message}" style="width:200px; height:100px"></textarea></td>
	</tr>
	<tr>
		<td colspan=2 align=center><input type="submit" name="send" value="{lang_send}"></td>
	</tr>
</table>
</form>
</center>
