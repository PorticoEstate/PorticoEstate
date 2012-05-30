<!-- BEGIN B_tr_blank -->
<tr><td colspan="2">&nbsp;<br /></td></tr>
<!-- END B_tr_blank -->

<!-- BEGIN B_tr_sec_title -->
<tr class="header">
  <td class="EmailNowrap">{section_title}</td>
  <td class="right">{show_help_lnk}</td>
</tr>
<!-- END B_tr_sec_title -->

<!-- BEGIN B_tr_long_desc -->
<tr class="bg_view"><td colspan="2" class="center" valign="middle"><strong>{lang_blurb}</strong>: <p>{long_desc}</p>&nbsp;<br /></td></tr>
<!-- END B_tr_long_desc -->

<!-- BEGIN B_tr_textarea -->
<tr class="bg_view">
  <td class="left">{lang_blurb}</td>
  <td class="center" valign="middle"><textarea name="{pref_id}" rows="6" cols="50">{pref_value}</textarea></td>
</tr>
<!-- END B_tr_textarea -->

<!-- BEGIN B_tr_textbox -->
<tr class="bg_view">
  <td class="left">{lang_blurb}</td>
  <td class="center" valign="middle" width="{right_col_width}"><input type="text" name="{pref_id}" value="{pref_value}" /></td>
</tr>
<!-- END B_tr_textbox -->

<!-- BEGIN B_tr_passwordbox -->
<tr class="bg_view">
  <td class="left">{lang_blurb}</td>
  <td class="center" valign="middle"><input type="password" name="{pref_id}" value="{pref_value}" /></td>
</tr>
<!-- END B_tr_passwordbox -->

<!-- BEGIN B_tr_combobox -->
<tr class="bg_view">
  <td class="left">{lang_blurb}</td>
  <td align="center" valign="middle">
    <select name="{pref_id}">
      {pref_value}
    </select>
  </td>
</tr>
<!-- END B_tr_combobox -->

<!-- BEGIN B_tr_checkbox -->
<tr class="bg_view">
  <td class="left">{lang_blurb}</td>
  <td class="center" valign="middle"><input type="checkbox" name="{pref_id}" value="{checked_flag}" {pref_value} /></td>
</tr>
<!-- END B_tr_checkbox -->

<!-- BEGIN B_submit_btn_only -->
<tr><td colspan="2" class="center"><input type="submit" name="{btn_submit_name}" value="{btn_submit_value}" /></td></tr>
<!-- END B_submit_btn_only -->

<!-- BEGIN B_submit_and_cancel_btns -->
<tr>
  <td class="center">
    <input type="hidden" name="{ex_acctnum_varname}" value="{ex_acctnum_value}" />
    <input type="submit" name="{btn_submit_name}" value="{btn_submit_value}" />
  </td>
  <td class="center"><input type="button" name="{btn_cancel_name}" value="{btn_cancel_value}" onClick="parent.location='{btn_cancel_url}'" /></td>
</tr>
<!-- END B_submit_and_cancel_btns -->

