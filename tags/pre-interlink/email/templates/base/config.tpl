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
    <td colspan="2">&nbsp;<b>{lang_Mail_settings}</b></td>
   </tr>

   <tr bgcolor="{row_on}">
    <td>{lang_Enter_your_POP}/{lang_IMAP_mail_server_hostname_or_IP_address}:</td>
    <td><input name="newsettings[mail_server]" value="{value_mail_server}"></td>
   </tr>

   <tr bgcolor="{row_off}">
    <td>{lang_Select_your_mail_server_type}:</td>
    <td>
     <select name="newsettings[mail_server_type]">
      <option value="imap" {selected_mail_server_type_imap}>IMAP</option>
      <option value="pop3" {selected_mail_server_type_pop3}>POP-3</option>
      <option value="imaps" {selected_mail_server_type_imaps}>IMAPS</option>
      <option value="pop3s" {selected_mail_server_type_pop3s}>POP-3S</option>
     </select>
    </td>
   </tr>

   <tr bgcolor="{row_on}">
    <td>{lang_IMAP_server_type}:</td>
    <td>
     <select name="newsettings[imap_server_type]">
      <option value="Cyrus" {selected_imap_server_type_Cyrus}>Cyrus or Courier</option>
      <option value="UWash" {selected_imap_server_type_UWash}>UWash</option>
      <option value="UW-Maildir" {selected_imap_server_type_UW-Maildir}>UW-Maildir</option>
     </select>
    </td>
   </tr>

   <tr bgcolor="{row_off}">
    <td>{lang_Enter_your_default_mail_domain_(_From:_user@domain_)}:</td>
    <td><input name="newsettings[mail_suffix]" value="{value_mail_suffix}"></td>
   </tr>

   <tr bgcolor="{row_on}">
    <td>{lang_Mail_server_login_type}:</td>
    <td>
     <select name="newsettings[mail_login_type]">
      <option value="standard" {selected_mail_login_type_standard}>standard</option>
      <option value="vmailmgr" {selected_mail_login_type_vmailmgr}>vmailmgr</option>
      <option value="ispman" {selected_mail_login_type_ispman}>ispman (experimental)</option>
     </select>
    </td>
   </tr>

   <tr bgcolor="{row_off}">
    <td>{lang_Enter_your_SMTP_server_hostname_or_IP_address}:</td>
    <td><input name="newsettings[smtp_server]" value="{value_smtp_server}"></td>
   </tr>

   <tr bgcolor="{row_on}">
    <td>{lang_Enter_your_SMTP_server_port}:</td>
    <td><input name="newsettings[smtp_port]" value="{value_smtp_port}"></td>
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
