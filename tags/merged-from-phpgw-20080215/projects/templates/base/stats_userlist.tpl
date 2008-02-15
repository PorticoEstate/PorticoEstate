<!-- $Id: stats_userlist.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->

{app_header}
<div class="projects_content"></div>
<center>
<table border="0" width="79%" cellspacing="2" cellpadding="2">
	<form method="POST" action="{action_url}">
	<tr>
		<td colspan="4" width="100%">
			<table border="0" width="100%">
				<tr>
				{left}
					<td align="center">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		{search_list}
	</tr>
</table>
<table border="0" width="79%" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="20%">{sort_lid}</td>
		<td width="20%">{sort_firstname}</td>
		<td width="20%">{sort_lastname}</td>
		<td align="center" width="8%">{lang_projects}</td>
	</tr>

<!-- BEGIN user_list -->

	<tr bgcolor="{tr_color}">
		<td>{lid}</td>
		<td>{firstname}</td>
		<td>{lastname}</td>
		<td align="center"><input type="checkbox" name="values[account_id][{account_id}]" value="{account_id}" {radio_checked}></td>
	</tr>

	{project_list}

<!-- END user_list -->


	<tr height="50" valign="bottom">
		<td colspan="4" align="right"><input type="submit" name="view" value="{lang_view_projects}"></td>
	</tr>
</table>
</center>
</form>
<!-- BEGIN pro_cols -->

	<tr>
		<td>&nbsp;</td>
		<td bgcolor="{th_bg}" colspan="2">{lang_projects}</td>
		<td>&nbsp;</td>
	</tr>

<!-- BEGIN pro_list -->

	<tr>
		<td>&nbsp;</td>
		<td bgcolor="{tr_color}" colspan="2">{pro_name}</td>
		<td>&nbsp;</td>
	</tr>

<!-- END pro_list -->

	<tr height="15">
		<td>&nbsp;</td>
	</tr>

<!-- END pro_cols -->
