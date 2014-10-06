<!-- begin folder.tpl -->

{widget_toolbar}

<form action="{form_action}" method="post">

<table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">
<tr>
	<td colspan="3" bgcolor="{title_backcolor}">
		&nbsp;<font size="3" face="{the_font}" color="{title_textcolor}"><strong>{title_text}<strong></font>

	</td>
</tr>
<tr bgcolor="{th_backcolor}" class="th">
	<td>
		<font size="2" face="{the_font}"><strong>{label_name_text}</strong></font>
		&nbsp;&nbsp;&nbsp;<a href="{view_lnk}">({view_txt})</a>
		
	</td>
	<td width="7%" align="right">
		<font size="2" face="{the_font}">{label_new_text}</font>
	</td>
	<td width="7%" align="right">
		<font size="2" face="{the_font}">{label_total_text}</font>
	</td>
</tr>

<!-- BEGIN B_folder_list -->
<tr bgcolor="{list_backcolor}" class="{list_backcolor_class}">
	<td>
		<font size="2" face="{the_font}">
		<a href="{folder_link}">{folder_name}</a>
		</font>
	</td>
	<td align="right">
		<font size="2" face="{the_font}">{msgs_unseen}</font>
	</td>
	<td align="right">
		<font size="2" face="{the_font}">{msgs_total}</font>
	</td>
</tr>
<!-- END B_folder_list -->

<tr class="th">
	<td colspan="3" align="right" bgcolor="{th_backcolor}">
		{all_folders_listbox}
		&nbsp;
		<select name="action">
			<option value="create">{form_create_txt}</option>
			<option value="delete">{form_delete_txt}</option>
			<option value="rename">{form_rename_txt}</option>
			<option value="create_expert">{form_create_expert_txt}</option>
			<option value="delete_expert">{form_delete_expert_txt}</option>
			<option value="rename_expert">{form_rename_expert_txt}</option>
		</select> 
		<input type="text" name="{target_fldball_boxname}">
		<input type="hidden" name="{hiddenvar_target_acctnum_name}" value="{hiddenvar_target_acctnum_value}">
		<input type="submit" value="{form_submit_txt}">
	</td>
</tr>
</table>
</form>
{debugdata}
<br />
<!-- end folder.tpl -->
