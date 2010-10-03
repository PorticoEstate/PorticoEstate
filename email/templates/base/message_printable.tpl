<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- the above line just began message_printable.tpl -->
<html>
  <head>
  <meta http-equiv="Content-Type" content="text/html"; charset="{charset}">
  <meta name="author" content="AngleMail for phpGroupWare http://www.anglemail.org">
  <meta name="description" content="AngleMail for phpGroupWare http://www.phpgroupware.org">
  <meta name="keywords" content="AngleMail for phpGroupWare">
  <style type="text/css">
   <!--
   A:link {text-decoration:none;}
   A:visted {text-decoration:none;}
   A:active {text-decoration:none;}
   body {
	margin-top: 0px; 
	margin-right: 0px; 
	margin-left: 0px; 
	margin-bottom: 0px; 
	font-family: "{font_family}"; 
	background-color: #ffffff;
	color: #000000;
   }
   TD {
	text-align: left;
	vertical-align: top;
   }
   TD.fullname { 
	font-weight: bold;
	font-size: 110%;
   }
   TD.partinfo { 
	font-size: small;
	border: 1px dotted black;
	padding: 0px;
   }
   center { text-decoration:none; }
   -->
  </style>
  <title>{page_title}</title>
</head>
<body bgcolor="#ffffff" color="#000000">

<table border="0" cellpadding="0" cellspacing="0" width="95%" align="center">
<tr>
	<td width="100%" align="left" colspan="3" class="fullname">
		<strong>{user_fullname}</strong>
	</td>
</tr>
<tr>
	<td width="100%" align="left" colspan="3">
		<hr width="100%">
	</td>
</tr>
<tr>
	<td width="15%" align="left">
		<strong>{lang_from}:</strong>
	</td>
	<td width="2%" align="left">
		&nbsp;
	</td>
	<td width="83%" align="left">
		{from_data_final}
	</td>
</tr>
<tr>
	<td>
		<strong>{lang_to}:</strong>
	</td> 
	<td>
		&nbsp;
	</td>
	<td>
		{to_data_final}
	</td>
</tr>

<!-- BEGIN B_cc_data -->
<tr>
	<td>
		<strong>{lang_cc}:</strong>
	</td> 
	<td>
		&nbsp;
	</td>
	<td>
		{cc_data_final}
	</td>
</tr>
<!-- END B_cc_data -->

<tr>
	<td>
		<strong>{lang_date}:</strong>
	</td> 
	<td>
		&nbsp;
	</td>
	<td>
		{message_date}
	</td>
</tr>

<!-- BEGIN B_attach_list -->
<tr>
	<td>
		<strong>{lang_files}:</strong>
	</td> 
	<td>
		&nbsp;
	</td>
	<td>
		{list_of_files}
	</td>
</tr>
<!-- END B_attach_list -->

<tr>
	<td>
		<strong>{lang_subject}:</strong>
	</td> 
	<td>
		&nbsp;
	</td>
	<td>
		{message_subject}
	</td>
</tr>
</table>

<!-- start message display -->
<br />
<table border="0" cellpadding="0" cellspacing="0" width="95%" align="center">

<!-- BEGIN B_display_part -->
<tr>
	<td width="100%" class="partinfo">
		{title_text} &nbsp; &nbsp; {display_str}
	</td>
</tr>
<tr>
	<td align="left">
		<br />{message_body}
	</td>
</tr>
<!-- END B_display_part -->

</table>

<!-- end message_printable.tpl -->
