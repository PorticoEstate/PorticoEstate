<div class="error_message">{messages}</div>

<!-- BEGIN form -->
{tabs}
{select_user}
<form method="POST" action="{action_url}">
{account_id}
	<!-- BEGIN list -->
	<table id="prefs_list">
		<thead>
			<tr>
				<th colspan="2"><b>{list_header}</b></th>
			</tr>
		</thead>
		<tbody>
			{rows}
		</tbody>
	</table>
	<!-- END list -->

	<div class="button_group">
		<input type="submit" name="submit" value="{lang_submit}">
		<input type="submit" name="cancel" value="{lang_cancel}">
		{help_button}
	</div>
</form>
<!-- END form -->

<!-- BEGIN row -->
		<tr class="{tr_class}">
			<td>{row_name}</td>
			<td>{row_value}</td>
		</tr>
<!-- END row -->

<!-- BEGIN help_row -->
		<tr class="{tr_class}">
			<td>{row_name}</td>
			<td>{row_value}</td>
		</tr>
		<tr class="{tr_class}">
			<td colspan="2">{help_value}</td>
		</tr>
<!-- END help_row -->
