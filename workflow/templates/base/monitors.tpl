<LINK href="{monitors_css_link}"  type="text/css" rel="StyleSheet">
<div class="message">{message}</div>
{monitor_tabs}
<table class="monitor_table_header">
	<tr class="th">
		<td colspan="3" class="monitor_header_title">
			{lang_List_of_monitors}
		</td>
	</tr>
	<tr class="row_on">
		<td>
			<a href="{link_monitor_processes}">{img_monitor_processes}</a>
		</td>
		<td>
			<a href="{link_monitor_processes}">{lang_monitor_processes}</a>
		</td>
		<td>
			{help_monitor_processes}
		</td>
	</tr>
	<tr class="row_off">
		<td>
			<a href="{link_monitor_activities}">{img_monitor_activities}</a>
		</td>
		<td>
			<a href="{link_monitor_activities}">{lang_monitor_activities}</a>
		</td>
		<td>
			{help_monitor_activities}
		</td>
	</tr>
	<tr class="row_on">
		<td>
			<a href="{link_monitor_instances}">{img_monitor_instances}</a>
		</td>
		<td>
			<a href="{link_monitor_instances}">{lang_monitor_instances}</a>
		</td>
		<td>
			{help_monitor_instances}
		</td>
	</tr>
	<tr class="row_off">
		<td>
			<a href="{link_monitor_workitems}">{img_monitor_workitems}</a>
		</td>
		<td>
			<a href="{link_monitor_workitems}">{lang_monitor_workitems}</a>
		</td>
		<td>
			{help_monitor_workitems}
		</td>
	</tr>
</table>
<table class="monitor_table_header">
<form action="{form_action}" method="post">
	<tr class="th">
		<td colspan="3" class="monitor_header_title">
			{lang_cleanup_actions}
		</td>
	</tr>
	<tr class="row_on">
		<td>
			{lang_Process:}
			<select name="filter_process_cleanup_aborted">
                        <option {filter_process_cleanup_aborted_selected_all} value="">{lang_All}</option>
                        <!-- BEGIN block_filter_process_cleanup_aborted -->
                        <option {filter_process_cleanup_aborted_selected} value="{filter_process_cleanup_aborted_value}">{filter_process_cleanup_aborted_name} {filter_process_cleanup_aborted_version}</option>
                        <!-- END block_filter_process_cleanup_aborted -->
                        </select>
		</td>
		<td>
			<input type="submit" name="cleanup_aborted_instances" value="{lang_cleanup_aborted_instances}" />
		</td>
		<td>
			{help_cleanup_aborted}
		</td>
	</tr>
	<tr class="row_off">
		<td>
			{lang_Process:}
			<select name="filter_process_cleanup">
                        <!-- BEGIN block_filter_process_cleanup -->
                        <option {filter_process_cleanup_selected} value="{filter_process_cleanup_value}">{filter_process_cleanup_name} {filter_process_cleanup_version}</option>
                        <!-- END block_filter_process_cleanup -->
                        </select>
		</td>
		<td>
			<input type="submit" name="cleanup_process" value="{lang_remove_all_instances_for_this_process}" />
		</td>
		<td>
			{help_cleanup}
		</td>
	</tr>
</form>
</table>
{monitor_stats}
