<!-- $Id: listservers.tpl 6387 2001-06-30 06:07:04Z milosch $ -->
<center>
<table border="0" cellspacing="2" cellpadding="2">
 <tr>
  <td colspan="6" align="center" bgcolor="#c9c9c9"><b>{title_servers}<b/></td>
</tr> 
<tr>
<td colspan="6" align=left>
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
  <td colspan="5" align=right>
  <form method="post" action="{actionurl}">
 <input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}"></form></td>
 </tr>
  <tr bgcolor="{th_bg}">
   <td width=20% bgcolor="{th_bg}">{sort_name}</td>
   <td width=20% bgcolor="{th_bg}">{sort_type}</td>
   <td width=16% bgcolor="{th_bg}">{sort_basedn}</td>
   <td width=16% bgcolor="{th_bg}">{sort_rootdn}</td>
   <td width=8% bgcolor="{th_bg}" align="center">{lang_default}</td>
   <td width=8% bgcolor="{th_bg}" align="center">{lang_edit}</td>
   <td width=8% bgcolor="{th_bg}" align="center">{lang_delete}</td>
  </tr>

<!-- BEGIN server_list -->

  <tr bgcolor="{tr_color}">
   <td>{server_name}&nbsp;</td>
   <td>{server_type}&nbsp;</td>
   <td>{server_basedn}&nbsp;</td>
   <td>{server_rootdn}&nbsp;</td>
   <td align="center">{is_default}</td>
   <td align="center"><a href="{edit}">{lang_edit_entry}</a></td>
   <td align="center"><a href="{delete}">{lang_delete_entry}</td>  
</tr>

<!-- END server_list -->  

<!-- BEGINN add   -->

<tr valign="bottom">
  <td>
     <form method="POST" action="{add_action}">
      <input type="submit" name="add" value="{lang_add}"></form>
    </td>
    </tr>
<tr valign="bottom">
  <td>
     <form method="POST" action="{doneurl}">
      <input type="submit" name="done" value="{lang_done}"></form>
    </td>
   </tr>

<!-- END add -->

</table>
</center>
