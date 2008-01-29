<!-- BEGIN form -->

<b>{lang_headlines}</b><hr><p>

 <form method="POST" action="{form_action}">
  <table align="center" width="25%">
   <tr bgcolor="{th_bg}">
    <td>&nbsp;{lang_header}</td>
   </tr>

   <tr>
    <td align="center" bgcolor="{tr_color_1}">
     <select name="headlines[]" multiple size="10">
      {select_options}
     </select>
    </td>
   </tr>

   <tr bgcolor="{tr_color_2}">
    <td align="center">
     <input type="submit" name="submit" value="{lang_submit}">
    </td>
   </tr>
  </table>
 </form>
 
<!-- END form -->
