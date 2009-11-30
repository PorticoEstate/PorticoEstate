<!-- BEGIN list -->
<p><b>{title}</b>
<hr><br>
<table border="0" width="65%" align="center" cellspacing="0">
 <tr class="th">
  <td>{lang_server}</td>
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
</table>
<!-- END list -->
<!-- BEGIN row -->
 <tr class="{class_row}">
  <td>{server_name}&nbsp;</td>
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
<p>&nbsp;</p>