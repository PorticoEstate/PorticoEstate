<!-- BEGIN header -->
<form method="POST" action="{action_url}">
<table border="0" align="center">
   <tr bgcolor="{th_bg}">
    <td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
   </tr>
<!-- END header -->
<!-- BEGIN body -->
   <tr bgcolor="{row_on}">
    <td colspan="2">&nbsp;</td>
   </tr>
   <tr bgcolor="{row_off}">
    <td colspan="2">&nbsp;<b>{lang_chora} {lang_settings} - {lang_program_locations}</b></font></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_co}:</td>
    <td><input name="newsettings[co]" value="{value_co}"></td>
   </tr>
   <tr bgcolor="{row_off}">
    <td>{lang_rcs}:</td>
    <td><input name="newsettings[rcs]" value="{value_rcs}"></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_rcsdiff}:</td>
    <td><input name="newsettings[rcsdiff]" value="{value_rcsdiff}"></td>
   </tr>
   <tr bgcolor="{row_off}">
    <td>{lang_rlog}:</td>
    <td><input name="newsettings[rlog]" value="{value_rlog}"></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_cvs}:</td>
    <td><input name="newsettings[cvs]" value="{value_cvs}"></td>
   </tr>
   <tr bgcolor="{row_off}">
    <td>{lang_adminname}:</td>
    <td><input name="newsettings[adminname]" value="{value_adminname}"></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_adminemail}.</td>
    <td><input name="newsettings[adminemail]" value="{value_adminemail}"></td>
   </tr>
   <tr bgcolor="{row_off}">
    <td colspan="2">&nbsp;<b>{lang_chora} {lang_settings} - {lang_other}</b></font></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_shortloglength}:</td>
    <td><input name="newsettings[shortloglength]" value="{value_shortloglength}"></td>
   </tr>
<!-- END body -->
<!-- BEGIN footer -->
  <tr bgcolor="{th_bg}">
    <td colspan="2">
&nbsp;
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" name="submit" value="{lang_submit}">
      <input type="submit" name="cancel" value="{lang_cancel}">
    </td>
  </tr>
</table>
</form>
<!-- END footer -->
