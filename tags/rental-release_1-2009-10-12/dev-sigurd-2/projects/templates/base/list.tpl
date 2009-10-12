<!-- $Id: list.tpl,v 1.4 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<!-- BEGIN project_main -->
<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_main}:&nbsp;<a href="{main_url}">{title_main}</a></b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td>{number_main}</td>
		<td>{lang_url}:</td>
		<td><a href="http://{url_main}" taget="_blank">{url_main}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td>{coordinator_main}</td>
		<td>{lang_customer}:</td>
		<td>{customer_main}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_files}:</td>
		<td>{attachment}</td>
		<td>{report}</td>
		<td>&nbsp</td>
	</tr>
</table>

<!-- END project_main -->

<center>{message}</center>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr width="100%">
		<td colspan="11" width="100%">
			<table border="0" width="100%">
				<tr width="100%">
				{left}
					<td align="center">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td width="25%" align="left"><form method="POST" action="{action_url}">{action_list}</form></td>
		<td width="20%" align="center">
			<form method="POST" name="status" action="{action_url}">
				<select name="status" onChange="this.form.submit();">{status_list}</select>
			</form>
		</td>
		<td width="20%" align="center"><form method="POST" name="filter" action="{action_url}">{filter_list}</form></td>
		<td width="35%" align="right"><nobr><form method="POST" name="query" action="{action_url}">{search_list}</nobr></form></td>
	</tr>
</table>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td>{sort_title}</td>

		<!-- BEGIN pro_sort_cols -->
		<td align="{col_align}">{sort_column}</td>
		<!-- END pro_sort_cols -->

	</tr>

<!-- BEGIN projects_list -->

	<tr class="{tr_class}">
		<td valign="top"><a href="{projects_url}">{title}</a></td>
		{pro_column}
	</tr>
<!-- END projects_list -->
</table>

<!-- BEGIN pro_cols -->
		<td align="{col_align}">{column}</td>
<!-- END pro_cols -->
