<!-- BEGIN form -->
<center>{errors}</center>
<form action="{form_action}" method="POST">
 <table border="0" width="40%" align="center">
  <p>
    {lang_explain}
  </p>
  <tr>
   <td>{lang_username}</td>
   <td><input name="r_reg[loginid]" value="{value_username}"></td>
  </tr>
 
  <tr>
   <td colspan="2"><input type="submit" name="submit" value="{lang_submit}"></td>
  </tr>
 </table>
</form>
<!-- END form -->
