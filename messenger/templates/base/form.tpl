<!-- BEGIN form -->


<center>{errors}</center>

<div class="card shadow mb-4">
	<!-- Card Header - Dropdown -->
	<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
		<h6 class="m-0 font-weight-bold text-primary">{header_message}</h6>
		<div class="dropdown no-arrow">
			<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
			{from}
			{to}
			{date}
			{status}

			<div class="form-group">
				<label for="subject">{lang_subject}</label>
				{value_subject}
			</div>

			<div class="form-group">
				{value_content}
			</div>

			{buttons}

		</form>
	</div>
</div>



<!-- END form -->

<!-- BEGIN form_date -->
<div class="form-group">
	<label for="date">{lang_date}</label>
	<div>{value_date}&nbsp;</div>
</div>
<!-- END form_date -->

<!-- BEGIN form_from -->
<div class="form-group">
	<label for="from">{lang_from}</label>
	<div>{value_from}&nbsp;&nbsp;&nbsp;<b>{value_status}</b></div>
</div>
<!-- END form_from -->

<!-- BEGIN form_to -->
<div class="form-group">
	<label for="recipient">{lang_to}</label>
	<select class="form-control" id="recipient" name="message[to]">
		<!-- BEGIN select_to -->
		<option value="{uid}">{full_name}</option>
		<!-- END select_to -->
	</select>
</div>
<!-- END form_to -->
<!-- BEGIN form_reply_to -->
<div class="form-group">
	<label for="to">{lang_to}</label>
	{value_to}
</div>
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
	<div>{button_cancel}&nbsp;{button_delete}&nbsp;{button_reply}&nbsp;{button_send}&nbsp;</div>
<!-- END form_buttons -->
