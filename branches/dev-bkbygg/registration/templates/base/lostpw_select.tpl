<!-- BEGIN form -->
<p>{errors}</p>
<form action="{form_action}" method="POST" class="pure-form pure-form-aligned">
	<p>
		{lang_explain}
	</p>
	<fieldset>
		<div class="pure-control-group">
			<label>{lang_username}</label>
			<input name="r_reg[loginid]" value="{value_username}" required="required" type="{input_type}">
		</div>

		<div class="pure-controls">
			<button type="submit" class="pure-button pure-button-primary" name="submit">{lang_submit}</button>
        </div>
	</fieldset>
</form>
<!-- END form -->
