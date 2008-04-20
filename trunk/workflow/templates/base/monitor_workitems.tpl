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
		<td colspan="7" class="monitor_header_title">
			{lang_List_of_workitems}
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
			{lang_Activity:}&nbsp;
		</td>
		<td class="filter_action_cell" colspan="4">
			<select name="filter_activity">
			<option {filter_activity_selected_all} value="">{lang_All}</option>
			<!-- BEGIN block_filter_activity -->
			<option {filter_activity_selected} value="{filter_activity_value}">{filter_activity_name}</option>
			<!-- END block_filter_activity -->
			</select>
		</td>
	</tr>
	<tr class="row_off">
		<td class="filter_label_cell">
			{lang_User:}&nbsp;
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
			{lang_Instance:}&nbsp;
		</td>
		<td class="filter_action_cell">
			<input type="text" name="filter_instance" value="{filter_instance}" size="4" />
		</td>
		<td class="filter_label_cell">
			{lang_Search:}&nbsp;
		</td>
		<td class="filter_action_cell">
			<input size="8" type="text" name="search_str" value="{search_str}" />
		</td>
		<td class="filter_action_cell">	
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
<table class="monitor_table_list">
	<tr><td colspan="9">
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
		<th class="th_mw_itemid">{header_wf_item_id}</th>
		<th class="th_mw_details">{header_details}</th>
		<th class="th_mw_procname">{header_wf_procname}</th>
		<th class="th_mw_actname">{header_wf_act_name}</th>
		<th class="th_mw_instanceid">{header_wf_instance_id}</th>
		<th class="th_mw_orderid">{header_wf_order_id}</th>
		<th class="th_mw_started">{header_wf_started}</th>
		<th class="th_mw_duration">{header_wf_duration}</th>
		<th class="th_mw_user">{header_wf_user}</th>
	</tr>
	<!-- BEGIN block_workitems_table -->
	<tr class="{class_alternate_row}">
		<td class="td_mw_itemid">
			<a href="{wi_href}">{wi_id}</a>
		</td>
		<td class="td_mw_details">
			{link_view_details}
		</td>
		<td class="td_mw_procname row_{process_css_name}">
			<span class="{process_css_name}">{wi_wf_procname} {wi_version}</span>
		</td>
		<td class="td_mw_actname">
			{act_icon} {wi_actname}
		</td>
		<td class="td_mw_instanceid">
		  <a href="{wi_adm_inst_href}">{wi_inst_id}</a>
		</td>
		<td class="td_mw_orderid">
		  {wi_order_id}
		</td>
		<td class="td_mw_started">
		  {wi_started}
		</td>
		<td class="td_mw_duration">
		  {wi_duration}
		</td>
		<td class="td_mw_user">
		  {wi_user}
		</td>
	</tr>
	<!-- END block_workitems_table -->
</table>
</form>
{monitor_stats}
