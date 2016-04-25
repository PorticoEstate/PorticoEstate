<!-- BEGIN tab_body_general_data -->
<td>
<table width="80%" border="0" align="center" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td>
	<table width="80%" border="0" align="center" cellspacing="0" cellpadding="0">
	<tbody>
	<th bgcolor="{th_bg}">{lang_general_data}</th>
	<tr>
	<td valign="top" width="40%">
		<table width="100%" border="0" align="center" cellspacing="2" cellpadding="2">
		<tbody>
			<input type="hidden" name="{current_id_name}" value="{current_id}">
			<input type="hidden" name="{current_action_name}" value="{current_action}">
			{input_fields}
			{other_fields}
			{detail_fields}
		</tbody>
		</table>
	</td>
	</tr>
	</tbody>
	</table>
</td>
</tr>
</tbody>
</table>
</td>
<!-- END tab_body_general_data -->

<!-- BEGIN input_data -->
{input_fields_cols}
<!-- END input_data -->

<!-- BEGIN input_other_data -->
{input_other_fields_cols}
<!-- END input_other_data -->

<!-- BEGIN input_data_col -->
<tr bgcolor="{row_bgc}">
	<td width="15%">{field_name_one}</td>
	<td width="35%"><input type="text" name="{input_name_one}" value="{input_value_one}"></td>
	<td width="15%">{field_name_two}</td>
	<td width="35%"><input type="text" name="{input_name_two}" value="{input_value_two}"></td>
</tr>
<!-- END input_data_col -->

<!-- BEGIN other_data -->
<tr bgcolor="{row_bgc}">
	<td width="15%">{field_other_name1}</td>
	<td width="35%">{value_other_name1}</td>
	<td width="15%">{field_other_name2}</td>
	<td width="35%">{value_other_name2}</td>
</tr>
<!-- END other_data -->
