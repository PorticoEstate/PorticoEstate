{processes_css}
<LINK href="{monitors_css_link}"  type="text/css" rel="StyleSheet">
<div class="message">{message}</div>
{monitor_tabs}
<form action="{form_action}" method="post">
<input type="hidden" name="start" value="0" />
<input type="hidden" name="sort" value="{sort}" />
<input type="hidden" name="order" value="{order}" />
<table class="monitor_table_header">
	<tr class="th">
		<td class="monitor_header_title" colspan="8">
			{lang_List_of_instances}
		</td>
	</tr>
	<tr class="row_off">
		<td class="filter_label_cell">
			{lang_Process:}
		</td>
		<td class="filter_action_cell">
			<select name="filter_process">
			<option {filter_process_selected_all} value="">{lang_All}</option>
			<!-- BEGIN block_filter_process -->
			<option {filter_process_selected} value="{filter_process_value}">{filter_process_name} {filter_process_version}</option>
			<!-- END block_filter_process -->
			</select>
		</td>
		<td class="filter_label_cell">
			{lang_Activity:}
		</td>
		<td class="filter_action_cell" colspan="3"> 
			<select name="filter_activity">
			<option {filter_activity_selected_all} value="">{lang_All}</option>
			<!-- BEGIN block_filter_activity -->
			<option {filter_activity_selected} value="{filter_activity_value}">{filter_activity_name}</option>
			<!-- END block_filter_activity -->
			</select>
		</td>
		<td class="filter_label_cell">
			{lang_Status:}
		</td>
		<td class="filter_action_cell">
			<select name="filter_status">
			<option {filter_status_selected_all} value="">{lang_All}</option>
			<!-- BEGIN block_filter_status -->
			<option {filter_status_selected} value="{filter_status_value}">{filter_status_name}</option>
			<!-- END block_filter_status -->
			</select>
		</td>
	</tr>
	<tr class="row_off">
		<td class="filter_label_cell">
			{lang_Act._Status:}
		</td>
		<td class="filter_action_cell">
			<select name="filter_act_status">
				<option value="" {filter_act_status_selected_all}>{lang_All}</option>
				<option value="running" {filter_act_status_running}>{lang_running}</option>
				<option value="completed" {filter_act_status_completed}>{lang_completed}</option>
			</select>
		</td>
		<td class="filter_label_cell">
			{lang_User:}
		</td>
		<td class="filter_action_cell">
			<select name="filter_user">
			<option {filter_user_selected_all} value="">{lang_All}</option>
			<!-- BEGIN block_filter_user -->
			<option {filter_user_selected} value="{filter_user_value}">{filter_user_name}</option>
			<!-- END block_filter_user -->
			</select>
		</td>
		<td class="filter_label_cell">
			{lang_Search:}
		</td>
		<td class="filter_action_cell">
			<input size="8" type="text" name="search_str" value="{search_str}" />
		</td>
		<td class="filter_action_cell" colspan="2">	
			<input type="submit" name="filter" value="{lang_filter}" />
		</td>
	</tr>
</table>	
</form>

<form action="{form_action}" method="post">
<input type="hidden" name="start" value="{start}" />
<input type="hidden" name="search_str" value="{search_str}" />
<input type="hidden" name="sort" value="{sort}" />
<input type="hidden" name="order" value="{order}" />
<input type="hidden" name="filter_process" value="{filter_process_up}" />
<table class="monitor_table_list">
	<tr><td colspan="8">
        <table class="table_showing_rows">
		<tr class="tr_showing_rows">
                	{left}
	        	<td><div align="center">{lang_showing}</div></td>
	                {right}
        	</tr>
	</table>
	</td>
	</tr>
	<tr class="th" style="font-weight:bold">
		<th class="th_mi_instanceid">{header_wf_instance_id}</th>
		<th class="th_mi_name">{header_wf_instance_name}</th>
		<th class="th_mi_procname">{header_wf_procname}</th>
		<th class="th_mi_actname">{header_wf_activity_name}</th>
		<th class="th_mi_status">{header_wf_status}</th>
		<th class="th_mi_actstatus">{header_wf_act_status}</th>
		<th class="th_mi_owner">{header_wf_owner}</th>
		<th class="th_mi_user">{header_wf_user}</th>
	</tr>
	<!-- BEGIN block_inst_table -->
	<tr class="{class_alternate_row}">
		<td class="td_mi_instanceid">
		  <a href="{inst_id_href}">{inst_id}</a>
		</td>
		<td class="td_mi_name">
			{instance_name}
		</td>
		<td class="td_mi_procname row_{process_css_name}">
			<span class="process_css_name"}>{inst_procname}&nbsp;{inst_version}</span>
		</td>
		<td class="td_mi_actname">
			{activity_name}
		</td>
		<td class="td_mi_status">
			{inst_status}
		</td>
		<td class="td_mi_actstatus">
			{inst_act_status}
		</td>
		<td class="td_mi_owner">
			{inst_owner}
		</td>
		<td class="td_mi_user">
			{inst_user}
		</td>
	</tr>
	<!-- END block_inst_table -->
</table>
</form>
{monitor_stats}
