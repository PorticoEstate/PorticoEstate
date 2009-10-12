<!-- $Id: orderproducts.tpl 6368 2001-06-29 02:25:28Z bettina $ -->
<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
<table border="0" width="100%">
	<tr>
		<td width="30%" align="left">
			<form method="POST" name="form" action="{filter_action}">
			<select name="filter" onchange="this.form.submit();"><option value="">{lang_select_cats}</option>{category_list}</select>
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
{error}<br>{message}<br>
<form method="POST" action="{actionurl}">
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="3%" bgcolor="{th_bg}" align="center">{h_lang_choose}</td>
		<td width="3%" bgcolor="{th_bg}">{lang_piece}</td>
		<td width="8%" bgcolor="{th_bg}">{sort_id}</td>
		<td width="20%" bgcolor="{th_bg}">{sort_serial}</td>
		<td width="20%" bgcolor="{th_bg}">{sort_name}</td>
		<td width="20%" bgcolor="{th_bg}">{sort_dist}</td>
		<td align="center" width="8%" bgcolor="{th_bg}">{sort_status}</td>
		<td align="right" width="8%" bgcolor="{th_bg}">{currency}&nbsp;{sort_cost}</td>
		<td align="right" width="8%" bgcolor="{th_bg}">{currency}&nbsp;{sort_price}</td>
		<td align="right" width="8%" bgcolor="{th_bg}">{currency}&nbsp;{sort_retail}</td>
		<td align="right" width="8%" bgcolor="{th_bg}">{sort_stock}</td>
	</tr>

<!-- BEGIN product_list -->

	<tr bgcolor="{tr_color}">
		<td align="center">{choose}</td>
		<td align="right">{piece}</td>
		<td>{id}</td>
		<td>{serial}</td>
		<td>{name}</td>
		<td>{dist}</td>
		<td align="center">{status}</td>
		<td align="right">{cost}</td>
		<td align="right"><b>{price}</b></td>
		<td align="right">{retail}</td>
		<td align="right">{stock}</td>
	</tr>

<!-- END product_list -->

</table>
</center>
<table border="0" cellpadding="2" cellspacing="2">
	<tr>
		{hidden_vars}
		<td><input type="submit" name="View" value="{lang_vieworder}"></td>
		<td>{addtoorder}</td>
		<td>{updateorder}</td>
		</form>
	</tr>
</table>
