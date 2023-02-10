<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered pure-table-striped pure-form">
		<tr>
			<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;<b>{lang_mobilefrontend}</b></td>
		</tr>
		<tr>
			<td>{lang_auth_type}:</td>
			<td>
				<select name="newsettings[auth_type]" class="pure-u-1">
					<option value="0" {selected_auth_type_0}>Same as framework</option>
					<option value="sql" {selected_auth_type_sql}>SQL</option>
					<option value="customsso" {selected_auth_type_customsso}>Custom SSO</option>
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
			<td>{lang_Use_ssl_for_backend_links}:</td>
			<td>
				<select name="newsettings[backend_ssl]" class="pure-u-1">
					<option value="">{lang_No}</option>
					<option value="True"{selected_backend_ssl_True}>{lang_Yes}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{lang_cookie_domain_for_sessions_-_if_Same_as_framework_leave_empty}</td>
			<td><input name="newsettings[cookie_domain]" value="{value_cookie_domain}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_Anonymous_user}:</td>
			<td><input name="newsettings[anonymous_user]" value="{value_anonymous_user}" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_Anonymous_password}:</td>
			<td><input type="password" name="newsettings[anonymous_passwd]" value="{value_anonymous_passwd}" autocomplete="off"
					   readonly="readonly" onfocus="this.removeAttribute('readonly');" class="pure-u-1"/></td>
		</tr>
		<tr>
			<td>{lang_external_host_address}:{lang_example}: https://www.bergen.kommune.no</td>
			<td><input name="newsettings[external_site_address]" value="{value_external_site_address}"/></td>
		</tr>
		<!-- END body -->
		<!-- BEGIN footer -->
		<tr>
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
