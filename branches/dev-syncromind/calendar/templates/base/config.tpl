<!-- BEGIN header -->
<form method="POST" action="{action_url}">
<table class="pure-table pure-table-bordered">
   <tr bgcolor="{th_bg}">
    <td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
   </tr>
<!-- END header -->

<!-- BEGIN body -->
   <tr bgcolor="{row_on}">
    <td colspan="2">&nbsp;</td>
   </tr>

   <tr bgcolor="{row_off}">
    <td colspan="2"><b>{lang_Calendar_settings}</b></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_Do_you_wish_to_autoload_calendar_holidays_files_dynamically?}</td>
    <td>
     <select name="newsettings[auto_load_holidays]">
      <option value=""{selected_auto_load_holidays_False}>{lang_No}</option>
      <option value="True"{selected_auto_load_holidays_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>
   <tr bgcolor="{row_off}">
    <td>{lang_Location_to_autoload_from}:</td>
    <td>
     <select name="newsettings[holidays_url_path]">
      <option value="localhost"{selected_holidays_url_path_localhost}>localhost</option>
      <option value="http://www.phpgroupware.org/cal"{selected_holidays_url_path_http://www.phpgroupware.org/cal}>www.phpgroupware.org</option>
     </select>
    </td>
   </tr>
<!-- END body -->

<!-- BEGIN footer -->
  <tr bgcolor="{th_bg}">
    <td colspan="2">
&nbsp;
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" name="submit" value="{lang_submit}">
      <input type="submit" name="cancel" value="{lang_cancel}">
    </td>
  </tr>
</table>
</form>
<!-- END footer -->
