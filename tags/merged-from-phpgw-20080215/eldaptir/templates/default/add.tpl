<!-- BEGIN header -->
 <center>
  <table border="0" width="70%">
    <form method="POST" action="{action_url}">
   <tr>
    <td align="left" colspan="3">{title}</td>
   </tr>
   <tr bgcolor="{th_bg}">
    <td>{lang_dn}:</td>
    <td colspan="3">{dn}</td>
   </tr>
   <tr bgcolor="{th_bg}">
    <td>{lang_obj}</td>
    <td>{lang_attr}</td>
    <td>{lang_value}</td>
    <td>{lang_rule}</td>
   </tr>
<!-- END header -->

<!-- BEGIN row -->
   <tr bgcolor="{tr_color}">
     <td>{objectclass}</td><td>{row_name}</td><td>{row_value}</td><td>{row_rule}</td>
   </tr>
<!-- END row -->

<!-- BEGIN footer -->
   <tr>
    <td>
      {hidden_vars}
      <input type="submit" name="submit" value="{lang_submit}">
    </td>
   </tr>
   </form>
  </table>
 </center>

  <form method="POST" action="{addobj_url}">
  <table border="0" width="70%" align="center">
   <tr>
    <td><input type="submit" value="{lang_addobj}"></td>
   </tr>
  </table>
 </form>

 <form method="POST" action="{cancel_url}">
  <table border="0" width="70%" align="center">
   <tr>
    <td align="left">
      <input type="submit" value="{lang_cancel}">
    </td>
   </tr>
  </table>
 </form>
<!-- END footer -->
