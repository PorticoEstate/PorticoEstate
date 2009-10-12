<!-- $Id: liststockprd.tpl 5534 2001-06-06 03:08:49Z bettina $ -->

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
<table border="0" width="100%">
	<tr>
		<td width="30%" align="left">
			<form action="{filter_action}" method="POST">
			<select name="filter" onChange="this.form.submit();"><option value="">{lang_select_room}</option>{room_list}</select>
			<noscript>&nbsp;<input type="submit" name="submit" value="{lang_submit}"></noscript></form></td>
		<td width="30%" align="center">{search_message}</td>
		<td width="30%" align="right">
			<form method="POST" action="{search_action}">
			<input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
			</form></td>
	</tr>
	<tr>
		<td colspan="7">
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

{error}<br><br>

<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td widht="10%">{sort_id}</td>
		<td widht="15%">{sort_serial}</td>
		<td width="13%">{sort_name}</td>
		<td width="13%">{sort_category}</td>
		<td align="right" width="6%">{sort_stock}</td>
		<td align="right" width="9%">{sort_mstock}</td>
		<td width="9%">{sort_status}</td>
		<td width="19%">{sort_note}</td>
		<td align="center" width="6%">{lang_view}</td>
		<td align="center" width="6%">{lang_edit}</td>
	</tr>

<!-- BEGIN listproducts -->

	<tr bgcolor="{tr_color}">
		<td>{id}</td>
		<td>{serial}</td>
		<td>{name}</td>
		<td>{category}</td>
		<td align="right">{stock}</td>
		<td align="right">{mstock}</td>
		<td>{status}</td>
		<td>{note}</td>
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
