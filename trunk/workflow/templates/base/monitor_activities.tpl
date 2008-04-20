{processes_css}
<LINK href="{monitors_css_link}"  type="text/css" rel="StyleSheet">
<div class="message">{message}</div>
{monitor_tabs}
<form id="filterf" action="{form_action}" method="post">
<input type="hidden" name="start" value="0" />
<input type="hidden" name="sort" value="{sort}" />
<input type="hidden" name="order" value="{order}" />
<table class="monitor_table_header">
	<tr class="th">
		<td class="monitor_header_title" colspan="8">
			{lang_List_of_activities}
		</td>
	</tr>
	<tr class="row_off">
		<td class="filter_label_cell">
			{lang_Process:}&nbsp;
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
			{lang_Type:}&nbsp;
		</td>
		<td class="filter_action_cell">
			<select name="filter_type">
				<option {filter_type_selected_all} value="">{lang_All}</option>
				<!-- BEGIN block_filter_type -->
				<option {filter_type_selected} value="{filter_types}">{filter_type}</option>
				<!-- END block_filter_type -->
			</select>
		</td>
		<td class="filter_label_cell">
			{lang_Interactive:}&nbsp;
		</td>
		<td align="center">
			<select name="filter_is_interactive">
				<option {filter_interac_selected_all} value="">{lang_All}</option>
				<option value="y" {filter_interac_selected_y}>{lang_Interactive}</option>
				<option value="n" {filter_interac_selected_n}>{lang_Automatic}</option>
			</select>
		</td>
		<td class="filter_label_cell">
			{lang_Routing:}&nbsp;
		</td>
		<td class="filter_action_cell">
			<select name="filter_is_autorouted">
				<option {filter_route_selected_all} value="">{lang_All}</option>
				<option value="n" {filter_route_selected_n}>{lang_Manual}</option>
				<option value="y" {filter_route_selected_y}>{lang_Automatic}</option>
			</select>
		</td>
	</tr>
	<tr class="row_off">
		<td class="filter_label_cell">
			{lang_Activity:}&nbsp;
		</td>
		<td colspan="4" class="filter_action_cell">
			<select name="filter_activity">
			<option {filter_activity_selected_all} value="">{lang_All}</option>
			<!-- BEGIN block_filter_activity -->
			<option {filter_activity_selected} value="{filter_activity_value}">{filter_activity_name}</option>
			<!-- END block_filter_activity -->
			</select>
		</td>
		<td class="filter_label_cell">
			{lang_Search:}&nbsp;
		</td>
		<td class="filter_action_cell">
			<input size="8" type="text" name="search_str" value="{search_str}" />
		</td>
		<td class="filter_action_cell">	
			<input type="submit" name="filter" value="{lang_Filter}" />
		</td>
	</tr>
</table>	
</form>

<form action="{form_action}" method="post">
<input type="hidden" name="start" value="{start}" />
<input type="hidden" name="search_str" value="{search_str}" />
<input type="hidden" name="sort" value="{sort}" />
<input type="hidden" name="order" value="{order}" />
<table class="monitor_table_list">
	<tr>
		<td colspan="7">
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
		<th class="th_ma_procname">{header_wf_procname}</th>
		<th class="th_ma_name">{header_wf_name}</th>
		<th class="th_ma_logo">&nbsp;</th>
		<th class="th_ma_type">{header_wf_type}</th>
		<th class="th_ma_interactive">{header_wf_is_interactive}</th>
		<th class="th_ma_autorouted">{header_wf_is_autorouted}</th>
		<th class="th_ma_instances">{lang_Instances}</th>
	</tr>
	<!-- BEGIN block_act_table -->
	<tr class="{class_alternate_row}">
		<td class="td_ma_procname row_{process_css_name}">
			<span class="{process_css_name}">{act_process}&nbsp;{act_process_version}</span>
		</td>
		<td class="td_ma_name">
		  <a href="{act_href}">{act_name}</a> {act_run}
		</td>
		<td  class="td_ma_logo">
			{act_icon}
		</td>
		<td class="td_ma_type">
			{act_type}
		</td>
		
		<td class="td_ma_interactive">
			{act_is_interactive}
		</td>
		<td class="td_ma_autorouted">
			{act_is_autorouted}
		</td>
		
		<td class="td_ma_instances">
			<table class=table_ma_instances_stats>
			<tr>
				 <td class="td_ma_in_instances"><a class="wf_active" href="{act_active_href}">{active_instances}</a></td>
				 <td class="td_ma_in_instances"><a class="wf_completed" href="{act_completed_href}">{completed_instances}</a></td>
				 <td class="td_ma_in_instances"><a class="wf_aborted" href="{act_aborted_href}">{aborted_instances}</a></td>
				 <td class="td_ma_in_instances"><a class="wf_exception" href="{act_exception_href}">{exception_instances}</a></td>
			</tr>
			</table>
		</td>
	</tr>
	<!-- END block_act_table -->
</table>
</form>
{monitor_stats}
