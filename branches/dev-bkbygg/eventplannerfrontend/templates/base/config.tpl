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
		<tr>
			<td colspan="2">&nbsp;<b>{lang_bookingfrontend_settings}</b></td>
		</tr>
		<tr>
			<td>{lang_site_title}</td>
			<td><input name="newsettings[site_title]" value='{value_site_title}'></td>
		</tr>
		<tr>
			<td>{lang_footer_info}</td>
			<td><input name="newsettings[footer_info]" value='{value_footer_info}'></td>
		</tr>
		<tr>
			<td>{lang_auth_type}:</td>
			<td>
				<select name="newsettings[auth_type]">
					<option value="0" {selected_auth_type_0}>Same as framework</option>
					<option value="sql" {selected_auth_type_sql}>SQL</option>
					<option value="customsso" {selected_auth_type_customsso}>Custom SSO</option>
				</select>
			</td>
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
			<td>{lang_Use_cookies_to_pass_sessionid}:</td>
			<td>
				<select name="newsettings[usecookies]">
					<option value="">{lang_No}</option>
					<option value="True"{selected_usecookies_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_cookie_domain_for_sessions_-_if_Same_as_framework_leave_empty}</td>
			<td><input name="newsettings[cookie_domain]" value="{value_cookie_domain}"></td>
		</tr>
		<tr>
			<td>{lang_Anonymous_user}:</td>
			<td><input name="newsettings[anonymous_user]" value="{value_anonymous_user}"></td>
		</tr>
		<tr>
			<td>{lang_Anonymous_password}:</td>
			<td><input type="password" name="newsettings[anonymous_passwd]" value="{value_anonymous_passwd}"></td>
		</tr>
		<tr>
			<td>{lang_custom_login_url}:</td>
			<td><input name="newsettings[custom_login_url]" value="{value_custom_login_url}"></td>
		</tr>
		<tr>
			<td>{lang_custom_login_url_parameter}:</td>
			<td><input name="newsettings[login_parameter]" value="{value_login_parameter}"></td>
		</tr>
		<tr>
			<td>{lang_login_header_key}:</td>
			<td><input name="newsettings[header_key]" value="{value_header_key}"></td>
		</tr>
		<tr>
			<td>{lang_login_header_regular_expression}:</td>
			<td><input name="newsettings[header_regular_expression]" value="{value_header_regular_expression}"></td>
		</tr>
		<tr>
			<td>{lang_login_soap_client_location}:</td>
			<td><input name="newsettings[soap_location]" value="{value_soap_location}"></td>
		</tr>
		<tr>
			<td>{lang_login_soap_client_uri}:</td>
			<td><input name="newsettings[soap_uri]" value="{value_soap_uri}"></td>
		</tr>
		<tr>
			<td>{lang_login_soap_client_proxy_host}:</td>
			<td><input name="newsettings[soap_proxy_host]" value="{value_soap_proxy_host}"></td>
		</tr>
		<tr>
			<td>{lang_login_soap_client_proxy_port}:</td>
			<td><input name="newsettings[soap_proxy_port]" value="{value_soap_proxy_port}"></td>
		</tr>
		<tr>
			<td>{lang_login_soap_client_encoding}:</td>
			<td><input name="newsettings[soap_encoding]" value="{value_soap_encoding}"></td>
		</tr>
		<tr>
			<td>{lang_login_soap_client_login}:</td>
			<td><input name="newsettings[soap_login]" value="{value_soap_login}"></td>
		</tr>
		<tr>
			<td>{lang_login_soap_client_password}:</td>
			<td><input type ="password" name="newsettings[soap_password]" value="{value_soap_password}"></td>
		</tr>
		<tr>
			<td>{lang_login_soap_client_wsdl}:</td>
			<td><input name="newsettings[soap_wsdl]" value="{value_soap_wsdl}"></td>
		</tr>
		<tr>
			<td>{lang_Debug}:</td>
			<td>
				<select name="newsettings[debug]">
					<option value="" {selected_debug_}>NO</option>
					<option value="1" {selected_debug_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_google_tracker_id}:</td>
			<td><input name="newsettings[tracker_id]" value="{value_tracker_id}"></td>
		</tr>
		<tr>
			<td>{lang_url_to_external_logout}:
				<br/> Redirect is computed if url ends with '='
			</td>
			<td><input name="newsettings[external_logout]" value="{value_external_logout}"></td>
		</tr>
		<tr>
			<td>{lang_bookingfrontend_host}:
				<br/> Needed for the return from the external_logout
			</td>
			<td><input name="newsettings[bookingfrontend_host]" value="{value_bookingfrontend_host}"></td>
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
