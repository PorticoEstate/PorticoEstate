<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<!-- BEGIN login_form -->
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="AUTHOR" content="phpGroupWare http://www.phpgroupware.org" />
<meta name="description" content="phpGroupWare login screen" />
<meta name="keywords" content="phpGroupWare login screen" />

<title>{website_title} - Login</title>
</head>

<body bgcolor="#FFFFFF">
<a href="http://www.phpgroupware.org"><img src="phpgwapi/templates/{template_set}/images/logo.gif" alt="phpGroupWare"  border="0" /></a>
<p>&nbsp;</p>
<center>{lang_message}</center>
<p>&nbsp;</p>

<table bgcolor="#000000" border="0" cellpadding="0" cellspacing="0" width="50%" align="center">
 <tr>
  <td>
   <table border="0" width="100%" bgcolor="#486591" cellpadding="2" cellspacing="1">
    <tr bgcolor="#486591">
     <td align="left" valign="middle">
      <font color="#fefefe">&nbsp;phpGroupWare</font>
     </td>
    </tr>
    <tr bgcolor="#e6e6e6">
     <td valign="baseline">

      <form method="post" action="{login_url}">
	  <input type="hidden" name="passwd_type" value="text" />
       <table border="0" align="center" bgcolor="#486591" width="100%" cellpadding="0" cellspacing="0">
        <tr bgcolor="#e6e6e6">
         <td colspan="3" align="center">
          {cd}
         </td>
        </tr>
        <tr bgcolor="#e6e6e6">
         <td align="right"><font color="#000000">{lang_username}:</font></td>
         <td align="right"><input name="login" value="{cookie}" /></td>
         <td align="left">&nbsp;@&nbsp;<select name="logindomain">{select_domain}</select></td>
        </tr>
        <tr bgcolor="#e6e6e6">
         <td align="right"><font color="#000000">{lang_password}:</font></td>
         <td align="right"><input name="passwd" type="password" onchange="this.form.submit()" /></td>
         <td>&nbsp;</td>
        </tr>
        <tr bgcolor="#e6e6e6">
         <td colspan="3" align="center">
          <input type="submit" value="{lang_login}" name="submitit" />
         </td>
        </tr>
        <tr bgcolor="#e6e6e6">
         <td colspan="3" align="right">
          <font color="#000000" size="-1">{version}</font>
         </td>
        </tr>       
       </table>
      </form>
     
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>

<!-- END login_form -->
</html>
