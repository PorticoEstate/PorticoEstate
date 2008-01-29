<!-- BEGIN many_actions -->
<table border="0" align="center" width="80%">
  <tbody>
    <th bgcolor="{th_bg}">{lang_general_title}</th>
    {combos_lists}
    {all_option_list_filter_body}

    <tr>
      <td>
		<table border="0" align="center" width="100%">
		<tbody align="center">
			<tr bgcolor="{row_on}">
				<td width="45%">{lang_all_option_list_title}</td>
				<td width="10%"></td>
				<td width="45%">{lang_selected_option_list_title}</td>
			</tr>

			<tr bgcolor="{row_off}">
			<td width="45%">
				<select multiple size="5" name="{all_option_list_name}" style="width:220">{all_option_list}</select>
			</td>
			<td width="10%">
				<table border="0" align="center">
				<tbody align="center">
					<tr>
					<td>
						<input type="button" onClick="selectMover('{widget_list_form_name}'); 
									moveSelectedOptions('{all_option_list_name}','{selected_option_list_name}'); 
									move_cbo('{current_opt}','{selected_option_list_name}');" value=">>">
					</td>
					</tr>
					<tr>
					<td>
						<input type="button" onClick="selectMover('{widget_list_form_name}'); 
									moveSelectedOptions('{selected_option_list_name}','{all_option_list_name}'); 
									move_cbo('{current_opt}','{selected_option_list_name}');" value="<<">
					</td>
					</tr>
				</tbody>
				</table>
			</td>
			<td width="45%">
				<select multiple size="5" name="{selected_option_list_name}" style="width:220">{selected_option_list}</select>
			</td>
			</tr>
		</tbody>
		</table>
	  </td>
    </tr>
  </tbody>
</table>
<!-- END many_actions -->

<!-- BEGIN combos -->
    <tr>
      <td>
		<table border="0" align="center" width="100%">
		<tbody>
			<tr bgcolor="{row_on}">
			<td width="45%">{lang_left_combo_title} {left_combo}</td>
			<td width="10%"></td>
			<td width="45%">{lang_right_combo_title} {right_combo}</td>
			</tr>
		</tbody>
		</table>
	  </td>
    </tr>
<!-- END combos -->

<!-- BEGIN option_filter -->
    <tr>
      <td>
		<table border="0" align="center" width="100%">
		<tbody>
			<tr bgcolor="{row_on}">
				<td width="45%">
					{filter_by_label} <select name="list_filter_by" 
onChange="process_list('{all_option_list_name}','{selected_option_list_name}'); this.form.submit();" style="width:200" >{filter_by_option_list}</select>
					<br />
				       	{search_by_label} &nbsp;<input type="text" name="searchautocomplete" value="{search_ac_value}" size="30"  onkeyup="
													javascript:
													//We only care if
													//a key was hit
													//so we can update
													///the nameselectbox
													//to autolimit itself
													//to what it should
														obj1.bldUpdate();
														
														">

				</td>
				<td width="10%"></td>
				<td width="45%"></td>
			</tr>
		</tbody>
		</table>
	  </td>
    </tr>
<!-- END option_filter -->