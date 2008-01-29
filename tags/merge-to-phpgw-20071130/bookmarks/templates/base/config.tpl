<!-- BEGIN header -->
<form method="POST" action="{action_url}">
 <table border="0" align="center" width="50%">
<!-- END header -->

<!-- BEGIN body -->
  <tr bgcolor="{th_bg}">
   <td colspan="2"><font color="{th_text}">&nbsp;<b>{title} - {lang_bookmarks_settings}</b></font></td>
  </tr>
  <tr bgcolor="{row_off}">
   <td colspan="2">{lang_Append_this_message_to_mailed_bookmarks}:</td>
  </tr>
  <tr bgcolor="{row_off}">
   <td colspan="2" align="center"><textarea rows="7" cols="50" name="newsettings[mail_footer]" wrap="hard">{value_mail_footer}</textarea></td>
  </tr>
<!-- END body -->

<!-- BEGIN footer -->
  <tr>
   <td align="left"><input type="submit" name="submit" value="{lang_submit}"></td>
   <td align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
  </tr>
 </table>
</form>
<!-- END footer -->
