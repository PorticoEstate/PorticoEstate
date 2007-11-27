<!-- $Id: stats_userlist_worktimes.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->

{app_header}
<div class="projects_content"></div>
<center>
<table border="0" width="96%" cellspacing="2" cellpadding="2">
	<form method="POST" action="{action_url}">
	<tr bgcolor="{th_bg}">
		<td align="center">{lang_employee}:<br\>{select_employee_list}</td>
		<!--td align="center">{lang_project}:<br\>{select_action_list}</td-->
		<td align="center">{lang_start_date}:<br\>{sdate_select}</td>
		<td align="center">{lang_end_date}:<br\>{edate_select}</td>
		<td align="center"><input type="submit" name="view" value="{lang_update}"></td>
	</tr>
</form>
</table>
<br>
<!-- BEGIN worktime_list -->
<table border="0" width="96%" cellspacing="2" cellpadding="2">
	<tr bgcolor="{tr_color}">
		<td colspan="2" width="75%" valign?"middle" align="center" bgcolor="{th_bg}">{info_1}</td>
		<td colspan="3" width="25%" align="center" width="15%" bgcolor="{th_bg}">{lang_workhours}</td>
	</tr>
	<tr bgcolor="{tr_color}">
		<td align="center" bgcolor="{th_bg}" >{info_1_1}</td>
		<td align="center" bgcolor="{th_bg}" width="15%">{info_1_2}</td>
		<td align="center" bgcolor="{th_bg}">{lang_workhours_project}</td>
		<td align="center" bgcolor="{th_bg}">{lang_workhours_journey}</td>
		<td align="center" bgcolor="{th_bg}">{lang_workhours_sum}</td>
	</tr>
	{project_list}
	<tr bgcolor="{tr_color}">
		<td colspan="2" valign?"middle" align="right" bgcolor="{th_bg}"><b>{lang_summery}</b></td>
		<td align="right" bgcolor="{th_bg}"><b>{lang_summery_workhours_project}</b></td>
		<td align="right" bgcolor="{th_bg}"><b>{lang_summery_workhours_journey}</b></td>
		<td align="right" bgcolor="{th_bg}"><b>{lang_summery_workhours_sum}</b></td>
	</tr>
	{ps_sum}
</table>
<!-- END worktime_list -->
</center>

<!-- BEGIN pro_list -->
	<tr bgcolor="{tr_color}">
		<td align="left" bgcolor="{row_on}">{pro_name}</td>
		<td align="left" bgcolor="{row_off}">{pro_number}</td>
		<td align="right" bgcolor="{row_on}">{pro_hours}</td>
		<td align="right" bgcolor="{row_off}">{pro_hours_journey}</td>
		<td align="right" bgcolor="{row_on}">{pro_hours_sum}</td>
	</tr>
<!-- END pro_list -->

<!-- BEGIN posible_sum -->
	<tr>
		<td colspan="2" align="right" bgcolor="{th_bg}"><b>{lang_summery_workhours_posible}:</b></td>
		<td colspan="3" align="right" bgcolor="{th_bg}"><b>{summery_workhours_posible}:00</b></td>
	</tr>
<!-- END posible_sum -->