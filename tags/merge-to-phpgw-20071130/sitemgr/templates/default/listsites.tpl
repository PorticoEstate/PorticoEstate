 <center>
  <table border="0" cellspacing="2" cellpadding="2">
   <tr>
    <td colspan="6" align="center" bgcolor="#c9c9c9"><b>{title_sites}<b/></td>
   </tr>
   <tr>
    <td colspan="6" align="left">
     <table border="0" width="100%">
      <tr>
      {left}
       <td align="center">{lang_showing}</td>
      {right}
      </tr>
     </table>
    </td>
   </tr>
   <tr>
    <td>&nbsp;</td>
    <td colspan="6" align="right">
     <form method="post" action="{actionurl}">
     <input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}"></form></td>
   </tr>
   <tr bgcolor="{th_bg}">
    <td>{sort_name}</td>
	<td>{sort_url}</td>
    <td align="center">{lang_edit}</td>
    <td align="center">{lang_delete}</td>
   </tr>

<!-- BEGIN site_list -->
   <tr bgcolor="{tr_color}">
    <td>{site_name}</td>
	<td>{site_url}</td>
    <td align="center"><a href="{edit}">{lang_edit_entry}</a></td>
    <td align="center"><a href="{delete}">{lang_delete_entry}</a></td>
   </tr>
<!-- END site_list -->

<!-- BEGIN add   -->
</form>
   <tr valign="bottom">
     <td><form method="POST" action="{add_action}">
       <input type="submit" name="add" value="{lang_add}"></form></td>
     </form></td>
     <td><form method="POST" action="{doneurl}">
       <input type="submit" name="done" value="{lang_done}">
     </form></td>
   </tr>
<!-- END add -->

  </table>
 </center>
