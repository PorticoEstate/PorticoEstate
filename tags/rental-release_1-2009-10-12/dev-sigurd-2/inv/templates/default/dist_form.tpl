<!-- $Id: dist_form.tpl 9969 2002-04-15 23:03:39Z ceb $ -->

{app_header}

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b></br>
    <hr noshade width="98%" align="center" size="1">
<center>
<FORM method="POST" action="{actionurl}">
<table width="98%" border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td width="25%" colspan="2"><div align="right">{lang_company}:</div></td>
		<td width="25%" height="35"><input name="values[company]" value="{company}"></td>
		<td width="25%" height="35"><div align="right">{lang_url}:</div></td>
		<td width="25%" height="35"><input name="values[url]" value="{url}"></td>
	</tr>
	<tr>
		<td width="25%" colspan="2"><div align="right">{lang_department}:</div></td>
		<td width="25%" height="35"><input name="values[department]" value="{department}"></td>
		<td width="25%" height="35"><div align="right">{lang_url_mirror}:</div></td>
		<td width="25%" height="35"><input name="values[url_mirror]" value="{url_mirror}"></td>
	</tr>
	<tr>
		<td width="25%" colspan="2"><div align="right">{lang_industry_type}:</div></td>
		<td width="25%"><input name="values[industry_type]" value="{industry_type}"></td>
		<td width="25%"><div align="right">{lang_ftp}:</div></td>
		<td width="25%"><input name="values[ftp]" value="{ftp}"></td>
	</tr>
	<tr>
		<td width="25%" colspan="2"><div align="right">{lang_software}:</div></td>
		<td width="25%"><input name="values[software]" value="{software}"></td>
		<td width="25%"><div align="right">{lang_ftp_mirror}:</div></td>
		<td width="25%"><input name="values[ftp_mirror]" value="{ftp_mirror}"></td>
	</tr>
	<tr>
		<td colspan="4" height="4">&nbsp;</td>
	</tr>
	<tr>
		<td width="25%"colspan="2"><div align="right"><b>{lang_contact}:</b></div></td>
		<td width="25%">&nbsp;</td>
		<td width="25%" colspan="2"><div align="right">&nbsp;</div></td>
		<td width="25%">&nbsp;</td>
	</tr>
	<tr>
		<td width="25%" colspan="2"><div align="right">{lang_lastname}:</div></td>
		<td width="25%"><input name="values[lastname]" value="{lastname}"></td>
		<td width="25%"><div align="right">{lang_phone}:</div></td>
		<td width="25%"><input name="values[wphone]" value="{wphone}"></td>
	</tr>
	<tr>
		<td width="25%" colspan="2"><div align="right">{lang_firstname}:</div></td>
		<td width="25%"><input name="values[firstname]" value="{firstname}"></td>
		<td width="25%"><div align="right">{lang_fax}:</div></td>
		<td width="25%"><input name="values[fax]" value="{fax}"></td>
	</tr>
	<tr>
		<td width="25%" colspan="2"><div align="right">{lang_email}:</div></td>
		<td width="25%"><input name="values[email]" value="{email}"></td>
		<td width="25%"><div align="right">{lang_cell}:</div></td>
		<td width="25%"><input name="values[cell]" value="{cell}"></td>
	</tr>
	<tr>
		<td colspan="4" height="4">&nbsp;</td>
	</tr>
	<tr>
		<td width="25%" colspan="2"><div align="right">{lang_access}:</div></td>
		<td width="25%">{access}</td>
		<td width="25%"><div align="right">{lang_cats}:</div></td>
		<td width="25%"><select name="values[cat_id]"><option value="">{lang_select_cats}</option>{cats_list}</select></td>
	</tr>
	<tr>
		<td width="25%" colspan="2"><div align="right">{lang_notes}:</div></td>
		<td width="25%"><textarea name="values[notes]" rows="4" cols="50" wrap="VIRTUAL">{notes}</textarea></td>
		<td width="25%"><div align="right">&nbsp;</div></td>
		<td width="25%">&nbsp;</td>
	</tr>
</table>

<!-- BEGIN add -->

<table width="50%" border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td height="50" align="right">
			<input name="submit" type="submit" value="{lang_save}"></td>
		<td height="50"><input type="reset" name="reset" value="{lang_reset}"></td></form>
	</tr>
</table>
</center>

<!-- END add -->

<!-- BEGIN edit -->

<table width="50%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td height="50" align="right">
			<input type="hidden" name="values[dist_id]" value="{dist_id}">
			<input type="submit" name="submit" value="{lang_save}">
			</form>
		</td>
		<td height="50">
			<form method="POST" action="{deleteurl}">
			<input type="hidden" name="values[dist_id]" value="{dist_id}">
			<input type="submit" name="delete" value="{lang_delete}">
			</form></td>
	</tr>
</table>
</center>

<!-- END edit -->
