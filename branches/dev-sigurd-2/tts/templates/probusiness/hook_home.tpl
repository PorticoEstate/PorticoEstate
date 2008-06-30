<!-- BEGIN tts_list -->

  <table class="padding">
    <tr>
      <td class="center">{tts_head_id}</td>
      <td class="center">{tts_head_openedby}</td>
      <td class="center">{tts_head_dateopened}</td>
      <td class="center">{tts_head_subject}</td>
    </tr>
    {rows}
  </table>

<!-- END tts_list -->

<!-- BEGIN tts_row -->
    <tr>
      <td class="center">{tts_ticket_id}</td>
      <td class="center">{tts_t_user}</td>
      <td class="center">{tts_t_timestampopened}</td>
      {tts_col_status}
      <td class="center"><a href="{tts_ticketdetails_link}">{tts_t_subject}</a></td>
    </tr>
<!-- END tts_row -->

<!-- BEGIN tts_ticket_id_unread -->
&gt;{tts_t_id}&lt;
<!-- END tts_ticket_id_unread -->

<!-- BEGIN tts_ticket_id_read -->
{tts_t_id}
<!-- END tts_ticket_id_read -->

