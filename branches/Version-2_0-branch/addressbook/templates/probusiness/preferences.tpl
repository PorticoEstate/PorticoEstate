<h3>{lang_abprefs}</h3>
<hr />
<br />
  <table class="basic" align="left">
    <tbody>
      <tr><td class="header">{lang_select_cols}</td></tr>
      <tr>
        {tabs}
      </tr>
      <tr>
        <td>
        <form name="{select_columns_form_name}" action="{select_columns_form_action}" method="post">
          <table>
            <tr>
              <td>
                <select name="{select_columns_selectbox_name}" multiple size="5">
                  {B_select_columns_form_options}
                </select>
              </td>
              <td>
                <select name="{select_columns_comtypes_name}" multiple size="5">
                  {B_select_ctypes_options}
                </select>
              </td>
            </tr>
            <tr><td class="center"><input type="submit" value="{select_columns_submit_value}" name="select_fields" /></td></tr>
          </table>
      </td>
    </tr>
    <tr><td><hr /></td></tr>
    <tr>
      <td>
        <table align="left">
          <tr><td class="header"><span>Select your default category</span></td></tr>
          <tr>
            <td class="left">
              <select name='cat_id'>
                {cat_options}
              </select>
            </td>
          </tr>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
  <br />
  <table align="left">
    <tbody>
      <tr>
        {B_selected_rows}
      </tr>
      <tr><td colspan="2"><hr /></td></tr>
      <tr>
        <td>
          <input type="submit" value="{submit_save_value}" name="save" />
          <input type="submit" value="{submit_cancel_value}" name="cancel" />
        </td>
      </tr>
    </tbody>
  </table>
</form>

