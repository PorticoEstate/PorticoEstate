
<!-- BEGIN header --> 
<form method="POST" action="{action_url}">
<table align="center" cellspacing=0 style="border: 1px solid #000000;">
<!-- END header -->

<!-- BEGIN body -->
   <tr bgcolor="{th_bg}">
    <td colspan="2"><b>{lang_javassh_config}</b></td>
  </tr>
  <tr bgcolor="{row_on}">
    <td align="right">
			<b>{lang_url_to_applet}:</b>
		</td>
    <td>
			<input name="newsettings[applet_url]" value="{value_applet_url}" size="50">ie http://domain.com/phpgroupware/javassh/applet/
		</td>
  </tr>
   <tr bgcolor="{row_off}">
    <td align="right">
			<b>{lang_applet_filename}:</b>
		</td>
    <td>
			<input name="newsettings[applet_file]" value="{value_applet_file}" size="50">
		</td>
<!-- END body -->

<!-- BEGIN footer -->
  <tr bgcolor="{th_bg}">
    <td colspan="2">
&nbsp;
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" name="submit" value="{lang_save}">
      <input type="submit" name="cancel" value="{lang_done}">
    </td>
  </tr>
</table>
</form>
<br>
<!-- END footer -->
