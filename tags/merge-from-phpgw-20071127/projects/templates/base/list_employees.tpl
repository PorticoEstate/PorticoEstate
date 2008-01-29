<!-- $Id: list_employees.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
<center>
<br/><br/>
{message}
<table width="95%" border="0" cellpadding="2" cellspacing="2" align="center">
	<tr bgcolor="{th_bg}">
		<td nowrap="nowrap">{sort_name}</td>
		<td nowrap="nowrap">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="2" align="center">{lang_period}</td>
				</tr>
				<tr align="center">
					<td width="50%" align="center">{sort_sdate}</td>
					<td width="50%" align="center">{sort_edate}</td>
				</tr>
			</table>
		</td>
		<td nowrap="nowrap">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="2" align="center">{currency}&nbsp;{lang_accounting}</td>
				</tr>
				<tr align="right">
					<td width="50%" align="center">{sort_per_hour}</td>
					<td width="50%" align="center">{sort_per_day}</td>
				</tr>
			</table>
		</td>
		<td align="center">{weekly_workhours}</td>
		<td align="center">{lang_location}</td>
		<td align="center">{cost_centre}</td>
		<td>&nbsp;</td>
	</tr>

<!-- BEGIN emp_list -->

	<tr bgcolor="{tr_color}">
		<td>{emp_name}</td>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="center">
					<td width="50%">{sdate_formatted}</td>
					<td width="50%">{edate_formatted}</td>
				</tr>
			</table>
		</td>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="right">
					<td width="50%">{factor}</td>
					<td width="50%">{d_factor}</td>
				</tr>
			</table>
		</td>
		<td align="right">{weekly_workhours_num}</td>
		<td align="right">{location_name}</td>
		<td align="right">{cost_centre_num}</td>
		<td align="center" nowrap="nowrap"><a href="{edit_emp}"><img src="{edit_img}" title="{lang_edit_factor}" border="0"></a>&nbsp;<a href="{delete_emp}"><img src="{delete_img}" title="{lang_delete_factor}" border="0"></a></td>
	</tr>

	{emp_timeframes}

<!-- END emp_list -->

	<tr height=20">
	</tr>
	<form method="POST" action="{action_url}">
	<tr>
		<td valign="top"><select name="values[account_id]">{emp_select}</select></td>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="center">
					<td width="50%">{sdate_select}</td>
					<td width="50%">{edate_select}</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="right">
					<td width="50%"><input type="text" name="values[accounting]" value="{accounting}" size="6"></td>
					<td width="50%"><input type="text" name="values[d_accounting]" value="{d_accounting}" size="6"></td>
				</tr>
			</table>
		</td>
		<td valign="top" align="right"><input type="text" name="values[weekly_workhours]" value="{weekly_workhours_num}" size="4" /></td>
		<td valign="top"><select name="values[location_id]">{location_select}</select></td>
		<td valign="top" align="right"><input type="text" name="values[cost_centre]" value="{cost_centre_num}" size="3" /></td>
		<td valign="top" align="center"><input type="submit" name="values[save]" value="{lang_add_factor}"></td>
	</tr>
	<tr height="30">
		<td valign="bottom" colspan="7">{lang_employees_not_in_list}</td>
	</tr>
	<tr>
		<td colspan="2"><select name="emp_not_in_list" size="5">{employees_not_in_list}</select></td>
		<td colspan="5" valign="bottom" align="center"><input type="submit" name="done" value="{lang_done}"></td>
	</tr>
	</form>
</table>
</center>

<!-- BEGIN emp_tframe -->

	<tr bgcolor="{tr_color}">
		<td>&nbsp;</td>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="center">
					<td width="50%">{sdate}</td>
					<td width="50%">{edate}</td>
				</tr>
			</table>
		</td>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="right">
					<td width="50%">{factor}</td>
					<td width="50%">{d_factor}</td>
				</tr>
			</table>
		</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="center"><a href="{edit_emp}"><img src="{edit_img}" title="{lang_edit_factor}" border="0"></a></td>
		<td align="center"><a href="{delete_emp}"><img src="{delete_img}" title="{lang_delete_factor}" border="0"></a></td>
	</tr>

<!-- END emp_tframe -->
