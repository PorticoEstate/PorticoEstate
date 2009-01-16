<!-- $Id: site_form.tpl 3772 2001-04-02 17:23:50Z milosch $ -->
<!-- BEGIN form -->
<center>
<table border="0" width="80%" cellspacing="2" cellpadding="2">
<tr>
  <td colspan="1" align="center" bgcolor="#c9c9c9"><b>{title_sites}<b/></td>
</tr>
</table>
{message}
<table border="0" width="80%" cellspacing="2" cellpadding="2"> 
  <form name="form" action="{actionurl}" method="POST">
    <tr>
     <td>{lang_name}:</td>
     <td><input name="site_name" size="50" value="{site_name}"></td>
    </tr>
    <tr>
     <td>{lang_title}:</td>
     <td><input name="site_title" size="50" value="{site_title}"></td>
    </tr>
    <tr>
     <td>{lang_location}:</td>
     <td><input name="site_location" size="50" value="{site_location}"></td>
    </tr>
    <tr>
     <td>{lang_intro}:</td>
     <td><input name="site_intro" size="50" value="{site_intro}"></td>
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
