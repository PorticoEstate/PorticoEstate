<!-- BEGIN form -->

<script language="JavaScript" type="text/javascript">
	var tos;

	function opentoswindow()
	{
		if (tos)
		{
			if (tos.closed)
			{
				tosWindow.stop();
				tosWindow.close();
			}
		}
		tosWindow = window.open("{tos_link}", "tos", "width=500,height=600,location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no");
		if (tosWindow.opener == null)
		{
			tosWindow.opener = window;
		}
	}
</script>

<h2 class="content-subhead">{message}</h2>
<p>{errors}</p>
<form action="{form_action}" method="POST" class="pure-form pure-form-aligned">
	<fieldset>

		<!-- BEGIN username -->
		<div class="pure-control-group">
			{domain_select}
		</div>
		<div class="pure-control-group">
			<label for="username">{missing_loginid}{lang_username}</label>
			{domain_from_host}<input id="username" type="text" placeholder="{lang_username}" name="r_reg[loginid]" value="{value_username}" required>
		</div>
		<!-- END username -->


		<!-- BEGIN password -->
		<div class="pure-control-group">
			{missing_passwd}
			<label for="password">{lang_password}</label>
			<input type="password" id="password" placeholder="{lang_password}" name="r_reg[passwd]" value="{value_passwd}" required>
		</div>

		<div class="pure-control-group">
			<label for="password2">{missing_passwd_confirm}{lang_reenter_password}</label>
			<input type="password" id="password2" placeholder="{lang_password}" name="r_reg[passwd_confirm]" value="{value_passwd_confirm}" required oninput="check(this)">
		</div>
		<!-- END password -->

		<!-- BEGIN other_fields_proto -->
		<div class="pure-control-group">
			<label>{missing_indicator} {lang_displayed_text}</label>
			{input_field}
		</div>
		<!-- END other_fields_proto -->

		<!-- BEGIN tos -->
		<div class="pure-controls">
			<label for="cb" class="pure-checkbox">
				{missing_tos_agree}
				<input id="cb" type="checkbox" name="r_reg[tos_agree]" {value_tos_agree} required="required">
				<a href="javascript:opentoswindow()">{lang_tos_agree}</a>
			</label>
		</div>
		<!-- END tos -->
		<div class="pure-controls">
			<button type="submit" class="pure-button pure-button-primary" name="submit">{lang_submit}</button>
        </div>

	</fieldset>
</form>
<script language='javascript' type='text/javascript'>
	function check(input)
	{
		if (input.value != document.getElementById('password').value)
		{
			input.setCustomValidity('{lang_error_match}');
		}
		else
		{
			// input is valid -- reset the error message
			input.setCustomValidity('');
		}
	}
</script>
<!-- END form -->

