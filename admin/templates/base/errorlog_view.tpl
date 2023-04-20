<!-- BEGIN list -->

<form class='pure-form' method='POST' action=''>
	<table class="pure-table">
		<tr>
			<td style="text-align: left;" colspan="2">
				<input id="date" name="date" onChange="this.form.submit();" value="{value_date}"/>
			</td>
			<td style="text-align: right;" colspan="2">
				{select_user}
			</td>
		</tr>
		<tbody
			<tr>
			{nextmatchs_left}&nbsp;{nextmatchs_right}
			</tr>
		</tbody>
	</table>
</form>
{showing}

<table id="admin_error_log_list" class="pure-table">
	<thead>
		<tr>
			<th>{lang_date}</th>
			<th>{lang_loginid}</th>
			<th>{lang_app}</th>
			<th>{lang_severity}</th>
			<th>{lang_file}</th>
			<th>{lang_line}</th>
		</tr>
		<tr>
			<td></td>
			<td colspan="5">{lang_message}</td>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="6" align="left">{footer_total}</td>
		</tr>
	</tfoot>
	<tbody>
		{rows_access}
	</tbody>
</table>
{purge_log_button}

<!-- END list -->

<!-- BEGIN row -->
<tr class="{tr_class}">
	<td>{row_date}</td>
	<td>{row_loginid}</td>
	<td>{row_app}</td>
	<td>{row_severity}</td>
	<td>{row_file}</td>
	<td>{row_line}</td>
</tr>
<tr class="{tr_class}">
	<td>&nbsp;</td>
	<td colspan="5"><pre>{row_message}</pre></td>
	</tr>
<!-- END row -->

<!-- BEGIN row_empty -->
	<tr class="{tr_class} row_empty">
		<td colspan="6">{row_message}</td>
	</tr>
<!-- END row_empty -->
