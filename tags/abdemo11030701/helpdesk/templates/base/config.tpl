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
			<td colspan="2">&nbsp;<b>{lang_helpdesk}/{lang_settings}</b></td>
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
		<tr class="row_on">
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
		<tr class="row_off">
			<td>{lang_Owner_Notification_TTS}.</td>
			<td>
				<select name="newsettings[ownernotification]">
					<option value="" {selected_ownernotification_}>NO</option>
					<option value="1" {selected_ownernotification_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
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
		<tr class="row_on">
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
			<td>{lang_Use_ACL_for_helpdesk_categories}.</td>
			<td>
				<select name="newsettings[acl_at_tts_category]">
					<option value="" {selected_acl_at_tts_category_}>NO</option>
					<option value="1" {selected_acl_at_tts_category_1}>YES</option>
				</select>
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
				<input type="submit" name="submit" value="{lang_submit}">
				<input type="submit" name="cancel" value="{lang_cancel}">
			</td>
		</tr>
	</table>
</form>
<!-- END footer -->
