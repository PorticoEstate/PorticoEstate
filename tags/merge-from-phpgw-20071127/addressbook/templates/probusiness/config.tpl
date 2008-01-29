<!-- BEGIN header -->
<form method="post" action="{action_url}">
  <div class="center">
    <table class="tableCenter">
      <tr class="head_background">
        <td colspan="2" class="center"><font>&nbsp;<b>{title}</b></font></td>
      </tr>
      <tr><td colspan="2">&nbsp;<font><b>{error}</b></font></td></tr>
<!-- END header -->
<!-- BEGIN body -->
      <tr><td colspan="2">&nbsp;</td></tr>
      <tr class="bg_color2"><td colspan="2">&nbsp;<b><font>{lang_Addressbook}/{lang_Contact_Settings}</b></font></td></tr>
      <tr class="bg_color1">
        <td><font>{lang_Contact_application}:</font></td>
        <td><input class="text" name="newsettings[contact_application]" value="{value_contact_application}"></td>
      </tr>
      <tr class="bg_color2"><td class="center" colspan="2">{lang_WARNING!!_LDAP_is_valid_only_if_you_are_NOT_using_contacts_for_accounts_storage!}</td></tr>
      <tr class="bg_color1">
        <td><font>{lang_Select_where_you_want_to_store}/{lang_retrieve_contacts}.</font></td>
        <td>
          <select name="newsettings[contact_repository]">
            <option value="sql" {selected_contact_repository_sql}>SQL</option>
            <option value="ldap" {selected_contact_repository_ldap}>LDAP</option>
          </select>
        </td>
      </tr>
      <tr class="bg_color2">
        <td><font>{lang_LDAP_host_for_contacts}:</font></td>
        <td><input class="text" name="newsettings[ldap_contact_host]" value="{value_ldap_contact_host}" /></td>
      </tr>
      <tr class="bg_color1">
        <td><font>{lang_LDAP_context_for_contacts}:</font></td>
        <td><input class="text" name="newsettings[ldap_contact_context]" value="{value_ldap_contact_context}" size="40" /></td>
      </tr>
      <tr class="bg_color2">
        <td><font>{lang_LDAP_root_dn_for_contacts}:</font></td>
        <td><input class="text" name="newsettings[ldap_contact_dn]" value="{value_ldap_contact_dn}" size="40" /></td>
      </tr>
      <tr class="bg_color1">
        <td><font>{lang_LDAP_root_pw_for_contacts}:</font></td>
        <td><input class="text" name="newsettings[ldap_contact_pw]" type="password" value="" /></td>
      </tr>
<!-- END body -->
<!-- BEGIN footer -->
      <tr class="bg_color2"><td colspan="2">&nbsp;</td></tr>
      <tr>
        <td colspan="2" class="center">
          <input class="button" type="submit" name="submit" value="{lang_submit}" />
          <input class="button" type="submit" name="cancel" value="{lang_cancel}" />
        </td>
      </tr>
    </table>
  </div>
</form>
<!-- END footer -->

