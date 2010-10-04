<!-- BEGIN header -->
<form method="POST" action="{action_url}">
<table class="calendarCenter" align="center">
  <tr>
    <td class="header" colspan="2" align="center">&nbsp;<b>{title}</b></td>
  </tr>
<!-- END header -->

<!-- BEGIN body -->
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>

   <tr class="bg_color1">
    <td colspan="2"><b>{lang_Calendar_settings}</b></td>
   </tr>
   <tr class="bg_color2">
    <td>{lang_Do_you_wish_to_autoload_calendar_holidays_files_dynamically?}</td>
    <td>
     <select name="newsettings[auto_load_holidays]">
      <option value=""{selected_auto_load_holidays_False}>{lang_No}</option>
      <option value="True"{selected_auto_load_holidays_True}>{lang_Yes}</option>
     </select>
    </td>
   </tr>
   <tr class="bg_color1">
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
  <tr class="bg_color2">
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td class="center">
      <input type="submit" name="submit" value="{lang_submit}" />
    </td>
    <td class="left">
      <input type="submit" name="cancel" value="{lang_cancel}" />
    </td>
  </tr>
</table>
</form>
<!-- END footer -->
