<!-- BEGIN form -->
 <p>
 <table border="0" align="center">
  <tr bgcolor="{th_bg}">
   <td>{sort_answer}</td>
   <td>{sort_title}</td>
   <td>{lang_edit}</td>
   <td>{lang_delete}</td>
  </tr>
  
  {rows}

 </table>
 
 <form method="POST" action="{add_action}">
  <center><input type="submit" name="add" value="{lang_add}"></center>
 </form>
<!-- END form -->

<!-- BEGIN row -->
  <tr bgcolor="{tr_color}">
   <td>{row_answer}</td>
   <td>{row_title}</td>
   <td>{row_edit}</td>
   <td>{row_delete}</td>
  </tr>
<!-- END row -->
