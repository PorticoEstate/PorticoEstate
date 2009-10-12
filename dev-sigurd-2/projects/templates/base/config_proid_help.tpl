<center>
<table border="0" cellpadding="2" cellspacing="2">
<form method="POST" action="{action_url}">
	<tr bgcolor="{row_on}">
		<td colspan="2"><textarea name="proid_help_msg" rows="20" cols="50" wrap="VIRTUAL">{helpmsg}</textarea></td>
	</tr>
	<tr>
		<td><input type="button" value="{lang_show}" onClick="open_popup('{help_url}');" /></td>
	</tr>
    <tr height="50" valign="bottom">
		<td><input type="submit" name="save" value="{lang_save}"></td>
		<td align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
</form>
</table>
</center>
