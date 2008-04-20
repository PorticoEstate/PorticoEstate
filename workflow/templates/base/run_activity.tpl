<div id="wf_run_activity_message">{wf_message}</div>
<LINK href="{run_activity_css_link}"  type="text/css" rel="StyleSheet" />
<LINK href="{run_activity_print_css_link}" type="text/css" rel="stylesheet" media="print" />
<div id="wf_run_activity_zone">
	<form method="post" enctype='multipart/form-data' name="workflow_form">
	<div id="wf_activity_playground">
				<!-- BEGIN block_title_zone -->
				<div class="th">
					<span id="wf_activity_title">
						{activity_title}
					</span>
				</div>
				<!-- END block_title_zone -->
				<!-- BEGIN block_instance_name_zone -->
				<div id="wf_instance_name_zone">
					<span id="wf_instance_name">
					{wf_instance_name}
					</span>
				</div>
				<!-- END block_instance_name_zone -->
				<div id="wf_activity_template">
					{activity_template}
				</div>
				<!-- BEGIN block_priority_zone -->
				<div id="wf_priority_zone">
					<span id="wf_priority_label">
						{Priority_text}&nbsp;
					</span>
					<span id="wf_priority_select">
						<select name="wf_priority">
						<!-- BEGIN block_priority_options -->
						<option  {selected_priority_options} value="{priority_option_name}">{priority_option_value}</option>
						<!-- END block_priority_options -->
						</select>
					</span>
				</div>
				<!-- END block_priority_zone -->
				<!-- BEGIN block_set_next_user_zone -->
				<div id="wf_set_next_user_zone">
					<span id="wf_set_next_user_label">
						{set_next_user_text}&nbsp;
					</span>
					<span id="wf_set_next_user_select">
						<!-- BEGIN block_select_next_user -->
						<select name="wf_set_next_user">
						<option  {selected_next_user_options_default} value="*">{lang_default_next_user}</option>
						<!-- BEGIN block_next_user_options --><option {selected_next_user_options} value="{next_user_option_id}">{next_user_option_value}</option>
						<!-- END block_next_user_options -->
						</select>
						<!-- END block_select_next_user -->
					</span>
				</div>
				<!-- END block_set_next_user_zone -->
				<!-- BEGIN block_set_owner_zone -->
				<div id="wf_set_owner_zone">
					<span id="wf_set_owner_label">
						{set_owner_text}&nbsp;
					</span>
					<span id="wf_set_owner_select">
						<!-- BEGIN block_select_owner -->
						<select name="wf_set_owner">
						<option  {selected_owner_options_default} value="">{lang_default_owner}</option>
						<!-- BEGIN block_owner_options --><option {selected_owner_options} value="{owner_option_id}">{owner_option_value}</option>
						<!-- END block_owner_options -->
						</select>
						<!-- END block_select_owner -->
					</span>
				</div>
				<!-- END block_set_owner_zone -->
				<!-- BEGIN block_print_mode_zone -->
				<div id="wf_print_mode_zone">
					<span id="wf_print_mode_button"><input type="submit" name="{print_mode_name}" value="{print_mode_value}"></span>
				</div>
				<!-- END block_print_mode_zone -->
				<!-- BEGIN block_submit_zone -->
				<div id="wf_submit_zone">
					<!-- BEGIN block_submit_select_area -->
					<span id="wf_submit_select">
						<select name="submit_options">
						<!-- BEGIN block_submit_options -->
						<option value="{submit_option_name}">{submit_option_value}</option>
						<!-- END block_submit_options -->
						</select>
					</span>
					<span class="wf_submit_buttons_button" id="wf_submit_button_unique">
						<input type="submit" name="{submit_button_name}" value="{submit_button_value}">
					</span>
					<!-- END block_submit_select_area -->
					<!-- BEGIN block_submit_buttons_area -->
					<div id="wf_submit_buttons">
						<table class="table_submit_buttons">
						<tr class="row_off">
						{submit_buttons}
						</tr>
						</table>
					</div>
					<!-- END block_submit_buttons_area -->
				</div>
				<!-- END block_submit_zone -->
		</form>
	</div>
	<!-- BEGIN workflow_info_zone -->
	<div id="workflow_info_zone">
		<table class="table_info">
		  <tr class="row_info"> 
		    <td class="cell_info_label">{lang_process:}</td>
		    <td class="cell_info_value">{wf_process_name}</td>
		    <td class="cell_info_label">{lang_instance:}</td>
		    <td class="cell_info_value">({wf_instance_id})-{wf_instance_name}</td>
		    <td class="cell_info_label">{lang_started:}</td>
		    <td class="cell_info_value">{wf_started}</td>
		    <td class="cell_info_label">{lang_owner:}</td>
		    <td class="cell_info_value">{wf_owner}</td>
		</tr>
		<tr class="row_info">
		    <td class="cell_info_label">{lang_process_version:}</td>
		    <td class="cell_info_value">{wf_process_version}</td>
		    <td class="cell_info_label">{lang_activity:}</td>
		    <td class="cell_info_value">{wf_activity_name}</td>
		    <td class="cell_info_label">{lang_date:}</td>
		    <td class="cell_info_value">{wf_date}</td>
		    <td class="cell_info_label">{lang_user:}</td>
		    <td class="cell_info_value">{wf_user_name}</td>
		  </tr>
		</table>
	</div>
	<!-- END workflow_info_zone -->
</div>
{history}