<!-- $Id: room_form.tpl 5493 2001-06-04 21:46:14Z bettina $ -->
<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
{message}
<form method="POST" name="order_form" action="{actionurl}">
<table width="85%" border="0" cellspacing="2" cellpadding="2" align="center">
	<tr>
		<td align="right">{lang_room_name}:</td>
		<td><input type="text" name="room_name" value="{room_name}"></td>
	</tr>
	<tr>
		<td align="right">{lang_room_note}:</td>
		<td align="left"><textarea name="room_note" rows=4 cols=50 wrap="VIRTUAL">{room_note}</textarea></td>
	</tr>
	<tr>
		<td align="right">{lang_access}:</td>
		<td>{access}</td>
	</tr>
</table>

<!-- BEGIN add -->

<table width="50%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td height="50">
			{hidden_vars}
			<input type="submit" name="submit" value="{lang_add}"></td>
		<td height="50">
			<input type="reset" name="reset" value="{lang_reset}"></form></td>
		<td height="50">
			<form method="POST" action="{done_action}">
			{hidden_vars}
			<input type="submit" name="done" value="{lang_done}"></form></td>
	</tr>
</table>
</center>

<!-- END add -->

<!-- BEGIN edit -->

<table width="50%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td height="50">
			{hidden_vars}
			<input type="submit" name="submit" value="{lang_edit}"></form></td>
		<td height="50">
			{hidden_vars}
			{delete}</td>
		<td height="50">
			<form method="POST" action="{done_action}">
			{hidden_vars}
			<input type="submit" name="done" value="{lang_done}"></form></td>
	</tr>
</table>
</center>

<!-- END edit -->
