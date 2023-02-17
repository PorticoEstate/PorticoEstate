<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered pure-table-striped pure-form">
		<tr class="th">
			<td colspan="2">&nbsp;<b>{title}</b></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;<b>{lang_bookingfrontend_settings}</b></td>
		</tr>

		<tr >
			<td>{lang_develope_mode}:</td>
			<td>
				<select name="newsettings[develope_mode]" class="pure-u-1">
					<option value="">{lang_No}</option>
					<option value="True"{selected_develope_mode_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_site_title}</td>
			<td><input name="newsettings[site_title]" value="{value_site_title}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_footer_info}</td>
			<td><input name="newsettings[footer_info]" value="{value_footer_info}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_footer_privacy_link}</td>
			<td><input name="newsettings[footer_privacy_link]" value="{value_footer_privacy_link}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_remote_authentication}:</td>
			<td>
				<select name="newsettings[authentication_method]" class="pure-u-1">
					{hook_authentication}
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_get_name_from_external_source}:</td>
			<td>
				<select name="newsettings[get_name_from_external]" class="pure-u-1">
					{hook_get_name_from_external}
				</select>
			</td>
		</tr>


		<tr>
			<td>{lang_bypass_external_login}:</td>
			<td>
				<select name="newsettings[bypass_external_login]" class="pure-u-1">
					<option value="">{lang_No}</option>
					<option value="True"{selected_bypass_external_login_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_Use_cookies_to_pass_sessionid}:</td>
			<td>
				<select name="newsettings[usecookies]" class="pure-u-1">
					<option value="">{lang_No}</option>
					<option value="True"{selected_usecookies_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_cookie_domain_for_sessions} - {lang_if_Same_as_framework_leave_empty}</td>
			<td><input name="newsettings[cookie_domain]" value="{value_cookie_domain}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_Anonymous_user}:</td>
			<td><input name="newsettings[anonymous_user]" value="{value_anonymous_user}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_Anonymous_password}:</td>
			<td>
				<input type="password" name="newsettings[anonymous_passwd]" value="{value_anonymous_passwd}" autocomplete="off"
				   readonly="readonly" onfocus="this.removeAttribute('readonly');" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_custom_login_url}:</td>
			<td><input name="newsettings[custom_login_url]" value="{value_custom_login_url}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_custom_login_url_parameter}:</td>
			<td><input name="newsettings[login_parameter]" value="{value_login_parameter}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_login_apikey} MinId2:</td>
			<td><input name="newsettings[apikey]" value="{value_apikey}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_login_webservicehost} MinId2:</td>
			<td><input name="newsettings[webservicehost]" value="{value_webservicehost}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_login_apikey} Fiks folkeregister:</td>
			<td><input name="newsettings[apikey_fiks_folkeregister]" value="{value_apikey_fiks_folkeregister}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_login_role_id} Fiks folkeregister:</td>
			<td><input name="newsettings[role_id_fiks_folkeregister]" value="{value_role_id_fiks_folkeregister}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_username} Fiks folkeregister:</td>
			<td><input name="newsettings[username_fiks_folkeregister]" value="{value_username_fiks_folkeregister}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_password} Fiks folkeregister:</td>
			<td><input type="password" name="newsettings[password_fiks_folkeregister]" value="{value_password_fiks_folkeregister}" autocomplete="off"
				readonly="readonly" onfocus="this.removeAttribute('readonly');" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_login_webservicehost} Fiks folkeregister:</td>
			<td><input name="newsettings[webservicehost_fiks_folkeregister]" value="{value_webservicehost_fiks_folkeregister}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_Debug_external_logn}:</td>
			<td>
				<select name="newsettings[debug]" class="pure-u-1">
					<option value="" {selected_debug_}>NO</option>
					<option value="1" {selected_debug_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_Debug_local_login}:</td>
			<td>
				<select name="newsettings[debug_local_login]" class="pure-u-1">
					<option value="" {selected_debug_local_login_}>NO</option>
					<option value="1" {selected_debug_local_login_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_test_organization}:</td>
			<td><input name="newsettings[test_organization]" value="{value_test_organization}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_test_person}:</td>
			<td><input name="newsettings[test_ssn]" value="{value_test_ssn}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_google_tracker_id}:</td>
			<td><input name="newsettings[tracker_id]" value="{value_tracker_id}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>Matomo url:</td>
			<td><input name="newsettings[tracker_matomo_url]" value="{value_tracker_matomo_url}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>Matomo site id:</td>
			<td><input type="number" name="newsettings[tracker_matomo_id]" value="{value_tracker_matomo_id}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_support_email_address}:</td>
			<td><input name="newsettings[support_address]" value="{value_support_address}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_url_to_external_logout}:
				<br/> {lang_Redirect_is_computed_if_url_ends_with} '='
			</td>
			<td><input name="newsettings[external_logout]" value="{value_external_logout}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_bookingfrontend_host}:
				<br/> {lang_Needed_for_the_return_from_the_external_logout}
			</td>
			<td><input name="newsettings[bookingfrontend_host]" value="{value_bookingfrontend_host}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_manual}:</td>
			<td><input name="newsettings[bookingfrontend_manual]" value="{value_bookingfrontend_manual}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_uustatus}:</td>
			<td><input name="newsettings[url_uustatus]" value="{value_url_uustatus}" class="pure-u-1"/></td>
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
