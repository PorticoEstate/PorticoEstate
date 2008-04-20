<table style="border: 0px;width:100%;">
	<tr class="row_on">
		<td colspan="4">
			{lang_Instances_selection}
		</td>
                <td >
			{lang_Activities_selection}
		</td>	
                <td >
			{lang_Actions}
		</td>	
	</tr>
	<tr class="row_off">
		<td >
			<input type="checkbox" name="add_exception_instances" {add_exception_instances} />
			{lang_Add_instances_in_exception}
		</td>
		<td>
			<input type="checkbox" name="add_completed_instances" {add_completed_instances} />
			{lang_Add_completed_instances}
		</td> 
		<td>
			<input type="checkbox" name="add_aborted_instances" {add_aborted_instances} />
			{lang_Add_aborted_instances}
		</td>
		<td>
			<input type="checkbox" name="remove_active_instances" {remove_active_instances} />
			{lang_Remove_active_instances}
		</td>
		<td >
			<select name="filter_act_status">
				<option {filter_act_status_all} value="">{lang_All}</option>
				<option value="running" {filter_act_status_running}>{lang_running}</option>
				<option value="completed" {filter_act_status_completed}>{lang_completed}</option>
				<option value="empty" {filter_act_status_empty}>{lang_empty}</option>
			</select>
		</td>
		<td >
			<input type="checkbox" name="show_advanced_actions" {show_advanced_actions} />
			{lang_Add_advanced_actions}
		</td>
        </tr>
</table>
