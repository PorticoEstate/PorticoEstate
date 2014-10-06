  <table class="basic" align="center">
    <tr><td class="header"><b>{num_msg} {lang_messages_found_in_folder} "{folder}"</b></td></tr>
  </table>
<form name="{form_name}" action="{delmov_action}" method="post">
  <input type="hidden" name="what" value="delete" />
  <input type="hidden" name="folder" value="{folder_short}" />
    <table class="basic" align="center">
      <tr class="header">
        <td class="center">&nbsp;</td>
        <td>&nbsp;</td>
        <td><strong>{lang_from}</strong></td>
        <td><strong>{lang_subject}</strong></td>
        <td class="center"><strong>{lang_date}</strong></td>
        <td class="center"><strong>{lang_size}</strong></td>
      </tr>
<!-- BEGIN search_result -->
      <tr class="bg_view">
        <td class="center"><input type="checkbox" name="delmov_list[]" value="{checkbox_val}" /></td>
        <td class="center">&nbsp;</td>
        <td class="left">{from}</td>
        <td class="left"><a href="{msg_link}">{subject}</a></td>
        <td class="center">{date}</td>
        <td class="center">{size}</td>
      </tr>
<!-- END search_result -->
      <tr>
        <td colspan="3" class="left">
          <a href="javascript:check_all('{form_name}')"><img src="/phpgroupware/email/templates/default/images/check.gif" border="0" height="16" width="21" /></a>&nbsp;&nbsp;
          <a href="javascript:do_action('{form_name}', 'delall')"><img src="/phpgroupware/email/templates/default/images/evo-trash-24.gif" border="0" alt="[image]" />&nbsp;Delete</a>
        </td>
        <td colspan="3" class="right">
          <select name="to_fldball_fake_uri" onChange="do_action('{form_name}', 'move')">
            <option value="">{lang_move_selected_messages_into}</option>
<!-- BEGIN folder_list -->
            <option value="{fld_link}">{fld_value}</option>
<!-- END folder_list -->
          </select>
        </td>
      </tr>
    </table>
</form>

