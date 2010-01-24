<!-- BEGIN admin -->
   
<!-- BEGIN set_paths -->
<table border='0' cellspacing='0'>
  <form name='faxadmin' action='{submit_url}' method='post'>
    <tr><th>{l_cover_pref}:</th><th> <input type='text' value='{cover_path}' name='cover_path'>
    </th></tr>
    <tr><th>{l_domain}:</th><th><input type='text' value='{domain}' name='domain'></th></tr>
    <tr colspan=2><td align=center><input type='submit' value='{l_update}'></td>
  </form>
</table>
<!-- END set_paths -->


<!-- BEGIN updated -->
<center><h3>{admin_up}</h3></center>
<center>
  <form name='faxadmin' action='{submit_url}' method='post'><input type='submit' value='OK'>
</center>
<!-- END updated -->


<!-- END admin -->
