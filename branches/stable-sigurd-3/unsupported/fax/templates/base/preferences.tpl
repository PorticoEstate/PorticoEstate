<!-- BEGIN preferences -->


<!-- BEGIN message -->
<p>
  <center>{msg}</center>
</p>
<!-- END message -->



<!-- BEGIN notify -->
<form name='notify' action='{submit_url}' method='post'>
<table border='0' cellspacing='0'>
<tr>
 <th align=right> {l_mnotify}: </th>
 <td><input type='radio' name='notify' value='N' {def_no}>{l_no}<input
type='radio' name='notify' value='Y' {def_yes}>{l_yes}</td>
 </tr>

<!-- END notify -->


<!-- BEGIN cover_header -->
<tr>
<th align=right>
{l_cover}:</th>
<td>
<select name='cover' onChange='this.form.submit()'>
<!-- END cover_header -->


<!-- BEGIN cover_row -->
 <option value='{cover_path}' {sel}>{cover_name}

<!-- END cover_row -->


<!-- BEGIN cover_footer -->
 </select> </td>
</tr>
<tr>
<td colspan=2 align=center><input type='hidden' value='{user_login}' name='user_login'>
<input name='action1' type='submit' value='OK'> </td></tr>

</table></form>
<br> <img src='{img_src}' border=1>

<!-- END cover_footer -->
 



<!-- BEGIN updated -->
<br>
<center>{message}</center>
<center>
 <form name='ok' action = '{submit_url}' method='post'>
 <input type='submit' value='OK'>
</form>
<!-- END updated -->




<!-- END preferences -->
