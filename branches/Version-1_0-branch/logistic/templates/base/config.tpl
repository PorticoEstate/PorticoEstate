<!-- $Id:$ -->
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
			<td colspan="2">&nbsp;<b>{lang_logistic} {lang_settings}</b></td>
		</tr>
		<tr class="row_off">
			<td>{lang_Use_ACL_for_control_areas}.</td>
			<td>
				<select name="newsettings[acl_at_control_area]">
					<option value="2" {selected_acl_at_control_area_2}>NO</option>
					<option value="1" {selected_acl_at_control_area_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>Antall planlagte kontroller som skal vises.</td>
			<td>
				<input type="text" name="newsettings[no_of_planned_controls]" value="{value_no_of_planned_controls}"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>Antall tildelte kontroller som skal vises</td>
			<td>
				<input type="text" name="newsettings[no_of_assigned_controls]" value="{value_no_of_assigned_controls}"/>
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
