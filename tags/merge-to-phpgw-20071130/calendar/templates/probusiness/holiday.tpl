
<!-- BEGIN form -->
{message}<br />
<table class="basic" align="center">
   <tr><td colspan="2" class="header">&nbsp;</td></tr> 
 <form name="form" action="{actionurl}" method="POST">
  {hidden_vars}
{rows}
 <tr>
  <td colspan="2">
   <table>
    <tr>
     <td>
      <input type="submit" name="submit" value="{lang_add}" />
</form>
     </td>
     <td>
      <br />{cancel_button}
     </td>
     <td class="right">
      {delete_button}
     </td>
    </tr>
   </table>
  <td>
 </tr>
</table>
<!-- END form -->
<!-- BEGIN list -->
 <tr class="bg_view">
  <td valign="top"><b>{field}:</b></td>
  <td valign="top">{data}</td>
 </tr>
<!-- END list -->
