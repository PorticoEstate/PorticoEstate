<!-- BEGIN tts_select_options -->
<option value="{tts_optionvalue}" {tts_optionselected}>{tts_optionname}</option>
<!-- END tts_select_options -->

<!-- BEGIN admin.tpl -->
<p><b>{lang_admin}:</b></p>
<hr />
<p></p>
<form method="post" action="{action_url}">
    <table class="padding" align="center">
      <tr class="header">
        <td>{lang_mailnotification}</td>
        <td><input type="checkbox" name="usemailnotification"{mailnotification} /></td>
      </tr>
      <tr class="bg_color1">
        <td>{lang_ownernotification}</td>
        <td>
          <select size="1" name="ownernotification">
            {tts_owneroptions}
          </select>
        </td>
      </tr>
      <tr class="bg_color2">
        <td>{lang_groupnotification}</td>
        <td>
          <select size="1" name="groupnotification">
            {tts_groupoptions}
          </select>
        </td>
      </tr>
      <tr class="bg_color1">
        <td>{lang_assignednotification}</td>
        <td>
          <select size="1" name="assignednotification">
            {tts_assignedoptions}
          </select>
        </td>
      </tr>
      <tr><td colspan="3" class="center"><input type="submit" name="submit" value="{lang_submit}" /></td></tr>
    </table>
  </div>
</form>

