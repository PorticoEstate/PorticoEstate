<!-- BEGIN form -->
 <p>
 <b>{header_message}</b>
 <hr><p>

 <center>{message}</center>

 <form method="POST" action="{form_action}">
 <input type="hidden" name="poll_id" value="{poll_id}">
  <table border="0" align="center">
   <tr bgcolor="{th_bg}">
    <td colspan="2">&nbsp;{td_message}</td>
   </tr>

   {rows}

   <tr>
    <td align="center">{form_button_1}</td>
    <td align="center">{form_button_2}</td>
   </tr>
  </table>
 </form>
<!-- END form -->

<!-- BEGIN row -->
   <tr bgcolor="{tr_color}">
    <td>{td_1}</td>
    <td>{td_2}</td>
   </tr>
<!-- END row -->
