<!-- $Id$ -->
<!-- BEGIN alarm_management -->
<form action="{action_url}" method="post" name="alarmform">
{hidden_vars}
<center>
  <table border="0" width="90%">
   {rows}
  </table>
</center>
<p>
<center>
<b>{input_text}</b><p>
{input_days}&nbsp;{input_hours}&nbsp;{input_minutes}&nbsp;{input_owner}
<p>
{input_add}
</center>
</form>
<!-- END alarm_management -->
<!-- BEGIN alarm_headers -->
  <tr class="th">
   <td align="left" width="25%">{lang_time}</td>
   <td align="left" width="30%">{lang_text}</td>
   <td align="left" width="25%">{lang_owner}</td>
   <td width="10%">{lang_enabled}</td>
   <td width="10%">{lang_select}</td>
  </tr>
<!-- END alarm_headers -->
<!-- BEGIN list -->
  <tr class="{class}">
   <td>{field}:</td>
   <td>{data}</td>
   <td>{owner}</td>
   <td align="center">{enabled}</td>
   <td align="center">{select}</td>
  </tr>
<!-- END list -->
<!-- BEGIN hr -->
 <tr bgcolor="{th_bg}">
  <td colspan="5" align="center"><b>{hr_text}</b></td>
 </tr>
<!-- END hr -->
<!-- BEGIN buttons -->
 <tr>
  <td colspan="6" align="right">
   {enable_button}&nbsp;{disable_button}&nbsp;{delete_button}
  </td>
 </tr>
<!-- END buttons -->
