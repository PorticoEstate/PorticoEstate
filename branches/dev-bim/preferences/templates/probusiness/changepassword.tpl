  <br />
  {messages}
  <form method="post" action="{form_action}">
    <table class="padding" align="center">
			<tr><td class="header" colspan="2">&nbsp;</td></tr>
      <tr>
        <td class="bg_color1">{lang_enter_password}</td>
        <td class="bg_color1"><input type="password" name="n_passwd" /></td>
      </tr>
      <tr>
        <td class="bg_color2">{lang_reenter_password}</td>
        <td class="bg_color2"><input type="password" name="n_passwd_2" /></td>
      </tr>
      <tr>
        <td colspan="2">
          <table class="prefPW">
            <tr>
              <td><input type="submit" name="change" value="{lang_change}" /></td>
              <td><input type="submit" name="cancel" value="{lang_cancel}" /></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </form>
  <br />
  <pre>{sql_message}</pre>

