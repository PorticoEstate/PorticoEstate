<!-- BEGIN many_actions -->
<td class="left">
  <table class="basic">
    <tbody>
      <tr><th class="header">{lang_general_data}</th></tr>
      <tr>
        <td>
          <table class="padding" align="center">
            <tbody>
              <tr>
                <td>{lang_person}{person}</td>
                <td></td>
                <td>{lang_defaul}{options}</td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table class="padding" align="center">
            <tbody class="center">
              <tr>
                <td>{lang_title_left}</td>
                <td></td>
                <td>{lang_title_rigth}</td>
              </tr>
              <tr>
                <td>{options_left}</td>
                <td>
                  <table align="center">
                    <tbody class="center">
                      <tr><td><input type="button" onClick="move('{all_opt}','{my_opt}','{current_opt}','{my_opt}')" value=">>" /></td></tr>
                      <tr><td><input type="button" onClick="move('{my_opt}','{all_opt}','{current_opt}','{my_opt}')" value="<<" /></td></tr>
                    </tbody>
                  </table>
                </td>
                <td>{options_rigth}</td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
</td>
<!-- END many_actions -->
