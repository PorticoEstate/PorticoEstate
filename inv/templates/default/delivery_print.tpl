<!-- $Id: delivery_print.tpl 6123 2001-06-22 01:59:33Z bettina $ -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML LANG="en">
<head>
<title>{site_title}</title>
<meta http-equiv="content-type" content="text/html; charset={charset}">
</head>
<body bgcolor="#FFFFFF">
<center>
<table width="70%" border="0" cellpadding="3" cellspacing="3">
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
	<tr>
		<td height="2">&nbsp;</td>
	</tr>
	<tr>
		<td><font face="{font}">{lang_delivery}:&nbsp;{delivery_num}</font></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><font face="{font}">{lang_date}:&nbsp;{delivery_date}</font></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><font face="{font}">{lang_order_descr}:&nbsp;{order_descr}</font></td>
		<td>&nbsp;</td>
	</tr>
</table><br><br><br>  
<table width="70%" border="0" cellspacing="3" cellpadding="3">
	<tr>
		<td width="5%" align="right"><font face="{font}">{lang_pos}</font></td>
		<td width="5%" align="right"><font face="{font}">{lang_piece}</font></td>
		<td width="10%"><font face="{font}">{lang_product_id}</font></td>
		<td width="10%"><font face="{font}">{lang_serial}</font></td>
		<td width="30%"><font face="{font}">{lang_product_name}</font></td>
	</tr>

<!-- BEGIN delivery_print -->

	<tr>
		<td align="right"><font face="{font}">{pos}</font></td>
		<td align="right"><font face="{font}">{piece}</font></td>
		<td><font face="{font}">{id}</font></td>
		<td><font face="{font}">{serial}</font></td>
		<td><font face="{font}">{name}</font></td>
	</tr>

<!-- END delivery_print -->

	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>{error}</td>
	</tr>
<hr noshade width="70%" size="1">
</table>
</center>
</body>
</html>
