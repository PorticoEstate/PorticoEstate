<!-- $Id: hours_formhours.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<center>{message}</center>
<!-- BEGIN main -->
<!--
<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_main}</b>:&nbsp;<a href="{main_url}">{pro_main}</a></td>
	</tr>
	<tr>
		<td style="height: 15px">
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_project}:</td>
		<td>{project_name}</td>
		<td colspan="2"></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_url}:</td>
		<td><a href="http://{url_main}" target="_blank">{url_main}</a></td>
		<td>{lang_hours} {lang_planned}:</td>
		<td style="text-align: right">{ptime_main}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_customer}:</td>
		<td>{customer_main}</td>
		<td>{lang_used_total}{lang_plus_jobs}:</td>
		<td style="text-align: right">{utime_main}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td>{coordinator_main}</td>
		<td>{lang_available}{lang_plus_jobs}:</td>
		<td style="text-align: right; border-top: 1px solid #000000">{atime_main}</td>
	</tr>
</table>
<br>
-->
<!-- END main -->

<table width="100%" border="0" cellspacing="2" cellpadding="2">
<form method="POST" action="{action_url}">
{hidden_vars}

	<tr bgcolor="{row_on}">
		<td style="font-weight: bold">{lang_employee}:</td>
		<td style="font-weight: bold">{employee}</td>
		<td style="font-weight: bold">{lang_project}:</td>
		<td colspan="3" style="font-weight: bold"><select name="project_id">{project_options}</select></td>
	</tr>

	<tr bgcolor="{row_off}" valign="top">
		<td class="must">{lang_activity}:</td>
		<td>

<!-- BEGIN activity -->

			<select name="values[activity_id]">{activity_list}</select>

<!-- END activity -->

<!-- BEGIN activity_own -->

			<input type="text" name="values[hours_descr]" size="45" value="{hours_descr}">
<!-- END activity_own -->  
		</td>
		<td colspan="2">&nbsp;</td>
		<td colspan="2">&nbsp;</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td rowspan="6" valign="top">{lang_remark}:</td>
		<td rowspan="6" valign="top"><textarea name="values[remark]" rows="5" cols="40" wrap="VIRTUAL">{remark}</textarea></td>
		<td bgcolor="{row_off}" colspan="4" style="height: 1px">
		</td>
	</tr>
	
	<tr bgcolor="{row_on}">

<!-- BEGIN booking_date -->

		<td>{lang_start_date}:</td>
		<td>{start_date_select}</td>
		<td>{lang_start_time}:</td>
		<td nowrap="nowrap"
			<input type="text" name="values[shour]" value="{shour}" size="2" maxlength="2">&nbsp;:&nbsp;<input type="text" name="values[smin]" value="{smin}" size="2" maxlength="2">&nbsp;[hh:mm]
			&nbsp;{sradio}
		</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_end_date}:</td>
		<td>{end_date_select}</td>
		<td>{lang_end_time}:</td>
		<td nowrap="nowrap"
			<input type="text" name="values[ehour]" value="{ehour}" size="2" maxlength=2>&nbsp;:&nbsp;<input type="text" name="values[emin]" value="{emin}" size="2" maxlength="2">&nbsp;[hh:mm]
			&nbsp;{eradio}
		</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td colspan="2" class="must">{lang_hours}:</td>
		<td colspan="2" nowrap="nowrap">
			<input type="text" name="values[hours]" value="{hours}" size="2" maxlength="2">&nbsp;:&nbsp;<input type="text" name="values[minutes]" value="{minutes}" size="2" maxlength="2">&nbsp;[hh:mm]
		</td>

<!-- END booking_date -->

<!-- BEGIN booking_time -->

		<td colspan="2" class="must">{lang_work_date}:</td>
		<td colspan="2">{start_date_select}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td colspan="2" class="must">{lang_hours}:</td>
		<td colspan="2" nowrap="nowrap">
			<input type="text" name="values[hours]" value="{hours}" size="2" maxlength="2">&nbsp;:&nbsp;<input type="text" name="values[minutes]" value="{minutes}" size="2" maxlength="2">&nbsp;[hh:mm]
		</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_start_time}:</td>
		<td nowrap="nowrap">
			<input type="text" name="values[shour]" value="{shour}" size="2" maxlength="2">&nbsp;:&nbsp;<input type="text" name="values[smin]" value="{smin}" size="2" maxlength="2">&nbsp;[hh:mm]
			&nbsp;{sradio}
		</td>
		<td>{lang_end_time}:</td>
		<td nowrap="nowrap">
			<input type="text" name="values[ehour]" value="{ehour}" size="2" maxlength=2>&nbsp;:&nbsp;<input type="text" name="values[emin]" value="{emin}" size="2" maxlength="2">&nbsp;[hh:mm]
			&nbsp;{eradio}
		</td>

<!-- END booking_time -->

	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_travel_time}:</td>
		<td><input type="text" name="values[t_journey_h]" value="{t_journey_h}" size="2" maxlength="2">&nbsp;:&nbsp;<input type="text" name="values[t_journey_m]" value="{t_journey_m}" size="2" maxlength="2">&nbsp;[hh:mm]</td>
		<td>{lang_distance}:</td>
		<td><input type="text" name="values[km_distance]" value="{km_distance}" size="5">&nbsp;[km]</td>
	</tr>
	
	<tr bgcolor="{row_off}">
		<td style="height: 5px" colspan="4"></td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_status}:</td>
		<td colspan="5"><select name="values[status]">{status_list}</select></td>
	</tr>
	
	<tr bgcolor="{row_on}">
		<td>{lang_non_billable}:</td>
		<td><input type="checkbox" name="values[billable]" value="True" {hours_billable_checked}></td>
		<td colspan="2">{lang_surcharge}:</td>
		<td colspan="2"><select name="values[surcharge]"><option value="">{lang_select_surcharge}</option>{surcharge_list}</select></td>
	</tr>

	<tr height="50">
		<td>{save}</td>
		<td><b>{booked}</b></td>
		<td colspan="2" align="center">{delete}</td><!-- <input type="submit" name="save" value="{lang_save}"> -->
		<td colspan="2" align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
	</form>
</table>
</center>
