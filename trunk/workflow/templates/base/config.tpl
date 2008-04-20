<!-- BEGIN header -->
<form method="POST" action="{action_url}">
<table border="0" align="center" cellspacing="1" cellpading="1" width="90%">
   <tr class="th">
	   <td colspan="3"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
   </tr>
   <tr class="row_on" bgcolor="{th_err}">
    <td colspan="3">&nbsp;<b>{error}</b></font></td>
   </tr>
<!-- END header -->
<!-- BEGIN body -->
	<tr class="th">
		<td colspan="3">&nbsp;<b>{lang_Workflow_configuration}</b></font></td>
	</tr>
	<tr class="row_on">
		<td colspan="3">&nbsp;<b>{error}</b></font></td>
	</tr>
	<tr class="th">
		<td colspan="2">&nbsp;<b>{lang_Default_settings_for_processes}</b></font></td>
		<td class="row_off">&nbsp;</td>
	</tr>
	<tr class="th">
		<td>&nbsp;<b>{lang_Graphic_options}</b></font></td>
		<td colspan="2" class="row_on">&nbsp;</td>
	</tr>
	<tr class="row_off">
		<td width="60%" class="td_left">
			{lang_draw_roles_on_the_graph_beside_activity_name_like_[that]}
		</td>
		<td width="20%" class="td_right">
			{lang_draw_roles}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[draw_roles]">
                                <option value="False" {selected_draw_roles_False}>{lang_No}</option>
                                <option value="True" {selected_draw_roles_True}>{lang_Yes}</option>
                        </select>
		</td>
	</tr>
	<tr class="row_on">
		<td width="60%" class="td_left">
			{lang_size_of_the_font_used_on_the_graph_12_should_be_a_good_default_value}
		</td>
		<td width="20%" class="td_right">
			{lang_font_size}:
		</td>
		<td width="20%" class="td_right">
			<input type="text" size="3" name="newsettings[font_size]" value="{value_font_size}">
		</td>
	</tr>

	<tr class="th">
		<td>&nbsp;<b>{lang_Running_activities_options}</b></font></td>
		<td colspan="2" class="row_off">&nbsp;</td>
	</tr>
	<tr class="row_on">
		<td width="60%" class="td_left">
			{lang_a_please_wait_message_is_shown_when_loading_the_activity_form_usefull_for_long_tasks}
		</td>
		<td width="20%" class="td_right">
			{lang_display_please_wait_message}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[display_please_wait_message]">
                               	<option value="False" {selected_display_please_wait_message_False}>{lang_No}</option>
                                <option value="True" {selected_display_please_wait_message_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
	<tr class="row_off">
		<td width="60%" class="td_left">
			{lang_if_automatic_parsing_is_disabled_next_options_will_be_useless}
		</td>
		<td width="20%" class="td_right">
			{lang_use_automatic_parsing}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[use_automatic_parsing]">
                               	<option value="False" {selected_use_automatic_parsing_False}>{lang_No}</option>
                                <option value="True" {selected_use_automatic_parsing_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
	<tr class="row_on">
		<td width="60%" class="td_left">
			{lang_the_instance_title_is_shown_on_top_of_the_form}
		</td>
		<td width="20%" class="td_right">
			{lang_show_activity_title}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[show_activity_title]">
                               	<option value="False" {selected_show_activity_title_False}>{lang_No}</option>
                                <option value="True" {selected_show_activity_title_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
	<tr class="row_off">
		<td width="60%" class="td_left">
			{lang_when_multiple_submit_options_are_avaible_we_draw_a_select_box_with_only_one_submit_instead_of_multiple_buttons}
		</td>
		<td width="20%" class="td_right">
			{lang_show_multiple_submit_as_select}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[show_multiple_submit_as_select]">
                               	<option value="False" {selected_show_multiple_submit_as_select_False}>{lang_No}</option>
                                <option value="True" {selected_show_multiple_submit_as_select_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
	<tr class="row_off">
		<td width="60%" class="td_left">
			{lang_this_will_display_an_activity_footer_when_any_activity_is_runned_with_workflow_engine_related_informations}
		</td>
		<td width="20%" class="td_right">
			{lang_show_activity_info_zone}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[show_activity_info_zone]">
                               	<option value="False" {selected_show_activity_info_zone_False}>{lang_No}</option>
                                <option value="True" {selected_show_activity_info_zone_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
	<tr class="row_on">
		<td width="60%" class="td_left">
			{lang_this_will_automatically_release_the_instance_when_leaving_an_activity_without_completing}
		</td>
		<td width="20%" class="td_right">
			{lang_auto-release_on_leaving_activity}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[auto-release_on_leaving_activity]">
                               	<option value="False" {selected_auto-release_on_leaving_activity_False}>{lang_No}</option>
                                <option value="True" {selected_auto-release_on_leaving_activity_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>

	<tr class="th">
		<td>&nbsp;<b>{lang_Actions_Right_Options}</b></font></td>
		<td colspan="2" class="row_off">&nbsp;</td>
	</tr>
	<tr class="row_on">
		<td width="60%" class="td_left">
			{lang_owner_of_the_instance_will_have_the_right_to_abort_the_instance_at_any_time}
		</td>
		<td width="20%" class="td_right">
			{lang_ownership_give_abort_right}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[ownership_give_abort_right]">
                               	<option value="False" {selected_ownership_give_abort_right_False}>{lang_No}</option>
                                <option value="True" {selected_ownership_give_abort_right_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
	<tr class="row_off">
		<td width="60%" class="td_left">
			{lang_owner_of_the_instance_will_have_the_right_to_exception_or_resume_the_instance_at_any_time}
		</td>
		<td width="20%" class="td_right">
			{lang_ownership_give_exception_right}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[ownership_give_exception_right]">
                               	<option value="False" {selected_ownership_give_exception_right_False}>{lang_No}</option>
                                <option value="True" {selected_ownership_give_exception_right_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
	<tr class="row_on">
		<td width="60%" class="td_left">
			{lang_owner_of_the_instance_will_have_the_right_to_release_(un-assign)_an_activity_assigned_to_an_user}
		</td>
		<td width="20%" class="td_right">
			{lang_ownership_give_release_right}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[ownership_give_release_right]">
                               	<option value="False" {selected_ownership_give_release_right_False}>{lang_No}</option>
                                <option value="True" {selected_ownership_give_release_right_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
	<tr class="row_off">
		<td width="60%" class="td_left">
			{lang_if_user_is_in_a_role_for_an_activity_he_will_have_the_right_to_abort_the_related_instance}
		</td>
		<td width="20%" class="td_right">
			{lang_role_give_abort_right}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[role_give_abort_right]">
                               	<option value="False" {selected_role_give_abort_right_False}>{lang_No}</option>
                                <option value="True" {selected_role_give_abort_right_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
	<tr class="row_on">
		<td width="60%" class="td_left">
			{lang_if_user_is_in_a_role_for_an_activity_he_will_have_the_right_to_exception_or_resume_the_related_instance}
		</td>
		<td width="20%" class="td_right">
			{lang_role_give_exception_right}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[role_give_exception_right]">
                               	<option value="False" {selected_role_give_exception_right_False}>{lang_No}</option>
                                <option value="True" {selected_role_give_exception_right_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
	<tr class="row_off">
		<td width="60%" class="td_left">
			{lang_if_user_is_in_a_role_for_an_activity_he_will_have_the_right_to_release_(un-assign)_the_related_instance}
		</td>
		<td width="20%" class="td_right">
			{lang_role_give_release_right}:
		</td>
		<td width="20%" class="td_right">
			<select name="newsettings[role_give_release_right]">
                               	<option value="False" {selected_role_give_release_right_False}>{lang_No}</option>
                                <option value="True" {selected_role_give_release_right_True}>{lang_Yes}</option>
       	                </select>
		</td>
	</tr>
<!-- END body -->
<!-- BEGIN footer -->
  <tr class="row_off">
    <td colspan="3">
&nbsp;
    </td>
  </tr>
  <tr>
    <td colspan="3" align="center">
      <input type="submit" name="submit" value="{lang_submit}">
      <input type="submit" name="cancel" value="{lang_cancel}">
    </td>
  </tr>
</table>
</form>
<!-- END footer -->
