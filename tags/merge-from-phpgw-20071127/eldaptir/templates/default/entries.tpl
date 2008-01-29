<!-- BEGIN list -->
<p>
 <table border="0" width="70%" align="center">
  <tr>
   <td align="left">{title}</td>
  </tr>
  <tr>
   <td align="center">{lang_showing}{lang_user_accounts}</td>
  </tr>
 </table>
 <table border="0" width="70%" align="center">
  <tr>
   <td></td>
   <td align="center">{next_matchs}</td>
   <td></td>
  </tr>
 </table>
 <center>
  <table border="0" width="70%">
   <tr bgcolor="{th_bg}">
    <td>{lang_loginid}</td>
    <td>{lang_firstname}</td>
    <td>{lang_lastname}</td>
    <td>{lang_edit}</td>
    <td>{lang_delete}</td>
    <td>{lang_view}</td>
   </tr>

   {rows}

  </table>
 </center>

  <table border="0" width="70%" align="center">
   <tr>
    <td align="left">
     <form method="POST" action="{action_url}">
     <input type="submit" value="{lang_add}">
     </form>
    </td>
    <td align="left">
     <form method="POST" action="{cancel_url}">
     <input type="submit" value="{lang_cancel}">
     </form>
    </td>
   </tr>
  </table>
<!-- END list -->

<!-- BEGIN row -->
   <tr bgcolor="{tr_color}">
    <td>{row_loginid}</td>
    <td>{row_firstname}</td>
    <td>{row_lastname}</td>
    <td width="5%">{row_edit}</td>
    <td width="5%">{row_delete}</td>
    <td width="5%">{row_view}</td>
   </tr>
<!-- END row -->

<!-- BEGIN empty_row -->
   <tr>
    <td colspan="5" align="center">{message}</td>
   </tr>
<!-- END empty_row -->
