<!-- $Id: listdist.tpl 9957 2002-04-14 22:27:31Z ceb $ -->

{app_header}

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
<table border="0" width="100%">
	<tr>
		<td width="33%" align="left">
			<form action="{cats_action}" name="form" method="POST">
			{lang_category}&nbsp;&nbsp;&nbsp;<select name="cat_id" onChange="this.form.submit();"><option value="">{lang_all}</option>{cats_list}</select>
			<noscript>&nbsp;<input type="submit" name="submit" value="{lang_submit}"></noscript></form></td>
		<td width="33%" align="center">{lang_showing}</td>
		<td width="33%" align="right">
			<form method="POST" action="{search_action}">
			<input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
			</form></td>
	</tr>
	<tr>
		<td colspan="8">
			<table border="0" width="100%">
				<tr>
				{left}
					<td>&nbsp;</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
</table>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{bg_color}">
		<td>{sort_company}</td>
		<td>{sort_department}</td>
		<td>{sort_industry_type}</td>
		<td align="center">{sort_url}</td>
		<td align="center">{sort_ftp}</td>
		<td align="center">{lang_products}</td>
		<td align="center">{lang_view}</td>
		<td align="center">{lang_edit}</td>
	</tr>

<!-- BEGIN distlist_list -->

	<tr bgcolor="{tr_color}">
		<td>{company}</td>
		<td>{department}</td>
		<td>{industry_type}</td>
		<td align="center"><a href="{url}" target="_blank">{lang_url}</a></td>
		<td align="center"><a href="{ftp}" target="_blank">{lang_ftp}</a></td>
		<td align="center"><a href="{products}">{lang_products}</a></td>
		<td align="center"><a href="{view}">{lang_view}</a></td>
		<td align="center"><a href="{edit}">{lang_edit}</a></td>
	</tr>

<!-- END distlist_list -->

<!-- BEGINN add   -->

	<tr valign="bottom">
		<td height="50">                                                                                                                                                                              
			<form method="POST" action="{add_action}">
			<input type="submit" value="{lang_add}">
			</form></td>
	</tr>

<!-- END add -->

</table>
</center>
