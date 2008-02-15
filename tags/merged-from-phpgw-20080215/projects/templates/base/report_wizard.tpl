<!-- BEGIN report_wizard.tpl -->
<!-- $Id: report_wizard.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<center>
<form method="POST" action="{actionurl}">
{details}
{activities}
</form>
</center>
<!-- END report_wizard.tpl -->

<!-- BEGIN details_handle -->
<table style="border: 2px solid #FFFFFF; width:800px; min-width:800px" align="center">
	<tr height="25" bgcolor="{th_bg}">
		<td colspan="2"><b>{lang_select_data}</b></td>
	</tr>
	<tr height="30" bgcolor="{row_off}">
		<td align="left">{lang_employee}:</td>
		<td align="left">{employee}</td>
	</tr>
	<tr height="40" bgcolor="{row_on}">
		<td align="left">{period}</td>
		<td align="left">{start_date_select}&nbsp;-&nbsp;{end_date_select}</td>
	</tr>
	<tr height="40" bgcolor="{row_off}">
		<td align="left">{template_name}:</td>
		<td align="left">{template_select}</td>
	</tr>
	<tr>
		<td align="right" colspan="2"><input type="submit" name="forward" value="{lang_forward}" /></td>
	</tr>
</table>
<!-- END details_handle -->

<!-- BEGIN activities_handle -->
<table style="border: 2px solid #FFFFFF; width:800px; min-width:800px" align="center">
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_filename}:</b></td>
	</tr>
	<tr height="30" bgcolor="{row_on}">
		<td colspan="4" align="left"><input type="text" size="50" name="filename" value="{filename}" />.sxw</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td colspan="4"></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_activities}:</b></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td colspan="4"></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td>&nbsp;</td>
		<td>{lang_date}</td>
		<td>{lang_description}</td>
		<td>{lang_duration}</td>
	</tr>
	{list}
	<tr>
		<td colspan="2" align="left"><input type="submit" name="back" value="{lang_back}" /></td>
		<td colspan="2" align="right"><input type="submit" name="yes" value="{lang_yes}" /></td>
	</tr>
</table>
<!-- END activities_handle -->

<!-- BEGIN list_activities -->
	<tr bgcolor="{tr_color}">
		<td><input type="checkbox" checked="checked" name="hourid[]" value="{id}"></td>
		<td>{activity_date}</td>
		<td>{activity_descr}</td>
		<td>{activity_duration}</td>
	</tr>
<!-- END list_activities -->