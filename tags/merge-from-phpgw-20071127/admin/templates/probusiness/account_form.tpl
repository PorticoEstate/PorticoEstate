<!-- BEGIN form -->
  {error_messages}
  <form method="post" action="{form_action}">
    <div align="center">
      <table class="basic">
        <tr>
          <td valign="top">{rows}</td>
          <td valign="top">
            <table>
              <tr>
                <td>
                  <table>
                    <thead><tr><td colspan="4">{lang_action}</td></tr></thead>
                    <tr>
                      <td class="bg_color1">{lang_loginid}</td>
                      <td class="bg_color2">{account_lid}&nbsp;</td>
                      <td class="bg_color1">{lang_account_active}:</td>
                      <td class="bg_color2">{account_status}</td>
                    </tr>
                    <tr>
                      <td class="bg_color1">{lang_firstname}</td>
                      <td class="bg_color2">{account_firstname}&nbsp;</td>
                      <td class="bg_color1">{lang_lastname}</td>
                      <td class="bg_color2">{account_lastname}&nbsp;</td>
                    </tr>
					<tr>
						<td class="bg_color1">{lang_add_addbook}</td>
						<td class="bg_color2">{add_addbook}{person_id}</td>
					 	<td class="bg_color1"></td>
						<td class="bg_color2"></td>
					</tr>
                    {password_fields}
                    <tr>
                      <td class="bg_color1">{lang_changepassword}</td>
                      <td class="bg_color2">{changepassword}</td>
                      <td class="bg_color1">{lang_anonyous}</td>
                      <td class="bg_color2">{anonymous}</td>
                    </tr>
                    <tr>
                      <td class="bg_color1">{lang_expires}</td>
                      <td class="bg_color2" colspan="3">{input_expires}&nbsp;&nbsp;{lang_never}&nbsp;{never_expires}</td>
                    </tr>

				{form_quota_view}
				{form_quota_edit}


                    <tr>
                      <td class="bg_color1">{lang_groups}</td>
                      <td class="bg_color2" colspan="3">{groups_select}&nbsp;</td>
                    </tr>
                    <tr class="bg_color1">
			     						<td colspan="4"><b>{lang_permissions}</b></td>
			    					</tr>
                    {permissions_list}
                    {form_buttons}
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </form>
<!-- END form -->

<!-- BEGIN form_passwordinfo -->
                    <tr>
                      <td class="bg_color1">{lang_password}</td>
                      <td class="bg_color2"><input type="password" name="account_passwd" value="{account_passwd}" /></td>
                      <td class="bg_color1">{lang_reenter_password}</td>
                      <td class="bg_color2"><input type="password" name="account_passwd_2" value="{account_passwd_2}" /></td>
                    </tr>
<!-- END form_passwordinfo -->

<!-- BEGIN form_buttons_ -->
                    <tr><td colspan="4" class="right"><input type="submit" name="submit" value="{lang_button}" /></td></tr>
<!-- END form_buttons_ -->

<!-- BEGIN form_logininfo -->
                    <tr>
                      <td class="bg_color1">{lang_lastlogin}</td>
                      <td class="bg_color2">{account_lastlogin}</td>
                      <td class="bg_color1">{lang_lastloginfrom}</td>
                      <td class="bg_color2">{account_lastloginfrom}</td>
                    </tr>
<!-- END form_logininfo -->

<!-- BEGIN link_row -->
                    <tr><td>&nbsp;<a href="{row_link}">{row_text}</a></td></tr>
<!-- END link_row -->
<!-- BEGIN form_quota_view -->
    <tr bgcolor="{tr_color1}">
     <td>Quota (MB)</td>
     <td colspan="3">{quota}</td>
    </tr>
<!-- END form_quota_view -->

<!-- BEGIN form_quota_edit -->
    <tr bgcolor="{tr_color1}">
     <td>Quota (MB)</td>
     <td colspan="3"><select size="1" name="quota">{quota_edit}</select></td>
    </tr>
<!-- END form_quota_edit -->
