<!-- begin preferences.tpl -->
{pref_errors}

<form method="POST" action="{form_action}">
<table border="0" align="center" cellspacing="1" cellpadding="1" width="70%">
<tr>
	<td colspan="2" class="th">
		<b>{section_title}</b>
	</td>
</tr>
<tr>
	<td align="left" bgcolor="{bg_row6}">
		{use_custom_settings_blurb}
	</td>
	<td align="center" bgcolor="{bg_row6}">
		<input type="checkbox" name="{use_custom_settings_checkbox_name}" value="{use_custom_settings_checkbox_value}" {use_custom_settings_checked}>
	</td>
</tr>
<tr>
	<td align="left" bgcolor="{bg_row7}">
		{userid_blurb}
	</td>
	<td align="center" bgcolor="{bg_row7}">
		<input type="text" name="{userid_text_name}" value="{userid_text_value}">
	</td>
</tr>
<tr>
	<td align="left" bgcolor="{bg_row8}">
		{passwd_blurb}
	</td>
	<td align="center" bgcolor="{bg_row8}">
		<input type="password" name="{passwd_text_name}" value="{passwd_text_value}">
	</td>
</tr>
<tr>
	<td align="left" bgcolor="{bg_row9}">
		{address_blurb}
	</td>
	<td align="center" bgcolor="{bg_row9}">
		<input type="text" name="{address_text_name}" value="{address_text_value}">
	</td>
</tr>
<tr>
	<td align="left" bgcolor="{bg_row10}">
		{mail_server_blurb}
	</td>
	<td align="center" bgcolor="{bg_row10}">
		<input type="text" name="{mail_server_text_name}" value="{mail_server_text_value}">
	</td>
</tr>
<tr>
	<td align="left" bgcolor="{bg_row11}">
		{mail_server_type_blurb}
	</td>
	<td align="center" bgcolor="{bg_row11}">
		<select name="{mail_server_type_select_name}">
		{mail_server_type_select_options}
		</select>
	</td>
</tr>
<tr>
	<td align="left" bgcolor="{bg_row12}">
		{imap_server_type_blurb}
	</td>
	<td align="center" bgcolor="{bg_row12}">
		<select name="{imap_server_type_select_name}">
		{imap_server_type_select_options}
		</select>
	</td>
</tr>
<tr>
	<td align="left" bgcolor="{bg_row13}">
		{mail_folder_blurb}
	</td>
	<td align="center" bgcolor="{bg_row13}">
		<input type="text" name="{mail_folder_text_name}" value="{mail_folder_text_value}">
	</td>
</tr>
<tr>
	<td colspan="3" align="center">
		<input type="submit" name="{btn_submit_name}" value="{btn_submit_value}">
	</td>
</tr>
</table>
</form>
<!-- end preferences.tpl -->