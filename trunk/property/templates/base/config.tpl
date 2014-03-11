<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table border="0" align="center" width="85%">
		<tr class="th">
			<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr class="row_on">
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="row_off">
			<td colspan="2">&nbsp;<b>{lang_Workorder}/{lang_FM_settings}</b></td>
		</tr>
		<tr class="row_on">
			<td>{lang_organisation}:</td>
			<td><input name="newsettings[org_name]" value="{value_org_name}"></td>
		</tr>
		<tr class="row_off">
			<td>{lang_org_unit_id}:</td>
			<td><input name="newsettings[org_unit_id]" value="{value_org_unit_id}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_delivery_address}:</td>
			<td>
				<textarea cols="40" rows="4" name="newsettings[delivery_address]" wrap="virtual">{value_delivery_address}</textarea>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_invoice_address}:</td>
			<td>
				<textarea cols="40" rows="4" name="newsettings[invoice_address]" wrap="virtual">{value_invoice_address}</textarea>
			</td>
		</tr>

		<tr class="row_on">
			<td>{lang_order_footer_header}:</td>
			<td><input name="newsettings[order_footer_header]" value="{value_order_footer_header}"></td>
		</tr>

		<tr class="row_off">
			<td>{lang_order_footer}:</td>
			<td>
				<textarea cols="40" rows="4" name="newsettings[order_footer]" wrap="virtual">{value_order_footer}</textarea>
			</td>
		</tr>

		<tr class="row_on">
			<td>{lang_order_logo}:</td>
			<td><input name="newsettings[order_logo]" value="{value_order_logo}"></td>
		</tr>

		<tr class="row_off">
			<td>{lang_order_logo_width}:</td>
			<td><input name="newsettings[order_logo_width]" value="{value_order_logo_width}"></td>
		</tr>

		<tr class="row_on">
			<td>{lang_SMS_client_order_notice}:'ref: __order_id__. Message'</td>
			<td>
				<textarea cols="40" rows="4" name="newsettings[sms_client_order_notice]" wrap="virtual">{value_sms_client_order_notice}</textarea>
			</td>
		</tr>
	</tr>
	<tr class="row_off">
		<td>{lang_dimb_responsible_1}:</td>
		<td>
			<select name="newsettings[dimb_responsible_1]">
				{hook_dimb_cat_1}
			</select>
		</td>
	</tr>
	<tr class="row_on">
		<td>{lang_dimb_responsible_2}:</td>
		<td>
			<select name="newsettings[dimb_responsible_2]">
				{hook_dimb_cat_2}
			</select>
		</td>
	</tr>
	<tr class="row_off">
		<td>{lang_invoicehandler}:</td>
		<td>
			<select name="newsettings[invoicehandler]">
				<option value="1" {selected_invoicehandler_1}>Default</option>
				<option value="2" {selected_invoicehandler_2}>Alternative</option>
			</select>
		</td>
	</tr>
	<tr class="row_on">
		<td>{lang_invoice_acl}:</td>
		<td>
			<select name="newsettings[invoice_acl]">
				<option value="default" {selected_invoice_acl_default}>ACL</option>
				<option value="dimb" {selected_invoice_acl_dimb}>DimB</option>
			</select>
		</td>
	</tr>
		<tr class="row_off">
			<td>{lang_project_status_on_approval}:</td>
			<td>
				<select name="newsettings[project_approval_status]">
					{hook_project_approval_status}
				</select>
			</td>
		</tr>

		<tr class="row_off">
			<td>{lang_project_status_on_last_order_closed}:</td>
			<td>
				<select name="newsettings[project_status_on_last_order_closed]">
					{hook_project_status_on_last_order_closed}
				</select>
			</td>
		</tr>

		<tr class="row_on">
			<td>{lang_workorder_status_on_approval}:</td>
			<td>
				<select name="newsettings[workorder_approval_status]">
					{hook_workorder_approval_status}
				</select>
			</td>
		</tr>

		<tr class="row_on">
			<td>{lang_request_status_on_project_hookup}:</td>
			<td>
				<select name="newsettings[request_project_hookup_status]">
					{hook_request_project_hookup_status}
				</select>
			</td>
		</tr>


	<tr class="row_off">
		<td>{lang_workorder_status_that_are_to_be_set_when_invoice_is_processed}:</td>
		<td>
			<select name="newsettings[workorder_closed_status]">
				{hook_workorder_closed_status}
			</select>
		</td>
		<tr class="row_on">
			<td>{lang_workorder_reopen_status_that_are_to_be_set_when_invoice_is_processed}:</td>
			<td>
				<select name="newsettings[workorder_reopen_status]">
					{hook_workorder_reopen_status}
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_require_vendor_at_workorder}:</td>
			<td>
				<select name="newsettings[workorder_require_vendor]">
					<option value="" {selected_workorder_require_vendor_}>NO</option>
					<option value="1" {selected_workorder_require_vendor_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_delay_operation_workorder_end_date}, {lang_last_day_in_year}:</td>
			<td>
				<select name="newsettings[delay_operation_workorder_end_date]">
					<option value="" {selected_delay_operation_workorder_end_date_}>NO</option>
					<option value="1" {selected_delay_operation_workorder_end_date_1}>YES</option>
				</select>
			</td>
		</tr>


		<tr class="row_off">
			<td>{lang_Default_municipal_number}:</td>
			<td><input name="newsettings[default_municipal]" value="{value_default_municipal}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_Tax}: [%]</td>
			<td><input name="newsettings[fm_tax]" value="{value_fm_tax}"></td>
		</tr>
		<tr class="row_off">
			<td>{lang_Enter_the_location_of_files_URL} <br>
				{lang_Example}: http://www.domain.com/files</td>
			<td><input name="newsettings[files_url]" value="{value_files_url}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_Path_to_external_files_for_use_with_location}:<br>
				{lang_On_windows_use}: "//computername/share" {lang_or} "\\\\computername\share"</td>
			<td><input name="newsettings[external_files]" value="{value_external_files}"></td>
		</tr>
		<tr class="row_off">
			<td>{lang_max_recursive_level_at_external_files}:</td>
			<td>
				<select name="newsettings[external_files_maxlevel]">
					<option value="0" {selected_external_files_maxlevel_0}>None</option>
					<option value="1" {selected_external_files_maxlevel_1}>1</option>
					<option value="2" {selected_external_files_maxlevel_2}>2</option>
					<option value="3" {selected_external_files_maxlevel_3}>3</option>
					<option value="4" {selected_external_files_maxlevel_4}>4</option>
					<option value="5" {selected_external_files_maxlevel_5}>5</option>
					<option value="6" {selected_external_files_maxlevel_6}>6</option>
					<option value="7" {selected_external_files_maxlevel_7}>7</option>
					<option value="8" {selected_external_files_maxlevel_8}>8</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_filter_at_level_at_external_files}: <br/>(loc1)</td>
			<td>
				<select name="newsettings[external_files_filterlevel]">
					<option value="0" {selected_external_files_filterlevel_0}>None</option>
					<option value="1" {selected_external_files_filterlevel_1}>1</option>
					<option value="2" {selected_external_files_filterlevel_2}>2</option>
					<option value="3" {selected_external_files_filterlevel_3}>3</option>
					<option value="4" {selected_external_files_filterlevel_4}>4</option>
					<option value="5" {selected_external_files_filterlevel_5}>5</option>
					<option value="6" {selected_external_files_filterlevel_6}>6</option>
					<option value="7" {selected_external_files_filterlevel_7}>7</option>
					<option value="8" {selected_external_files_filterlevel_8}>8</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_Enter_MAP_URL} <br>
				{lang_Example}: http://www.domain.com/map</td>
			<td><input name="newsettings[map_url]" value="{value_map_url}"></td>
		</tr>
		<tr class="row_off">
			<td>{lang_Enter_GAB_Location_Level} <br>
				{lang_Default_value_is}: 3</td>
			<td><input name="newsettings[gab_insert_level]" value="{value_gab_insert_level}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_Enter_GAB_URL} <br>
				{lang_Example}: http://www.domain.com/gab</td>
			<td><input name="newsettings[gab_url]" value="{value_gab_url}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_gab_url_paramtres}:<br>
				lang_Example: type=eiendom&knr=__kommune_nr__&Gnr=__gaards_nr__&Bnr=__bruks_nr__&Fnr=__feste_nr__&Snr=__seksjons_nr__
			</td>
			<td>
				<textarea cols="40" rows="4" name="newsettings[gab_url_paramtres]" wrap="virtual">{value_gab_url_paramtres}</textarea>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_suppress_old_tenant}:</td>
			<td>
				<select name="newsettings[suppress_tenant]">
					<option value="" {selected_suppress_tenant_}>NO</option>
					<option value="1" {selected_suppress_tenant_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_show_billable_hours}:</td>
			<td>
				<select name="newsettings[show_billable_hours]">
					<option value="" {selected_show_billable_hours_}>NO</option>
					<option value="1" {selected_show_billable_hours_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td colspan="2">&nbsp;<b>{lang_TTS}::{lang_settings}</b></td>
		</tr>
		<tr class="row_on">
			<td>{lang_Open_translates_to}: <br>
				{lang_default}: {lang_Open}</td>
			<td><input name="newsettings[tts_lang_open]" value="{value_tts_lang_open}"></td>
		</tr>
		<tr class="row_off">
			<td valign = 'top'>{lang_TTS_assign_group_candidates}:</td>
			<td>
				<!--to be able to blank the setting - need an empty value-->
				<input type = 'hidden' name="newsettings[fmtts_assign_group_candidates][]" value="">
				<table>
					{hook_fmtts_assign_group_candidates}
				</table>
			</td>
		</tr>
		<tr class="row_on">
			<td >{lang_TTS_disable_assign_to_user_on_add}:</td>
			<td>
				<select name="newsettings[tts_disable_userassign_on_add]">
					<option value="" {selected_tts_disable_userassign_on_add_}>NO</option>
					<option value="1" {selected_tts_disable_userassign_on_add_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td valign = 'top'>{lang_TTS_simplified_group}:</td>
			<td>
				<!--to be able to blank the setting - need an empty value-->
				<input type = 'hidden' name="newsettings[fmttssimple_group][]" value="">
				<table>
					{hook_fmttssimple_group}
				</table>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_Mail_Notification}:</td>
			<td>
				<select name="newsettings[mailnotification]">
					<option value="" {selected_mailnotification_}>NO</option>
					<option value="1" {selected_mailnotification_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_Owner_Notification_Project}.</td>
			<td>
				<select name="newsettings[notify_project_owner]">
					<option value="" {selected_notify_project_owner_}>NO</option>
					<option value="1" {selected_notify_project_owner_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_Owner_Notification_TTS}.</td>
			<td>
				<select name="newsettings[ownernotification]">
					<option value="" {selected_ownernotification_}>NO</option>
					<option value="1" {selected_ownernotification_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_Assigned_Notification_TTS}.</td>
			<td>
				<select name="newsettings[assignednotification]">
					<option value="" {selected_assignednotification_}>NO</option>
					<option value="1" {selected_assignednotification_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_Group_Notification_TTS}.</td>
			<td>
				<select name="newsettings[groupnotification]">
					<option value="" {selected_groupnotification_}>NO</option>
					<option value="1" {selected_groupnotification_1}>YES</option>
					<option value="2" {selected_groupnotification_2}>Never</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_TTS_file_upload}:</td>
			<td>
				<select name="newsettings[fmttsfileupload]">
					<option value="" {selected_fmttsfileupload_}>NO</option>
					<option value="1" {selected_fmttsfileupload_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_mandatory_title_TTS}.</td>
			<td>
				<select name="newsettings[tts_mandatory_title]">
					<option value="" {selected_tts_mandatory_title_}>NO</option>
					<option value="1" {selected_tts_mandatory_title_1}>YES</option>
				</select>
			</td>
		</tr>

		<tr class="row_on">
			<td valign = 'top'>{lang_TTS_finnish_date}:</td>
			<td>
				<!--to be able to blank the setting - need an empty value-->
				<input type = 'hidden' name="newsettings[fmtts_group_finnish_date][]" value="">
				<table>
					{hook_fmtts_group_finnish_date}
				</table>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_send_response_TTS}.</td>
			<td>
				<select name="newsettings[tts_send_response]">
					<option value="" {selected_tts_send_response_}>NO</option>
					<option value="1" {selected_tts_send_response_1}>YES</option>
				</select>
			</td>
		</tr>

		<tr class="row_off">
			<td>{lang_Ask_for_workorder_approval_by_e-mail}.</td>
			<td>
				<select name="newsettings[workorder_approval]">
					<option value="" {selected_workorder_approval_}>NO</option>
					<option value="1" {selected_workorder_approval_1}>YES</option>
				</select>
			</td>
		</tr>

		<tr class="row_off">
			<td>{lang_Ask_for_project_approval_by_e-mail}.</td>
			<td>
				<select name="newsettings[project_approval]">
					<option value="" {selected_project_approval_}>NO</option>
					<option value="1" {selected_project_approval_1}>YES</option>
				</select>
			</td>
		</tr>

		<tr class="row_on">
			<td>{lang_project_suppress_meter}.</td>
			<td>
				<select name="newsettings[project_suppressmeter]">
					<option value="" {selected_project_suppressmeter_}>NO</option>
					<option value="1" {selected_project_suppressmeter_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_project_suppress_coordination}.</td>
			<td>
				<select name="newsettings[project_suppresscoordination]">
					<option value="" {selected_project_suppresscoordination_}>NO</option>
					<option value="1" {selected_project_suppresscoordination_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_project_optional_category}.</td>
			<td>
				<select name="newsettings[project_optional_category]">
					<option value="" {selected_project_optional_category_}>NO</option>
					<option value="1" {selected_project_optional_category_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_request_show_dates}.</td>
			<td>
				<select name="newsettings[request_show_dates]">
					<option value="" {selected_request_show_dates_}>NO</option>
					<option value="1" {selected_request_show_dates_1}>YES</option>
				</select>
			</td>
		</tr>

		<tr class="row_off">
			<td>{lang_request_location_level}.</td>
			<td>
				<select name="newsettings[request_location_level]">
					{hook_list_location_level_otions}
				</select>
			</td>
		</tr>


		<tr class="row_on">
			<td>{lang_lang_request_coordinator}:</td>
			<td><input name="newsettings[lang_request_coordinator]" value="{value_lang_request_coordinator}"></td>
		</tr>

		<tr class="row_on">
			<td>{lang_meter_table}:</td>
			<td><input name="newsettings[meter_table]" value="{value_meter_table}"></td>
		</tr>
		<tr class="row_off">
			<td>{lang_email_addresses_(comma-separated)_to_be_notified_about_tenant_claim_(empty_for_no_notify)}:</td>
			<td>
				<input name="newsettings[tenant_claim_notify_mails]" value="{value_tenant_claim_notify_mails}" size="40">
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_Receive_workorder_status_by_SMS}.</td>
			<td>
				<select name="newsettings[wo_status_sms]">
					<option value="" {selected_wo_status_sms_}>NO</option>
					<option value="1" {selected_wo_status_sms_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_Use_ACL_for_accessing_location_based_information}.</td>
			<td>
				<select name="newsettings[acl_at_location]">
					<option value="" {selected_acl_at_location_}>NO</option>
					<option value="1" {selected_acl_at_location_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_Bypass_ACL_for_accessing_tickets}.</td>
			<td>
				<select name="newsettings[bypass_acl_at_tickets]">
					<option value="" {selected_bypass_acl_at_tickets_}>NO</option>
					<option value="1" {selected_bypass_acl_at_tickets_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_Bypass_ACL_for_accessing_entities}.</td>
			<td>
				<!--to be able to blank the setting - need an empty value-->
				<input type = 'hidden' name="newsettings[bypass_acl_at_entity][]" value="">
				<table>
					{hook_bypass_acl_at_entity}
				</table>
			</td>
		</tr>

		<tr class="row_on">
			<td>{lang_Use_ACL_for_helpdesk_categories}.</td>
			<td>
				<select name="newsettings[acl_at_tts_category]">
					<option value="" {selected_acl_at_tts_category_}>NO</option>
					<option value="1" {selected_acl_at_tts_category_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_Use_location_at_workorder}.</td>
			<td>
				<select name="newsettings[location_at_workorder]">
					<option value="" {selected_location_at_workorder_}>NO</option>
					<option value="1" {selected_location_at_workorder_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_budget_at_project_level}.</td>
			<td>
				<select name="newsettings[budget_at_project]">
					<option value="" {selected_budget_at_project_}>NO</option>
					<option value="1" {selected_budget_at_project_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_update_project_budget_from_order}.</td>
			<td>
				<select name="newsettings[update_project_budget_from_order]">
					<option value="" {selected_update_project_budget_from_order_}>NO</option>
					<option value="1" {selected_update_project_budget_from_order_1}>YES</option>
				</select>
			</td>
		</tr>

		<tr class="row_off">
			<td>{lang_disallow_multiple_condition_types_at_demands}.</td>
			<td>
				<select name="newsettings[disallow_multiple_condition_types]">
					<option value="" {selected_disallow_multiple_condition_types_}>NO</option>
					<option value="1" {selected_disallow_multiple_condition_types_1}>YES</option>
				</select>
			</td>
		</tr>

		<tr class="row_on">
			<td valign = 'top'>{lang_list_location_level}:</td>
			<td>
				<!--to be able to blank the setting - need an empty value-->
				<input type = 'hidden' name="newsettings[list_location_level][]" value="">
				<table>
					{hook_list_location_level}
				</table>
			</td>
		</tr>

		<tr class="row_off">
			<td>{lang_ntlm_alternative_host}:</td>
			<td><input name="newsettings[ntlm_alternative_host]" value="{value_ntlm_alternative_host}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_uploader_filetypes}: jpg,gif,png</td>
			<td><input name="newsettings[uploader_filetypes]" value="{value_uploader_filetypes}"></td>
		</tr>

	<tr class="row_off">
		<td>{lang_filter_buildingpart}:</td>
		<td>
			<table>
				{hook_filter_buildingpart}
			</table>
		</td>
	</tr>

	<tr class="row_on">
		<td>{lang_condition_survey_import_category}:</td>
		<td>
			<table>
				{hook_condition_survey_import_cat}
			</table>
		</td>
	</tr>

	<tr class="row_off">
		<td>{lang_initial_status_that_are_to_be_set_when_condition_survey_are_imported}:</td>
		<td>
			<select name="newsettings[condition_survey_initial_status]">
				{hook_condition_survey_initial_status}
			</select>
		</td>
	</tr>

	<tr class="row_off">
		<td>{lang_hidden_status_that_are_to_be_set_when_condition_survey_are_imported}:</td>
		<td>
			<select name="newsettings[condition_survey_hidden_status]">
				{hook_condition_survey_hidden_status}
			</select>
		</td>
	</tr>

	<tr class="row_on">
		<td>{lang_obsolete_status_that_are_to_be_set_for_old_records_when_condition_survey_are_imported}:</td>
		<td>
			<select name="newsettings[condition_survey_obsolete_status]">
				{hook_condition_survey_obsolete_status}
			</select>
		</td>
	</tr>

		<!--
		groupnotification
		-->

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
