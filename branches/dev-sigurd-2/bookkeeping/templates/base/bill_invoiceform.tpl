<!-- $Id$ -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML LANG="en">
<head>
<title>{site_title}</title>
<meta http-equiv="content-type" content="text/html; charset={charset}">
<STYLE type="text/css">
   A {text-decoration:none;}
   <!--
   A:link {text-decoration:none;}
   A:visted {text-decoration:none;}
   A:active {text-decoration:none;}
   body {margin-top: 0px; margin-right: 0px; margin-left: 0px;}
   td {text-decoration:none;}
   tr {text-decoration:none;}
   table {text-decoration:none;}
   center {text-decoration:none;}
   -->
</STYLE>
</head>
<body bgcolor="#FFFFFF">
<center>
<table width="93%" border="0" cellpadding="3" cellspacing="3">
	<tr>
		<td valign="bottom" width="53%">{myaddress}</td>
		<td align="right" width="37%"><img src="{img_src}"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>{customer}</td>
		<td align="right">
			<table border="0" align="right" cellpadding="2" cellspacing="2">
				<tr>
					<td><font face="{font}" size="{fontsize}">{lang_invoice_num}:&nbsp;{invoice_num}</font></td>
				</tr>
				<tr>
					<td><font face="{font}" size="{fontsize}">{lang_invoice_date}:&nbsp;{invoice_date}</font></td>					
				</tr>
				<tr>
					<td><font face="{font}" size="{fontsize}">{lang_project_num}:&nbsp;{project_num}</font></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><b><font face="{font}" size="{fontsize}">{lang_invoice_for_project}:&nbsp;{title}</font></b></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>
<table width="93%" border="0" cellspacing="3" cellpadding="3">
	<tr>
		<td width="8%" align="right"><font face="{font}" size="{fontsize}">{lang_position}</font></td>
		<td width="10%" align="center"><font face="{font}" size="{fontsize}">{lang_work_date}</font></td>
		<td width="30%"><font face="{font}" size="{fontsize}">{lang_descr}</font></td>
		<td width="10%" align="right"><font face="{font}" size="{fontsize}">{lang_workunits}</font></td>
		<td width="15%" align="right"><font face="{font}" size="{fontsize}">{currency}&nbsp;{lang_per}</font></td>
		<td width="10%" align="right"><font face="{font}" size="{fontsize}">{currency}&nbsp;{lang_sum}</font></td>
	</tr>

<!-- BEGIN bill_list -->

	<tr>
		<td align="right"><font face="{font}" size="{fontsize}">{pos}</font></td>
		<td align="center"><font face="{font}" size="{fontsize}">{hours_date}</font></td>
		<td><font face="{font}" size="{fontsize}">{act_descr}</font></td>
		<td align="right"><font face="{font}" size="{fontsize}">{aes}</font></td>
		<td align="right"><font face="{font}" size="{fontsize}">{billperae}</font></td>
		<td align="right"><font face="{font}" size="{fontsize}">{sumpos}</font></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><font face="{font}" size="{fontsize}">{hours_descr}</font></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

<!-- END bill_list -->

</table>
<table width="90%" border="0" cellspacing="3" cellpadding="3">
	<tr>
		<td width="8%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="30%">&nbsp;</td>
		<td width="10%"><font face="{font}" size="{fontsize}">{currency}&nbsp;{lang_netto}:</font></td>
		<td width="10%" align="right"><font face="{font}" size="{fontsize}">{sum_netto}</font></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><font face="{font}" size="{fontsize}">{currency}&nbsp;{tax}&nbsp;%&nbsp;{lang_tax}:</font></td>
		<td align="right"><font face="{font}" size="{fontsize}">{sum_tax}</font></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><font face="{font}" size="{fontsize}"><b>{currency}&nbsp;{lang_sum}:</b></font></td>
		<td align="right"><font face="{font}" size="{fontsize}"><b>{sum_sum}</b></font></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>{message}</font></td>
		<td>{error_hint}</font></td>
	</tr>
	<hr noshade width="93%" size="1"> 
</table>
<table width="93%" border="0" cellspacing="3" cellpadding="3" valign="bottom">
	<hr noshade width="93%" size="1">
	<tr>
		<td align="left" valign="bottom">{fulladdress}</td>
	</tr>
</table>
</center>
</body>
</html>
