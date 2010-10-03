<!-- $Id: hours_view.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<center>
{error}
<br>
<!-- BEGIN main -->
<!--
<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="100%" colspan="7"><b>{lang_main}</b>:&nbsp;<a href="{main_url}">{pro_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td colspan="2">{number_main}</td>
		<td>{lang_url}:</td>
		<td colspan="3"><a href="http://{url_main}" target="_blank">{url_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td colspan="2">{coordinator_main}</td>
		<td>{lang_customer}:</td>
		<td colspan="3">{customer_main}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_hours}:</td>
		<td>{lang_planned}:</td>
		<td>{ptime_main}</td>
		<td>{lang_used_total}{lang_plus_jobs}:</td>
		<td>{utime_main}</td>
		<td>{lang_available}{lang_plus_jobs}:</td>
		<td>{atime_main}</td>
	</tr>
	<tr height="5">
		<td>&nbsp;</td>
	</tr>
</table>
-->
<!-- END main -->

<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{row_off}">
		<td>{lang_project}:</td>
		<td>{project_name}</td>
		<td>{lang_employee}:</td>
		<td>{employee}</td>
	</tr>
	</tr>
	<tr bgcolor="{row_on}" valign="top">
		<td>{lang_activity}:</td>
		<td>{activity}</td>
		<td>{lang_remark}:</td>
		<td>{remark}</td>
	</tr>
	<tr height="5">
		<td>&nbsp;</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_work_date}</b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_start_date}:</td>
		<td>{sdate}</td>
		<td>{lang_end_date}:</td>
		<td>{edate}</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_work_time}</b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_start_time}:</td>
		<td>{stime}</td>
		<td>{lang_end_time}:</td>
		<td>{etime}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_hours}:</td>
		<td>{hours}.{minutes}</td>
		<td>{lang_status}:</td>
		<td>{status}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_distance}:</td>
		<td>{km_distance}</td>
		<td>{lang_time_of_journey}:&nbsp;[hh.mm]</td>
		<td>{t_journey}</td>
	</tr>
<!--
	<tr height="50" valign="bottom">
		<td colspan="4">
			<form method="POST" action="{doneurl}">
			<input type="submit" name="done" value="{lang_done}"></form>
		</td>
	</tr>
-->
</table>
</center>

