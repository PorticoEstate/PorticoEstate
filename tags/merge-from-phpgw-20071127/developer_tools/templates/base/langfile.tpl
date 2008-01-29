<!-- BEGIN header -->
<form method="post" action="{action_url}">
 <table width="90%" align="center">
  <tr>
   <td>{lang_source}</td>
   <td>
    <select name="sourcelang">
{sourcelangs}
    </select>
   </td>
  </tr>
  <tr>
   <td>{lang_target}</td>
   <td>
    <select name="targetlang">
{targetlangs}
    </select>
   </td>
  </tr>
  <tr>
  <input name="app_name" type="hidden" value="{app_name}">
   <td><input type="Submit" name="submit" value="{lang_submit}"></td>
 </form>
 <form method="post" action="{cancel_link}">
   <td><input type="submit" name="cancel" value="{lang_cancel}"></td>
  </tr>
 </table>
 </form>
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
   <td align="left">{lang_translation}</td>
  </tr>
<!-- END postheader -->

<!-- BEGIN detail -->
  <tr class="{tr_color}">
   <td><input type="checkbox" name="delete[{mess_id}]"></td>
   <td>{transapp}</td>
   <td>{mess_id}</td>
   <td>{source_content}</td>
   <td><input name="translations[{mess_id}]" type="text" size="50" maxlength="255" value="{content}"></td>
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
   <td align="center">
     <input name="app_name" type="hidden" value="{app_name}">
     <input name="sourcelang" type="hidden" value="{sourcelang}">
     <input type="submit" name="dlsource" value="{lang_download}">
   </td>
<!-- END srcdownload -->

<!-- BEGIN srcwrite -->
   <td align="center">
     <input type="submit" name="writesource" value="{lang_write}">
   </td>
<!-- END srcwrite -->

<!-- BEGIN tgtdownload -->
  </tr>
  <tr>
   <td align="left">{lang_target}</td>
   <td>
     {tgt_file}
   </td>
   <td align="center">
     <input name="app_name" type="hidden" value="{app_name}">
     <input name="targetlang" type="hidden" value="{targetlang}">
     <input type="submit" name="dltarget" value="{lang_download}">
   </td>
<!-- END tgtdownload -->

<!-- BEGIN tgtwrite -->
   <td align="center">
     <input type="submit" name="writetarget" value="{lang_write}">
   </td>
<!-- END tgtwrite -->

<!-- BEGIN footer -->
</tr>
</table>
<hr>
<table width="90%" align="center">
  <tr valign="top">
     <input name="app_name"  type="hidden" value="{app_name}">
   <td align="center"><input type="submit" name="update" value="{lang_update}"></td>
  </form>
  <form method="post" action="{loaddb_url}">
     <input name="app_name"   type="hidden" value="{app_name}">
     <input name="sourcelang" type="hidden" value="{sourcelang}">
     <input name="targetlang" type="hidden" value="{targetlang}">
   <td align="center"><input  type="submit" name="loaddb" value="{lang_loaddb}"></td>
  </form>
  <form method="post" action="{missing_link}">
     <input name="app_name"  type="hidden" value="{app_name}">
     <input name="sourcelang" type="hidden" value="{sourcelang}">
     <input name="targetlang" type="hidden" value="{targetlang}">
   <td align="center"><input type="submit" name="addphrase" value="{lang_missingphrase}"></td>
  </form>
  <form method="post" action="{phrase_link}">
     <input name="app_name"  type="hidden" value="{app_name}">
     <input name="sourcelang" type="hidden" value="{sourcelang}">
     <input name="targetlang" type="hidden" value="{targetlang}">
   <td align="center"><input type="submit" name="addphrase" value="{lang_addphrase}"></td>
  </form>
  <form method="post" action="{revert_url}">
     <input name="app_name"  type="hidden" value="{app_name}">
   <td align="center"><input name="revert" type="submit" value="{lang_revert}"></td>
  </form>
  <form method="post" action="{cancel_link}">
   <td align="center"><input type="submit" name="cancel" value="{lang_cancel}"></td>
  </tr>
  </form>
</table>
<!-- END footer -->
