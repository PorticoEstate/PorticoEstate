<!-- BEGIN body -->

<form method="post" action="{form_action}">
<table border="0" bgcolor="#EEEEEE" align="center" width="80%" cellpadding="3">
	<tr bgcolor="{th_bg}">
		<td colspan="2">&nbsp;<b>{lang_header}</b></td>
	</tr>
	<tr>
		<td>{lang_url}&nbsp;{mail_this_link}</td>
		<td>{input_url}&nbsp;</td>
	</tr>
	<tr>
		<td>{lang_name}</td>
		<td>{input_name}&nbsp;</td>
	</tr>
	<tr>
		<td>{lang_desc}</td>
		<td>{input_desc}&nbsp;</td>
	</tr>
	<tr>
		<td>{lang_keywords}</td>
		<td>{input_keywords}&nbsp;</td>
	</tr>
	<tr>
		<td width="25%" valign="top">{lang_category}&nbsp; &nbsp;{category_image}</td>
		<td width="25%">{input_category}</td>
	</tr>
	<tr>
		<td width="25%">{lang_rating}</td>
		<td width="25%">{input_rating}</td>
	</tr>
	<tr>
		<td>{lang_access}</td>
		<td>{input_access}</td>
	</tr>

{info}
 
	<tr>
		<td colspan="2" align="right">
			{delete_button}
			{edit_button}
			&nbsp;&nbsp;&nbsp;
			{cancel_button}
			{save_button}
		</td>
	</tr>
</table>
</form>

<!-- END body -->

<!-- BEGIN form_info -->

	<tr>
		<td>{lang_owner}</td>
		<td>{owner_value}&nbsp;</td>
	</tr>
	<tr>
		<td>{lang_added}</td>
		<td>{added_value}&nbsp;</td>
	</tr>
	<tr>
		<td>{lang_visited}</td>
		<td>{visited_value}&nbsp;</td>
	</tr>
	<tr>
		<td>{lang_visits}</td>
		<td>{total_visits}&nbsp;</td>
	</tr>
	<tr>
		<td>{lang_updated}</td>
		<td>{updated_value}&nbsp;</td>
	</tr>

<!-- END form_info -->
