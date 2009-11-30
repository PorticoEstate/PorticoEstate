<!-- $Id$ -->
<form enctype="multipart/form-data" method="post" action="{FORM_ACTION}">
 <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
 <table border=0 bgcolor="#EEEEEE" align="center" width=60%>
 <tr>
  <td colspan=2>{lang_name}
   <br>&nbsp;
  </td>
 </tr>
 <tr>
  <td align=left>{lang_file}</td>
  <td align=left><input type="file" name="bkfile"></td>
 </tr>
 <tr>
  <td align=left>{lang_catchoose}</td>
  <td align=left>{input_categories}</td>
 </tr>
 <tr>
  <td colspan=2 align=right>
   <input type="submit" name="import" value="{lang_import_button}">
  </td>
 </tr>
 <tr>
  <td colspan="2" align="left">{lang_note}</td>
 </tr>
</table>
</form>
