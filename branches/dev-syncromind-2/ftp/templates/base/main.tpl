<!-- BEGIN main -->
 <center>{misc_data}</center>
<table border="0" width="95%" align="center" cellspacing="0" cellpadding="0">
	<tr class="th">
		{nextmatchs_left}
		<td colspan="3"><b>FTP</b> - {ftp_location}</td>
		{nextmatchs_right}
	</tr>
	<tr class="th">
		<td>{lang_name}</td>
		<td width="5%" align="center">{lang_owner}</td>
		<td width="5%" align="center">{lang_group}</td>
		<td width="10%" align="center">{lang_permissions}</td>
		<td width="7%" align="center">{lang_size}</td>
		<td width="10%" align="center">{lang_delete}</td>
		<td width="10%" align="center">{lang_rename}</td>
	</tr>
	{rowlist_dir}
	{rowlist_file}
	<tr class="th">
		<td colspan="3">
			{ul_form_open}
			{ul_select}{ul_submit}
			{ul_form_close}
		</td>
		<td style="text-align: center;">{relogin_link}</td>
		<td colspan="3" style="text-align: right;">
			{crdir_form_open}
			{crdir_textfield}{crdir_submit}
			{crdir_form_close}
		</td>
	</tr>
</table>
<!-- END main -->

<!-- BEGIN row -->
	<tr class="{bgclass}">
		<td>{name}&nbsp;</td>
		<td width="5%" align="center">{owner}&nbsp;</td>
		<td width="5%" align="center">{group}&nbsp;</td>
		<td width="10%" align="center">{permissions}&nbsp;</td>
		<td width="7%" align="right">{size}&nbsp;</td>
		<td width="10%" align="center">{del_link}</td>
		<td width="10%" align="center">{rename_link}</td>
	</tr>
<!-- END row -->
