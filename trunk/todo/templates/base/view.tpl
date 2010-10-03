<!-- $Id$ -->
<p>&nbsp;&nbsp;&nbsp;<b>{lang_todo_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
{error}
<table width="90%" border="0" cellspacing="0" cellpadding="2">
	<tr bgcolor="{row_on}">
		<td>{lang_title}:</td>
		<td>{value_title}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_descr}:</td>
		<td>{value_descr}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_parent}:</td>
		<td>{value_parent}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_category}:</td>
		<td>{value_category}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_owner}:</td>
		<td>{owner}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_assigned}:</td>
		<td>{assigned}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_access}:</td>
		<td>{access}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_start_date}:</td>
		<td>{value_start_date}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_end_date}:</td>
		<td>{value_end_date}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_completed}:</td>
		<td>{value_completed} %</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_urgency}:</td>
		<td>{value_urgency}</td>
	</tr>
	<tr>
		<td align="left">{button_edit}</td>
		<td align="center">{button_done}</td>
		<td align="right">{button_delete}</td>
	</tr>
</table>
<p>
{history}
<table width="90%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td height="50">
			<form method="POST" action="{done_action}">
			<input type="submit" name="done" value="{lang_done}">
			</form></td>
	</tr>
</table>
