<!-- BEGIN form -->
  <form method="post" action="{form_action}">
    <input type="hidden" name="account_id" value="{account_id}" />
      <table class="basic" align="center">
        <thead><tr><td class="center" colspan="2">{lang_new_owner}</td></tr></thead>
        <tr><td class="center" colspan="2">{new_owner_select}</td></tr>
        <tr>
          <td class="right"><input type="submit" name="cancel" value="{cancel}" /></td>
          <td class="left"><input type="submit" name="delete_account" value="{delete}" /></td>
        </tr>
      </table>
  </form>
<!-- END form -->

