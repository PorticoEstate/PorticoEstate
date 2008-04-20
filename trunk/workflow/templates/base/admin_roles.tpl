{processes_css}
<div style="color:red; text-align:center">{message}</div>
<div>
	<div>
		{proc_bar}
	</div>
	<div>
		{errors}
	</div>
</div>

<form action="{form_action_adminroles}" method="post">
<input type="hidden" name="p_id" value="{p_id}" />
<input type="hidden" name="role_id" value="{role_info_role_id}" />
<input type="hidden" name="sort_mode" value="{sort_mode}" />
<input type="hidden" name="sort_mode2" value="{sort_mode2}" />
<input type="hidden" name="find" value="{find}" />
<input type="hidden" name="start" value="{start}" />
<table style="border: 1px solid black;width:100%; margin-bottom:10px">
	<tr class="th">
		<td colspan="2" style="font-size: 120%; font-weight:bold">
			{lang_Add_or_edit_a_role}
		</td>
	</tr>
	<tr class="row_on">
	  <td>{lang_Name}</td>
	  <td><input type="text" name="name" value="{role_info_name}" /></td>
	</tr>
	<tr class="row_off">
	  <td>{lang_Description}</td>
	  <td><textarea name="description" rows="4" cols="60">{role_info_description}</textarea></td>
	</tr>
	<tr class="th">
          <td colspan="2">
	  <table cellpadding="0" cellspacing="0" width="100%">
		<tr>
		  <td style="text-align: left; font-weight:bold;"><input type="submit" name="save" value="{lang_save}" /> </td>
		  <td style="text-align: right; font-weight:bold;"> <input type="submit" name="new_role" value="{lang_New}" /></td>
		</tr>
	  </table>
          </td>
	</tr>
</table>
</form>

<form action="{form_action_adminroles}" method="post">
<input type="hidden" name="sort_mode" value="{sort_mode}" />
<input type="hidden" name="p_id" value="{p_id}" />
<input type="hidden" name="role_id" value="{info_role_id}" />
<input type="hidden" name="sort_mode" value="{sort_mode}" />
<input type="hidden" name="sort_mode2" value="{sort_mode2}" />
<input type="hidden" name="find" value="{find}" />
<input type="hidden" name="start" value="{start}" />
<table style="border: 1px solid black;width:100%; margin-bottom:10px">
	<tr class="th">
		<td colspan="3" style="font-size: 120%; font-weight: bold;">
			{lang_Process_roles}
		</td>
	</tr>
	<tr class="th">
		<td>{lang_Name}</td>
		<td>{lang_Description}</td>
		<td width="1%">&nbsp;</td>
	</tr>
	<!-- BEGIN block_process_roles_list -->
	<tr bgcolor="{color_line}">
		<td>
		  <a href="{all_roles_href}">{all_roles_name}</a>
		</td>
		<td>
		  {all_roles_description}
		</td>
		<td>
			<input type="checkbox" name="role[{all_roles_role_id}]" />
		</td>
	</tr>
	<!-- END block_process_roles_list -->
	<tr class="th">
		<td colspan="3" align="right">
			<input type="submit" name="delete_roles" value="{lang_delete_selected}" />
		</td>
	</tr>
</table>
</form>	

<!-- BEGIN block_map_roles -->
	<form method="post" action="{form_action_adminroles}">
	<input type="hidden" name="p_id" value="{p_id}" />
	<input type="hidden" name="start" value="{start}" />
	<input type="hidden" name="sort_mode" value="{sort_mode}" />
	<input type="hidden" name="sort_mode2" value="{sort_mode2}" />
	<input type="hidden" name="search_str" value="{search_str}" />
	<table style="border: 1px solid black;width:100%; margin-bottom:10px">
	<tr class="th">
		<td colspan="2" style="font-size: 120%; font-weight:bold">
			{lang_Map_users/groups_to_roles}
		</td>
	</tr>
	<tr class="th">
		<td width="50%" align="center">
			{lang_Users/Groups}
		</td>
		<td width="50%" align="center">
			{lang_Roles}		  		
		</td>
	</tr>
	<tr>
		<td align="center">
			<select name="user[]" multiple="multiple" size="10">
				<!-- BEGIN block_select_users -->
				<option value="{account_id}">{account_name}</option>
				<!-- END block_select_users -->
			</select>
		</td>
		<td align="center">
			<select name="role[]" multiple="multiple" size="10">
				<!-- BEGIN block_select_roles -->
				<option value="{select_role_id}">{select_role_name}</option>
				<!-- END block_select_roles -->
			</select>	  		
		</td>
	</tr>
	<tr class="th">
		<td colspan="2" style="text-align:center;" class="formcolor">
			<input type="submit" name="save_map" value="{lang_map}" />
		</td>
	</tr>
	</table>
	</form>
<!-- END block_map_roles -->

<form action="{form_action_adminroles}" method="post">
<input type="hidden" name="p_id" value="{p_id}" />
<input type="hidden" name="start" value="{start}" />
<input type="hidden" name="find" value="{find}" />
<input type="hidden" name="sort_mode" value="{sort_mode}" />
<input type="hidden" name="sort_mode2" value="{sort_mode2}" />
<table style="border: 1px solid black;width:100%;">
	<tr class="th">
		<td colspan="3" style="font-size: 120%; font-weight:bold">
			{lang_List_of_mappings}
		</td>
	</tr>
	<tr class="th">
		<td width="50%">{lang_Role}</td>
		<td width="49%">{lang_User/Group}</td>
		<td width="1%">&nbsp;</td>
	</tr>
	<!-- BEGIN block_list_mappings -->
	<tr bgcolor="{color_line}">
		<td>
		  {map_role_name}
		</td>
		<td>
		  {map_user_name}
		</td>
		<td>
			<input type="checkbox" name="map[{map_user_id}:::{map_role_id}]" />
		</td>
	</tr>
	<!-- END block_list_mappings -->
	<tr class="th">
		<td colspan="3" align="right">
			<input type="submit" name="delete_map" value="{lang_delete_selected}" />
		</td>
	</tr>
</table>
</form>
