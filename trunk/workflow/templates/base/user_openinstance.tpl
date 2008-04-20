{processes_css}
<div style="color:red; text-align:center">{message}</div>
{user_tabs}
<table style="border: 0px;width:100%;" cellspacing="0">
	<tr class="th">
		<td colspan="2" style="font-size: 120%; font-weight:bold">
			{lang_New_Instance}
		</td>
	</tr>
</table>
<table style="border: 0px;width:100%;" cellspacing="1">
	<tr class="row_off">
		<td colspan="2">
			<table style="border: 0; width:100%">
				<tr>
					<td>
						<form action="{form_action}" method="post">
						<input type="hidden" name="start" value="0" />
						<input type="hidden" name="sort" value="{sort}" />
						<input type="hidden" name="order" value="{order}" />
						<input size="18" type="text" name="find" value="{search_str}" />
			                        <input type="submit" name="search" value="{lang_search}" />
						</form>
					</td>
					<td class="row_on">
						{help_info}
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		        <table style="border: 0px;width:100%; margin:0 auto">
		                <tr class="row_off">
		                        {left}
		                        <td><div align="center">{lang_showing}</div></td>
		                        {right}
		                </tr>
		        </table>
	        </td>
	</tr>
	<tr class="th">
		<form action="{form_action}" method="post">
		<input type="hidden" name="start" value="{start}" />
		<input type="hidden" name="find" value="{search_str}" />
		<input type="hidden" name="sort" value="{sort}" />
		<input type="hidden" name="order" value="{order}" />
		<td>{header_wf_procname}</td>
		<td>{header_wf_name}</td>
	</tr>
	<!-- BEGIN block_table -->
	<tr class="{color_line}">
		<td class="row_{process_css_name}">
		  <span class="{process_css_name}">{wf_procname}</span>
		</td>
		<td>
		  <a href="{link_starting}">{actname}</a>{arrow}
		</td>

	</tr>
	<!-- END block_table -->
</table>
</form>
