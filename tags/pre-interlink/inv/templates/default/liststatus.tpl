<!-- $Id: liststatus.tpl 9883 2002-04-05 23:35:03Z ceb $ -->

{app_header}

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
{next_matchs}
<br><br>{message}<br><br>
<table border="0" cellpadding="2" cellspacing="2">
	<tr bgcolor="{bg_color}">
		<td width="20%">{lang_status_name}</td>
		<td width="8%" align="center">{lang_edit}</td>
		<td width="8%" align="center">{lang_delete}</td>
	</tr>

<!-- BEGIN status_list -->

	<tr bgcolor="{tr_color}">
		<td>{name}</td>
			<td align="center"><a href="{edit}">{lang_edit_entry}</a></td>
			<td align="center"><a href="{delete}">{lang_delete_entry}</a></td>
	</tr>

<!-- END status_list -->

<!-- BEGINN add   -->
	<tr valign="bottom">
		<td height="50">
			<form method="POST" action="{add_action}">
			<input type="submit" value="{lang_add}">
			</form>
			</td>
	</tr>

<!-- END add -->

</table>
</center>
