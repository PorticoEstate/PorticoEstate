<p><b>{lang_message}</b><hr><p>
{lang_error_messages}

<form method="POST" action="{form_action}">
 <table border="0" align="center">
  <tr>
   <td>{lang_message_id}</td>
   <td>{message_id_field}</td>
  </tr>
  <tr>
   <td>{lang_translation}</td>
   <td>{translation_field}</td>
  </tr>
  <tr>
{app_name}
     <input name="entry[app_name]" type="hidden" value="{app_name}">
     <input name="app_name"  type="hidden" value="{app_name}">
     <input name="sourcelang" type="hidden" value="{sourcelang}">
     <input name="targetlang" type="hidden" value="{targetlang}">
   <td colspan="2" align="center"><input type="submit" name="submit" value="{lang_button}"></td>
  </tr>
 </table>
</form>
