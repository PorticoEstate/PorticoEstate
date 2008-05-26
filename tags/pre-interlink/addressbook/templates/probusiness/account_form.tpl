<!-- BEGIN form -->
{error_messages}
<form method="post" action="{form_action}">
  <div class="center">
    <table class="addressbook95">
      <tr>
        <td class="top">{rows}</td>
        <td class="top">
          <table class="addressbook100">
            <tr><td colspan="4"><b><font>{lang_action}</font></b></td></tr>
            <tr>
              <td id="25"><font>{lang_loginid}</font></td>
              <td id="25">{account_lid}&nbsp;</td>
              <td id="25"><font>{lang_account_active}:</font></td>
              <td id="25">{account_status}</td>
            </tr>
            <tr>
              <td><font>{lang_firstname}</font></td>
              <td>{account_firstname}&nbsp;</td>
              <td><font>{lang_lastname}</font></td>
              <td>{account_lastname}&nbsp;</td>
            </tr>
            <tr>
              <td><font>{lang_domain}</font></td>
              <td>{domain}</td>
              <td><font>{lang_add_addbook}</font></td>
              <td>{add_addbook}{person_id}</td>
            </tr>
            {password_fields}
            <tr>
              <td><font>{lang_changepassword}</font></td>
              <td>{changepassword}</td>
              <td><font>{lang_anonymous}</font></td>
              <td>{anonymous}</td>
            </tr>
            <tr>
              <td><font>{lang_expires}</font></td>
              <td colspan="3">{input_expires}&nbsp;&nbsp;<font>{lang_never}</font>&nbsp;{never_expires}</td>
            </tr>
            <tr>
              <td><font>{lang_groups}</font></td>
              <td colspan="3">{groups_select}&nbsp;</td>
            </tr>
            {permissions_list}
            {form_buttons}
          </table>
        </td>
      </tr>
    </table>
  </div>
</form>
<!-- END form -->

<!-- BEGIN form_passwordinfo -->
            <tr>
              <td><font>{lang_password}</font></td>
              <td><input class="text" type="password" name="account_passwd" value="{account_passwd}" /></td>
              <td><font>{lang_reenter_password}</font></td>
              <td><input class="text" type="password" name="account_passwd_2" value="{account_passwd_2}" /></td>
            </tr>
<!-- END form_passwordinfo -->

<!-- BEGIN form_buttons_ -->
            <tr><td colspan="4" class="right"><input class="button" type="submit" name="submit" value="{lang_button}" /></td></tr>
<!-- END form_buttons_ -->

<!-- BEGIN form_logininfo -->
            <tr>
              <td><font>{lang_lastlogin}</font></td>
              <td>{account_lastlogin}</td>
              <td><font>{lang_lastloginfrom}</font></td>
              <td>{account_lastloginfrom}</td>
            </tr>
<!-- END form_logininfo -->

<!-- BEGIN link_row -->
            <tr><td>&nbsp;<a href="{row_link}">{row_text}</a></td></tr>
<!-- END link_row -->

