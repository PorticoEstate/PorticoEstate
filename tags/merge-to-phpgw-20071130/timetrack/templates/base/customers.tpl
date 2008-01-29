<!-- BEGIN header -->
<p>
 <table border="0" width="65%" align="center">
  <tr bgcolor="{bg_color}">
   <td align="left">{left_next_matchs}</td>
   <td align="center"><h3>{lang_customer_list}</h3></td>
   <td align="right">{right_next_matchs}</td>
  </tr>
 </table>

 <center>
  <table border=0 width=65%>
   <tr bgcolor="{th_bg}">
    <th>{lang_company_name}</th>
    <th>{lang_industry_type}</th>
    <th>{lang_status}</th>
    <th>{lang_edit}</th>
    <th>{lang_delete}</th>
    <th>{lang_view}</th>
   </tr>
<!-- END header -->

<!-- BEGIN row -->
   <tr bgcolor="{tr_color}">
    <td>{row_company_name}</td>
    <td>{row_industry_type}</td>
    <td>{row_status}</td>
    <td width="5%">{row_edit}</td>
    <td width="5%">{row_delete}</td>
    <td width="5%">{row_view}</td>
   </tr>
<!-- END row -->

<!-- BEGIN footer -->
  </table>
 </center>

 <form method="POST" action="{actionurl}">
  <table border="0" width="65%" align="center">
   <tr>
    <td align=left>
     <input type="submit" value="{lang_add}"></form>
    </td>
    <td align="right">
     <form method="POST" action="{queryurl}">
      {lang_search}&nbsp;
      <input name="query">
     </form>
    </td>
   </tr>
  </table>

<!-- END footer -->
