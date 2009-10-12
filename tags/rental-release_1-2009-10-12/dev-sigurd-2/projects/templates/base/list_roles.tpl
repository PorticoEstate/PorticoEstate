<!-- $Id: list_roles.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<center>
{message}
<table width="40%" border="0" cellpadding="2" cellspacing="2" align="center">
	<tr>
		<td colspan="2" width="100%">
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
		<td width="100%" align="right"><form method="POST" name="query" action="{search_action}">{search_list}</form></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td width="75%">{sort_name}</td>
		<td width="25%">&nbsp;</td>
	</tr>

<!-- BEGIN roles_list -->

	<tr bgcolor="{tr_color}">
		<td>{role_name}</td>
		<td align="center"><a href="{delete_role}">{lang_delete}</a></td>
	</tr>

<!-- END roles_list -->

	<form method="POST" action="{action_url}">
	<tr>
		<td ><input type="text" name="role_name"></td>
		<td align="center"><input type="submit" name="save" value="{lang_add_role}"></td>
	<tr>
	<tr height="50" valign="bottom">
		<td align="right"><input type="submit" name="done" value="{lang_done}"></td>
	<tr>
</form>
</table>
</center>
