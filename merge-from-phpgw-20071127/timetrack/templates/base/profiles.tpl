<!-- BEGIN header -->
<p>
 <table border="0" width="65%" align="center">
  <tr bgcolor="{bg_color}">
   <td align="left">{left_next_matchs}</td>
   <td align="center"><h3>{lang_profile_list}</h3></td>
   <td align="right">{right_next_matchs}</td>
  </tr>
 </table>

 <center>
  <table border=0 width=65%>
   <tr bgcolor="{th_bg}">
    <th>{lang_loginid}</th>
    <th>{lang_firstname}</th>
    <th>{lang_lastname}</th>
    <th>{lang_edit}</th>
    <th>{lang_view}</th>
    <th>{lang_status}</th>
   </tr>
<!-- END header -->

<!-- BEGIN row -->
   <tr bgcolor="{tr_color}">
    <td width="20%">{row_loginid}</td>
    <td width="28%">{row_firstname}</td>
    <td width="28%">{row_lastname}</td>
    <td width="7%">{row_edit}</td>
    <td width="7%">{row_view}</td>
    <td width="10%">{row_status}</td>
   </tr>
<!-- END row -->

<!-- BEGIN footer -->
  </table>

  <table border="0" width="65%" align="center">
   <tr>
    <td align="right">
     <form method="POST" action="{queryurl}">
      {lang_search}&nbsp;
      <input name="query">
     </form>
    </td>
   </tr>
  </table>

  {notice_profiles_created}
 </center>

<!-- END footer -->
