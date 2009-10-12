<!-- $Id: listorders.tpl 5430 2001-06-03 22:59:46Z bettina $ -->
<p><b>&nbsp;&nbsp;&nbsp;{title_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
<table border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="9" align="left">
			<table border="0" width="100%">
				<tr>
				{left}
					<td align="center">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan="9" align="right">
			<form method="post" action="{search_action}">
			<input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
			</form></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td width="8%" bgcolor="{th_bg}">{sort_num}</td>
		<td width="8%" bgcolor="{th_bg}" align="center">{sort_date}</td>
		<td width="8%" bgcolor="{th_bg}" align="center">{sort_status}</td>
		<td width="20%" bgcolor="{th_bg}">{sort_descr}</td>
		<td width="25%" bgcolor="{th_bg}">{sort_customer}</td>
		<td width="8%" bgcolor="{th_bg}" align="center">{lang_products}</td>
		<td width="8%" bgcolor="{th_bg}" align="center">{lang_delivery}</td>
		<td width="8%" bgcolor="{th_bg}" align="center">{lang_invoice}</td>
		<td width="8%" bgcolor="{th_bg}" align="center">{lang_edit}</td>
	</tr>

<!-- BEGIN order_list -->

	<tr bgcolor="{tr_color}">
		<td>{num}</td>
		<td align="center">{date}</td>
		<td align="center">{status}</td>
		<td>{descr}</td>
		<td>{customer}</td>
		<td align="center"><a href="{products}">{lang_products}</a></td>
		<td align="center"><a href="{delivery}">{lang_delivery}</a></td>
		<td align="center"><a href="{invoice}">{lang_invoice}</a></td>
		<td align="center"><a href="{edit}">{lang_edit_entry}</a></td>
	</tr>

<!-- END order_list -->

<!-- BEGINN add   -->

	<tr valign="bottom">
  		<td height="50">
			{hidden_vars}
			{action}
		</td>
	</tr>

<!-- END add -->

</table>
</center>
