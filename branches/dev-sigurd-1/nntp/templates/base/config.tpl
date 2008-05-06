<!-- BEGIN header -->
<form method="POST" action="{action_url}">
<table border="0" align="center">
   <tr bgcolor="{th_bg}">
    <td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
   </tr>
<!-- END header -->
<!-- BEGIN body -->
   <tr bgcolor="{row_on}">
    <td colspan="2">&nbsp;</td>
   </tr>

   <tr bgcolor="{row_off}">
    <td colspan="2">&nbsp;<b>{lang_News_Reader_settings}</b></td>
   </tr>
   
   <tr bgcolor="{row_on}">
    <td>{lang_Enter_your_NNTP_server_hostname}:</td>
    <td><input name="newsettings[nntp_server]" value="{value_nntp_server}"></td>
   </tr>

   <tr bgcolor="{row_off}">
    <td>{lang_Enter_your_NNTP_server_port}:</td>
    <td><input name="newsettings[nntp_port]" value="{value_nntp_port}"></td>
   </tr>

   <tr bgcolor="{row_on}">
    <td>{lang_Enter_your_NNTP_sender}:</td>
    <td><input name="newsettings[nntp_sender]" value="{value_nntp_sender}"></td>
   </tr>

   <tr bgcolor="{row_off}">
    <td>{lang_Enter_your_NNTP_organization}:</td>
    <td><input name="newsettings[nntp_organization]" value="{value_nntp_organization}"></td>
   </tr>

   <tr bgcolor="{row_on}">
    <td>{lang_Enter_your_NNTP_admin's_email_address}:</td>
    <td><input name="newsettings[nntp_admin]" value="{value_nntp_admin}"></td>
   </tr>

   <tr bgcolor="{row_off}">
    <td>{lang_Enter_your_NNTP_login}:</td>
    <td><input name="newsettings[nntp_login_username]" value="{value_nntp_login_username}"></td>
   </tr>

   <tr bgcolor="{row_on}">
    <td>{lang_Enter_your_NNTP_password}:</td>
    <td><input name="newsettings[nntp_login_password]" value="{value_nntp_login_password}"></td>
   </tr>
<!-- END body -->
<!-- BEGIN footer -->
  <tr bgcolor="{th_bg}">
    <td colspan="2">
&nbsp;
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" name="submit" value="{lang_submit}">
      <input type="submit" name="cancel" value="{lang_cancel}">
    </td>
  </tr>
</table>
</form>
<!-- END footer -->
