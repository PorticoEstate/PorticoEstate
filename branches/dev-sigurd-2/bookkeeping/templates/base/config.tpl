<!-- BEGIN header -->
<form method="POST" action="{action_url}">
<table border="0" align="center">
   <tr bgcolor="{th_bg}">
	   <td colspan="2" align="center"><font color="{th_text}"><b>{title}</b></font></td>
   </tr>
<!-- END header -->

<!-- BEGIN body -->
   <tr bgcolor="{row_off}">
    <td colspan="2"><b>{lang_invoicing}&nbsp;{lang_settings}</b></td>
   </tr>
   <tr bgcolor="{row_on}">
    <td>{lang_enable_acl_for_invoicing_part}:</td>
		<td>
			<select name="newsettings[invoice_acl]">
				<option value="yes"{selected_invoice_acl_yes}>{lang_yes}</option>
				<option value="no"{selected_invoice_acl_no}>{lang_no}</option>
			</select>
		</td>
	</tr>
<!-- END body -->
<!-- BEGIN footer -->
  <tr height="50" valign="bottom">
    <td><input type="submit" name="submit" value="{lang_submit}"></td>
	<td align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
  </tr>
</table>
</form>
<!-- END footer -->
