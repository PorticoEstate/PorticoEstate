<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered pure-table-striped pure-form">
		<tr class="th">
			<td colspan="2">&nbsp;<b>{title}</b></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr class="row_on">
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="row_off">
			<td colspan="2">&nbsp;<b>{lang_booking_settings}</b></td>
		</tr>
		<tr class="row_on">
			<td>{lang_external_host_address}:{lang_example}: https://www.bergen.kommune.no</td>
			<td><input name="newsettings[external_site_address]" value="{value_external_site_address}" class="pure-u-1"/></td>
		</tr>
		<tr class="row_off">
			<td>{lang_custom_email_sender}:{lang_example}: noreply&lt;noreply@Bergen.Kommune.no&gt;</td>
			<td><input name="newsettings[email_sender]" value="{value_email_sender}" class="pure-u-1"/></td>
		</tr>
		<tr class="row_off">
			<td>Reply to:</td>
			<td><input name="newsettings[email_reply_to]" value="{value_email_reply_to}" class="pure-u-1"/></td>
		</tr>
		<tr class="row_on">
			<td>{lang_logopath_frontend}:{lang_example}: /phpgwapi/templates/bkbooking/images/bergen_logo.png</td>
			<td><input name="newsettings[logopath_frontend]" value="{value_logopath_frontend}" class="pure-u-1"/></td>
		</tr>
		<tr class="row_off">
			<td>{lang_image_maxheight}:{lang_example}: 300</td>
			<td><input name="newsettings[image_maxheight]" value="{value_image_maxheight}" class="pure-u-1"/></td>
		</tr>
		<tr class="row_on">
			<td>{lang_image_maxwidth}:{lang_example}: 300</td>
			<td><input name="newsettings[image_maxwidth]" value="{value_image_maxwidth}" class="pure-u-1"/></td>
		</tr>
		<tr class="row_off">
			<td>{lang_support_email_address}:</td>
			<td>
				<input name="newsettings[support_address]" value="{value_support_address}" size="40"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_e_lock_request_method}:</td>
			<td>
				<select name="newsettings[e_lock_request_method]" class="pure-u-1">
					{hook_request_method}
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_proxy}:</td>
			<td>
				<input name="newsettings[proxy]" value="{value_proxy}" class="pure-u-1"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_e_lock_webservice}:</td>
			<td>
				<input name="newsettings[e_lock_webservice]" value="{value_e_lock_webservice}" class="pure-u-1"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_e_lock_login}:</td>
			<td>
				<input name="newsettings[e_lock_login]" value="{value_e_lock_login}" class="pure-u-1"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_e_lock_password}:</td>
			<td>
				<input name="newsettings[e_lock_password]" value="{value_e_lock_password}" class="pure-u-1"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_participant_limit}:</td>
			<td>
				<input name="newsettings[participant_limit]" value="{value_participant_limit}" class="pure-u-1"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_participant_limit_sms}:</td>
			<td>
				<select name="newsettings[participant_limit_sms]" class="pure-u-1">
					<option value="">{lang_No}</option>
					<option value="True"{selected_participant_limit_sms_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_enable_upload_attachment}:</td>
			<td>
				<select name="newsettings[enable_upload_attachment]" class="pure-u-1">
					<option value="" {selected_enable_upload_attachment_}>{lang_No}</option>
					<option value="1" {selected_enable_upload_attachment_1}>{lang_Yes}</option>
				</select>
			</td>
		</tr>

		<tr class="row_off">
			<td>{lang_billing_delay}:</td>
			<td>
				<input type="number" min="0" max="15" name="newsettings[billing_delay]" value="{value_billing_delay}" class="pure-u-1"/>
			</td>
		</tr>

		<tr class="row_off">
			<td>{lang_activate_application_articles}:</td>
			<td>
				<select name="newsettings[activate_application_articles]" class="pure-u-1">
					<option value="">{lang_No}</option>
					<option value="True"{selected_activate_application_articles_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_landing_sections}:</td>
			<td>
				<table class='table'>
					{hook_landing_sections}
				</table>
			</td>
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
				<input type="submit" name="submit" value="{lang_submit}" class="pure-button"/>
				<input type="submit" name="cancel" value="{lang_cancel}" class="pure-button"/>
			</td>
		</tr>
	</table>
</form>
<!-- END footer -->
