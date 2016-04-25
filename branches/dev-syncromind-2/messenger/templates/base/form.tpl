<!-- BEGIN form -->
{app_header}

<center>{errors}</center>
<form action="{form_action}" method="POST">
	<table border="0" width="93%" align="center">
		<tr bgcolor="{th_bg}">
			<td colspan="2">

				<table border="0" width="100%">
					<tr>
						<td align="left"><b>{header_message}</b>&nbsp;</td>
						<td align="right">{read_buttons}</td>
					</tr>
		</tr>
    </table>

</td>
</tr>

{from}
{to}
{date}
{status}

<tr bgcolor="{row_off}">
	<td>{lang_subject}</td>
	<td>{value_subject}&nbsp;</td>
</tr>

<tr bgcolor="{row_off}">
	<td colspan="2" align="left">{value_content}&nbsp;</td>
</tr>

{buttons}

</table>
</form>
<!-- END form -->

<!-- BEGIN form_date -->
<tr bgcolor="{row_off}">
	<td>{lang_date}</td>
	<td>{value_date}&nbsp;</td>
</tr>
<!-- END form_date -->

<!-- BEGIN form_from -->
<tr bgcolor="{row_off}">
	<td>{lang_from}&nbsp;</td>
	<td>{value_from}&nbsp;&nbsp;&nbsp;<b>{value_status}</b></td>
</tr>
<!-- END form_from -->

<!-- BEGIN form_to -->
<tr bgcolor="{row_off}">
	<td>{lang_to}</td>
	<td>
		<select name="message[to]">
			<!-- BEGIN select_to -->
			<option value="{uid}">{full_name}</option>
			<!-- END select_to -->
		</select>
	</td>
</tr>
<!-- END form_to -->
<!-- BEGIN form_reply_to -->
<tr bgcolor="{row_off}">
	<td>{lang_to}</td>
	<td>
		{value_to}
	</td>
</tr>
<!-- END form_reply_to -->

<!-- BEGIN form_read_buttons -->
{link_reply}&nbsp;|&nbsp;{link_forward}&nbsp;|&nbsp;{link_delete}&nbsp;
<!-- END form_read_buttons -->

<!-- BEGIN form_read_buttons_for_global -->
{link_delete}&nbsp;
<!-- END form_read_buttons_for_global -->

<!-- BEGIN form_buttons -->
<tr bgcolor="{row_off}">
	<td colspan="2" align="right">{button_cancel}&nbsp;{button_delete}&nbsp;{button_reply}&nbsp;{button_send}&nbsp;</td>
</tr>
<!-- END form_buttons -->
