<!-- $Id$ -->
<center>
<table width="80%" border="0" cellpadding="2" cellspacing="2">	
	<tr>
		<td colspan="3"><b>{lang_users}</b></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td width="33%" bgcolor="{th_bg}">{sort_lid}</td>
		<td width="33%" bgcolor="{th_bg}">{sort_firstname}</td>
		<td width="33%" bgcolor="{th_bg}">{sort_lastname}</td>
	</tr>

<!-- BEGIN user_list -->

	<tr bgcolor="{tr_color}">                                                                                                                                             
		<td>{lid}</td>
		<td>{firstname}</td>
		<td>{lastname}</td>
	</tr>

<!-- END user_list -->

	<tr height="5">
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3"><b>{lang_groups}</b></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td width="33%" bgcolor="{th_bg}">{sort_name}</td>
		<td width="33%" bgcolor="{th_bg}">&nbsp;</td>
		<td width="33%" bgcolor="{th_bg}">&nbsp;</td>
	</tr>

<!-- BEGIN group_list -->

	<tr bgcolor="{tr_color}">                                                                                                                                             
		<td>{lid}</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

<!-- END group_list -->

	<tr valign="bottom" height="50">
	<form method="POST" action="{action_url}">
		<td colspan="2"><input type="submit" name="edit" value="{lang_edit}"></td>
		<td colspan="2" align="right"><input type="submit" name="done" value="{lang_done}"></td>
	</form>
	</tr>
</table>
</center>
