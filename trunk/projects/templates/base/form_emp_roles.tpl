<!-- $Id: form_emp_roles.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<center>
<!-- BEGIN project_data -->
<!--
<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_project}:&nbsp;<a href="{pro_url}">{title_pro}</a></b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td>{number_pro}</td>
		<td>{lang_url}:</td>
		<td><a href="http://{url_pro}" taget="_blank">{url_pro}</a></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_coordinator}:</td>
		<td>{coordinator_pro}</td>
		<td>{lang_customer}:</td>
		<td>{customer_pro}</td>
	</tr>
	<tr height="5">
		<td></td>
	</tr>
</table>
-->
<!-- END project_data -->

{message}
<table width="100%" border="0" cellpadding="2" cellspacing="2" align="center">
	<tr bgcolor="{th_bg}">
		<td width="35%">{sort_name}</td>
		<td width="25%">{sort_role}</td>
		<td width="35%">{lang_events}</td>
		<td width="5%">&nbsp;</td>
	</tr>

<!-- BEGIN role_list -->

	<tr bgcolor="{tr_color}">
		<td valign="top">{edit_link}{emp_name}{end_link}</td>
		<td valign="top">{role_name}</td>
		<td>{events}</td>
		<td align="center" valign="top">{edit_link}{edit_img}&nbsp;{delete_role}{delete_img}</td>
	</tr>

<!-- END role_list -->

	<tr height="5">
		<td></td>
	</tr>
	<form method="POST" action="{action_url}">
	<input type="hidden" name="order" value="{order}">
	<input type="hidden" name="sort" value="{sort}">
	<tr>
		<td valign="top"><select name="values[account_id]">{emp_select}</select></td>
		<td valign="top"><select name="values[role_id]"><option value="">{lang_select_role}</option>{role_select}</select></td>
		<td><select name="values[events][]" multiple>{event_select}</select></td>
		<td align="center" valign="top"><input type="submit" name="save" value="{lang_assign}"></td>
	<tr>
	<tr height="50" valign="bottom" align="right">
		<td colspan="4"><input type="submit" name="done" value="{lang_done}"></td>
	<tr>
	</form>
</table>
</center>
