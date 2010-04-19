<!-- BEGIN header -->
<form method="POST" action="{action_url}">
<table border="0" align="center">
   <tr bgcolor="{th_bg}">
	   <td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
   </tr>
   <tr bgcolor="{th_err}">
    <td colspan="2">&nbsp;<b>{error}</b></font></td>
   </tr>
<!-- END header -->
<!-- BEGIN body -->
   <tr bgcolor="{row_on}">
    <td colspan="2">&nbsp;</td>
   </tr>
   <tr bgcolor="{row_off}">
    <td colspan="2">&nbsp;<b>{lang_communik8r_settings}</b></font></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_smtp_hostname}:</td>
    <td><input name="newsettings[smtp_host]" value="{value_smtp_host}" size="40"></td>
   </tr>
   <tr bgcolor="{row_off}">
    <td>{lang_smtp_port}:</td>
    <td><input name="newsettings[smtp_port]" value="{value_smtp_port}" size="4"></td>
   </tr>
   <tr bgcolor="{row_on}"><td colspan="2">SMTP AUTH Support option coming soon</td></tr>
   <tr bgcolor="{row_off}"><td colspan="2">SSL Support coming soon - I hope</td></tr>
  <!--
  <tr bgcolor="{row_off}">
   <td>{lang_LDAP_root_pw_for_contacts}:</td>
   <td><input name="newsettings[ldap_contact_pw]" type="password" value=""></td>
  </tr>
  -->
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
