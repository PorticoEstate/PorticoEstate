<!-- BEGIN form -->
<center>
 {message}
 <form name="form" action="{actionurl}" method="POST">
  <input type="hidden" name="site_id" value="{site_id}">
  <table border="0" width="80%" cellspacing="2" cellpadding="2"> 
   <tr bgcolor="{th}">
    <td colspan="2" align="center"><b>{title_sites}<b/></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_name}:<br><input name="site[name]" size="50" value="{site_name}"></td>
	<td width="50%" valign="middle"><i>{note_name}</i></td>
   </tr>
   <tr bgcolor="{row_off}">
    <td>{lang_sitedir}:<br><input name="site[dir]" size="50" value="{site_dir}"></td>
	<td width="50%" valign="middle"><i>{note_dir}</i></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_siteurl}:<br><input name="site[url]" size="50" value="{site_url}"></td>
	<td width="50%" valign="middle"><i>{note_url}</i></td>
   </tr>
   <tr bgcolor="{row_off}">
    <td>{lang_anonuser}:<br><input name="site[anonuser]" size="50" value="{site_anonuser}"></td>
	<td width="50%" valign="middle"><i>{note_anonuser}</i></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_anonpasswd}:<br><input name="site[anonpasswd]" size="50" value="{site_anonpasswd}"></td>
	<td width="50%" valign="middle"><i>{note_anonpasswd}</i></td>
   </tr>
   <tr bgcolor="{row_off}">
    <td>{lang_adminlist}:<br><select name="site[adminlist][]" multiple="multiple" size="5">{site_adminlist}</select></td>
	<td width="50%" valign="middle"><i>{note_adminlist}</i></td>
   </tr>
<!-- BEGIN add -->
   <tr>
    <td nowrap>
     <input type="submit" name="save" value="{lang_add}"> &nbsp;
     <input type="submit" name="done" value="{lang_done}">
    </td>
    <td align="right">
     <input type="reset" name="reset" value="{lang_reset}">
    </td>
   </tr>
<!-- END add -->

<!-- BEGIN edit -->
   <tr>
    <td nowrap>
     <input type="submit" name="save" value="{lang_save}"> &nbsp;
     <input type="submit" name="done" value="{lang_done}">
    </td>
    <td align="right">
     <input type="submit" name="delete" value="{lang_delete}">
    </td>
   </tr>
<!-- END edit -->
  
  </table>
 </form>
</center>
<!-- END form -->
