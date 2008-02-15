<!-- BEGIN options_select -->
    <option value="{optionvalue}" {optionselected}>{optionname}</option>
<!-- END options_select -->

<!-- BEGIN form -->
<form method="post" action="{form_action}" enctype="multipart/form-data">

	<label for="ticket_reported_by">{lang_reported_by}:</label>
	<select name="ticket[reported_by]" id="ticket_reported_by">
		<option value="0">{lang_please_select}</option>
		<!-- BEGIN reported_by -->
		<option value="{acct_id}">{acct_name}</option>
		<!-- END reported_by -->
	</select><br />
	
	<label for="ticket_reported_note">{lang_reported_note}:</label>
	<input type="text" name="ticket[reported_note]" id="ticket_reported_note" /><br />
	
	<label for="ticket_reported_via">{lang_reported_via}</label>
	<select name="ticket[reported_via]" id="ticket_reported_via">
		<option value="0">{lang_please_select}</option>
		<!-- BEGIN reported_via -->
		<option value="{via_id}">{via_text}</option>
		<!-- END reported_via -->
	</select><br />

	<label for="ticket_cat_top">{lang_category}:</label>
	<select name="ticket[cat_top]" id="ticket_cat_top">
		<option value="0">{lang_please_select}</option>
		{value_cat_top}
	</select>

	<select name="ticket[category]" id="ticket_category" disabled="disabled">
		<option></option>
	</select><br />

	<label for="ticket_priority">{lang_priority}:</label>
	<select name="ticket[priority]" id="ticket_priority">
		<!-- BEGIN ticket_priority -->
			<option value="{priority_val}" {priority_selected}>{priority_text}</option>
		<!-- END ticket_priority -->
	</select><br />

	<label for="ticket_group">{lang_assignedto}:</label>
	<select name="ticket[group]" id="ticket_group">
		<option value="0">{lang_please_select}</option>
		<!-- BEGIN ticket_group -->
			<option value="{group_id}" {group_selected}>{group_name}</option>
		<!-- END ticket_group -->		
	</select>

	<select name="ticket[assignedto]" id="ticket_assignedto" disabled="disabled">
		<option></option>
	</select><br />
		
	<label for="ticket_effort">{lang_effort}:</label>
	<input type="text" name="ticket[effort]" id="ticket_effort" /><br />

	<label for="ticket_billable_hours">{lang_billable_hours}:</label>
	<input name="ticket[billable_hours]" id="ticket_billable_hours" value="{value_billable_hours}" /><br />

	<label for="ticket_billable_rate">{lang_billable_hours_rate}:</label>
	<input name="ticket[billable_rate]" id="ticket_billable_rate" value="{value_billable_hours}" /><br />

	<label for="ticket_deadline">{lang_deadline}:</label>
	{ticket_deadline}<br />

	<label for="attachment">{lang_attachment}:</label>	
	<input type="file" name="attachment" id="attachment" /><br />

	<label for="ticket_subject">{lang_subject}:</label>
	<input name="ticket[subject]" id="ticket_subject" value="{value_subject}" /><br />

	<label for="ticket_details">{lang_details}:</label>
	<textarea name="ticket[details]" id="ticket_details" rows="4" cols="50">{value_details}</textarea>

	<div class="btngrp">
		<button type="button" name="help" id="help" value="0" onclick="showHelp();" class="help">
			<img src="{img_help}" alt="{lang_help}" /><span class="btnlabel">{lang_help}</span>
		</button>
		
		<button type="submit" name="cancel" id="cancel" value="0" onclick="this.value=1; return true;">
			<img src="{img_cancel}" alt="{lang_cancel}" /><span class="btnlabel">{lang_cancel}</span>
		</button>

		<button type="submit" name="submit" id="submit" value="0" onclick="this.value=1; return true;">
			<img src="{img_ok}" alt="{lang_ok}" /><span class="btnlabel">{lang_ok}</span>
		</button><br />
	</div>
</form>
<!-- END form -->
