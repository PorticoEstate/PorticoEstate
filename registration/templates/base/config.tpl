<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered pure-table-striped pure-form">
		<tr bgcolor="{th_bg}">
			<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</tr>
		<!-- END header -->

		<!-- BEGIN body -->
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td colspan="2"><b>{lang_Registration_settings}</b></td>
		</tr>
		<tr>
			<td>{lang_Use_trial_accounts?}</td>
			<td>
				<select name="newsettings[trial_accounts]" class="pure-u-1">
					<option value=""{selected_trial_accounts_False}>{lang_No}</option>
					<option value="True"{selected_trial_accounts_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_Days_until_trial_accounts_expire}:</td>
			<td><input name="newsettings[days_until_trial_account_expires]" value="{value_days_until_trial_account_expires}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_Display_Terms_of_Service?}</td>
			<td>
				<select name="newsettings[display_tos]" class="pure-u-1">
					<option value=""{selected_display_tos_False}>{lang_No}</option>
					<option value="True"{selected_display_tos_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_terms_of_service}</td>
			<td>
				<textarea cols="40" rows="4" name="newsettings[terms_of_service]" wrap="virtual">{value_terms_of_service}</textarea>
			</td>
		</tr>
		<tr>
		<tr class="row_on">
			<td>{lang_default_group}:</td>
			<td>
				<select name="newsettings[default_group_id]" class="pure-u-1">
					{hook_default_group}
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_Activate_account}:</td>
			<td>
				<select name="newsettings[activate_account]" class="pure-u-1">
					<option value="pending_approval"{selected_activate_account_pending_approval}>{lang_pending_approval}</option>
					<option value="email"{selected_activate_account_email}>{lang_Send_Email}</option>
					<option value="immediately"{selected_activate_account_immediately}>{lang_Immediately}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_Username_is}:</td>
			<td>
				<select name="newsettings[username_is]" class="pure-u-1">
					<option value="choice"{selected_username_is_choice}>{lang_Users_Choice}</option>
					<option value="email"{selected_username_is_email}>{lang_email_address}</option>
					<option value="http"{selected_username_is_http}>{lang_HTTP_Username}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_Password_is}:</td>
			<td>
				<select name="newsettings[password_is]" class="pure-u-1">
					<option value="choice"{selected_password_is_choice}>{lang_Users_Choice}</option>
					<option value="http"{selected_password_is_http}>{lang_HTTP_Password}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_Anonymous_user}:</td>
			<td><input name="newsettings[anonymous_user]" value="{value_anonymous_user}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_Anonymous_password}:</td>
			<td><input type="password" name="newsettings[anonymous_pass]" value="{value_anonymous_pass}" autocomplete="off"
					   readonly="readonly" onfocus="this.removeAttribute('readonly');" class="pure-u-1"/></td>
		</tr>

		<tr>
			<td>{lang_Email_address_registration_admin}:</td>
			<td><input name="newsettings[registration_admin]" value="{value_registration_admin}" class="pure-u-1"/></td>
		</tr>

		<tr>
			<td>{lang_Email_address_to_send_notices_from}:</td>
			<td><input name="newsettings[mail_nobody]" value="{value_mail_nobody}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_Email_address_to_display_for_support}:</td>
			<td><input name="newsettings[support_email]" value="{value_support_email}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_Subject_for_confirmation_email}:</td>
			<td><input name="newsettings[subject_confirm]" value="{value_subject_confirm}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_messenger_welcome_message}</td>
			<td>
				<textarea cols="40" rows="4" name="newsettings[messenger_welcome_message]" wrap="virtual">{value_messenger_welcome_message}</textarea>
			</td>
		</tr>
		<tr>
			<td>{lang_registration_form_header}:</td>
			<td><input name="newsettings[form_header]" value="{value_form_header}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_login_url}:</td>
			<td><input name="newsettings[login_url]" value="{value_login_url}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_Debug}:</td>
			<td>
				<select name="newsettings[debug]" class="pure-u-1">
					<option value="" {selected_debug_}>NO</option>
					<option value="1" {selected_debug_1}>YES</option>
				</select>
			</td>
		</tr>

			<!-- END body -->

			<!-- BEGIN footer -->
		<tr bgcolor="{th_bg}">
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
