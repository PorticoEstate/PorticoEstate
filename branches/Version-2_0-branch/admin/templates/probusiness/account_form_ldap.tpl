<!-- BEGIN form -->
{error_messages}
<form method="post" action="{form_action}">
    <table class="basic" align="center">
      <tr>
        <td class="top">{rows}</td>
        <td class="top">
            <table class="basic">
              <thead><tr><td colspan="4"><b>{lang_action}</b></td></tr></thead>
              <tr id="25">
                <td>{lang_loginid}</td>
                <td>{account_lid}&nbsp;</td>
                <td>{lang_account_active}:</td>
                <td>{account_status}</td>
              </tr>
              <tr>
                <td>{lang_firstname}</td>
                <td>{account_firstname}&nbsp;</td>
                <td>{lang_lastname}</td>
                <td>{account_lastname}&nbsp;</td>
              </tr>
              {password_fields}
              <tr>
                <td>{lang_homedir}</td>
                <td>{homedirectory}&nbsp;</td>
                <td>{lang_shell}</td>
                <td>{loginshell}&nbsp;</td>
              </tr>
              <tr>
                <td>{lang_expires}</td>
                <td colspan="3">{input_expires}&nbsp;&nbsp;{lang_never}&nbsp;{never_expires}</td>
              </tr>
              <tr>
                <td>{lang_changepassword}</td>
                <td>{changepassword}</td>
                <td>{lang_anonymous}</td>
                <td>{anonymous}</td>
              </tr>
              <tr>
                <td>{lang_groups}</td>
                <td colspan="3">{groups_select}&nbsp;</td>
              </tr>
              {permissions_list}
              {form_buttons}
            </table>
          </font>
        </td>
      </tr>
    </table>
</form>
<!-- END form -->

<!-- BEGIN form_passwordinfo -->
              <tr>
                <td>{lang_password}</td>
                <td><input type="password" name="account_passwd" value="{account_passwd}" /></td>
                <td>{lang_reenter_password}</td>
                <td><input type="password" name="account_passwd_2" value="{account_passwd_2}" /></td>
              </tr>
<!-- END form_passwordinfo -->

<!-- BEGIN form_buttons_ -->
              <tr><td colspan="4" class="right"><input type="submit" name="submit" value="{lang_button}" /></td></tr>
<!-- END form_buttons_ -->

<!-- BEGIN form_logininfo -->
              <tr>
                <td>{lang_lastlogin}</td>
                <td>{account_lastlogin}</td>
                <td>{lang_lastloginfrom}</td>
                <td>{account_lastloginfrom}</td>
              </tr>
<!-- END form_logininfo -->

<!-- BEGIN link_row -->
              <tr><td>&nbsp;<a href="{row_link}">{row_text}</a></td></tr>
<!-- END link_row -->

