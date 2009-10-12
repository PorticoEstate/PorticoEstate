<!-- $Id: config.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
<!-- BEGIN header -->
<br/>

<table width="75%" border="0" align="center">
<form method="POST" action="{action_url}">
   <tr bgcolor="{th_bg}">
	   <td colspan="2" align="center"><font color="{th_text}"><b>{title}</b></font></td>
   </tr>
<!-- END header -->

<!-- BEGIN body -->
	<tr bgcolor="{row_off}">
    	<td colspan="2"><b>{lang_customer_version}</b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td width="50%">{lang_customer_version_id}:</td>
		<td width="50%">
			<select name="newsettings[customer_version_id]">
				<option value="standard"{selected_customer_version_id_standard}>{lang_Standard}</option>
				<option value="pb"{selected_customer_version_id_pb}>{lang_pro|business_AG}</option>
			</select>
		</td>
	</tr>

	<tr bgcolor="{row_on}">
    	<td colspan="2"><b>{lang_user_interface}</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td width="50%">{lang_show_sidebox}:</td>
		<td width="50%">
			<select name="newsettings[show_sidebox]">
				<option value="no"{selected_show_sidebox_no}>{lang_no}</option>
				<option value="yes"{selected_show_sidebox_yes}>{lang_yes}</option>
			</select>
		</td>
	</tr>

	<tr bgcolor="{row_off}">
    	<td colspan="2"><b>{lang_accounting}</b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_hours_of_work_day}:</td>
		<td><input type="text" name="newsettings[hwday]" value="{value_hwday}" size="3" maxlength="2">&nbsp;[hh]</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_project_accounting}:</td>
		<td>
			<select name="newsettings[accounting]">
				<option value="own"{selected_accounting_own}>{lang_definition_per_project}</option>
				<option value="activity"{selected_accounting_activity}>{lang_use_activities}</option>
			</select>
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td colspan="2">{lang_if_using_activities}:</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_Invoicing_of_work_time}:</td>
		<td>
			<select name="newsettings[activity_bill]">
				<option value="h"{selected_bill_h}>{lang_Exact_accounting_[hh.mm]}</option>
				<option value="wu"{selected_bill_wu}>{lang_per_workunit}</option>
			</select>
		</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td colspan="2"><b>{lang_workhours_booking}</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_permit_booking_of_hours_for_more_than_one_day}:</td>
		<td>
			<select name="newsettings[hoursbookingday]">
				<option value="no"{selected_hoursbookingday_no}>{lang_no}</option>
				<option value="yes"{selected_hoursbookingday_yes}>{lang_yes}</option>
			</select>
		</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_permit_booking_of_hours_with_null_values}:</td>
		<td>
			<select name="newsettings[hoursbookingnull]">
				<option value="no"{selected_hoursbookingnull_no}>{lang_no}</option>
				<option value="yes"{selected_hoursbookingnull_yes}>{lang_yes}</option>
			</select>
		</td>
	</tr>

	<tr bgcolor="{row_off}">
		<td colspan="2"><b>{lang_project}</b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_how_to_create_project_number}:</td>
		<td>
			<select name="newsettings[projectnr]">
				<option value="generate"{selected_projectnr_generate}>{lang_generate_automatically}</option>
				<option value="manually"{selected_projectnr_manually}>{lang_manually_show_help_msg}</option>
			</select>
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_permit_double_project_id}:</td>
		<td>
			<select name="newsettings[permit_double_project_id]">
				<option value="no"{selected_permit_double_project_id_no}>{lang_no}</option>
				<option value="yes"{selected_permit_double_project_id_yes}>{lang_yes}</option>
			</select>
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_categorie_required}:</td>
		<td>
			<select name="newsettings[categorie_required]">
				<option value="no"{selected_categorie_required_no}>{lang_no}</option>
				<option value="yes"{selected_categorie_required_yes}>{lang_yes}</option>
			</select>
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_show_project_option_not_billable}:</td>
		<td>
			<select name="newsettings[show_project_option_not_billable]">
				<option value="no"{selected_show_project_option_not_billable_no}>{lang_no}</option>
				<option value="yes"{selected_show_project_option_not_billable_yes}>{lang_yes}</option>
			</select>
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_show_project_option_direct_work}:</td>
		<td>
			<select name="newsettings[show_project_option_direct_work]">
				<option value="no"{selected_show_project_option_direct_work_no}>{lang_no}</option>
				<option value="yes"{selected_show_project_option_direct_work_yes}>{lang_yes}</option>
			</select>
		</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_show_project_option_discount}:</td>
		<td>
			<select name="newsettings[show_project_option_discount]">
				<option value="no"{selected_show_project_option_discount_no}>{lang_no}</option>
				<option value="yes"{selected_show_project_option_discount_yes}>{lang_yes}</option>
			</select>
		</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td colspan="2"><b>{lang_project_dependencies}</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_move_start_date_if_pervious_projects_end_date_changes}:</td>
		<td>
			<select name="newsettings[dateprevious]">
				<option value="no"{selected_dateprevious_no}>{lang_no}</option>
				<option value="yes"{selected_dateprevious_yes}>{lang_yes}</option>
			</select>
		</td>
	</tr>
	<tr height="5">
		<td colspan="2">&nbsp;</td>
	</tr>

<!-- END body -->
<!-- BEGIN footer -->
  <tr height="25" valign="bottom">
    <td><input type="submit" name="submit" value="{lang_submit}"></td>
	<td align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
  </tr>
</form>
</table>
<!-- END footer -->
