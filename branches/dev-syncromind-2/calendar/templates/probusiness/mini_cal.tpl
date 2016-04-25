
<!-- BEGIN mini_cal -->
<table class="calendar_table" valign="top" cols="7">
  <tr class="middle">
    <td class="left">&nbsp;&nbsp;</td>
    <td class="header" align="center" colspan="0">
      <span class="calender_font" style="white-space:nowrap"><b>{month}</b></span>
    </td>
    <td class="right">{prevmonth}&nbsp;&nbsp;{nextmonth}</td>
  </tr>
  <tr>
    <td class="center" colspan="7"><img src="{cal_img_root}" width="90%" height="5" /></td>
 </tr>
 <tr class="top">
  <td colspan="7">
   <table valign="top" cols="7">
   	<tr><span class="calender_font">{daynames}</span></tr>
     {display_monthweek}
    </table>
  </td>
 </tr>
</table>
<!-- END mini_cal -->
<!-- BEGIN mini_week -->
    <tr>{monthweek_day}</tr>
<!-- END mini_week -->
<!-- BEGIN mini_day -->
     <td class="center" {day_image}>{dayname}</td>
<!-- END mini_day -->
