<!-- BEGIN row_no_history -->
	<tr class="row_on">
		<td align="center" colspan="5">{lang_no_history}</td>
	</tr>
<!-- END row_no_history -->

<!-- BEGIN row -->
	<tr class="tr_class">
		<td>&nbsp;{row_date}</td>
		<td>&nbsp;{row_owner}</td>
		<td>&nbsp;{row_status}</td>
		<td>&nbsp;{row_old_value}</td>
		<td>&nbsp;{row_new_value}</td>
	</tr>
<!-- END row -->

<!-- BEGIN list -->
<table border="0" width="95%">
	<thead>
		<tr>
			<th>{sort_date}</th>
			<th>{sort_owner}</th>
			<th>{sort_status}</th>
			<th>{sort_old_value}</th>
			<th>{sort_new_value}</th>
		</tr>
	</thead>
	<tbody>
		{rows}
	</tbody>
</table>
<!-- END list -->
