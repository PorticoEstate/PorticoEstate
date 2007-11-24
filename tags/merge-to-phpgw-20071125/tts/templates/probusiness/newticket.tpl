<!-- BEGIN options_select -->
    <option value="{optionvalue}" {optionselected}>{optionname}</option>
<!-- END options_select -->

<!-- BEGIN form -->
<b>{lang_create_new_ticket}</b>
<hr />
<p></p>
{messages}
<form method="post" action="{form_action}" enctype="multipart/form-data">
  <table class="basic_with_background-color" align="center">
            <tr><td class="header" colspan="4">&nbsp;</td></tr>
            <tr>
              <td class="bg_color1" align="left">{lang_assignedto}:</td>
              <td class="bg_color1" align="left">{value_assignedto}</td>
              <td class="bg_color1" align="left">{lang_billable_hours}:</td>
              <td class="bg_color1" align="left"><input name="ticket[billable_hours]" value="{value_billable_hours}" /></td>
            </tr>
            <tr class="bg_color2">
              <td class="bg_color2" align="left">{lang_priority}:</td>
              <td class="bg_color2" align="left">{value_priority}</td>
              <td class="bg_color2" align="left">{lang_billable_hours_rate} {currency}:</td>
              <td class="bg_color2" align="left"><input name="ticket[billable_rate]" value="{value_billable_hours_rate}" /></td>
            </tr>
            <tr>
              <td class="bg_color1" align="left">{lang_group}:</td>
              <td class="bg_color1" align="left"><select name="ticket[group]">{options_group}</select></b></td>
              <td class="bg_color1" align="left">{lang_category}:</td>
              <td class="bg_color1" align="left">{value_category}</td>
            </tr>
            <tr>
              <td class="bg_color2" align="left"></td>
              <td class="bg_color2" align="left"></td>
              <td class="bg_color2" align="left">{lang_type}:</td>
              <td class="bg_color2" align="left">{value_type}</td>
            </tr>
            <tr>
              <td class="bg_color1" align="left">{lang_deadline}:</td>
              <td class="bg_color1" align="left"><input type="text" name="ticket[deadline][year]" size="4" /> . {option_month} . {option_day}</td>
              <td class="bg_color1" align="left">{lang_effort}:</td>
              <td class="bg_color1" align="left"><input type="text" name="ticket[effort]" /></td>
            </tr>
            <tr>
              <td class="bg_color2" align="left">{lang_platform}:</td>
              <td class="bg_color2" align="left">{value_platform}</td>
              <td class="bg_color2" align="left">{lang_attachment}</td>
              <td class="bg_color2" align="left"><input type="file" name="attachment" /></td>
            </tr>
            <tr><td class="header" colspan="4" class="center">&nbsp;</td></tr>
            <tr><td class="bg_color1" colspan="4">{lang_subject}:</td></tr>
            <tr><td class="header" colspan="4"><input name="ticket[subject]" value="{value_subject}" size="65" /></td></tr>
            <tr><td class="bg_color1" colspan="4">&nbsp;</td></tr>
            <tr>
              <td class="header" colspan="4">
                {lang_details}:<br />
                <textarea rows="10" name="ticket[details]" cols="65" wrap="hard">{value_details}</textarea>
              </td>
            </tr>
            <tr>
              <td class="bg_color1" align="left"><input type="submit" name="submit" value="{lang_submit}" /></td>
              <td class="bg_color1" colspan="2">&nbsp;</td>
              <td class="bg_color1" class="right"><input type="submit" name="cancel" value="{lang_cancel}" /></td>
            </tr>
  </table>
</form>
<!-- END form -->

