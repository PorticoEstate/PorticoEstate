<!-- begin class_prefs_ex_accounts.tpl -->
{pref_errors}
<p>
  <b>{page_title}</b>
  <hr />
</p>
<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
<tr bgcolor="{tr_titles_color}" class="{tr_titles_class}">
	<td width="60%" align="left">
		<font face="{font}">{account_name_header}</font>
	</td>
	<td width="10%" align="center">
		<font face="{font}">{lang_status}</font>
	</td>
	<td width="10%" align="center">
		<font face="{font}">{lang_go_there}</font>
	</td>
	<td width="10%" align="center">
		<font face="{font}">{lang_edit}</font>
	</td>
	<td width="10%" align="center">
		<font face="{font}">{lang_delete}</font>
	</td>
</tr>
<!-- BEGIN B_accts_list -->
<tr bgcolor="{tr_color}" class="{tr_color_class}">
	<td width="60%" align="left">
		<font face="{font}">{indentity}</font>
	</td>
	<td width="10%" align="center">
		<font face="{font}">{status}</font>
	</td>
	<td width="10%" align="center">
		<font face="{font}">{go_there_href}</font>
	</td>
	<td width="10%" align="center">
		<font face="{font}">{edit_href}</font>
	</td>
	<td width="10%" align="center">
		<font face="{font}">{delete_href}</font>
	</td>
</tr>
<!-- END B_accts_list -->
<tr>
	<td colspan="4" align="center">
		&nbsp;
	</td>
</tr>
<tr>
	<td colspan="4" align="center">
		{add_new_acct_href}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{done_href}
	</td>
</tr>
</table>
<p>
	&nbsp;
</p>
{debugdata}
<!-- end class_prefs_ex_accounts.tpl -->
