<!-- $Id: view_dist.tpl 9980 2002-04-17 20:33:52Z ceb $ -->

{app_header}

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b></br>
<hr noshade width="98%" align="center" size="1">
<center>
<table width="90%" border="0" cellpadding="2" cellspacing="2" align="center">
	<tr>
		<td colspan="4" width="20%"><b>{lang_company}:</b></td>
		<td>{company}</td>
		<td><b>{lang_url}:</b></td>
		<td><a href="{url}" target="_blank">{url}</a></td>
	</tr>
	<tr>
		<td colspan="4"><b>{lang_department}:</b></td>
		<td>{department}</td>
		<td><b>{lang_url_mirror}:</b></td>
		<td><a href="{url_mirror}" target="_blank">{url_mirror}</a></td>
	</tr>
	<tr>
		<td colspan="4"><b>{lang_industry_type}:</b></td>
		<td>{industry_type}</td>
		<td><b>{lang_ftp}:</b></td>
		<td><a href="{ftp}" target="_blank">{ftp}</a></td>
	</tr>
	<tr>
		<td colspan="4"><b>{lang_software}:</b></td>
		<td>{software}</td>
		<td><b>{lang_ftp_mirror}:</b></td>
		<td><a href="{ftp_mirror}" target="_blank">{ftp_mirror}</a></td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4"><b>{lang_contact}:</b></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>                                                                                                        
		<td>&nbsp;</td>                                                                                                       
	</tr>
	<tr>
		<td colspan="4"><b>{lang_lastname}:</b></td>
		<td>{lastname}</td>
		<td><b>{lang_phone}:</b></td>
		<td>{wphone}</td>
	</tr>
	<tr>
		<td colspan="4"><b>{lang_firstname}:</b></td>
		<td>{firstname}</td>
		<td><b>{lang_fax}:</b></div></td>
		<td>{fax}</td>
	</tr>
	<tr>
		<td colspan="4"><b>{lang_email}:</b></td>
		<td>{email}</td>
		<td><b>{lang_cell}:</b></td>
		<td>{cell}</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4"><b>{lang_access}:</b></td>
		<td>{access}</td>
		<td><b>{lang_cat}:</b></td>
		<td>{cat}</td>
	</tr>
	<tr>
		<td colspan="2"><b>{lang_notes}:</b></td>
		<td>{notes}</td>
	</tr>

<!-- BEGIN done -->

	<tr valign="bottom">
		<td height="50">
			<form method="POST" action="{done_action}">
			<input type="submit" name="submit" value="{lang_done}">
			</form>
		</td>
	</tr>
</table>
</center>

<!-- END done -->
