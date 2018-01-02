<!-- BEGIN form -->
<b>{lang_changepassword} {value_username}</b><hr><p>

<center>{errors}</center>

<form method="POST" action="{form_action}" class="pure-form pure-form-aligned">
    <fieldset>
		<div class="pure-control-group">
			<label>
				{lang_enter_password}
			</label>
			<input type="password" name="r_reg[passwd]" required="required" id="password">
		</div>
		<div class="pure-control-group">
			<label>
				{lang_reenter_password}
			</label>
			<input type="password" name="r_reg[passwd_2]" required="required" id="password_confirm" oninput="check(this)">
		</div>
		<div class="pure-controls">
			<button type="submit" class="pure-button pure-button-primary" name="submit">{lang_change}</button>
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
<br>
<pre>{sql_message}</pre>
<!-- END form -->
