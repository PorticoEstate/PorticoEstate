<!-- $Id: bill_list.tpl 16496 2006-03-12 10:48:44Z skwashd $ -->

{app_header}

<center>
<table border="0" width="98%" cellpadding="2" cellspacing="2">
	<tr>
		<td width="100%" colspan="5" align="center">
			<table border="0" width="100%" align="center">
				<tr>
				{left}
					<td align="center">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="25%">
			<form method="POST" action="{action_url}">{action_list}</form>
		</td>
		<td width="25%" align="center">
			<form method="POST" name="status" action="{action_url}">
				<select name="status" onChange="this.form.submit();">{status_list}</select>
			</form>
		</td>
		<td width="20%"><form method="POST" name="filter" action="{action_url}">{filter_list}</form></td>
		<td width="30%" align="right"><form method="POST" name="query" action="{action_url}">{search_list}</form></td>
	</tr>
</table>
<table border="0" width="98%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td width="8%" bgcolor="{th_bg}">{sort_number}</td>
		<td width="18%" bgcolor="{th_bg}">{sort_title}</td>
		<td width="18%" bgcolor="{th_bg}">{sort_coordinator}</td>
        <td width="18%" bgcolor="{th_bg}">{sort_action}</td>
		<td width="5%" bgcolor="{th_bg}" align="center">{sort_end_date}</td>
		{lang_action}
		<td width="15%" align="center">{h_lang_part}</td>
		<td width="15%" align="center">{h_lang_partlist}</td>
	</tr>
  
<!-- BEGIN projects_list -->

	<tr bgcolor="{tr_color}">
		<td>{number}</td>
		<td>{title}</td>
		<td>{coordinator}</td>
        <td>{td_action}</td>
		<td align="center">{end_date}</td>
		{action_entry}
		<td align="center"><a href="{part}">{lang_part}</a></td>
		<td align="center"><a href="{partlist}">{lang_partlist}</a></td>
	</tr>

<!-- END projects_list -->

</table><br><br>

<!-- link fuer alle invoices -->

<table border="0" cellpadding="2" cellspacing="2">     
	<tr>
		<td><a href="{all_partlist}">{lang_all_partlist}</a></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><a href="{all_part2list}">{lang_all_part2list}</a></td>
	</tr>
</table>
</center>
