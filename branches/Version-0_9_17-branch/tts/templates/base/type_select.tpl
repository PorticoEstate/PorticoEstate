<form action="{url_form_action}" method="post">
	<label for="ticket_type">{lang_ticket_type}</label>: 
	<select name="ticket_type" id="type_id">
		<!-- BEGIN type_options -->
			<option value="{id}">{name}</option>
		<!-- END type_options -->
	</select><br />
	<div class="btngrp">
		<input type="submit" name="cancel" value="{lang_cancel}">
		<input type="submit" name="next" value="{lang_next}">
	</div>
</form>
