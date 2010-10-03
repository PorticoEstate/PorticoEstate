<!-- $Id: home_list.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->

<center>

<table border="0" width="98%" cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="7" width="100%">
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
		<td colspan="2" width="33%" align="left">
			<form method="POST" action="{action_url}">
				{action_list}
				<noscript>&nbsp;<input type="submit" name="submit" value="{lang_submit}"></noscript>
			</form>
		</td>
		<td colspan="2" width="32%" align="center">
			<form method="POST" name="status" action="{action_url}">
				<select name="status" onChange="this.form.submit();">{status_list}</select>
				<noscript>&nbsp;<input type="submit" name="submit" value="Submit"></noscript>
			</form>
		</td>
		<td colspan="2" width="35%" align="right"><form method="POST" name="query" action="{action_url}">{search_list}</form></td>
	</tr>
</table>
<table border="0" width="98%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td width="10%">{sort_number}</td>
		<td width="20%">{sort_title}</td>
		<td width="20%">{sort_coordinator}</td>
        <td width="20%" align="center">{lang_milestones}</td>
		<td width="10%" align="center">{sort_end_date}</td>
		<td width="5%" align="center">{lang_view}</td>
	</tr>

<!-- BEGIN projects_list -->

	<tr bgcolor="{tr_color}">
		<td valign="top">{number}</td>
		<td valign="top"><a href="{projects_url}">{title}</a></td>
		<td valign="top">{coordinator}</td>
        <td>{milestones}</td>
		<td align="center" valign="top">{end_date}</td>
		<td align="center" valign="top"><a href="{view}">{lang_view_entry}</a></td>
	</tr>

<!-- END projects_list -->

</table>
</center>
