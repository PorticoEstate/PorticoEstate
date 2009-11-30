<p class="errors">{errors}</p>

<form id="news_form" method="POST" name="news_form" action="{form_action}">
	<fieldset>
		<legend>{lang_header}</legend>

		<label for="news_subject">{label_subject}</label>
		<input id="news_subject" name="news[subject]" size="60" value="{value_subject}" /><br>

		<label for="news_teaser">{label_teaser}</label>
		<textarea id="news_teaser" name="news[teaser]" cols=60 rows="3">{value_teaser}</textarea><br>

		<textarea id="news_content" name="news[content]" cols="60" rows="6">{value_content}</textarea><br>

		<label for="news_category">{label_category}</label>
		<select id="news_category" name="news[category]">
			{value_category}
		</select><br>

		<label for="from">{label_visible}</label>
		<div id="date_ranges">
			<select id="from" onChange="toggle();" name="from">
				<option value="1" {from_always_selected}>{lang_always}</option>
				<option value="0" {from_never_selected}>{lang_never}</option>
				<option value="0.5" {from_from_selected}>{lang_from}</option>
			</select>
			<div id="visible_until">
				{value_begin}
				<select id="until" onChange="toggle();" name="nuntil">
					<option value="1" {to_always_selected}>{lang_always}</option>
					<option value="0.5" {to_until_selected}>{lang_until}</option>
				</select>
				<div id="end">{value_end}</div>
			</div>
		</div>
		<br />

		<div class="buttons">
			<button type="submit" name="cancel">
				<img src="{img_cancel}" alt="{lang_cancel}">{lang_cancel}</button>
			<button type="submit" name="submitit">
				<img src="{img_save}" alt="{lang_save}">{lang_save}</button>
		</div>
	</fieldset>
	<input type="hidden" name="news[id]" value="{value_id}" />
	<input type="hidden" name="news[is_html]" value="1" />
</form>
