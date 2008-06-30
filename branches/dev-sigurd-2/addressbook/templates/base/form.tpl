<!-- BEGIN TABHOLDER -->
<table width="80%" border="0" align="center" cellspacing="2" cellpadding="2">
	<tbody>
		<tr>
			{principal_tabs_inc}
		</tr>
		<form action="{action}" method="post" name="body_form" cellspacing="0" cellpadding="0" {onsubjs}>
			<tr>
				{tab}
			</tr>
			<tr>
				<input type="hidden" name="{old_tab_name}" value="{old_tab}">
				<input type="hidden" name="referer" value="{referer}">
				<input type="hidden" name="ab_id" value="{ab_id}">
				<input type="hidden" name="owner" value="{owner}">
				<input type="hidden" name="record_name" value="{record_name}">
				{current_tab_body}
			</tr>
			{control_buttons}
		</form>
	</tbody>
</table>
<!-- END TABHOLDER -->
