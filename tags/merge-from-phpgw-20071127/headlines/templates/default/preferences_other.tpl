<br>
<center><h2>{title}</h2></center>
<p>

<form method="POST" action="{action_url}">
 <table border="0" cellpadding="0" cellspacing="0" width="85%" align="center">
  <tr align="center" bgcolor="{th_bg}">
   <td colspan="4" align="right">
    {template_label}:    
    <select name="headlines_layout">
     {template_options}
    </select>
   </td>
  </tr>
  <tr>
   <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
   <td>{layout_1}</td>
   <td>{layout_2}</td>
   <td>{layout_3}</td>
   <td>{layout_4}</td>
  </tr>
  <tr>
   <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
   <td colspan="4"><input type="checkbox" value="True" name="mainscreen_showheadlines"{selected_mainscreen}>{lang_mainscreen}</td>
  </tr>
  <tr>
   <td align="left">
    <input type="submit" name="submit" value="{action_label}">
   </td>
   <td colspan="2" align="center">
    <input type="submit" name="done" value="{done_label}">   
   </td>
   <td align="right">
    <input type="reset" name="reset" value="{reset_label}">
   </td>
  </tr>
 </table>
</form>
