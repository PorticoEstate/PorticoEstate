<!-- $Id: ttracker_form.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->

{app_header}
<div class="projects_content"></div>
<center>
{message}
<table width="100%" border="0" cellspacing="2" cellpadding="2">
<form method="POST" action="{action_url}">
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_activity}</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_activity}:</td>
		<td colspan="3">
<!-- BEGIN activity -->

			<select name="values[activity_id]">{activity_list}</select>

<!-- END activity -->

<!-- BEGIN act_own -->

			<input type="text" name="values[hours_descr]" size="50" value="{hours_descr}">

<!-- END act_own -->

		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_remark}:</td>
		<td colspan="3"><textarea name="values[remark]" rows="5" cols="50" wrap="VIRTUAL">{remark}</textarea></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_work_date}</b></td>
	</tr>
	<tr bgcolor="{row_on}">

<!-- BEGIN booking_date -->

		<td>{lang_start_date}:</td>
		<td>{start_date_select}</td>
		<td>{lang_end_date}:</td>
		<td>{end_date_select}</td>

<!-- END booking_date -->

<!-- BEGIN booking_time -->

		<td>{lang_date}:&nbsp;</td>
		<td colspan="3">{start_date_select}</td>

<!-- END booking_time -->

	</tr>
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_work_time}</b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_start_time}:</td>
		<td>
			<input type="text" name="values[shour]" value="{shour}" size="2" maxlength="2">&nbsp;:&nbsp;<input type="text" name="values[smin]" value="{smin}" size="2" maxlength="2">&nbsp;[hh:mm]&nbsp;{sradio}
		</td>
		<td>{lang_end_time}:</td>
		<td>
			<input type="text" name="values[ehour]" value="{ehour}" size="2" maxlength="2">&nbsp;:&nbsp;<input type="text" name="values[emin]" value="{emin}" size="2" maxlength="2">&nbsp;[hh:mm]&nbsp;{eradio}
		</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_hours}:</td>
		<td colspan="3">
			<input type="text" name="values[hours]" value="{hours}" size="2" maxlength="2">&nbsp;:&nbsp;<input type="text" name="values[minutes]" value="{minutes}" size="2" maxlength="2">&nbsp;[hh.mm]
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_travel_time}:</td>
		<td><input type="text" name="values[t_journey_h]" value="{t_journey_h}" size="2" maxlength="2">&nbsp;:&nbsp;<input type="text" name="values[t_journey_m]" value="{t_journey_m}" size="2" maxlength="2">&nbsp;[hh:mm]</td>
		<td>{lang_distance}:</td>
		<td><input type="text" name="values[km_distance]" value="{km_distance}" size="5">&nbsp;[km]</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_surcharge}:</td>
		<td colspan="3"><select name="values[surcharge]"><option value="">{lang_select_surcharge}</option>{surcharge_list}</select></td>
	</tr>

	<tr valign="bottom" height="50">
		<td colspan="3"><input type="submit" name="save" value="{lang_save}"></td>
		<td align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
</form>
</table>
</center>
