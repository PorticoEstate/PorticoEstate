<!-- BEGIN list -->
<table class="pure-table">
						<tr valign="bottom">
		<td align="center" colspan="4">
								{lang_last_x_logins}
							</td>
	</tr>
									<tr>
										{nextmatchs_left}&nbsp;{nextmatchs_right}
									</tr>
</table>
{showing}

<table border="0" width="95%"  class="pure-table pure-table-bordered">
	<thead
		<tr>
			<th width="10%">{lang_loginid}</th>
			<th width="15%">{lang_ip}</th>
			<th width="20%">{lang_login}</th>
			<th width="30%">{lang_logout}</th>
			<th>{lang_total}</th>
						</tr>
	</thead>
	<tbody
						{rows_access}
						<tr class="th">
							<td colspan="5" align="left">{footer_total}</td>
						</tr>
	</tbody>
	<tfoot>
						<tr class="th">
							<td colspan="5" align="left">{lang_percent}</td>
						</tr>
	</tfoot>
</table>

<!-- END list -->

<!-- BEGIN row -->
<tr class="{tr_class}">
		<td>{row_loginid}</td>
		<td>{row_ip}</td>
		<td>{row_li}</td>
		<td>{row_lo}&nbsp;</td>
		<td>{row_total}&nbsp;</td>
</tr>
<!-- END row -->

<!-- BEGIN row_empty -->
<tr class="{tr_class}">
		<td align="center" colspan="5">{row_message}</td>
</tr>
<!-- END row_empty -->
