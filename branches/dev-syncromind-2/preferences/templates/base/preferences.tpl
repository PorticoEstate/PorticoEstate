<div class="error_message">{messages}</div>

<!-- BEGIN form -->
<div id="tab-content">
{tabs}
{select_user}
<form method="POST" action="{action_url}" class="pure-form pure-form-aligned">
{account_id}
	<!-- BEGIN list -->
	{pre_div}
		{list_header}
			{rows}
	{post_div}
	
	<!-- END list -->

	<div class="button_group">
		<input type="submit" name="submit" value="{lang_submit}">
		<input type="submit" name="cancel" value="{lang_cancel}">
		{help_button}
	</div>
</form>
</div>
<!-- END form -->

<!-- BEGIN row -->
		<div class="pure-control-group">
			<label>{row_name}</label>
				{row_value}
		</div>
<!-- END row -->

<!-- BEGIN help_row -->
		<div class="pure-control-group">
			<label>{row_name}</label>
				{row_value}
		</div>
		<div class="pure-control-group">
			<label colspan="2">{help_value}</label>
		</div>
<!-- END help_row -->
