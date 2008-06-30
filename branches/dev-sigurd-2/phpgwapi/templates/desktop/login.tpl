<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<!-- BEGIN login_form -->
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="AUTHOR" content="phpGroupWare http://www.phpgroupware.org" />
<meta name="description" content="{website_title} login screen, working environment powered by phpGroupWare" />
<meta name="keywords" content="{website_title} login screen, phpgroupware, groupware, groupware suite" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<title>{website_title} - {lang_login}</title>
</head>

<body bgcolor="#{bg_color}">
<a href="http://{logo_url}"><img src="phpgwapi/templates/{template_set}/images/{logo_file}" alt="{logo_title}" title="{logo_title}" border="0" /></a>
<p>&nbsp;</p>
<center>{lang_message}</center>
<p>&nbsp;</p>

<table bgcolor="#000000" border="0" cellpadding="0" cellspacing="0" width="40%" align="center">
 <tr>
  <td>
   <table border="0" width="100%" bgcolor="#486591" cellpadding="2" cellspacing="1">
    <tr bgcolor="#{bg_color_title}">
     <td align="left" valign="middle">
      <font color="#FEFEFE">&nbsp;{website_title}</font>
     </td>
    </tr>
    <tr bgcolor="#e6e6e6">
     <td valign="baseline">

		<form name="login" method="post" action="{login_url}" {autocomplete}>
		<input type="hidden" name="passwd_type" value="text" />
			<table border="0" align="center" bgcolor="#486591" width="100%" cellpadding="0" cellspacing="0">
				<tr bgcolor="#e6e6e6">
					<td colspan="2" align="center">{cd}</td>
				</tr>
				<tr bgcolor="#e6e6e6">
					<td align="right"><font color="#000000">{lang_username}:&nbsp;</font></td>
					<td><input name="login" value="{cookie}" />{logindomain}</td>
				</tr>
				<tr bgcolor="#e6e6e6">
					<td align="right"><font color="#000000">{lang_password}:&nbsp;</font></td>
					<td><input name="passwd" type="password" onchange="this.form.submit();" /></td>
				</tr>
				<tr bgcolor="#e6e6e6">
					<td colspan="2" align="center"><input type="submit" value="{lang_login}" name="submitit" /></td>
				</tr>
				<tr bgcolor="#e6e6e6">
					<td colspan="2" align="right"><font color="#000000" size="-1">phpGroupWare {version}</font></td>
				</tr>       
			</table>
		</form>
     
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</body>
<!-- END login_form -->
</html>
