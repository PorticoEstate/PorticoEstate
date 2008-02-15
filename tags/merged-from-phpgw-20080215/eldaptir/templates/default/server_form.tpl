<!-- $Id: server_form.tpl 6387 2001-06-30 06:07:04Z milosch $ -->
<!-- BEGIN form -->
<center>
<table border="0" width="80%" cellspacing="2" cellpadding="2">
<tr>
  <td colspan="1" align="center" bgcolor="#c9c9c9"><b>{title_servers}<b/></td>
</tr>
</table>
{message}
<table border="0" width="80%" cellspacing="2" cellpadding="2"> 
  <form name="form" action="{actionurl}" method="POST">
    <tr>
     <td>{lang_name}:</td>
     <td><input name="server_name" size="50" value="{server_name}"></td>
    </tr>
    <tr>
     <td>{lang_type}:</td>
     <td>{server_type}</td>
    </tr>
    <tr>
     <td>{lang_basedn}:</td>
     <td><input name="server_basedn" size="50" value="{server_basedn}"></td>
    </tr>
    <tr>
     <td>{lang_rootdn}:</td>
     <td><input name="server_rootdn" size="50" value="{server_rootdn}"></td>
    </tr>
    <tr>
     <td>{lang_rootpw}:</td>
     <td><input type="password" name="server_rootpw" size="50" value="{server_rootpw}"></td>
    </tr>
    <tr>
     <td>{lang_default}:</td>
     <td><input type="checkbox" name="is_default"{is_default}></td>
    </tr>
    </table>

<!-- BEGIN add -->
         <table width="50%" border="0" cellspacing="2" cellpadding="2">
         <tr valign="bottom">
          <td height="50" align="center">
           {hidden_vars}
           <input type="submit" name="submit" value="{lang_add}"></td>
          <td height="50" align="center">
           <input type="reset" name="reset" value="{lang_reset}"></form></td>
          <td height="50" align="center">
            <form method="POST" action="{doneurl}">
           {hidden_vars}
         <input type="submit" name="done" value="{lang_done}"></form></td>
         </tr>
         </table>
         </form>
         </center>
<!-- END add -->

<!-- BEGIN edit -->
         <table width="50%" border="0" cellspacing="2" cellpadding="2">
         <tr valign="bottom">
          <td height="50" align="center">
           {hidden_vars}
           <input type="submit" name="submit" value="{lang_edit}"></form></td>
          <td height="50" align="center">
            <form method="POST" action="{deleteurl}">
           {hidden_vars}
         <input type="submit" name="delete" value="{lang_delete}"></form></td>
          <td height="50" align="center">
            <form method="POST" action="{doneurl}">
           {hidden_vars}
         <input type="submit" name="done" value="{lang_done}"></form></td>
         </tr>
         </table>
         </center>
<!-- END edit -->
<!-- END form -->
