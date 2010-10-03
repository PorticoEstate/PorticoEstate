<!-- BEGIN preferences -->

<!-- BEGIN display_user -->
{user}:{login_id}<br>
<!-- END display_user -->

<!-- BEGIN row_header -->
<table border='0' width='95%'>
 <tr bgcolor={td_color}><td>{t_address}</td><td>{t_type}</td><td>{t_edit}</td><td>{t_delete}</td></tr>
<!-- END row_header -->

<!-- BEGIN row -->
 <tr bgcolor={bgcolor}><td>{value}</td><td>{type}</td><td>{edit}</td><td>{delete}</td></tr>
<!-- END row -->

<!-- BEGIN row_footer -->
</table>
<br>
<form name='add' action='{url_add}' method='POST'>
 <input type='submit' value='{add}' name='add new'>
 <input type='hidden' value='{login_id}' name='login_id'>
</form>
<!-- END row_footer -->

<!-- BEGIN params -->
<hr align=left width='100%'>
 <center><b>{genset}</b></center>
<hr align=left width='100%'>
<br>
<form action='{update_settings}' name='params' method='POST'>

<table width='95%'>
<tr>
 <td align=center colspan=3><input type=text size=6 name=required_hits value="{req_hits}"></td>
 <td align=center><b>{reqhits}</b></td>
 <td> {reqhitstxt1}
 </td>
</tr>
<tr>
 <td colspan=4>&nbsp;</td>
 <td>{reqhitstxt2}</td>
</tr>

<tr>
 <td align=center>{l_on}</td>
 <td align=center>{l_off}</td>
 <td align=center>{l_default}</td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr>

<tr>
 <td align=center><input type=radio name="rewrite_subject" value=1 {rewrite_subjectON}></td>
 <td align=center><input type=radio name="rewrite_subject" value=0 {rewrite_subjectOFF}></td>
 <td align=center><input type=radio name="rewrite_subject" value=-1 {rewrite_subjectGLOB}></td>
 <td align=center><b>{rewrite}</b></td>
 <td>{rewritetxt}</td>
</tr>

<tr>
   <td align=center><input type=radio name="report_header" value=1 {report_headerON}></td>
   <td align=center><input type=radio name="report_header" value=0 {report_headerOFF}></td>
   <td align=center><input type=radio name="report_header" value=-1 {report_headerGLOB}></td>
   <td align=center><b>{report}</b></td>
   <td>{reporttxt}
   </td>
</tr>

<tr>
 <td align=center><input type=radio name="defang_mime" value=1 {defang_mimeON}></td>
 <td align=center><input type=radio name="defang_mime" value=0 {defang_mimeOFF}></td>
 <td align=center><input type=radio name="defang_mime" value=-1 {defang_mimeGLOB}></td>
 <td align=center><b>{dehtml}<b></td>
 <td>{dehtmltxt} </td>
</tr>

<tr>
  <td align=center><input type=radio name="use_terse_report" value=1 {use_terse_reportON}></td>
  <td align=center><input type=radio name="use_terse_report" value=0 {use_terse_reportOFF}></td>
  <td align=center><input type=radio name="use_terse_report" value=-1 {use_terse_reportGLOB}></td>
  <td align=center><b>{shortrep}</b></td>
  <td>{shortreptxt}</td>
</tr>

<tr>
  <td colspan=4><input name=submit type=submit value="{updatesett}"></td>
</tr>
</table>

<input type='hidden' name='login_id' value='{login_id}'>
</form>

<!-- END params -->


<!-- BEGIN edit_row -->
<p>
{edittxt}
</p>

<form name='edit_row' action='{url}' method='POST'>
 <input type='text' name='value' value='{value}'>
  <select name = 'type'>
   <option value = 'allow' {allow}>{l_allow}
   <option value = 'deny' {deny}>{l_deny}
  </select>
 <input type='submit' name='submit' value='OK'>
 <input type='hidden' name='pref_id' value='{pref_id}'>
 <input type='hidden' name='login_id' value='{login_id}'>
</form>
<!-- END edit_row -->


<!-- END preferences -->
