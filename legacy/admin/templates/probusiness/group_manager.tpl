<!-- BEGIN form -->
{error_messages}
<table class="basic" align="center">
	<tr>
		<td class="top">{rows}</td>
		<td class="top">
			<form action="{form_action}" method="post">
			 {hidden}
				<table>
					<tr class="header">
						<td><b>{lang_group}:</b></td>
						<td><b>{group_name}</b></td>
					</tr>
					<tr class="bg_color1">
						<td>{lang_select_managers}</td>
						<td>{group_members}</td>
					</tr>
					 {form_buttons}
				</table>
			</form>
		</td>
	</tr>
</table>
<!-- END form -->

<!-- BEGIN link_row -->
    <tr><td>&nbsp;<a href="{row_link}">{row_text}</a></td></tr>
<!-- END link_row -->

