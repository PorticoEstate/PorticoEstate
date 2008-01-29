<!-- BEGIN list -->
  <table class="basic" align="center">
    <tr>
      <td class="left">{tabs}</td>
    </tr>
  </table>
  <table class="basic_noCollapse" align="center">
    {rows}
  </table>
<!-- END list -->

<!-- BEGIN app_row -->
    <tr class="header">
      <td class="middle"><img src="{app_icon}" alt="[ {app_name} ]" /><a name="{a_name}"></a></td>
      <td width="95%" class="middle">&nbsp;&nbsp;{app_name}</td>
    </tr>
<!-- END app_row -->

<!-- BEGIN app_row_noicon -->
    <tr><td colspan="2" width="95%" class="middle">&nbsp;&nbsp;{app_name}<a name="{a_name}"></a></td></tr>
<!-- END app_row_noicon -->

<!-- BEGIN link_row -->
    <tr><td colspan="2">&nbsp;&#8226;&nbsp;<a href="{pref_link}">{pref_text}</a></td></tr>
<!-- END link_row -->

<!-- BEGIN spacer_row -->
    <tr><td colspan="2">&nbsp;</td></tr>
<!-- END spacer_row -->

