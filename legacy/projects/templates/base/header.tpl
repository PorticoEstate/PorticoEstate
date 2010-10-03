<!-- $Id: header.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
<!-- BEGIN projects_header -->
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr width="100%">
		<td bgcolor="{row_on}" width="60%">
			<a href="{link_projects}"><b>{lang_projects}</b></a>
			&nbsp;|&nbsp;
			<a href="{link_jobs}"><b>{lang_jobs}</b></a>
			&nbsp;|&nbsp;
			<a href="{link_hours}"><b>{lang_hours}</b></a>
			&nbsp;|&nbsp;
			<a href="{link_ttracker}"><b>{lang_ttracker}</b></a>
			&nbsp;|&nbsp;
			<a href="{link_statistics}"><b>{lang_statistics}</b></a>
		</td>
		<td width="40%" align="right" bgcolor="{row_on}">
			<a href="{link_budget}"><b>{lang_budget}</b></a>
			{admin_header}
		</td>
	</tr>
</table>

<!-- END projects_header -->

<!-- BEGIN projects_admin_header -->
			&nbsp;|&nbsp;
			<a href="{link_accounting}"><b>{lang_accounting}</b></a>
<!-- END projects_admin_header -->
