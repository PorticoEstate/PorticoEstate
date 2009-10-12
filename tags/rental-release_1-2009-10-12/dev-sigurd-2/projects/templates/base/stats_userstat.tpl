<!-- $Id: stats_userstat.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->

{app_header}
<div class="projects_content"></div>
<center>
<table width="75%" border="0" cellspacing="2" cellpadding="2">
<form method="POST" action="{actionurl}">
	<tr>
		<td>{lang_employee}:</td>
		<td>{employee}</td>
	</tr>
	<tr>
		<td>{lang_start_date}</td>
		<td>{start_date_select}</td>
	</tr>
	<tr>
		<td>{lang_end_date}:</td>
		<td>{end_date_select}</td>
	</tr>
	<tr>
		<td>{lang_billedonly}:</td>
		<td>{billed}</td>
	</tr>
</table>
<table width="75%" border="0" cellspacing="2" cellpadding="2">
	<tr valign="bottom">
		<td height="50">
			<input type="submit" name="submit" value="{lang_calculate}">
		</td>
	</tr>
</form>
</table>
<table width="85%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="10%" bgcolor="{th_bg}">{lang_project}</td>
		<td width="10%" bgcolor="{th_bg}">{lang_activity}</td>
		<td width="10%" bgcolor="{th_bg}" align="right">{lang_hours}</td>
	</tr>

<!-- BEGIN user_stat -->

	<tr bgcolor="{tr_color}">
		<td>{e_project}</td>
		<td>{e_activity}</td>
		<td align="right">{e_hours}</td>
	</tr>

<!-- END user_stat -->

</table>
</center>
