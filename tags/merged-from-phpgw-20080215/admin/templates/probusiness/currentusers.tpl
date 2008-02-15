<!-- BEGIN list -->
  <table class="padding" align="center">
    <tr>
      {left_next_matchs}
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      {right_next_matchs}
    </tr>
    <tr class="header">
      <td>{sort_loginid}</td>
      <td>{sort_ip}</td>
      <td>{sort_login_time}</td>
      <td>{sort_action}</td>
      <td>{sort_idle}</td>
      <td>{lang_kill}</td>
    </tr>
    {rows}
  </table>

<br />
<!-- END list -->

<!-- BEGIN row -->
      <tr>
        <td class="bg_color1">{row_loginid}</td>
        <td class="bg_color2">{row_ip}</td>
        <td class="bg_color1">{row_logintime}</td>
        <td class="bg_color2">{row_action}</td>
        <td class="bg_color1">{row_idle}</td>
        <td class="bg_color2">{row_kill}</td>
      </tr>
<!-- END row -->

