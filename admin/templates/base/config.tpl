<!-- BEGIN header -->
<div class="{error_class} msg">
	{error}
</div>
<form method="post" action="{action_url}">
	<table class="pure-table pure-table-bordered pure-table-striped pure-form">
		<thead>
			<tr>
				<th colspan="2">{title}</th>
			</tr>
		</thead>
		<tbody>
			<!-- END header -->
			<!-- BEGIN body -->
			<!--tr class="pure-table-odd">
				<td>{lang_Would_you_like_to_check_for_a_new_version_when_admins_login}?:</td>
				<td>
					<select name="newsettings[checkfornewversion]">
						<option value="">{lang_No}</option>
						<option value="True"{selected_checkfornewversion_True}>{lang_Yes}</option>
					</select>
				</td>
			</tr-->
			<tr>
				<td>{lang_cache_refresh_token}:</td>
				<td><input size="8" name="newsettings[cache_refresh_token]" value="{value_cache_refresh_token}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_privacy_url}:</td>
				<td><input name="newsettings[privacy_url]" value="{value_privacy_url}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_privacy_message}:</td>
				<td><input name="newsettings[privacy_message]" value="{value_privacy_message}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_Timeout_for_sessions_in_seconds} (default 14400 = 4 hours):</td>
				<td><input size="8" name="newsettings[sessions_timeout]" value="{value_sessions_timeout}" class="pure-u-1"/></td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_Timeout_for_application_session_data_in_seconds} (default 86400 = 1 day):</td>
				<td><input size="8" name="newsettings[sessions_app_timeout]" value="{value_sessions_app_timeout}" class="pure-u-1"/></td>
			</tr>

			<tr>
				<td>{lang_Would_you_like_to_show_each_applications_upgrade_status}?:</td><td>
					<select name="newsettings[checkappversions]" class="pure-u-1">
						<option value="">{lang_No}</option>
						<option value="Admin"{selected_checkappversions_Admin}>{lang_Admins}</option>
						<option value="All"{selected_checkappversions_All}>{lang_All_Users}</option>
					</select>
				</td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_Would_you_like_phpGroupWare_to_cache_the_phpgw_info_array}?:</td>
				<td>
					<select name="newsettings[cache_phpgw_info]" class="pure-u-1">
						<option value="">{lang_No}</option>
						<option value="True"{selected_cache_phpgw_info_True}>{lang_Yes}</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>{lang_Maximum_entries_in_click_path_history}:</td>
				<td><input size="8" name="newsettings[max_history]" value="{value_max_history}" class="pure-u-1"/></td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_Would_you_like_to_automaticaly_load_new_langfiles_at_login_time}?:</td>
				<td>
					<select name="newsettings[disable_autoload_langfiles]" class="pure-u-1">
						<option value="">{lang_Yes}</option>
						<option value="True"{selected_disable_autoload_langfiles_True}>{lang_No}</option>
					</select>
				</td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_Would_you_like_phpGroupWare_to_cache_data_in_shared_memory}?:</td>
				<td>
					<select name="newsettings[shm_enable]" class="pure-u-1">
						<option value="">{lang_No}</option>
						<option value="True"{selected_shm_enable_True}>{lang_Yes}</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>{lang_Would_you_like_phpGroupWare_to_cache_data_in_redis_database}?:</td>
				<td>
					<select name="newsettings[redis_enable]" class="pure-u-1">
						<option value="">{lang_No}</option>
						<option value="True"{selected_redis_enable_True}>{lang_Yes}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_redis_host}: (port 6379)</td>
				<td><input size="8" name="newsettings[redis_host]" value="{value_redis_host}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_redis_database}: (0 - 15)</td>
				<td><input type="number" min="0" max="15" name="newsettings[redis_database]" value="{value_redis_database}" class="pure-u-1"/></td>
			</tr>

			<tr class="th">
				<td colspan="2">&nbsp;<b>{lang_SMTP_settings}</b></td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_SMTP_server_hostname_or_IP_address}:</td>
				<td><input name="newsettings[smtp_server]" value="{value_smtp_server}"  class="pure-u-1"/></td>
			</tr>

			<tr>
				<td>{lang_SMTP_server_port_number}:</td>
				<td><input name="newsettings[smtp_port]" value="{value_smtp_port}"  class="pure-u-1"/></td>
			</tr>

			<tr>
				<td>{lang_SMTP_server_timeout}:</td>
				<td><input name="newsettings[smtp_timeout]" value="{value_smtp_timeout}"  class="pure-u-1"/></td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_Use_SMTP_auth}:</td>
				<td>
					<select name="newsettings[smtpAuth]" class="pure-u-1">
						<option value="">{lang_no}</option>
						<option value="yes" {selected_smtpAuth_yes}>{lang_yes}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_Enter_your_SMTP_server_user}:</td>
				<td>
					<input name="newsettings[smtpUser]" value="{value_smtpUser}" autocomplete="off"
						   readonly="readonly" onfocus="this.removeAttribute('readonly');" class="pure-u-1"/>
				</td>
			</tr>
			<tr class="pure-table-odd">
				<td>{lang_Enter_your_SMTP_server_password}:</td>
				<td><input type= "password" name="newsettings[smtpPassword]" value="{value_smtpPassword}" autocomplete="off"
						   readonly="readonly" onfocus="this.removeAttribute('readonly');" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_SMTPSecure}:</td>
				<td>
					<select name="newsettings[smtpSecure]" class="pure-u-1">
						<option value="">{lang_No}</option>
						<option value="ssl"{selected_smtpSecure_ssl}>ssl</option>
						<option value="tls"{selected_smtpSecure_tls}>tls</option>
					</select>
				</td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_SMTPDebug}:</td>
				<td>
					<select name="newsettings[SMTPDebug]" class="pure-u-1">
						<option value="0">{lang_No}</option>
						<option value="1"{selected_SMTPDebug_1}>commands</option>
						<option value="2"{selected_SMTPDebug_2}>commands and data</option>
						<option value="3"{selected_SMTPDebug_3}>plus connection status</option>
						<option value="4"{selected_SMTPDebug_4}>Low-level data output, all messages</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>{lang_Debugoutput}:</td>
				<td>
					<select name="newsettings[Debugoutput]" class="pure-u-1">
						<option value="echo">echo</option>
						<option value="html"{selected_Debugoutput_html}>html</option>
						<option value="errorlog"{selected_Debugoutput_errorlog}>error log</option>
					</select>
				</td>
			</tr>



			<tr class="th">
				<td colspan="2">&nbsp;<b>{lang_appearance}</b></td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_Enter_the_title_for_your_site}:</td>
				<td><input name="newsettings[site_title]" value="{value_site_title}" class="pure-u-1"/></td>
			</tr>


			<tr>
				<td>{lang_Enter_the_url_where_your_logo_should_link_to}:</td>
				<td>http://<input name="newsettings[logo_url]" value="{value_logo_url}" class="pure-u-1"/></td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_Enter_the_title_of_your_logo}:</td>
				<td><input name="newsettings[logo_title]" value="{value_logo_title}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_Enter_the_url_where_your_bakcground_image_should_link_to}:</td>
				<td>http://<input name="newsettings[bakcground_image]" value="{value_bakcground_image}" class="pure-u-1"/></td>
			</tr>

			<tr class="th">
				<td colspan="2">&nbsp;<b>{lang_security}</b></td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_Use_cookies_to_pass_sessionid}:</td>
				<td>
					<select name="newsettings[usecookies]" class="pure-u-1">
						<option value="">{lang_No}</option>
						<option value="True"{selected_usecookies_True}>{lang_Yes}</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>{lang_check_ip_address_of_all_sessions}:</td>
				<td>
					<select name="newsettings[sessions_checkip]" class="pure-u-1">
						<option value="">{lang_No}</option>
						<option value="True"{selected_sessions_checkip_True}>{lang_Yes}</option>
					</select>
				</td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_Deny_all_users_access_to_grant_other_users_access_to_their_entries}?:</td>
				<td>
					<select name="newsettings[deny_user_grants_access]" class="pure-u-1">
						<option value="">{lang_No}</option>
						<option value="True"{selected_deny_user_grants_access_True}>{lang_Yes}</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>{lang_How_many_days_should_entries_stay_in_the_access_log_before_they_get_deleted}? (default 90):</td>
				<td>
					<input name="newsettings[max_access_log_age]" value="{value_max_access_log_age}" size="5" class="pure-u-1"/>
				</td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_After_how_many_unsuccessful_attempts_to_login_an_account_should_be_blocked}? (default 3):</td>
				<td>
					<input name="newsettings[num_unsuccessful_id]" value="{value_num_unsuccessful_id}" size="5" class="pure-u-1"/>
				</td>
			</tr>

			<tr>
				<td>{lang_After_how_many_unsuccessful_attempts_to_login_an_IP_should_be_blocked}? (default 3):</td>
				<td>
					<input name="newsettings[num_unsuccessful_ip]" value="{value_num_unsuccessful_ip}" size="5" class="pure-u-1"/>
				</td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_How_many_minutes_should_an_account_or_IP_be_blocked}? (default 30):</td>
				<td>
					<input name="newsettings[block_time]" value="{value_block_time}" size="5" class="pure-u-1"/>
				</td>
			</tr>

			<tr>
				<td>{lang_comma_separated_admin_email_addresses_to_be_notified_about_the_blocking}:</td>
				<td>
					<input name="newsettings[admin_mails]" value="{value_admin_mails}" size="40" class="pure-u-1"/>
				</td>
			</tr>

			<tr class="pure-table-odd">
				<td>{lang_Disable_auto_completion_of_the_login_form_}:</td>
				<td>
					<select name="newsettings[autocomplete_login]" class="pure-u-1">
						<option value="">{lang_No}</option>
						<option value="True"{selected_autocomplete_login_True}>{lang_Yes}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_collect_missing_translations_}:</td>
				<td>
					<select name="newsettings[collect_missing_translations]" class="pure-u-1">
						<option value="">{lang_No}</option>
						<option value="True"{selected_collect_missing_translations_True}>{lang_Yes}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_support_email_address}:</td>
				<td>
					<input name="newsettings[support_address]" value="{value_support_address}" size="40" class="pure-u-1"/>
				</td>
			</tr>
			<tr>
				<td>{lang_email_domain}:</td>
				<td><input name="newsettings[email_domain]" value="{value_email_domain}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_Enter_your_HTTP_proxy_server}:</td>
				<td><input name="newsettings[httpproxy_server]" value="{value_httpproxy_server}" class="pure-u-1"/>
			</tr>

			<tr>
				<td>{lang_Enter_your_HTTP_proxy_server_port}:</td>
				<td><input name="newsettings[httpproxy_port]" value="{value_httpproxy_port}" class="pure-u-1"/>
			</tr>


			<!-- END body -->

			<!-- BEGIN footer -->
		</tbody>
	</table>
	<div class="button_group">
		<input type="submit" name="submit" value="{lang_submit}" class="pure-button"/>
		<input type="submit" name="cancel" value="{lang_cancel}" class="pure-button"/>
	</div>
</form>
<!-- END footer -->
