<!-- $Id: inv_listproducts.tpl 5996 2001-06-17 06:06:13Z bettina $ -->
<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
{message} 
<form method="POST" action="{actionurl}">
<table border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td>{lang_choose}</td>
		<td>{choose}</td>
	</tr>
	<tr>
		<td>{title_invoice_num} :</td>
		<td><input type=text name="invoice_num" value="{invoice_num}"></td>
	</tr>
	<tr>
		<td>{title_descr} :</td>
		<td>{descr}</td>
	</tr>
	<tr>
		<td>{title_customer} :</td>
		<td>{customer}</td>
	</tr>
	<tr>
		<td>{lang_invoice_date} :</td>
		<td>{date_select}</td>
	</tr>
</table><br><br> 
{error}
{hidden_vars}
  
<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="5%" bgcolor="{th_bg}" align="right">{lang_pos}</td>
		<td width="5%" bgcolor="{th_bg}" align="right">{lang_piece}</td>
		<td width="10%" bgcolor="{th_bg}">{lang_id}</td>
		<td width="15%" bgcolor="{th_bg}">{lang_serial}</td>
		<td width="15%" bgcolor="{th_bg}">{lang_name}</td>
		<td width="10%" bgcolor="{th_bg}" align="right">{lang_procent}&nbsp;{lang_tax}</td>
		<td width="10%" bgcolor="{th_bg}" align="right">{currency}&nbsp;{lang_price}</td>
		<td width="10%" bgcolor="{th_bg}" align="right">{currency}&nbsp;{lang_sum_net}</td>
		<td width="10%" bgcolor="{th_bg}" align="right">{currency}&nbsp;{lang_sum}</td>
	</tr>

<!-- BEGIN product_list -->

	<tr bgcolor="{tr_color}">
		<td align="right">{pos}</td>
		<td align="right">{piece}</td>
		<td>{id}</td>
		<td>{serial}</td>
		<td>{name}</td>
		<td align="right">{tax}</td>
		<td align="right">{price}</td>
		<td align="right">{sum_piece}</td>
		<td align="right">{sum_retail}</td>
	</tr>

<!-- END product_list -->

</table><br><br>
<table width=100% border="0" cellspacing="0" cellpadding="0">
	<tr bgcolor="{tr_color}">
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="15%">&nbsp;</td>
		<td width="15%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="10%" align="right"><font size="4"><b>{currency}&nbsp;{lang_sum_net}:</b></font></td>
		<td width="10%" align="right"><font size="4"><b>{sum_price}</b></font></td>
	</tr>
	<tr bgcolor="{tr_color}">
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="15%">&nbsp;</td>
		<td width="15%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="10%" align="right"><font size="4"><b>{currency}&nbsp;{lang_tax}:</b></font></td>
		<td width="10%" align="right"><font size="4"><b>{sum_tax}</b></font></td>
	</tr>
	<tr bgcolor="{tr_color}">
		<td width="5%">&nbsp;</td>
		<td width="5%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="15%">&nbsp;</td>
		<td width="15%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="10%" align="right"><font size="4"><b>{currency}&nbsp;{lang_sum}:</b></font></td>
		<td width="10%" align="right"><font size="4"><b>{sum_sum}</b></font></td>
	</tr>
</table>
<table width="70%" border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td>{create}</td>
			</form>
		<td><a href={print_invoice} target="_blank">{lang_print_invoice}</a></td>
		<td><a href={list_invoice}>{lang_list_invoice}</a></td>
	</tr>
</table>
</center>
