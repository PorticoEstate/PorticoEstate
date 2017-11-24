<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered pure-table-striped">
		<thead>
			<tr>
				<th colspan="2">{title}</th>
			</tr>
		</thead>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="row_off">
			<td colspan="2">&nbsp;<b>{lang_helpdesk}/{lang_settings}</b></td>
		</tr>
		<tr>
			<td>{lang_app_name}: <br>
				{lang_default}: {lang_Helpdesk}</td>
			<td><input name="newsettings[app_name]" value="{value_app_name}"></td>
		</tr>
		<tr>
			<td>{lang_new_message}:</br> '__ID__' </td>
			<td><input name="newsettings[new_message]" value="{value_new_message}"></td>
		</tr>
		<tr>
			<td>{lang_set_user_message}:</br> '__ID__' </td>
			<td><input name="newsettings[set_user_message]" value="{value_set_user_message}"></td>
		</tr>
		<tr>
			<td>{lang_update_message}:</br> '__ID__', '__#__' </td>
			<td><input name="newsettings[update_message]" value="{value_update_message}"></td>
		</tr>
		<tr>
			<td>{lang_close_message}:</br> '__ID__', '__#__'</td>
			<td><input name="newsettings[close_message]" value="{value_close_message}"></td>
		</tr>

		<tr>
			<td>{lang_from_email}: <br>
			<td><input name="newsettings[from_email]" value="{value_from_email}"></td>
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
		<tr>
			<td>{lang_Open_translates_to}: <br>
				{lang_default}: {lang_Open}</td>
			<td><input name="newsettings[tts_lang_open]" value="{value_tts_lang_open}"></td>
		</tr>
		<tr>
			<td>{lang_reopen_status}:</td>
			<td>
				<select name="newsettings[reopen_status]">
					{hook_reopen_status}
				</select>
			</td>
		<tr>
		<tr class="pure-table-odd">
			<td >{lang_TTS_disable_assign_to_user_on_add}:</td>
			<td>
				<select name="newsettings[tts_disable_userassign_on_add]">
					<option value="" {selected_tts_disable_userassign_on_add_}>NO</option>
					<option value="1" {selected_tts_disable_userassign_on_add_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="pure-table-odd">
			<td >{lang_TTS_disable_assign_to_group_on_add}:</td>
			<td>
				<select name="newsettings[tts_disable_groupassign_on_add]">
					<option value="" {selected_tts_disable_groupassign_on_add_}>NO</option>
					<option value="1" {selected_tts_disable_groupassign_on_add_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="pure-table-odd">
			<td >{lang_TTS_disable_priority}:</td>
			<td>
				<select name="newsettings[disable_priority]">
					<option value="" {selected_disable_priority_}>NO</option>
					<option value="1" {selected_disable_priority_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr>
			<td >{lang_TTS_default_interface}:</td>
			<td>
				<select name="newsettings[tts_default_interface]">
					<option value="" {selected_tts_default_interface_}>Full</option>
					<option value="simplified" {selected_tts_default_interface_simplified}>{lang_simplified}</option>
				</select>
			</td>
		</tr>

		<tr>
			<td valign = 'top'>{lang_TTS_simplified_group}:</td>
			<td>
				<!--to be able to blank the setting - need an empty value-->
				<input type = 'hidden' name="newsettings[fmttssimple_group][]" value="">
				<table>
					{hook_fmttssimple_group}
				</table>
			</td>
		</tr>
		<tr>
			<td valign = 'top'>{lang_TTS_assign_group_candidates}:</td>
			<td>
				<!--to be able to blank the setting - need an empty value-->
				<input type = 'hidden' name="newsettings[fmtts_assign_group_candidates][]" value="">
				<table>
					{hook_fmtts_assign_group_candidates}
				</table>
			</td>
		</tr>
		<tr>
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
		<tr class="row_off">
			<td>{lang_Owner_Notification_TTS}.</td>
			<td>
				<select name="newsettings[ownernotification]">
					<option value="" {selected_ownernotification_}>NO</option>
					<option value="1" {selected_ownernotification_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_Assigned_Notification_TTS}.</td>
			<td>
				<select name="newsettings[assignednotification]">
					<option value="" {selected_assignednotification_}>NO</option>
					<option value="1" {selected_assignednotification_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_Group_Notification_TTS}.</td>
			<td>
				<select name="newsettings[groupnotification]">
					<option value="" {selected_groupnotification_}>NO</option>
					<option value="1" {selected_groupnotification_1}>YES</option>
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
		<tr>
			<td>{lang_priority_levels_TTS}.</td>
			<td>
				<select name="newsettings[prioritylevels]">
					<option value="" {selected_prioritylevels_}>3</option>
					<option value="4" {selected_prioritylevels_4}>4</option>
					<option value="5" {selected_prioritylevels_5}>5</option>
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

		<tr>
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
			<td>{lang_Use_ACL_for_helpdesk_categories}.</td>
			<td>
				<select name="newsettings[acl_at_tts_category]">
					<option value="" {selected_acl_at_tts_category_}>NO</option>
					<option value="1" {selected_acl_at_tts_category_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_activate_autocreate_user}:</td>
			<td>
				<select name="newsettings[autocreate_user]">
					<option value="" {selected_autocreate_user_}>NO</option>
					<option value="1" {selected_autocreate_user_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_autocreate_default_group}:</td>
			<td>
				<select name="newsettings[autocreate_default_group]">
					{hook_autocreate_default_group}
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_uploader_filetypes}: jpg,gif,png</td>
			<td><input name="newsettings[uploader_filetypes]" value="{value_uploader_filetypes}"></td>
		</tr>

		<!-- END body -->
		<!-- BEGIN footer -->
		<tfoot>
			<tr>
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
		</tfoot>
	</table>
</form>
<!-- END footer -->
