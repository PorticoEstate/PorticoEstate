
<!-- BEGIN day -->
<table class="basic">
 {row}
</table>
<!-- END day -->
<!-- BEGIN day_row -->
    <tr>{item}
    </tr>
<!-- END day_row -->
<!-- BEGIN day_event_on -->
     
     <td width="100%" class="bg_color1"{extras}>{event}</td>
<!-- END day_event_on -->
<!-- BEGIN day_event_off -->
     <td class="bg_color2"{extras}>{event}</td>
<!-- END day_event_off -->
<!-- BEGIN day_event_holiday -->
     <td class="event-holiday"{extras}>{event}</td>
<!-- END day_event_holiday -->
<!-- BEGIN day_time -->
     	
     <td class="time" nowrap>{open_link}{time}{close_link}</td>

<!-- END day_time -->

