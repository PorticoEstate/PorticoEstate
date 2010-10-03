<!-- begin folder.tpl -->
{widget_toolbar}
<form action="{form_action}" method="post">
    <table class="basic" align="center">
      <tr><td colspan="3" class="bg_color2">&nbsp;<strong>{title_text}<strong></td></tr>
      <tr class="header">
        <td><strong>{label_name_text}</strong>&nbsp;&nbsp;&nbsp;<a href="{view_lnk}">({view_txt})</a></td>
        <td id="7" class="right">{label_new_text}</td>
        <td id="7" class="right">{label_total_text}</td>
      </tr>
<!-- BEGIN B_folder_list -->
      <tr class="bg_color1">
        <td><a href="{folder_link}">{folder_name}</a></td>
        <td class="right">{msgs_unseen}</td>
        <td class="right">{msgs_total}</td>
      </tr>
<!-- END B_folder_list -->
      <tr class="th">
        <td colspan="3" class="right" class="header">
          {all_folders_listbox}
          &nbsp;
          <select name="action">
            <option value="create">{form_create_txt}</option>
            <option value="delete">{form_delete_txt}</option>
            <option value="rename">{form_rename_txt}</option>
            <option value="create_expert">{form_create_expert_txt}</option>
            <option value="delete_expert">{form_delete_expert_txt}</option>
            <option value="rename_expert">{form_rename_expert_txt}</option>
          </select>
          <input type="text" name="{target_fldball_boxname}" />
          <input type="hidden" name="{hiddenvar_target_acctnum_name}" value="{hiddenvar_target_acctnum_value}" />
          <input type="submit" value="{form_submit_txt}" />
        </td>
      </tr>
    </table>
</form>
{debugdata}
<br />
<!-- end folder.tpl -->

