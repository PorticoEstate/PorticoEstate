<!-- BEGIN form -->
<b>{lang_changepassword} {value_username}</b><hr><p>

   <center>{errors}</center>

   <form method="POST" action="{form_action}">
    <table border="0">
     <tr>
       <td>
        {lang_enter_password}
       </td>
       <td>
        <input type="password" name="r_reg[passwd]">
       </td>
     </tr>
     <tr>
       <td>
        {lang_reenter_password}
       </td>
       <td>
        <input type="password" name="r_reg[passwd_2]">
       </td>
     </tr>
     <tr>
       <td colspan="2">
        <input type="submit" name="submit" value="{lang_change}">
       </td>
     </tr>
    </table>
   </form>
   <br>
   <pre>{sql_message}</pre>
<!-- END form -->
