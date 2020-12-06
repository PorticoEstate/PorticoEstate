<!-- BEGIN form -->


<center>{errors}</center>

<div class="card shadow mb-4">
	<!-- Card Header - Dropdown -->
	<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
		<h6 class="m-0 font-weight-bold text-primary">{header_message}</h6>
		<div class="dropdown no-arrow">
			<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
			</a>
			<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
				<div class="dropdown-header">Dropdown Header:</div>
				{read_buttons}

			</div>
		</div>
	</div>
	<!-- Card Body -->
	<div class="card-body">
		<form action="{form_action}" method="POST">
			<table border="0" width="93%" align="center">
				<tr bgcolor="{th_bg}">
					<td colspan="2">
						<table border="0" width="100%">
							<tr>
								<td align="left"><b>{header_message}</b>&nbsp;</td>
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
	</div>
</div>



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
<a class="dropdown-item" href="{link_inbox}">{lang_inbox}</a>
<a class="dropdown-item" href="{link_compose}">{lang_compose}</a>
<div class="dropdown-divider"></div>
<a class="dropdown-item" href="{link_reply}">{lang_reply}</a>
<a class="dropdown-item" href="{link_forward}">{lang_forward}</a>
<a class="dropdown-item" href="{link_delete}">{lang_delete}</a>

<!-- END form_read_buttons -->

<!-- BEGIN form_read_buttons_for_global -->
<a class="dropdown-item" href="{link_inbox}">{lang_inbox}</a>
<a class="dropdown-item" href="{link_compose}">{lang_compose}</a>
<div class="dropdown-divider"></div>
<a class="dropdown-item" href="{link_delete}">{lang_delete}</a>
<!-- END form_read_buttons_for_global -->

<!-- BEGIN form_buttons -->
<tr bgcolor="{row_off}">
	<td colspan="2" align="right">{button_cancel}&nbsp;{button_delete}&nbsp;{button_reply}&nbsp;{button_send}&nbsp;</td>
</tr>
<!-- END form_buttons -->
