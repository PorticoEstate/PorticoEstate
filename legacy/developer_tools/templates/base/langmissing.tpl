<!-- BEGIN header -->
 <hr>
<!-- END header -->

<!-- BEGIN postheader -->
 <table width="90%" align="center">
  <tr class="th">
   <td colspan="5" align="center">{lang_application}:&nbsp;{app_name}</td>
  </tr>
  <tr class="th">
   <form method="post" action="{action_url}">
   <td align="left">{lang_remove}</td>
   <td align="left">{lang_appname}</td>
   <td align="left">{lang_message}</td>
   <td align="left">{lang_original}</td>
  </tr>
<!-- END postheader -->

<!-- BEGIN detail -->
  <tr class="{tr_color}">
   <td><input type="checkbox" name="delete[{mess_id}]"></td>
   <td>{transapp}</td>
   <td>{mess_id}</td>
   <td>{source_content}</td>
   <!td><!input name="translations[{mess_id}]" type="text" size="50" maxlength="255" value="{content}"><!/td>
  </tr>
<!-- END detail -->

<!-- BEGIN prefooter -->
</table>
<hr>
<table width="90%" align="center">
<!-- END prefooter -->

<!-- BEGIN srcdownload -->
  <tr>
   <td align="left">{lang_source}</td>
   <td>
     {src_file}
   </td>
   <!td align="center">
     <!input name="app_name" type="hidden" value="{app_name}">
     <!input name="sourcelang" type="hidden" value="{sourcelang}">
     <!input type="submit" name="dlsource" value="{lang_download}">
   <!/td>
<!-- END srcdownload -->

<!-- BEGIN srcwrite -->
   <td align="center">
     <input type="submit" name="writesource" value="{lang_write}">
   </td>
<!-- END srcwrite -->

<!-- BEGIN footer -->
</tr>
</table>
<table width="90%" align="center">
  <tr valign="top">
	
     <input name="app_name"  type="hidden" value="{app_name}">
   <td align="center"><input type="submit" name="update" value="{lang_update}">
  </td>
  </form>
  <form method="post" action="{view_link}">
   <td align="center"><input type="submit" name="edit" value="{lang_view}">
</td>

  </tr>
  </form>
</table>
<!-- END footer -->
