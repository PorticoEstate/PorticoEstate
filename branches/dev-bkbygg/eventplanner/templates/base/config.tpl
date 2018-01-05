<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered">
		<tr class="th">
			<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr>
			<td>{lang_default_application_category}:</td>
			<td>
				<select name="newsettings[default_application_category]">
					{hook_default_application_category}
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_default_vendor_category}:</td>
			<td>
				<select name="newsettings[default_vendor_category]">
					{hook_default_vendor_category}
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_booking_interval}:</td>
			<td>
				<input type="number" name="newsettings[booking_interval]" value="{value_booking_interval}">
			</td>
		</tr>
		<tr>
			<td>{lang_active_application_year}</td>
			<td><input type="number" name="newsettings[active_year]" value="{value_active_year}"></td>
		</tr>
		<tr>
			<td>{lang_receipt_blind_copy}:</td>
			<td>
				<input type="text" name="newsettings[receipt_blind_copy]" value="{value_receipt_blind_copy}">
			</td>
		</tr>
		<tr>
			<td>{lang_receipt_subject}:</td>
			<td>
				<input type="text" name="newsettings[receipt_subject]" value="{value_receipt_subject}">
			</td>
		</tr>
		<tr>
			<td>{lang_vendor_receipt_text}:</td>
			<td>
				<textarea id="vendor_receipt_text" name="newsettings[vendor_receipt_text]">{value_vendor_receipt_text}</textarea>
			</td>
			{hook_vendor_receipt_text_editor}
		</tr>
		<tr>
			<td>{lang_customer_receipt_text}:</td>
			<td>
				<textarea id="customer_receipt_text" name="newsettings[customer_receipt_text]">{value_customer_receipt_text}</textarea>
			</td>
			{hook_customer_receipt_text_editor}
		</tr>
		<tr>
			<td>{lang_canceled_subject}:</td>
			<td>
				<input type="text" name="newsettings[canceled_subject]" value="{value_canceled_subject}">
			</td>
		</tr>
		<tr>
			<td>{lang_vendor_canceled_text}:</td>
			<td>
				<textarea id="vendor_canceled_text" name="newsettings[vendor_canceled_text]">{value_vendor_canceled_text}</textarea>
			</td>
			{hook_vendor_canceled_text_editor}
		</tr>
		<tr>
			<td>{lang_customer_canceled_text}:</td>
			<td>
				<textarea id="customer_canceled_text" name="newsettings[customer_canceled_text]">{value_customer_canceled_text}</textarea>
			</td>
			{hook_customer_canceled_text_editor}
		</tr>
		<tr>
			<td>{lang_uploader_filetypes}: jpg,gif,png</td>
			<td><input name="newsettings[uploader_filetypes]" value="{value_uploader_filetypes}"></td>
		</tr>

		<!-- END body -->
		<!-- BEGIN footer -->

		<tr class="th">
			<td colspan="2">
				&nbsp;
			</td>
		</tr>

		<tr>
			<td colspan="2" align="center">
				<input type="submit" name="submit" value="{lang_submit}">
				<input type="submit" name="cancel" value="{lang_cancel}">
			</td>
		</tr>
	</table>
</form>
<!-- END footer -->
