<p class="errors">{messages}</p>
<div id="news_nlbuild">
	<form name="preview" method="POST" action="{form_action}">
		<div id="nlb_content1">
			<label for="subject">{lang_subject}:</label>
			<input type="text" id="subject" name="subject">
			<br>

			<label for="cc_recipients_text">{lang_cc_recipients}:</label>
			<input type="text" name="cc_recepients_text" id="cc_recipients_text">
			<input type="button" value="{lang_add}" onClick="addRecipient()" class="small">
			<br>

			<select id="cc_recipients" name="cc_recipients" size="5" multiple="multiple"></select>
			<input type="button" name="remove" value="{lang_remove}" onClick="removeSelected();" class="small"><br>
		</div>

		<div id="nlb_content2">
			<table>
				<col class="check">
				<col class="title">
				<col class="author">
				<col class="news_teaser">
				<thead>
					<tr>
						<th class="check">&nbsp;</th>
						<th class="title">{lang_title}</th>
						<th class="author">{lang_author}</th>
						<th class="news_teaser">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<!-- BEGIN article -->
					<tr class="{css_row}">
						<td class="check"><input type="checkbox" id="check_{id}" name="check_{id}" value="1" onClick="toggleStory({id})"></td>
						<td class="title" id="title_{id}">{subject}</td>
						<td class="author">{author}</td>
						<td id="teaser_{id}" class="news_teaser">{teaser}</td>
					</tr>
					<!-- END article -->
				</tbody>
			</table>
		</div>
		
		<div id="nlb_content3">
			<textarea id="nl_content" name="nl_content">
				{nl_content}
			</textarea>

			<div class="buttons">
				<button type="button" name="help" id="help" onClick="window.open('{href_help}'); return false;">
					<img src="{img_help}" alt="{lang_help}">{lang_help}</button>
				<button type="button" name="cancel" id="cancel" onClick="(confirm('{lang_all_changes_will_be_lost}') ? window.location = '{href_cancel}' : false)">
					<img src="{img_cancel}" alt="{lang_cancel}"> {lang_cancel}</button>
				<button type="submit" name="send" id="send" value="1" onClick="selectAllBCC(); return true">
					<img src="{img_send}" alt="{lang_send}">{lang_send}</button>
			</div>
		</div>
	</form>
</div>
