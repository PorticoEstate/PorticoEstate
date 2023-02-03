<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered pure-table-striped pure-form">
		<thead>
			<tr>
				<th colspan="2">{title}</th>
			</tr>
		</thead>
		<!-- END header -->
		<!-- BEGIN body -->
		<tbody>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;<b>{lang_rental}</b></td>
			</tr>

			<tr>
				<td>{lang_organization_for_billing_config}, (default: Bergen):</td>
				<td>
					<select name="newsettings[organization]" class="pure-u-1">
						<option value="" {selected_organization_}>Bergen kommune</option>
						<option value="nlsh" {selected_organization_nlsh}>NLSH</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_area_suffix}:</td>
				<td><input name="newsettings[area_suffix]" value="{value_area_suffix}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_currency_prefix}:</td>
				<td><input name="newsettings[currency_prefix]" value="{value_currency_prefix}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_currency_suffix}:</td>
				<td><input name="newsettings[currency_suffix]" value="{value_currency_suffix}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_serial_start}:</td>
				<td><input name="newsettings[serial_start]" value="{value_serial_start}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_serial_stop}:</td>
				<td><input name="newsettings[serial_stop]" value="{value_serial_stop}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_billing_time_limit}:</td>
				<td><input name="newsettings[billing_time_limit]" value="{value_billing_time_limit}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_from_email_setting}:</td>
				<td><input name="newsettings[from_email_setting]" value="{value_from_email_setting}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_http_address_for_external_users}:</td>
				<td><input name="newsettings[http_address_for_external_users]" value="{value_http_address_for_external_users}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_create_user_based_on_email_group}:</td>
				<td>
					<select name="newsettings[create_user_based_on_email_group]" class="pure-u-1">
						{hook_create_user_based_on_email_group}
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_entity_config_move_in}:</td>
				<td>
					{hook_entity_config_move_in}
				</td>
			</tr>

			<tr>
				<td>{lang_category_config_move_in}:</td>
				<td>
					<select name="newsettings[category_config_move_in]" class="pure-u-1">
						{hook_category_config_move_in}
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_entity_config_move_out}:</td>
				<td>
					{hook_entity_config_move_out}
				</td>
			</tr>
			<tr>
				<td>{lang_category_config_move_out}:</td>
				<td>
					<select name="newsettings[category_config_move_out]" class="pure-u-1">
						{hook_category_config_move_out}
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_contract_future_info}:</td>
				<td>
					<select name="newsettings[contract_future_info]" class="pure-u-1">
						<option value="" {selected_contract_future_info_}>NO</option>
						<option value="1" {selected_contract_future_info_1}>YES</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_contract_furnished_status}:</td>
				<td>
					<select name="newsettings[contract_furnished_status]" class="pure-u-1">
						<option value="" {selected_contract_furnished_status_}>NO</option>
						<option value="1" {selected_contract_furnished_status_1}>YES</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_use_fellesdata}:</td>
				<td>
					<select name="newsettings[use_fellesdata]" class="pure-u-1">
						<option value="" {selected_use_fellesdata_}>NO</option>
						<option value="1" {selected_use_fellesdata_1}>YES</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;<b>{lang_external_db}</b></td>
			</tr>
			<tr>
				<td>{lang_Debug}:</td>
				<td>
					<select name="newsettings[external_db_debug]" class="pure-u-1">
						<option value="" {selected_external_db_debug_}>NO</option>
						<option value="1" {selected_external_db_debug_1}>YES</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_login_external_db_host}:</td>
				<td><input name="newsettings[external_db_host]" value="{value_external_db_host}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_login_external_db_port}:</td>
				<td><input name="newsettings[external_db_port]" value="{value_external_db_port}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_type}:</td>
				<td>
					<select name="newsettings[external_db_type]" class="pure-u-1">
						<option value="" {selected_external_db_type_}>None</option>
						<option value="mssql" {selected_external_db_type_mssql}>mssql</option>
						<option value="mysql" {selected_external_db_type_mysql}>mysql</option>
						<option value="oracle" {selected_external_db_type_oracle}>oracle</option>
						<option value="postgres" {selected_external_db_type_postgres}>postgres</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{lang_login_external_db_name}:</td>
				<td><input name="newsettings[external_db_name]" value="{value_external_db_name}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_login_external_db_user}:</td>
				<td><input name="newsettings[external_db_user]" value="{value_external_db_user}" class="pure-u-1"/></td>
			</tr>


			<tr>
				<td>{lang_login_external_db_password}:</td>
				<td><input type ="password" name="newsettings[external_db_password]" value="{value_external_db_password}" autocomplete="off"
						   readonly="readonly" onfocus="this.removeAttribute('readonly');" class="pure-u-1"/></td>
			</tr>

			<tr>
				<td>{lang_path_to_wkhtmltopdf}:</td>
				<td><input name="newsettings[path_to_wkhtmltopdf]" value="{value_path_to_wkhtmltopdf}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td valign = 'top'>{lang_contract_types}:</td>
				<td>
					<!--to be able to blank the setting - need an empty value-->
					<input type = 'hidden' name="newsettings[contract_types][]" value="">
					<table>
						{hook_contract_types}
					</table>
				</td>
			</tr>
			<tr>
				<td valign = 'top'>{lang_default_billing_term}:</td>
				<td>
					<!--to be able to blank the setting - need an empty value-->
					<!--input type = 'hidden' name="newsettings[default_billing_term]" value=""-->
					<table>
						{hook_default_billing_term}
					</table>
				</td>
			</tr>
			<tr>
				<td>{lang_notify_on_expire_email}:</td>
				<td><input name="newsettings[notify_on_expire_email]" value="{value_notify_on_expire_email}" class="pure-u-1"/></td>
			</tr>
			<tr>
				<td>{lang_notify_reminder_days}:</td>
				<td><input name="newsettings[notify_reminder_days]" value="{value_notify_reminder_days}" class="pure-u-1"/></td>
			</tr>
		</tbody>

		<!-- END body -->
		<!-- BEGIN footer -->
		<tfoot>
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
		</tfoot>
	</table>
</form>
<!-- END footer -->
