<form action="{form_action}" method="post">
<input type="hidden" name="iid" value="{iid}" />
<table style="border: 1px solid black;width:100%;margin-bottom:10px">
	<tr class="th">
		<td colspan="2" style="font-size: 120%; font-weight:bold">
			{instance_process}
		</td>
	</tr>
	<tr class="row_on">
		<td>{lang_Created}</td>
		<td>{inst_started}</td>
	</tr>
	<tr class="row_off">
		<td>{lang_Ended}</td>
		<td>{inst_ended}</td>
	</tr>
	<tr class="row_on">
               <td>{lang_Name}</td>
               <td>
			<input {input_type} name="instance_name" value="{instance_name}">
			<input type="hidden" name="instance_previous_name" value="{instance_name}">
	       </td>
       </tr>
	<tr class="row_off">
               <td>{lang_Priority}</td>
               <td>
			<input {input_type} name="instance_priority" value="{instance_priority}">
			<input type="hidden" name="instance_previous_priority" value="{instance_priority}">
	       </td>
       </tr>
       <tr class="row_on">
		<td>{lang_Status}</td>
		<td>
		<select {select_type} name="status">
			<option value="active" {status_active}>{lang_active}</option>
			<option value="exception" {status_exception}>{lang_exception}</option>
			<option value="completed" {status_completed}>{lang_completed}</option>
			<option value="aborted" {status_aborted}>{lang_aborted}</option>
		</select>
		<input type="hidden" name="instance_previous_status" value="{status}">
		</td>
	</tr>
	<tr class="row_off">
		<td>{lang_Owner}</td>
		<td>
			{select_owner}
			<input type="hidden" name="instance_previous_owner" value="{owner}">
		</td>
	</tr>
	<tr class="row_on">
               <td>{lang_Category}</td>
               <td>
			{instance_category_select}
			<input type="hidden" name="instance_previous_category" value="{instance_category}">
	       </td>
       </tr>
	<tr class="row_off">
		<td>{lang_Activities}</td>
		<td>
		<!-- BEGIN block_instance_acts -->
			<table>
			<tr class="row_on">
				<td style="text-align:center">{lang_Activity}</td>
				<td style="text-align:center">{lang_Act_status}</td>
				<td style="text-align:center">{lang_User}</td>
			</tr>
			<!-- BEGIN block_instance_acts_table -->
			<tr class="row_off">
				<td>
					{inst_act_name}
				</td>
				<td>{inst_act_status}</td>
				<td>
					<input type="hidden" name="previous_acts[{inst_act_id}]" value="{activity_user}">
					{select_user}{send}{restart}
				</td>
			</tr>
			<!-- END block_instance_acts_table -->
			</table>
		<!-- END block_instance_acts -->
		</td>
	</tr>
	<!-- BEGIN block_sendallactivities -->
	<tr class="row_on">
		<td>{lang_Send_all_activities_to}</td>
		<td>
			<select name="sendto">
			  <option value="">{lang_Don't_move}</option>
			  <!-- BEGIN block_select_sendto -->
			  <option value="{sendto_act_value}">{sendto_act_name}</option>
			  <!-- END block_select_sendto -->
			</select>
		</td>
	</tr>
	<!-- END block_sendallactivities -->

	<tr class="th">
		<td><input type="submit" name="refresh" value="{lang_Refresh}" /></td>
		<td>&nbsp;
		<!-- BEGIN block_button_update -->
		<input type="submit" name="save" value="{lang_Update}" />
		<!-- END block_button_update -->
		</td>
	</tr>
</table>
</form>

<form action="{form_action}" method="post">
<input type="hidden" name="iid" value="{iid}" />
<table style="border: 1px solid black;width:100%;margin-bottom:10px">
	<tr class="th">
		<td colspan="2" style="font-size: 120%; font-weight:bold">
			{lang_Instance_properties}
		</td>
	</tr>
	<tr class="th">
		<td>{lang_Property}</td>
		<td>{lang_Value}</td>
	</tr>
	<!-- BEGIN block_properties -->
	<tr class="{color_line}">
		<td>
		 <!-- BEGIN block_button_delete -->
		 <a href="{prop_href}"><img border="0" src="{img_trash}" alt="{lang_delete}" title="{lang_delete}" /></a>
		 <!-- END block_button_delete -->
		 <b>{prop_key}</b>
		 </td>
		<td>
			{prop_value}
		</td>
	</tr>
	<!-- END block_properties -->
	<tr class="th">
		<td>&nbsp;</td>
		<td>&nbsp;<!-- BEGIN block_button_update_properties -->
			<input type="submit" name="saveprops" value="{lang_update}" />
			<!-- END block_button_update_properties -->
		</td>
	</tr>
</table>
</form>
<!-- BEGIN block_add_property -->
<form action="{form_action}" method="post">
<input type="hidden" name="iid" value="{iid}" />
<table style="border: 1px solid black;width:100%;margin-bottom:10px">
	<tr class="th">
		<td colspan="2" style="font-size: 120%; font-weight:bold">
			{lang_Add_property}
		</td>
	</tr>
	<tr class="row_on">
		<td>{lang_Name}</td>
		<td><input type="text" name="name" /></td>
	</tr>
	<tr class="row_off">
		<td>{lang_Value}</td>
		<td><textarea name="value" rows="4" cols="80"></textarea></td>
	</tr>
	<tr class="th">
		<td>&nbsp;</td>
		<td><input type="submit" name="addprop" value="{lang_add}" /></td>
	</tr>
</table>
</form>
<!-- END block_add_property -->
