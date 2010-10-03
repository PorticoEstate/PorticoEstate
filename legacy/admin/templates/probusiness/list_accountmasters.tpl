	<table class="basic" align="center">	
		<tr class="header">
			<td colspan="3"><b>{lang_users}</b></td>
		</tr>
		<tr class="bg_color1">
			<td width="33%">{sort_lid}</td>
			<td width="33%">{sort_firstname}</td>
			<td width="33%">{sort_lastname}</td>
		</tr>

<!-- BEGIN user_list -->

	<tr class="bg_color2">                                                                                                                                             
		<td>{lid}</td>
		<td>{firstname}</td>
		<td>{lastname}</td>
	</tr>

<!-- END user_list -->

	<tr height="5">
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr class="header">
		<td colspan="3"><b>{lang_groups}</b></td>
	</tr>
	<tr class="bg_color1">
		<td width="33%">{sort_name}</td>
		<td width="33%">&nbsp;</td>
		<td width="33%">&nbsp;</td>
	</tr>

<!-- BEGIN group_list -->

	<tr class="bg_color2">                                                                                                                                             
		<td>{lid}</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

<!-- END group_list -->

	<tr valign="bottom" height="50">
		<form method="POST" action="{action_url}">
			<td colspan="2">
				<input type="submit" name="edit" value="{lang_edit}" />
			</td>
			<td colspan="1" align="right">
				<input type="submit" name="done" value="{lang_done}" />
			</td>
		</form>
	</tr>
</table>

