<!-- $Id: listrooms.tpl 5496 2001-06-05 01:09:33Z bettina $ -->
<p><b>&nbsp;&nbsp;&nbsp;{title_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
<table border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="9" align="left">
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
		<td>&nbsp;</td>
		<td colspan="9" align="right">
			<form method="post" action="{search_action}">
			<input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
			</form></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td width="8%" bgcolor="{th_bg}">{sort_name}</td>
		<td width="20%" bgcolor="{th_bg}">{sort_note}</td>
		<td width="8%" bgcolor="{th_bg}" align="center">{lang_products}</td>
		<td width="8%" bgcolor="{th_bg}" align="center">{lang_edit}</td>
		<td width="8%" bgcolor="{th_bg}" align="center">{lang_delete}</td>
	</tr>

<!-- BEGIN room_list -->

	<tr bgcolor="{tr_color}">
		<td>{room_name}</td>
		<td>{room_note}</td>
		<td align="center"><a href="{products}">{lang_products_entry}</a></td>
		<td align="center"><a href="{edit}">{lang_edit_entry}</a></td>
		<td align="center"><a href="{delete}">{lang_delete_entry}</a></td>
	</tr>

<!-- END room_list -->

<!-- BEGINN add   -->

	<tr valign="bottom">
  		<td height="50">
			<form method="POST" action="{add_action}">
			{hidden_vars}
            <input type="submit" value="{lang_add}">
			</form>
		</td>
	</tr>

<!-- END add -->

</table>
</center>
