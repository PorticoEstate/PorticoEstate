<!-- BEGIN form -->
<center>{errors}</center>

<!-- BEGIN input -->
<form action="{form_action}" method="POST" id="form" class="pure-form pure-form-aligned">
	<fieldset border="0" width="40%" align="center">

		{domain_select}
		<div class="pure-control-group">
			<label>{lang_username}</label>
			{domain_from_host}<input name="r_reg[loginid]" value="{value_username}" data-validation="length alphanumeric" data-validation-length="3-10"/>
		</div>

		<div class="pure-controls">
			<button type="submit" class="pure-button pure-button-primary" name="submit">{lang_submit}</button>
        </div>
	</fieldset>
</form>
<!-- END input -->
<!-- END form -->
