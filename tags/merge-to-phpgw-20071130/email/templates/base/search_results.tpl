
<table border="0" cellspacing="0" cellpadding="8" align="center">
<tr bgcolor="DDDDDD">
  <td>
	<b>{num_msg} {lang_messages_found_in_folder} "{folder}"</b>
  </td>
</tr>
</table>

<form name="{form_name}" action="{delmov_action}" method="POST">
<input type="hidden" name="what" value="delete">
<input type="hidden" name="folder" value="{folder_short}">
<table border="0" cellpadding="4" cellspacing="1" width="95%" align="center">
<tr bgcolor="#D3DCE3">
	<td width="3%" align="center">
		&nbsp;
	</td>
	<td width="2%">
		&nbsp;

	</td>
	<td width="20%">
		<font size="2" face="Arial, Helvetica, san-serif">
		<strong>{lang_from}</strong>
		</font>
	</td>
	<td width="39%">
		<font size="2" face="Arial, Helvetica, san-serif">

 		<strong>{lang_subject}</strong>
		</font>
	</td>
	<td width="10%" align="center">
		<font size="1" face="Arial, Helvetica, san-serif">
		<strong>{lang_date}</strong>
		</font>
	</td>

	<td width="4%" align="center">
		<font size="1" face="Arial, Helvetica, san-serif">
		<strong>{lang_size}</strong>
		</font>
	</td>
</tr>


<!-- BEGIN search_result -->
<tr bgcolor="#DDDDDD">
	<td align="center">
		<input type="checkbox" name="delmov_list[]" value="{checkbox_val}">
	</td>
	<td align="center">

		&nbsp;
	</td>
	<td align="left">
		<font size="2" face="Arial, Helvetica, san-serif">{from}</font>
	</td>
	<td align="left">
		<font size="2" face="Arial, Helvetica, san-serif"><a href="{msg_link}">{subject}</a></font>

	</td>
	<td align="center">
		<font size="2" face="Arial, Helvetica, san-serif">{date}</font>
	</td>
	<td align="center">
		<font size="1" face="Arial, Helvetica, san-serif">{size}</font>
	</td>
</tr>
<!-- END search_result -->

<tr bgcolor="#D3DCE3">
	<td colspan="3" align="left">
		<a href="javascript:check_all('{form_name}')"><img src="/phpgroupware/email/templates/default/images/check.png" border="0" height="16" width="21"></a>&nbsp;&nbsp;
	<a href="javascript:do_action('{form_name}', 'delall')"><img src="/phpgroupware/email/templates/default/images/evo-trash-24.png" border="0" alt="[image]">&nbsp;Delete</a>
	</td>
	<td colspan="3" align="right">
		<select name="to_fldball_fake_uri" onChange="do_action('{form_name}', 'move')">
			<option value="">{lang_move_selected_messages_into}</option>
			<!-- BEGIN folder_list -->
			<option value="{fld_link}">{fld_value}</option>
			<!-- END folder_list -->
		</select>
	</td>
</tr>
</table>
</form>

