<!-- $Id: hours_listhours.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
<script type="text/javascript">

	function hideColumn (colIndex)
	{
  		var table = document.all ? document.all.aTable:document.getElementById('aTable');
  		for (var r = 0; r < table.rows.length; r++)
    		table.rows[r].cells[colIndex].style.display = 'none';
	}

	function showColumn (colIndex)
	{
  		var table = document.all ? document.all.aTable:document.getElementById('aTable');
		for (var r = 0; r < table.rows.length; r++)
    		table.rows[r].cells[colIndex].style.display = '';
	}


</script>
{app_header}
<div class="projects_content"></div>
<!-- BEGIN project_main -->
<!--
<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="7"><b>{lang_main}:&nbsp;<a href="{main_url}">{title_main}</a></b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td>{number_main}</td>
		<td>{lang_url}:</td>
		<td colspan="4"><a href="http://{url_main}" taget="_blank">{url_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td>{coordinator_main}</td>
		<td>{lang_customer}:</td>
		<td colspan="4">{customer_main}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_hours}:</td>
		<td>{lang_planned}:</td>
		<td>{ptime_main}</td>
		<td>{lang_used_total} {lang_plus_jobs}:</td>
		<td>{utime_main}</td>
		<td>{lang_available} {lang_plus_jobs}:</td>
		<td>{atime_main}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_files}:</td>
		<td colspan="6">{attachment}</td>
	</tr>
</table>
-->
<!-- END project_main -->

<center>{error}</center>


<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<form method="POST" action="{action_url}">
	<tr bgcolor="{th_bg}" style="vertical-align:top">
		<td align="center">{lang_employee}</td>
		<td align="center">{lang_start_date}</td>
		<td align="center">{lang_end_date}</td>
		<td align="center">{lang_status}</td>
		<td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td align="center">{lang_search}</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td align="center">{filter_list}</td>
		<td align="center" style="white-space: nowrap;">&nbsp;{sdate_select}&nbsp;</td>
		<td align="center" style="white-space: nowrap;">&nbsp;{edate_select}&nbsp;</td>
		<td align="center"><select name="state">{state_list}</select></td>
		<td><input type="submit" name="submit" value="{lang_update}"></td>
		<td align="right"><nowrap>{search_list}</nowrap></td>
	</tr>
	</form>
</table>
<br>
<table id="aTable" border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}" style="text-align: center">
		<td style="width: 50px">&nbsp;</td>
		<td>{sort_hours_descr}</td>
		<td>{sort_status}</td>
		<td>{sort_start_date}</td>
		<td>{sort_start_time}</td>
		<td>{sort_end_time}</td>
		<td>{sort_hours}</td>
		<td>{sort_t_journey}</td>
		<td>{sort_employee}</td>
	</tr>

<!-- BEGIN hours_list -->

	<tr bgcolor="{tr_color}">
		<td align="center">
			{booked}
			<a href="{edit_url}"><img src="{edit_img}" border="0" title="{lang_edit_hours}" /></a>
		</td>
		<td>{hours_descr}</td>
		<td align="center">{status}</td>
		<td align="center">{start_date}</td>
		<td align="center">{start_time}</td>
		<td align="center">{end_time}</td>
		<td align="right">{wh}</td>
		<td align="right">{t_journey}</td>
		<td>{employee}</td>
	</tr>

<!-- END hours_list -->

	<tr height="5">
		<td>&nbsp;</td>
	</tr>
</table>
</center>
