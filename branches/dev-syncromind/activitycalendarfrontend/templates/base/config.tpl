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
    <td colspan="2"><b>{Registration_settings}</b></td>
  </tr>
  <tr bgcolor="{row_on}">
   <td>{lang_ajaxURL}:</td>
   <td><input name="newsettings[AJAXURL]" value="{value_AJAXURL}"></td>
  </tr>
  <tr bgcolor="{row_off}">
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr bgcolor="{row_on}">
   <td>{lang_Anonymous_user}:</td>
   <td><input name="newsettings[anonymous_user]" value="{value_anonymous_user}"></td>
  </tr>
  <tr bgcolor="{row_off}">
   <td>{lang_Anonymous_password}:</td>
   <td><input type="password" name="newsettings[anonymous_pass]" value="{value_anonymous_pass}"></td>
  </tr>
  <tr bgcolor="{row_on}">

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
