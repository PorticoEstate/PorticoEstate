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
<table width="73%" border="0" cellpadding="3" cellspacing="3">
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
					<td><font face="{font}" size="{fontsize}">{lang_delivery_num}:&nbsp;{delivery_num}</font></td>
				</tr>
				<tr>
					<td><font face="{font}" size="{fontsize}">{lang_delivery_date}:&nbsp;{delivery_date}</font></td>					
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
		<td><b><font face="{font}" size="{fontsize}">{lang_delivery_note_for_project}:&nbsp;{title}</font></b></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>
<table width="70%" border="0" cellspacing="3" cellpadding="3">
	<tr>
		<td width="5%" align="right"><font face="{font}" size="{fontsize}">{lang_position}</font></td>
		<td width="30%"><font face="{font}" size="{fontsize}">{lang_descr}</font></td>
		<td width="10%" align="center"><font face="{font}" size="{fontsize}">{lang_work_date}</font></td>
		<td width="10%" align="right"><font face="{font}" size="{fontsize}">{lang_workunits}</font></td>
	</tr>

<!-- BEGIN del_list -->

	<tr>
		<td align="right"><font face="{font}" size="{fontsize}">{pos}</font></td>
		<td><font face="{font}" size="{fontsize}">{act_descr}</font></td>
		<td align="center"><font face="{font}" size="{fontsize}">{hours_date}</font></td>
		<td align="right"><font face="{font}" size="{fontsize}">{aes}</font></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><font face="{font}" size="{fontsize}">{hours_descr}</font></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

<!-- END del_list -->

</table>
<table width="70%" border="0" cellspacing="3" cellpadding="3">
	<tr>
		<td width="5%">&nbsp;</td>
		<td width="30%"><font face="{font}" size="{fontsize}"><b>{lang_sumaes}</b></font></td>
		<td width="10%">&nbsp;</td>
		<td width="10%" align="right"><font face="{font}" size="{fontsize}"><b>{sumaes}</b></font></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>{message}</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<hr noshade width="70%" size="1">
</table>
<table width="70%" border="0" cellspacing="3" cellpadding="3" valign="bottom">
	<hr noshade width="70%" size="1">
	<tr>
		<td align="left" valign="bottom">{fulladdress}</td>
	</tr>
</table>
</body>
</html>
