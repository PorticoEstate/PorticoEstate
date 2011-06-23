<!-- BEGIN list -->
  <table class="basic_noCollapse" align="center">
    {rows}
  <tr><td>
  	<form method="post" action="{cancel_action}">
    	<input type="submit" name="cancel" value="{lang_cancel}" />
  	</form>
  </td></tr>
  </table>
<!-- END list -->

<!-- BEGIN app_row -->
    <tr class="header">
      <td><img src="{app_icon}" alt="[ {app_name} ]" /><a name="{a_name}"></a></td>
      <td align="left">&nbsp;&nbsp;{app_name}</td>
    </tr>
<!-- END app_row -->

<!-- BEGIN app_row_noicon -->
    <tr><td colspan="2" class="header">&nbsp;&nbsp;{app_name}<a name="{a_name}"></a></td></tr>
<!-- END app_row_noicon -->

<!-- BEGIN link_row -->
    <tr><td class="bg_view" colspan="3">&nbsp;&#8226;&nbsp;<a href="{link_location}">{lang_location}</a></td></tr>
<!-- END link_row -->

<!-- BEGIN spacer_row -->
    <tr><td colspan="2">&nbsp;</td></tr>
<!-- END spacer_row -->

