<!-- $Id: list_mstones.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
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
	<tr bgcolor="{row_off}">
		<td>{lang_start_date}:</td>
		<td>{sdate}</td>
		<td>{lang_date_due}:</td>
		<td>{edate}</td>
	</tr>
</table>
-->
<!-- END project_data -->

<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr><td align="center">{message}&nbsp;</td></tr>
</table>

<table border="0" cellspacing="0" cellpadding="2" width="100%">
	<tr bgcolor="{th_bg}">
		<td>{lang_title}</td>
		<td>{lang_date_due}</td>
		<td>&nbsp;</td>
	</tr>
<!-- BEGIN mstone_list -->
	<tr bgcolor="{tr_color}">
		<td><a href="{edit_url}">{title}</a></td>
		<td>{datedue}</td>
		<td align="center"><a href="{edit_url}">{edit_img}</a>&nbsp;<a href="{delete_url}">{delete_img}</a></td>
	</tr>
<!-- END mstone_list -->
	<form method="POST" action="{action_url}">
	<tr height="50" valign="bottom">
		<td><input type="text" name="values[title]" size="35" value="{title}"></td>
		<td>{end_date_select}</td>
		<td nowrap="nowrap">
			<input type="hidden" name="values[old_edate]" value="{old_edate}">
			<input type="hidden" name="s_id" value="{s_id}">
			<input type="checkbox" name="values[new]" value="True" {new_checked}>{lang_new}&nbsp;
			<input type="submit" name="save" value="{lang_save_mstone}">
		</td>
	</tr>
	<tr valign="bottom" height="75">
		<td align="right" colspan="3"><input type="submit" name="done" value="{lang_done}"></td>
	</tr>
</form>
</table>
</center>
