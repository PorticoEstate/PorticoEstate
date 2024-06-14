<!-- $Id$ -->
<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered pure-table-striped pure-form">
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
				<select name="newsettings[acl_at_control_area]" class="pure-u-1">
					<option value="2" {selected_acl_at_control_area_2}>{lang_no}</option>
					<option value="1" {selected_acl_at_control_area_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_required_actual_hours}.</td>
			<td>
				<select name="newsettings[required_actual_hours]" class="pure-u-1">
					<option value="" {selected_required_actual_hours_}>{lang_no}</option>
					<option value="1" {selected_required_actual_hours_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>Antall planlagte kontroller som skal vises.</td>
			<td>
				<input type="number" name="newsettings[no_of_planned_controls]" value="{value_no_of_planned_controls}" class="pure-u-1"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>Antall tildelte kontroller som skal vises</td>
			<td>
				<input type="number" name="newsettings[no_of_assigned_controls]" value="{value_no_of_assigned_controls}" class="pure-u-1"/>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_home_alternative}</td>
			<td>
				<select name="newsettings[home_alternative]" class="pure-u-1">
					<option value="" {selected_home_alternative_}>{lang_no}</option>
					<option value="1" {selected_home_alternative_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_deadline_end_of_year}</td>
			<td>
				<select name="newsettings[deadline_end_of_year]" class="pure-u-1">
					<option value="" {selected_deadline_end_of_year_}>{lang_no}</option>
					<option value="1" {selected_deadline_end_of_year_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_control_mandatory_location}</td>
			<td>
				<select name="newsettings[control_mandatory_location]" class="pure-u-1">
					<option value="" {selected_control_mandatory_location_}>{lang_no}</option>
					<option value="1" {selected_control_mandatory_location_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_get_inherited_values_on_entities}</td>
			<td>
				<select name="newsettings[get_inherited_values]" class="pure-u-1">
					<option value="" {selected_get_inherited_values_}>{lang_no}</option>
					<option value="1" {selected_get_inherited_values_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_disable_auto_ticket_creation}</td>
			<td>
				<select name="newsettings[disable_auto_ticket_creation]" class="pure-u-1">
					<option value="" {selected_disable_auto_ticket_creation_}>{lang_no}</option>
					<option value="1" {selected_disable_auto_ticket_creation_1}>{lang_yes}</option>
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
				<select name="newsettings[ticket_category]" class="pure-u-1">
					{hook_ticket_category}
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_from_email}: </td>
			<td><input name="newsettings[from_email]" value="{value_from_email}" class="pure-u-1"/>
			</td>
		</tr>
		<tr>
			<td>{lang_report_email}: </td>
			<td><input name="newsettings[report_email]" value="{value_report_email}" class="pure-u-1"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_report_as_pdf}</td>
			<td>
				<select name="newsettings[report_as_pdf]" class="pure-u-1">
					<option value="" {selected_report_as_pdf_}>{lang_no}</option>
					<option value="1" {selected_report_as_pdf_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_start_inspection_card}</td>
			<td>
				<select name="newsettings[start_inspection_card]" class="pure-u-1">
					<option value="" {selected_start_inspection_card_}>{lang_no}</option>
					<option value="1" {selected_start_inspection_card_1}>{lang_yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_start_inspection_text}: </td>
			<td><input name="newsettings[start_inspection_text]" value="{value_start_inspection_text}" class="pure-u-1"/>
			</td>
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
				<input type="submit" name="submit" value="{lang_submit}" class="pure-button"/>
				<input type="submit" name="cancel" value="{lang_cancel}" class="pure-button"/>
			</td>
		</tr>
	</table>
</form>
<!-- END footer -->
