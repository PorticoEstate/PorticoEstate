{processes_css}
<LINK href="{monitors_css_link}"  type="text/css" rel="StyleSheet">
<div class="message">{message}</div>
{monitor_tabs}
<form action="{form_action}" method="post">
<input type="hidden" name="start" value="0" />
<input type="hidden" name="order" value="{order}" />
<input type="hidden" name="sort" value="{sort}" />
<table class="monitor_table_header" cellspacing="0">
	<tr class="th">
		<td colspan="9" class="monitor_header_title">
			{lang_List_of_processes}
		</td>
	</tr>
	<tr class="row_off">
		<td><div class="filter_label_cell">
			{lang_Process:}&nbsp;
		</div></td>
		<td><div class="filter_action_cell">
			<select onchange='this.form.submit();' name="filter_process">
				<option {filter_process_selected_all} value="">{lang_All}</option>
				<!-- BEGIN block_filter_process -->
					<option {filter_process_selected} value="{filter_process_value}">{filter_process_name} {filter_process_version}</option>
				<!-- END block_filter_process -->
			</select>
		</div></td>
		<td><div class="filter_label_cell">
			{lang_Active:}&nbsp;
		</div></td>
		<td><div class="filter_action_cell">
			<select onchange='this.form.submit();' name="filter_active">
				<option {selected_active_all} value="">{lang_All}</option>
				<option value="y" {selected_active_active}>{lang_Active}</option>
				<option value="n" {selected_active_inactive}>{lang_Inactive}</option>
			</select>
		</div></td>
		<td><div class="filter_label_cell">
			{lang_Valid:}&nbsp;
		</div></td>
		<td><div class="filter_action_cell">
			<select onchange='this.form.submit();' name="filter_valid">
				<option {selected_valid_all} value="">{lang_All}</option>
				<option {selected_valid_valid} value="y">{lang_Valid}</option>
				<option {selected_valid_invalid} value="n">{lang_Invalid}</option>
			</select>
		</div></td>
		<td><div class="filter_label_cell">
			{lang_Search:}&nbsp;
		</div></td>
		<td class="filter_action_cell">
			<input size="18" type="text" name="search_str" value="{search_str}" />
		</td>
		<td><div class="filter_action_cell">	
			<input type="submit" name="filter" value="{lang_filter}" />
		</div></td>
	</tr>
</table>	
</form>

<form action="{form_action}" method="post">
<input type="hidden" name="start" value="{start}" />
<input type="hidden" name="search_str" value="{search_str}" />
<input type="hidden" name="sort" value="{sort}" />
<input type="hidden" name="order" value="{order}" />
<input type="hidden" name="filter_process" value="{filter_process_up}" />
<input type="hidden" name="filter_active" value="{filter_active_up}" />
<input type="hidden" name="filter_valid" value="{filter_valid_up}" />
<table class="monitor_table_list">
	<tr><td colspan="5">
        <table class="table_showing_rows">
		<tr class="tr_showing_rows">
                	{left}
	        	<td><div align="center">{lang_showing}</div></td>
	                {right}
        	</tr>
	</table>
	</td></tr>
	<tr class="th">
		<th class="th_mp_name">{header_wf_name}</th>
		<th class="th_mp_activities">{lang_Activities}</th>
		<th class="th_mp_is_active">{header_wf_is_active}</th>
		<th class="th_mp_is_valid">{header_wf_is_valid}</th>
		<th class="th_mp_instances">{lang_Instances}</th>
	</tr>
	<!-- BEGIN block_listing -->
	<tr class="{class_alternate_row}">
		<td class="td_mp_name row_{process_css_name}">
			<span class="{process_css_name}"><a href="{process_href}">{process_name} {process_version}</a></span>
		</td>
		<td class="td_mp_activities">
			<a href="{process_href_activities}">{process_activities}</a>
		</td>
		<td class="td_mp_is_active">
			{process_active_img}
		</td>
		<td class="td_mp_is_valid">
		  <img src='{process_valid_img}' alt=' ({process_valid_alt}) ' title='{process_valid_alt}' />
		</td>
		<td class="td_mp_instances">
			<table class="table_mp_instances_stats">
			<tr>
			 <td class="td_mp_in_instances"><a class="wf_active" href="{process_href_inst_active}">{process_inst_active}</a></td>
			 <td class="td_mp_in_instances"><a class="wf_completed" href="{process_href_inst_comp}">{process_inst_comp}</a></td>
			 <td class="td_mp_in_instances"><a class="wf_aborted" href="{process_href_inst_abort}">{process_inst_abort}</a></td>
			 <td class="td_mp_in_instances"><a class="wf_exception" href="{process_href_inst_excep}">{process_inst_excep}</a></td>
			</tr>
			</table>
		</td>
	</tr>
	<!-- END block_listing -->
</table>
</form>
{monitor_stats}
