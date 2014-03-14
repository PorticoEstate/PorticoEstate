<!-- $Id: config.tpl 11483 2013-11-24 19:54:40Z sigurdne $ -->
<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table border="0" align="center" width="85%">
		<tr class="th">
			<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr class="row_on">
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="row_off">
			<td colspan="2">&nbsp;<b>{lang_manual} {lang_settings}</b></td>
		</tr>
		<tr class="row_off">
			<td>{lang_Use_ACL_for_document_types}.(not implementet)</td>
			<td>
				<select name="newsettings[acl_at_control_area]">
					<option value="2" {selected_acl_at_control_area_2}>NO</option>
					<option value="1" {selected_acl_at_control_area_1}>YES</option>
				</select>
			</td>
		</tr>
		<!-- END body -->
		<!-- BEGIN footer -->
		<tr class="th">
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
