<!-- $Id: form_emp_factor.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<center>
{message}
	<form method="POST" action="{action_url}">
	<input type="hidden" name="values[account_id]" value="{account_id}">
<table width="75%" border="0" cellpadding="2" cellspacing="2" align="center">
	<tr>
		<td>{lang_employee}:</td>
		<td>{employee}</td>
	</tr>
		<td>{lang_sdate}:</td>
		<td>{sdate_select}</td>
	</tr>
	<tr>
		<td>{lang_edate}:</td>
		<td>{edate_select}</td>
	</tr>
	<tr>
		<td>{lang_accounting} {lang_per_hour}:&nbsp;[{currency}.c]</td>
		<td width="50%"><input type="text" name="values[accounting]" value="{accounting}" size="10"></td>
	</tr>
	<tr>
		<td>{lang_accounting} {lang_per_day}:&nbsp;[{currency}.c]</td>
		<td width="50%"><input type="text" name="values[d_accounting]" value="{d_accounting}" size="10"></td>
	</tr>
	<tr>
		<td>{weekly_workhours}:</td>
		<td width="50%"><input type="text" name="values[weekly_workhours]" value="{weekly_workhours_num}" size="4"></td>
	</tr>
	<tr>
		<td>{cost_centre}:</td>
		<td width="50%"><input type="text" name="values[cost_centre]" value="{cost_centre_num}" size="10"></td>
	</tr>
	<tr>
		<td>{lang_location}:</td>
		<td width="50%"><select name="values[location_id]">{location_select}</select></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="values[save]" value="{lang_save_factor}">&nbsp;<input type="submit" name="cancel" value="{lang_cancel}"></td>
	<tr>
</table>
	</form>
