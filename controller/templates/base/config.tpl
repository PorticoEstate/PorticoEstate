<!-- $Id$ -->
<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered">
		<tr class="th">
			<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr class="row_on">
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="row_off">
			<td colspan="2">&nbsp;<b>{lang_controller} {lang_settings}</b></td>
		</tr>
		<tr class="row_on">
			<td>{lang_Use_ACL_for_control_areas}.</td>
			<td>
				<select name="newsettings[acl_at_control_area]">
					<option value="2" {selected_acl_at_control_area_2}>{lang_no}</option>
					<option value="1" {selected_acl_at_control_area_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_required_actual_hours}.</td>
			<td>
				<select name="newsettings[required_actual_hours]">
					<option value="" {selected_required_actual_hours_}>{lang_no}</option>
					<option value="1" {selected_required_actual_hours_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>Antall planlagte kontroller som skal vises.</td>
			<td>
				<input type="text" name="newsettings[no_of_planned_controls]" value="{value_no_of_planned_controls}"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>Antall tildelte kontroller som skal vises</td>
			<td>
				<input type="text" name="newsettings[no_of_assigned_controls]" value="{value_no_of_assigned_controls}"/>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_home_alternative}</td>
			<td>
				<select name="newsettings[home_alternative]">
					<option value="" {selected_home_alternative_}>{lang_no}</option>
					<option value="1" {selected_home_alternative_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_deadline_end_of_year}</td>
			<td>
				<select name="newsettings[deadline_end_of_year]">
					<option value="" {selected_deadline_end_of_year_}>{lang_no}</option>
					<option value="1" {selected_deadline_end_of_year_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_control_mandatory_location}</td>
			<td>
				<select name="newsettings[control_mandatory_location]">
					<option value="" {selected_control_mandatory_location_}>{lang_no}</option>
					<option value="1" {selected_control_mandatory_location_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_get_inherited_values_on_entities}</td>
			<td>
				<select name="newsettings[get_inherited_values]">
					<option value="" {selected_get_inherited_values_}>{lang_no}</option>
					<option value="1" {selected_get_inherited_values_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_document_category}:</td>
			<td>
				<!--to be able to blank the setting - need an empty value-->
				<input type = 'hidden' name="newsettings[document_cat][]" value="">
				<table>
					{hook_document_cat}
				</table>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_ticket_category}:</td>
			<td>
				<select name="newsettings[ticket_category]">
					{hook_ticket_category}
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_from_email}: </td>
			<td><input name="newsettings[from_email]" value="{value_from_email}"></td>
		</tr>

		<!-- END body -->
		<!-- BEGIN footer -->
		<tr class="th">
			<td colspan="2">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" name="submit" value="{lang_submit}">
				<input type="submit" name="cancel" value="{lang_cancel}">
			</td>
		</tr>
	</table>
</form>
<!-- END footer -->
