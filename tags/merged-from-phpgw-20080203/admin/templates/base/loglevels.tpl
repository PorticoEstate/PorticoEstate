<!-- BEGIN loglevels -->
<h1>{lang_set_levels}</h1>
<table>
	<tbody>
		<tr class="{tr_class}">
			<td>{lang_global_level}</td>
			<td>{global_option}</td>
		</tr>
 		<tr>
			<td>{lang_module_level}</td>
			<td>
				<table>
					{module_list}
					{module_add_row}
				</table>
			</td>
		</tr>
		<tr>
			<td>{lang_user_level}</td>
			<td>
				<table>
					{user_list}
					{user_add_row}
				</table>
			</td>
		</tr>
	</tbody>
</table>
<!-- END loglevels -->
 
<!-- BEGIN module -->
			<tr class="{tr_class}">
				<td>{module_name}</td>
				<td>{module_option}</td>
				<td><a href="{remove_url}">{lang_remove}</a></td>
			</tr>
<!-- END module -->
 
<!-- BEGIN module_add -->
	<tr class="{tr_class}">
		<form action="{module_add_link}" method="post" name="{type}_add_form">
		<td>
			<select name="{type}_add_name_select" size="1">
				{module_add_options}
			</select>		
		</td>
		<td>
			<select name="{type}_add_level_select" size="1">
				<option value="F">{lang_fatal}</option>
				<option value="E">{lang_error}</option>
				<option value="N">{lang_notice}</option>
				<option value="W">{lang_warn}</option>
				<option value="I">{lang_info}</option>
				<option value="D" selected>{lang_debug}</option>
			</select>
		</td>
		<td align="center"><input value="{lang_add}" type="submit"></td>
		</form>
	</tr>
<!-- END module_add -->
