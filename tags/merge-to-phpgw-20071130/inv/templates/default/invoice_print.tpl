<!-- $Id: invoice_print.tpl 6128 2001-06-22 02:33:10Z bettina $ -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">                                                                              
<HTML LANG="en"> 
<head>
<title>{site_title}</title>
<meta http-equiv="content-type" content="text/html; charset={charset}">
</head>
<body bgcolor="#FFFFFF">
<center>
<table width="90%" border="0" cellpadding="3" cellspacing="3">
	<tr>
		<td valign="bottom">{myaddress}</td>
		<td align="right"><img src="doc/logo.jpg"></td>
	</tr>
	<tr>
		<td height="2">&nbsp;</td>
	</tr>
	<tr>
		<td>{customer}</td>
		<td>&nbsp;</td>
	</tr>
		<td height="2">&nbsp;</td>
	</tr>
	<tr>
		<td><font face="{font}">{lang_invoice}:&nbsp;{invoice_num}</font></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><font face="{font}">{lang_date}:&nbsp;{invoice_date}</font></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><font face="{font}">{lang_order_descr}:&nbsp;{order_descr}</font></td>
		<td>&nbsp;</td>
	</tr>
</table><br><br><br>  
<table width="90%" border="0" cellspacing="3" cellpadding="3">
	<tr>
		<td width="5%" align="right"><font face="{font}">{lang_pos}</font></td>
		<td width="5%" align="right"><font face="{font}">{lang_piece}</font></td>
		<td width="10%"><font face="{font}">{lang_product_id}</font></td>
		<td width="10%"><font face="{font}">{lang_serial}</font></td>
		<td width="15%"><font face="{font}">{lang_product_name}</font></td>
		<td width="10%" align="right"><font face="{font}">{lang_procent}&nbsp;{lang_tax}</font></td>
		<td width="10%" align="right"><font face="{font}">{currency}&nbsp;{lang_price}</font></td>
		<td width="10%" align="right"><font face="{font}">{currency}&nbsp;{lang_sum_net}</font></td>
		<td width="10%" align="right"><font face="{font}">{currency}&nbsp;{lang_sum}</font></td>
	</tr>

<!-- BEGIN invoice_print -->

	<tr>
		<td align="right"><font face="{font}">{pos}</font></td>
		<td align="right"><font face="{font}">{piece}</font></td>
		<td><font face="{font}">{id}</font></td>
		<td><font face="{font}">{serial}</font></td>
		<td><font face="{font}">{name}</font></td>
		<td align="right"><font face="{font}">{tax}</font></td>
		<td align="right"><font face="{font}">{price}</font></td>
		<td align="right"><font face="{font}">{sum_piece}</font></td>
		<td align="right"><font face="{font}">{sum_retail}</font></td>
	</tr>

<!-- END invoice_print -->

	<tr>
		<td><br>&nbsp;</td>
		<td><br>&nbsp;</td>
		<td><br>&nbsp;</td>
		<td><br>&nbsp;</td>
		<td><br>{error}</td>
		<td><br>&nbsp;</td>
		<td align="right"><font size="4"><br><b>{currency}&nbsp;{lang_sum_net}</b></font></td>
		<td><br>&nbsp;</td>
		<td align="right"><font size="4"><br><b>{sum_price}</b></font></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="right"><font size="4"><b>{currency}&nbsp;{lang_tax}</b></font></td>
		<td>&nbsp;</td>
		<td align="right"><font size="4"><b>{sum_tax}</b></font></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="right"><font size="4"><b>{currency}&nbsp;{lang_sum}</b></font></td>
		<td>&nbsp;</td>
		<td align="right"><font size="4"><b>{sum_sum}</b></font></td>
	</tr>
	<hr noshade width="90%" size="1">
</table>
</center>
</body>
</html>