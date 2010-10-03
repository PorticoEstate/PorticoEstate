<form name="search_frm" action="{form_action}" method="post">
    <table align="center">
      <tbody>
        <tr>
          <td colspan="2">
            <table>
              <tbody>
                <tr>
                  <td colspan="2" class="header">
                    <div class="center"><b>{lang_search_string}</b></div>
                    <table align="center" width="100%">
                      <tbody>
                        <tr class="bg_color1">
                          <td class="left" valign="middle">{lang_subject}</td>
                          <td><input size="30" name="search_subject" maxlength="50" /></td>
                        </tr>
                        <tr class="bg_color2">
                          <td class="left" valign="middle">{lang_from}<br /></td>
                          <td><input size="30" name="search_from" maxlength="50" /></td>
                        </tr>
                        <tr class="bg_color1">
                          <td class="left" valign="middle">Body</td>
                          <td><input size="30" name="search_body" maxlength="50" /></td>
                        </tr>
                        <tr class="bg_color2">
                          <td class="left" valign="middle">{lang_to}<br /></td>
                          <td><input size="30" name="search_to" maxlength="50" /></td>
                        </tr>
                        <tr class="bg_color1">
                          <td class="left" valign="middle">{lang_cc}<br /></td>
                          <td><input size="30" name="search_cc" maxlength="50" /></td>
                        </tr>
                        <tr class="bg_color2">
                          <td class="left" valign="middle">{lang_bcc}<br /></td>
                          <td><input size="30" name="search_bcc" maxlength="50" /></td>
                        </tr>
                        <tr class="bg_color1">
                          <td class="left" valign="middle">{lang_keyword}<br /></td>
                          <td><input size="30" name="search_keyword" maxlength="50" /></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td>
                    <table width="100%">
                      <tr><td class="header"><b>{lang_search}</b></td></tr>
<!-- BEGIN folder -->
                      <tr><td class="bg_color1"><input type="checkbox" name="folder_list[]" value="{fld_value}" {fld_checked} />&nbsp;{fld_value}</td></tr>
<!-- END folder -->
                    </table>
                  </td>
                </tr>
                <tr>
                  <td>
                    <table>
                      <tbody>
                        <tr>
                          <td>
                            <table width="100%">
                              <tbody>
                                <tr class="header"><td valign="top" colspan="2" class="center"><b>{lang_return_mails_during}<b><br /></td></tr>
                                <tr>
                                  <td valign="top" class="bg_color2"><input type="checkbox" name="date_on" />&nbsp;&nbsp;&nbsp;{lang_on}</td>
                                  <td valign="top" class="bg_color2">
                                    <select name="date_on_month">
<!-- BEGIN month_on -->
                                      <option value="{month_value}" {selected}>{month_option}</option>
<!-- END month_on -->
                                    </select>
                                    <select name="date_on_day">
<!-- BEGIN day_on -->
                                      <option value="{day_option}" {selected}>{day_option}</option>
<!-- END day_on -->
                                    </select>
                                    <select name="date_on_year">
<!-- BEGIN year_on -->
                                      <option value="{year_option}" {selected}>{year_option}</option>
<!-- END year_on -->
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td valign="top" class="bg_color2"><input type="checkbox" name="date_before" />&nbsp;{lang_before}</td>
                                  <td valign="top" class="bg_color2">
                                    <select name="date_before_month">
<!-- BEGIN month_before -->
                                      <option value="{month_value}" {selected}>{month_option}</option>
<!-- END month_before -->
                                    </select>
                                    <select name="date_before_day">
<!-- BEGIN day_before -->
                                      <option value="{day_option}" {selected}>{day_option}</option>
<!-- END day_before -->
                                    </select>
                                    <select name="date_before_year">
<!-- BEGIN year_before -->
                                      <option value="{year_option}" {selected}>{year_option}</option>
<!-- END year_before -->
                                    </select>
                                  </td>
                                </tr>
                                <tr>
                                  <td valign="top" class="bg_color2"><input type="checkbox" name="date_after">&nbsp;&nbsp;{lang_after}</td>
                                  <td class="bg_color2" valign="top">
                                    <select name="date_after_month">
<!-- BEGIN month_after -->
                                      <option value="{month_value}" {selected}>{month_option}</option>
<!-- END month_after -->
                                    </select>
                                    <select name="date_after_day">
<!-- BEGIN day_after -->
                                      <option value="{day_option}" {selected}>{day_option}</option>
<!-- END day_after -->
                                    </select>
                                    <select name="date_after_year">
<!-- BEGIN year_after -->
                                      <option value="{year_option}" {selected}>{year_option}</option>
<!-- END year_after -->
                                    </select>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
          <td width="30%" valign="top">
            <table>
              <tbody>
                <tr>
                  <td>
                    <table>
                      <tbody>
                        <tr><td class="header"><div class="center"><b>{lang_check_flags}</b></div></td></tr>
<!-- BEGIN flag -->
                        <tr><td class="bg_color1"><input type="checkbox" name="{flg_name}" />&nbsp;{flg_value}</td></tr>
<!-- END flag -->
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <br />
          </td>
        </tr>
        <tr>
          <td class="left" id="5"><input type="submit" value="{lang_search_button}" /></td>
          <td class="left"><input type="reset" value="{lang_clear_form_button}" /></td>
          <td>&nbsp;</td>
        </tr>
      </tbody>
    </table>
  <br />
</form>

