<!-- BEGIN interserv.tpl -->
<table>
  <tr>
    <td colspan="3"><font size="+1">{lang_title}</font></td>
  </tr>
  <tr>
    <td colspan="3">
      <form method="POST" action="{action_url}">
      {lang_select_target}:&nbsp;{server_list}&nbsp;{lang_st_note}
    </td>
  </tr>
  <tr>
    <td colspan="3">{login_type}:&nbsp;<input type="checkbox" name="xserver" value="True"{xserver}></td>
  <tr>
    <td colspan="3">
    {lang_this_servername}:&nbsp;<input name="xserver_name" value="{xserver_name}">&nbsp;{lang_sd_note}
    </td>
  </tr>
  <tr>
    <td colspan="3">{lang_username}:&nbsp;<input name="xusername" value="{xusername}"></td>&nbsp;
  </tr>
  <tr>
    <td colspan="3">{lang_password}:&nbsp;<input type="password" name="xpassword" value="{xpassword}"></td>
  </tr>
  <tr>
    <td colspan="3">{lang_session}:&nbsp;{xsessionid}&nbsp;{lang_kp3}:&nbsp;{xkp3}</td>
  </tr>
  <tr>
    <td colspan="3">
    <input type="submit" name="login" value="{lang_login}">
    <input type="submit" name="logout" value="{lang_logout}">
    <input type="submit" name="addressbook" value="{lang_addressbook}">
    <input type="submit" name="calendar" value="{lang_calendar}">
    <input type="submit" name="methods" value="{lang_list} {method_type}{lang_methods}">&nbsp;
    {applist}
    </td>
  </tr>
  <tr>
    <td colspan="3">
    <input type="submit" name="apps" value="{lang_list} {lang_apps}">
    <input type="submit" name="users" value="{lang_list} {lang_users}">
    <input type="submit" name="bogus" value="{lang_bogus}">
    <input type="hidden" name="xsessionid" value="{xsessionid}">
    <input type="hidden" name="xkp3" value="{xkp3}">
    </td>
  </tr>
  <tr>
    <td colspan="3">
    {note}
    </td>
  </tr>
</form>
</table>
<!-- END interserv.tpl -->
