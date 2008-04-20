{processes_css}
<div style="color:red; text-align:center">{message}</div>
<form action="{form_action}" method="post" id='fform'>
<input type="hidden" name="start" value="0" />
<input type="hidden" name="sort" value="{sort}" />
<input type="hidden" name="order" value="{order}" />
<input type="hidden" name="show_globals" value="{show_globals}" />
{user_tabs}
<table style="border: 0;width:100%;" cellspacing="0">
	<tr class="th">
		<td colspan="3" style="font-size: 120%; font-weight:bold; border-bottom:3px solid white;">
			{lang_List_of_activities}
		</td>
	</tr>
</table>
<table style="border: 0;width:100%;" cellspacing="1">
	<tr class="row_off">
                <td align="center">
                        {lang_Process:}
			<select onchange='this.form.submit();' name="filter_process">
				<option {filter_process_all_selected} value="">{lang_All}</option>
				<!-- BEGIN block_select_process -->
				<option {filter_process_selected} value="{filter_process_value}">{filter_process_name} {filter_process_version}</option>
				<!-- END block_select_process -->
			</select>
		</td>
		<td align="center">
			{lang_Activity:}
                        <select  onchange='this.form.submit();' name="filter_activity">
                        <option {filter_activity_selected_all} value="">{lang_All}</option>
                        <!-- BEGIN block_filter_activity -->
                        <option {filter_activity_selected} value="{filter_activity_name}">{filter_activity_name}</option>
                        <!-- END block_filter_activity -->
                        </select>
                </td>
                <td align="center">
                        <input size="18" type="text" name="find" value="{search_str}" />
                        <input type="submit" name="search" value="{lang_search}" />
                </td>
	</tr>
</table>
</form>
<form action="{form_action}" method="post">
<input type="hidden" name="start" value="{start}" />
<input type="hidden" name="find" value="{search_str}" />
<input type="hidden" name="sort" value="{sort}" />
<input type="hidden" name="order" value="{order}" />
<input type="hidden" name="show_globals" value="{show_globals}" />
<input type="hidden" name="filter_process" value="{filter_process}" />
<input type="hidden" name="filter_activity" value="{filter_activity}" />
<table style="border: 0;width:100%;" cellspacing="1">
	<tr>
		<td colspan="2">
		        <table style="border: 0px;width:100%; margin:0 auto">
		                <tr class="row_off">
		                        {left}
		                        <td><div align="center">{lang_showing}</div></td>
		                        {right}
		                </tr>
		        </table>
	        </td>
	</tr>
	<tr class="th" style="font-weight:bold">
		<td>
			{header_wf_procname}:
		</td>
		<td>
			{header_wf_name}
		</td>
	</tr>
	<!-- BEGIN block_activities_list -->
	<tr class="{color_line}">
		<td class="row_{process_css_name}">
			<span class="{process_css_name}">{act_wf_procname} {act_proc_version}</span>
		</td>
		<td style="text-align:left;">
			{act_icon} {act_name} {run_act}
		</td>
	</tr>
	<!-- END block_activities_list -->
</table>
</form>
