<!-- BEGIN list -->
<p><b>{title}</b>
<hr><br>
<table border="0" width="65%" align="center">
 <tr bgcolor="{th_bg}">
  <td>{lang_site}</td>
  <td width="5%">{lang_edit}</td>
  <td width="5%">{lang_delete}</td>
  <td width="5%">{lang_view}</td>
 </tr>
 {rows}

 <form method="POST" action="{add_url}">
 <tr>
  <td colspan="5"><input type="submit" value="{lang_add}"></td>
 </tr>
 </form>
 <tr>
  <td colspan="5"><a href="{grab_more_url}">{lang_grab_more}</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="{reload_url}">{lang_reload}</a></td>
 </tr>
</table>
<!-- END list -->

<!-- BEGIN row -->
 <tr bgcolor="{tr_color}">
  <td>{row_display}&nbsp;</td>
  <td width="5%"><a href="{row_edit}">{lang_edit}</a></td>
  <td width="5%"><a href="{row_delete}">{lang_delete}</a></td>
  <td width="5%"><a href="{row_view}">{lang_view}</a></td>
 </tr>
<!-- END row -->

<!-- BEGIN row_empty -->
 <tr bgcolor="{tr_color}">
  <td colspan="4" align="center">{lang_row_empty}</td>
 </tr>
<!-- END row_empty -->
