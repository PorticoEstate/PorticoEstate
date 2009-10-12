<!-- $Id: ttracker.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->

{app_header}
<div class="projects_content"></div>
<center>{message}</center>
<fieldset>
<legend>[&nbsp;{lang_project and activity}&nbsp;]</legend>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<form method="POST" action="{action_url}">
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_project}:</td>
		<td colspan="3"><select onChange="getElementById('radio_'+this.value).checked = true;" id="select_project" name="values[project_id]">{select_project}</select><input style="display:none" type="radio" name="values[project_id]" id="radio_" value=""></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_activity}:</td>
		<td valign="top">
<!-- BEGIN act_own -->
			<input type="text" name="values[hours_descr]" size="30" value="{hours_descr}">
<!-- END act_own -->
<!-- BEGIN activity -->
			<select name="values[activity_id]"><option value="">{lang_select_activity}</option>{activity_list}</select>
<!-- END activity -->
		</td>
		<td valign="top">{lang_remark}:</td>
		<td><textarea name="values[remark]" rows="4" cols="30" wrap="VIRTUAL">{remark}</textarea></td>
	</tr>
</table>
</fieldset>
<br>

<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td valign="top" width="49%">
<fieldset>
<legend>[&nbsp;{lang_manual_mode}&nbsp;]</legend>
<table border="0" width="100%" height="185" cellpadding="2" cellspacing="2">
	<tr bgcolor="{row_off}">

<!-- BEGIN booking_date -->

		<td width="50%" align="right">{lang_start_date}:</td>
		<td width="50%">{start_date_select}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td width="50%" align="right">{lang_end_date}:</td>
		<td width="50%">{end_date_select}</td>
	</tr>

<!-- END booking_date -->

<!-- BEGIN booking_time -->

		<td width="50%" align="right">{lang_date}:&nbsp;</td>
		<td width="50%">{start_date_select}</td>

<!-- END booking_time -->

	</tr>
	<tr bgcolor="{row_off}">
		<td align="right">{lang_work_time}&nbsp;</td>
		<td><input type="text" size="2" name="values[hours]" maxlength="2" value="{hours}">&nbsp;:&nbsp;<input type="text" size="2" name="values[minutes]" maxlength="2" value="{minutes}">&nbsp;[hh:mm]</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td align="right">{lang_travel_time}:&nbsp;</td>
		<td><input type="text" name="values[t_journey_h]" value="{t_journey_h}" size="2" maxlength="2">&nbsp;:&nbsp;<input type="text" name="values[t_journey_m]" value="{t_journey_m}" size="2" maxlength="2">&nbsp;[hh:mm]</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td align="right">{lang_distance}:&nbsp;</td>
		<td><input type="text" name="values[km_distance]" value="{km_distance}" size="6">&nbsp;[km]</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td align="right">{lang_surcharge}:&nbsp;</td>
		<td><select name="values[surcharge]"><option value="">{lang_select_surcharge}</option>{surcharge_list}</select></td>
	</tr>
	<tr height="30">
		<td colspan="2" valign="bottom" align="right"><input type="submit" name="values[apply]" value="{lang_apply}"></td>
	</tr>
</table>
</fieldset>
</td>
<td valign="top" width="2%">&nbsp;</td>
<td valign="top" width="49%">
<fieldset>
<legend>[&nbsp;{lang_live_mode}&nbsp;]</legend>
<table border="0" width="100%" height="185" cellpadding="2" cellspacing="2">
	<tr bgcolor="{row_off}">
		<td width="25%" align="right">{lang_date}:&nbsp;</td>
		<td width="75%">{curr_date}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td width="25%" align="right">{lang_time}:&nbsp;</td>
		<td width="75%">{curr_time}</td>
	</tr>
	<tr bgcolor="{row_off}"><td colspan="2">&nbsp;</td></tr>
	<tr bgcolor="{row_off}"><td colspan="2">&nbsp;</td></tr>
	<tr height="30">
		<td colspan="4" valign="bottom" align="right"><input type="submit" name="values[start]" value="{lang_start}">&nbsp;<input type="submit" name="values[pause]" value="{lang_pause}">&nbsp;<input type="submit" name="values[continue]" value="{lang_continue}">&nbsp;<input type="submit" name="values[stop]" value="{lang_stop}"></td>
	</tr>
</table>
</fieldset>
</td>
</tr>
</table>
<br>

<fieldset>
<legend>[&nbsp;{lang_projects_and_captured_activities}&nbsp;]</legend>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr>
		<td>
			<table border="0" width="100%" cellpadding="2" cellspacing="2">
				<tr bgcolor="{th_bg}" width="100%">
					<td>{lang_entry}</td>
					<td>{lang_activity}</td>
					<td>{lang_from}</td>
					<td>{lang_till}</td>
					<td>{lang_hours}</td>
					<td>{lang_travel_time}</td>
					<td align="center">{lang_select}</td>
				</tr>
<!-- BEGIN ttracker -->

				<tr bgcolor="{th_bg}">
					<td colspan="6">{project_title}</td>
					<td align="center"><input type="radio" name="values[project_id]" onClick="var select = getElementById('select_project'); if(!select) return; for(i=0; i<select.options.length; i++){ select.options[i].selected = (select.options[i].value=={project_id}); }" value="{project_id}" id="radio_{project_id}" {radio_checked}></td>
				</tr>
				{thours_list}
				<tr height="5">
					<td>&nbsp;</td>
				</tr>
<!-- END ttracker -->

			</table>
		</td>
		<td width="20%" align="right" valign="top"><input type="submit" name="values[save]" value="{lang_save_activities}"></td>
	</tr>
	</form>
</table>
</fieldset>

<!-- BEGIN ttracker_list -->
		<tr bgcolor="{tr_color}">
			<td>{statusout}: {apply_time}</td>
			<td><a href="{edit_url}">{hours_descr}</a></td>
			<td>{start_time}</td>
			<td>{end_time}</td>
			<td>{wh}</td>
			<td>{journey}</td>
			<td align="center"><a href="{delete_url}"><img src="{delete_img}" border="0" title="{lang_delete}"></a></td>
		</tr>
<!-- END ttracker_list -->
