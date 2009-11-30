<!-- $Id$ -->

{app_header}

<center>
{message}
{pref_message}
<form method="POST" action="{actionurl}">
{hidden_vars}
<table border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td>{lang_choose}</td>
		<td>{choose}</td>
	</tr>
	<tr>
		<td>{lang_delivery_num}&nbsp;:</td>
		<td><input type=text name="values[delivery_num]" value="{delivery_num}"></td>
	</tr>
	<tr>
		<td>{lang_customer}&nbsp;:</td>
		<td>{customer}</td>
	</tr>
	<tr>
		<td>{lang_project}&nbsp;:</td>
		<td>{project}</td>
	</tr>
	<tr>
		<td>{lang_delivery_date}&nbsp;:</td>
		<td>{date_select}</td>
	</tr>
</table> 
<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="3%" bgcolor="{th_bg}" align="center">{lang_select}</td>
		<td width="25%" bgcolor="{th_bg}">{lang_activity}</td>
		<td width="25%" bgcolor="{th_bg}">{lang_hours}</td>
		<td width="10%" bgcolor="{th_bg}" align="center">{lang_status}</td>
		<td width="10%" bgcolor="{th_bg}" align="center">{lang_work_date}</td>
		<td width="10%" bgcolor="{th_bg}" align="right">{lang_workunits}</td>
		<td width="10%" bgcolor="{th_bg}" align="center">{lang_edit}</td>
	</tr>

<!-- BEGIN hours_list -->

	<tr bgcolor="{tr_color}">
		<td align="center">{select}</td>
		<td>{activity}</td>
		<td>{hours_descr}</td>
		<td align="center">{status}</td>
		<td align="center">{start_date}</td>
		<td align="right">{aes}</td>
		<td align="center"><a href="{edithour}">{lang_edit_entry}</a></td>
	</tr>

<!-- END hours_list -->

</table><br><br>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr bgcolor="{tr_color}">
		<td width="3%">&nbsp;</td>
		<td width="25%">&nbsp;</td>
		<td width="25%" align="center"><font size="4"><b>{lang_sumaes}</b></font></td>
		<td width="10%">&nbsp;</td>
		<td width="10%">&nbsp;</td>
		<td width="10%" align="right"><font size="4"><b>{sum_aes}</b></font></td>
		<td width="10%">&nbsp;</td>
	</tr>
</table>
<br>
<table width="69%" border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td align="center">{delivery}</td>
		</form>

<!-- url zum druck -->

		<td align="center"><a href={print_delivery} target=_blank>{lang_print_delivery}</a></td>
		<td align="center"><form method="POST" action="{doneurl}"> 
			<input type="submit" name="done" value="{lang_done}"></form></td>
	</tr>
</table>
</center>
