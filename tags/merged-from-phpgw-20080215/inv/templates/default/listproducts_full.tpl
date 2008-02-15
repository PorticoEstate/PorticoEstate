<!-- $Id: listproducts_full.tpl 9377 2002-01-31 22:33:07Z ceb $ -->

{app_header}

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
<table border="0" width="100%">
	<tr>
		<td width="33%" align="left">
			{selection_list}
			<noscript>&nbsp;<input type="submit" name="submit" value="{lang_submit}"></noscript></form></td>
		<td width="33%" align="center">{search_message}</td>
		<td width="33%" align="right">
			<form method="POST" action="{search_action}">
			<input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
			</form></td>
	</tr>
	<tr>
		<td colspan="11">
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

{pref_message}<br><br>

<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{bg_color}">
		<td>{sort_num}</td>
		<td>{sort_serial}</td>
		<td>{sort_name}</td>
		<td>{sort_selection}</td>
		<td align="center">{sort_status}</td>
		<td align="right">{currency}&nbsp;{sort_cost}</td>
		<td align="right">{currency}&nbsp;{sort_price}</td>
		<td align="right">{currency}&nbsp;{sort_retail}</td>
		<td align="right">{sort_stock}</td>
		<td align="center">{lang_view}</td>
		<td align="center">{lang_edit}</td>
	</tr>

<!-- BEGIN listproducts -->

	<tr bgcolor="{tr_color}">
		<td>{num}</td>
		<td>{serial}</td>
		<td>{name}</td>
		<td>{selection}</td>
		<td align="center">{status}</td>
		<td align="right">{cost}</td>
		<td align="right"><b>{price}</b></td>
		<td align="right">{retail}</td>
		<td align="right">{stock}</td>
		<td align="center"><a href="{view}">{lang_view}</a></td>
		<td align="center"><a href="{edit}">{lang_edit_entry}</a></td>
	</tr>

<!-- END listproducts -->  

<!-- BEGINN add   -->                                                                                                                                                                

	<tr valign="bottom">
		<td height="50">{action}</td>
	</tr>

<!-- END add -->

</table>
</center>
