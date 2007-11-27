<!-- $Id: status_form.tpl 9883 2002-04-05 23:35:03Z ceb $ -->

{app_header}

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>{message}</center>
<form method="POST" action="{form_action}">
<input type="hidden" name="status_id" value="{status_id}">
<table border="0" align="center">
	<tr>
		<td>{lang_status_name}</td>
		<td><input type="text" name="status_name" value="{status_name}"></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><input type="submit" name="submit" value="{lang_save}"></td>
	</tr>
</table>
</form>