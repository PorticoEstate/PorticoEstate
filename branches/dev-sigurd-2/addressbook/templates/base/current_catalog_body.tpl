<!-- BEGIN tab_body_general_data -->
<td>
	<table width="80%" border="0" align="center" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th>{lang_general_data}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<input type="hidden" name="{current_id_name}" value="{current_id}">
					<input type="hidden" name="{current_action_name}" value="{current_action}">
				</td>
			{input_fields}
			{detail_fields}
			</tr>
		</tbody>
	</table>
</td>
<!-- END tab_body_general_data -->

<!-- BEGIN input_data -->
			<tr class="{tr_class}">
				{input_fields_cols}
			</tr>
<!-- END input_data -->
