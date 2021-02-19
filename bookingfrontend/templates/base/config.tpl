<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered">
		<tr class="th">
			<td colspan="2">&nbsp;<b>{title}</b></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="pure-table-odd">
			<td colspan="2">&nbsp;<b>{lang_bookingfrontend_settings}</b></td>
		</tr>
		<tr>
			<td>{lang_site_title}</td>
			<td><input name="newsettings[site_title]" value="{value_site_title}"></td>
		</tr>
		<tr>
			<td>{lang_footer_info}</td>
			<td><input name="newsettings[footer_info]" value="{value_footer_info}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_footer_privacy_link}</td>
			<td><input name="newsettings[footer_privacy_link]" value="{value_footer_privacy_link}"></td>
		</tr>
		<tr>
			<td>{lang_remote_authentication}:</td>
			<td>
				<select name="newsettings[authentication_method]">
					{hook_authentication}
				</select>
			</td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_get_name_from_external_source}:</td>
			<td>
				<select name="newsettings[get_name_from_external]">
					{hook_get_name_from_external}
				</select>
			</td>
		</tr>


		<tr>
			<td>{lang_bypass_external_login}:</td>
			<td>
				<select name="newsettings[bypass_external_login]">
					<option value="">{lang_No}</option>
					<option value="True"{selected_bypass_external_login_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_Use_cookies_to_pass_sessionid}:</td>
			<td>
				<select name="newsettings[usecookies]">
					<option value="">{lang_No}</option>
					<option value="True"{selected_usecookies_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_cookie_domain_for_sessions} - {lang_if_Same_as_framework_leave_empty}</td>
			<td><input name="newsettings[cookie_domain]" value="{value_cookie_domain}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_Anonymous_user}:</td>
			<td><input name="newsettings[anonymous_user]" value="{value_anonymous_user}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_Anonymous_password}:</td>
			<td><input type="password" name="newsettings[anonymous_passwd]" value="{value_anonymous_passwd}" autocomplete="off"></td>
		</tr>
		<tr>
			<td>{lang_custom_login_url}:</td>
			<td><input name="newsettings[custom_login_url]" value="{value_custom_login_url}"></td>
		</tr>
		<tr>
			<td>{lang_custom_login_url_parameter}:</td>
			<td><input name="newsettings[login_parameter]" value="{value_login_parameter}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_apikey} MinId2:</td>
			<td><input name="newsettings[apikey]" value="{value_apikey}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_webservicehost} MinId2:</td>
			<td><input name="newsettings[webservicehost]" value="{value_webservicehost}"></td>
		</tr>
		<tr>
			<td>{lang_login_apikey} Fiks folkeregister:</td>
			<td><input name="newsettings[apikey_fiks_folkeregister]" value="{value_apikey_fiks_folkeregister}"></td>
		</tr>
		<tr>
			<td>{lang_login_role_id} Fiks folkeregister:</td>
			<td><input name="newsettings[role_id_fiks_folkeregister]" value="{value_role_id_fiks_folkeregister}"></td>
		</tr>
		<tr>
			<td>{lang_username} Fiks folkeregister:</td>
			<td><input name="newsettings[username_fiks_folkeregister]" value="{value_username_fiks_folkeregister}"></td>
		</tr>
		<tr>
			<td>{lang_password} Fiks folkeregister:</td>
			<td><input type="password" name="newsettings[password_fiks_folkeregister]" value="{value_password_fiks_folkeregister}" autocomplete="off"></td>
		</tr>
		<tr>
			<td>{lang_login_webservicehost} Fiks folkeregister:</td>
			<td><input name="newsettings[webservicehost_fiks_folkeregister]" value="{value_webservicehost_fiks_folkeregister}"></td>
		</tr>

		<tr class="pure-table-odd">
			<td>{lang_login_header_key}:</td>
			<td><input name="newsettings[header_key]" value="{value_header_key}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_header_regular_expression}:</td>
			<td><input name="newsettings[header_regular_expression]" value="{value_header_regular_expression}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_soap_client_location}:</td>
			<td><input name="newsettings[soap_location]" value="{value_soap_location}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_soap_client_uri}:</td>
			<td><input name="newsettings[soap_uri]" value="{value_soap_uri}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_soap_client_proxy_host}:</td>
			<td><input name="newsettings[soap_proxy_host]" value="{value_soap_proxy_host}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_soap_client_proxy_port}:</td>
			<td><input name="newsettings[soap_proxy_port]" value="{value_soap_proxy_port}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_soap_client_encoding}:</td>
			<td><input name="newsettings[soap_encoding]" value="{value_soap_encoding}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_soap_client_login}:</td>
			<td><input name="newsettings[soap_login]" value="{value_soap_login}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_soap_client_password}:</td>
			<td><input type ="password" name="newsettings[soap_password]" value="{value_soap_password}" autocomplete="off"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_login_soap_client_wsdl}:</td>
			<td><input name="newsettings[soap_wsdl]" value="{value_soap_wsdl}"></td>
		</tr>
		<tr>
			<td>{lang_Debug_external_logn}:</td>
			<td>
				<select name="newsettings[debug]">
					<option value="" {selected_debug_}>NO</option>
					<option value="1" {selected_debug_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_Debug_local_login}:</td>
			<td>
				<select name="newsettings[debug_local_login]">
					<option value="" {selected_debug_local_login_}>NO</option>
					<option value="1" {selected_debug_local_login_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_test_organization}:</td>
			<td><input name="newsettings[test_organization]" value="{value_test_organization}"></td>
		</tr>
		<tr>
			<td>{lang_test_person}:</td>
			<td><input name="newsettings[test_ssn]" value="{value_test_ssn}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_google_tracker_id}:</td>
			<td><input name="newsettings[tracker_id]" value="{value_tracker_id}"></td>
		</tr>
		<tr>
			<td>{lang_support_email_address}:</td>
			<td><input name="newsettings[support_address]" value="{value_support_address}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_url_to_external_logout}:
				<br/> {lang_Redirect_is_computed_if_url_ends_with} '='
			</td>
			<td><input name="newsettings[external_logout]" value="{value_external_logout}"></td>
		</tr>
		<tr class="pure-table-odd">
			<td>{lang_bookingfrontend_host}:
				<br/> {lang_Needed_for_the_return_from_the_external_logout}
			</td>
			<td><input name="newsettings[bookingfrontend_host]" value="{value_bookingfrontend_host}"></td>
		</tr>
		<tr>
			<td>{lang_manual}:</td>
			<td><input name="newsettings[bookingfrontend_manual]" value="{value_bookingfrontend_manual}"></td>
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
