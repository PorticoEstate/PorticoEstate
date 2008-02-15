<!-- $Id: list_surcharges.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
<center>
<table border="0" cellpadding="2" cellspacing="2" align="center">
	<tr height="5"><td></td></tr>
	<tr><td>{message}&nbsp;</td></tr>
	<tr height="5"><td></td></tr>
</table>

<table width="50%" border="0" cellpadding="2" cellspacing="2" align="center">
	<tr bgcolor="{th_bg}">
		<td>{lang_descr}</td>
		<td align="right">%&nbsp;{lang_surcharge}</td>
		<td>&nbsp;</td>
	</tr>

<!-- BEGIN charge_list -->

	<tr bgcolor="{tr_color}">
		<td><a href="{edit_url}">{charge_name}</a></td>
		<td align="right">{charge_percent}</td>
		<td align="center"><a href="{edit_url}"><img src="{edit_img}" border="0" title="{lang_edit_surcharge}"></a>&nbsp;<a href="{delete_url}"><img src="{delete_img}" border="0" title="{lang_delete_surcharge}"></a></td>
	</tr>

<!-- END charge_list -->

	<tr height="15">
		<td>&nbsp;</td>
	</tr>

<form method="POST" action="{action_url}">
	<tr bgcolor="{row_on}">
		<td><input type="text" name="values[charge_name]" value="{charge_name}"></td>
		<td align="right"><input type="text" name="values[charge_percent]" value="{charge_percent}" maxlength="6" size="6"></td>
		<td align="center"><nobr><input type="hidden" name="values[charge_id]" value="{charge_id}">
			<input type="submit" name="save" value="{lang_save_surcharge}"><!-- &nbsp;<input type="checkbox" name="new_charge" value="True" {new_charge_selected}>&nbsp;{lang_new_surcharge} --></nobr></td>
	</tr>
	<tr height="50" valign="bottom">
		<td align="right" colspan="3"><input type="submit" name="done" value="{lang_done}">
	<tr>
</form>
</table>
</center>
