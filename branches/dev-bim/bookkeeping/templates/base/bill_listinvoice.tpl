<!-- $Id$ -->

{app_header}

<center>
<table width="79%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td colspan="4">
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
		<td width="33%">&nbsp;</td>
		<td width="33%">&nbsp;</td>
		<td width="33%" align="right">
			<form method="POST" name="query" action="{search_action}">{search_list}</form></td>
	</tr>
</table>
{pref_message}
<table width="79%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="10%" bgcolor="{th_bg}">{sort_num}</td>
		<td width="20%" bgcolor="{th_bg}">{sort_customer}</td>
		<td width="20%" bgcolor="{th_bg}">{sort_title}</td>
		<td width="10%" bgcolor="{th_bg}" align="center">{sort_date}</td>
		{sort_sum}
		<td width="10%" bgcolor="{th_bg}" align="center">{lang_data}</td>
	</tr>
  
<!-- BEGIN projects_list -->
      
	<tr bgcolor="{tr_color}">
		<td>{num}</td>
		<td>{customer}</td>
		<td>{title}</td>
		<td align="center">{date}</td>
		{sum}
		<td align="center"><a href="{td_data}">{lang_td_data}</a></td>
	</tr>

<!-- END projects_list -->

</table>
</center>
