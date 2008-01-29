<!-- $Id: listdelivery.tpl 6368 2001-06-29 02:25:28Z bettina $ -->
<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
<table border="0" width="45%" cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="3" align="left">
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
		<td colspan="3" align="right">
			<form method="post" action="{searchurl}">
			<input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
			</form></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td width=10% bgcolor="{th_bg}" align="center">{sort_num}</td>
		<td width=10% bgcolor="{th_bg}" align="center">{sort_date}</td>
		<td width=10% bgcolor="{th_bg}" align="center">{head_delivery}</td>
	</tr>

<!-- BEGIN delivery_list -->
  
	<tr bgcolor="{tr_color}">
		<td align="center">{num}</td>
		<td align="center">{date}</td>
		<td align="center"><a href="{delivery}">{lang_delivery}</a></td>
</tr>

<!-- END delivery_list -->  

</table>
</center>
