{processes_css}
<div style="color:red; text-align:center">{message}</div>
{user_tabs}
<table style="border: 0;width:100%;" cellspacing="0">
	<tr class="th">
		<td colspan="3" style="font-size: 120%; font-weight:bold">
			{lang_List_of_processes}
		</td>
	</tr>
</table>
<table style="border: 0;width:100%;" cellspacing="1">
	<tr class="row_off">
                <td colspan="3" align="left">
			<form action="{form_action}" method="post">
			<input type="hidden" name="start" value="0" />
			<input type="hidden" name="order" value="{order}" />
			<input type="hidden" name="sort" value="{sort}" />
                        <input size="18" type="text" name="find" value="{search_str}" />
	                <input type="submit" name="search" value="{lang_search}" />
			</form>
                </td>
	</tr>
	<tr>
		<td colspan="3">
		        <table style="border: 0px;width:100%; margin:0 auto">
		                <tr class="row_off">
		                        {left}
		                        <td><div align="center">{lang_showing}</div></td>
		                        {right}
		                </tr>
		        </table>
	        </td>
	</tr>
	<form action="{form_action}" method="post">
	<input type="hidden" name="start" value="{start}" />
	<input type="hidden" name="find" value="{search_str}" />
	<input type="hidden" name="sort" value="{sort}" />
	<input type="hidden" name="order" value="{order}" />
        <tr class="th">
		<td>{header_wf_procname}</td>
		<td>{lang_Activities}</td>
		<td>{lang_Instances}</td>
	</tr>
	<!-- BEGIN block_table -->
	<tr class="{color_line}">
		<td class="row_{process_css_name}">
			<span class="{process_css_name}"><a href="{link_wf_procname}">{item_wf_procname} {item_version}</a></span>
		</td>
		<td style="text-align:right;">
			<a  href="{link_activities}">{item_activities}</a>
		</td>
		<td style="text-align:right;">
			<a  href="{link_instances}">{item_instances}</a>
		</td>
	</tr>
	<!-- END block_table -->
	</form>
</table>
