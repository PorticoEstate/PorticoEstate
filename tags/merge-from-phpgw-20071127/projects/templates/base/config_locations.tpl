<!-- $Id: config_locations.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
<center>
<table border="0" cellpadding="2" cellspacing="2" align="center">
	<tr height="5"><td></td></tr>
	<tr><td>{message}&nbsp;</td></tr>
	<tr height="5"><td></td></tr>
</table>

<table width="75%" border="0" cellpadding="2" cellspacing="2" align="center">
	<tr bgcolor="{th_bg}">
		<td width="30%">{lang_location}</td>
		<td width="15%">{lang_ident}</td>
		<td width="15%">{lang_custnum}</td>
		<td width="20%" align="center">{lang_edit}</td>
		<td width="20%" align="center">{lang_delete}</td>
	</tr>

<!-- BEGIN location_list -->

	<tr bgcolor="{tr_color}">
		<td>{location_name}</td>
		<td>{location_ident}</td>
		<td>{location_custnum}</td>
		<td align="center"><a href="{edit_url}"><img src="{edit_img}" border="0" title="{lang_edit_surcharge}"></a></td>
		<td align="center"><a href="{delete_url}"><img src="{delete_img}" border="0" title="{lang_delete_surcharge}"></a></td>
	</tr>

<!-- END location_list -->

	<tr height="15">
		<td colspan="4">&nbsp;</td>
	</tr>
	<form method="POST" action="{action_url}">
	<tr bgcolor="{th_bg}">
		<td colspan="5">{lang_submit_action}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td><input type="text" name="values[location_name]" value="{input_location_name}" maxlength="255" size="20"></td>
		<td><input type="text" name="values[location_ident]" value="{input_location_ident}" maxlength="6" size="6"></td>
		<td><input type="text" name="values[location_custnum]" value="{input_location_custnum}" maxlength="255" size="10"></td>
		<td colspan="2"><nobr><input type="hidden" name="values[location_id]" value="{input_location_id}">
			<input type="submit" name="save" value="{lang_location_button}">&nbsp;{cancel_button}</nobr></td>
	</tr>
	<tr height="50" valign="bottom">
		<td align="right" colspan="5"><input type="submit" name="done" value="{lang_done}">
	<tr>
	</form>
</table>
</center>
