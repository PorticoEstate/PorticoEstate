<!-- BEGIN tab_body_general_data -->
<td>
  <table>
    <tbody>
      <tr>
        <td>
          <table align="left">
            <tbody>
              <tr><th class="header">{lang_general_data}</th></tr>
              <tr>
                <td class="top">
                  <input type="hidden" name="{current_id_name}" value="{current_id}" />
                  <input type="hidden" name="{current_action_name}" value="{current_action}" />
                  <table class="padding" align="center">
                    <tbody>
                      {input_fields}
                      {other_fields}
                      {detail_fields}
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
<!-- END tab_body_general_data -->

<!-- BEGIN input_data -->
                      {input_fields_cols}
<!-- END input_data -->

<!-- BEGIN input_other_data -->
                      {input_other_fields_cols}
<!-- END input_other_data -->

<!-- BEGIN input_data_col -->
                      <tr>
                        <td class="bg_color2" width="15%"><span class="no_wrap">{field_name_one}</span></td>
                        <td class="bg_color1" width="35%"><input type="text" name="{input_name_one}" value="{input_value_one}" /></td>
                        <td class="bg_color2" width="15%"><span class="no_wrap">{field_name_two}</span></td>
                        <td class="bg_color1" width="35%"><input type="text" name="{input_name_two}" value="{input_value_two}" /></td>
                      </tr>
<!-- END input_data_col -->

<!-- BEGIN other_data -->
                      <tr>
                        <td width="15" class="bg_color2"><span class="no_wrap">{field_other_name1}</span></td>
                        <td width="35" class="bg_color1"><span class="no_wrap">{value_other_name1}</span></td>
                        <td width="15" class="bg_color2"><span class="no_wrap">{field_other_name2}</span></td>
                        <td width="35" class="bg_color1"><span class="no_wrap">{value_other_name2}</span></td>
                      </tr>
<!-- END other_data -->

