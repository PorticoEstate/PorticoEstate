<script language="JavaScript1.1" type="text/javascript"><!--
  self.name="first_Window";
  function accounts_popup()
   {
    Window1 = window.open('{accounts_link}',"Search","width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes");
   }
  // -->
</script>
{error}
  <table class="basic" align="center">
    <tr>
      <td class="top">{rows}</td>
      <td class="top">
        <table>
          <form method="post" action="{form_action}" name="app_form">
            {hidden_vars}
            <tr>
              <td class="header">{lang_group_name}</td>
              <td class="bg_view"><input name="account_name" value="{group_name_value}" /></td>
            </tr>
            <tr>
              <td class="header" valign="top">{lang_include_user}</td>
              <td class="bg_view">{accounts}</td>
            </tr>
            <tr>
              <td>{lang_file_space}</td>
              <td>{account_file_space}{account_file_space_select}</td>
            </tr>
            <tr>
              <td class="header" valign="top">{lang_permissions}</td>
              <td>
                <table cols="6">
                  {permissions_list}
                </table>
              </td>
            </tr>
            <tr>
            	<td colspan="2" class="left">
            		<input type="submit" name="edit" value="{lang_submit_button}" />
            	</td>
            </tr>
          </form>
        </table>
      </td>
    </tr>
  </table>

<!-- BEGIN select -->
  <select name="account_user[]" multiple size="{select_size}">
    {user_list}
  </select>
<!-- END select -->

<!-- BEGIN popwin -->
  <table align="center">
    <tr>
      <td>
        <select name="account_user[]" multiple size="{select_size}">
          {user_list}
        </select>
      </td>
      <td class="top">
        <input type="button" value="{lang_open_popup}" onClick="accounts_popup()" />
        <input type="hidden" name="accountid" value="{accountid}" />
      </td>
    </tr>
  </table>
<!-- END popwin -->

