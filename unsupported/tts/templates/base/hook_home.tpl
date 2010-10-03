<!-- $Id$ -->
<!-- BEGIN tts_list -->
  <table width="100%" cellspacing="1" cellpadding="1" border="0">
   <tr bgcolor="{tts_head_bgcolor}">
    <td align="center">{tts_head_id}</td>
    <td align="center">{tts_head_openedby}</td>
    <td align="center">{tts_head_dateopened}</td>
    <td align="center">{tts_head_subject}</td>
   </tr>
   {rows}
  </table>
<!-- END tts_list -->

<!-- BEGIN tts_row -->
   <tr bgcolor="{tts_row_color}">
     <td align="center">{tts_ticket_id}</td>
     <td align="center">{tts_t_user}</td>
     <td align="center">{tts_t_timestampopened}</td>
     {tts_col_status}
     <td align="center"><a href="{tts_ticketdetails_link}">{tts_t_subject}</a></td>
   </tr>
<!-- END tts_row -->

<!-- BEGIN tts_ticket_id_unread -->
&gt;{tts_t_id}&lt;
<!-- END tts_ticket_id_unread -->

<!-- BEGIN tts_ticket_id_read -->
{tts_t_id}
<!-- END tts_ticket_id_read -->
