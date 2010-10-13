<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table border="0" align="center" width="85%">
		<tr class="th">
			<td colspan="2">&nbsp;<b>{title}</b></td>
		</tr>
<!-- END header -->
<!-- BEGIN body -->
		<tr class="row_on">
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="row_off">
			<td colspan="2">&nbsp;<b>{lang_frontend_settings}</b></td>
		</tr>
		<tr class="row_on">
			<td>{lang_tab_sorting}:</td>
			<td>
			 <table>
{hook_tab_sorting}
			 </table>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_ticket_default_group}:</td>
			<td>
			 <select name="newsettings[tts_default_group]">
{hook_tts_default_group}
			 </select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_document_category_for_building_picture}:</td>
			<td>
				<select name="newsettings[picture_building_cat]">
{hook_picture_building_cat}
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_delegate_limit}:</td>
			<td><input name="newsettings[delegate_limit]" value="{value_delegate_limit}"></td>
		</tr>		
		<tr class="row_on">
			<td colspan="2">&nbsp;<b>{lang_external_db}</b></td>
		</tr>
		<tr class="row_on">
			<td>{lang_Debug}:</td>
			<td>
				<select name="newsettings[external_db_debug]">
					<option value="" {selected_external_db_debug_}>NO</option>
					<option value="1" {selected_external_db_debug_1}>YES</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_external_db_host}:</td>
			<td><input name="newsettings[external_db_host]" value="{value_external_db_host}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_type}:</td>
			<td>
				<select name="newsettings[external_db_type]">
					<option value="" {selected_external_db_type_}>None</option>
					<option value="mssql" {selected_external_db_type_mssql}>mssql</option>
					<option value="mysql" {selected_external_db_type_mysql}>mysql</option>
					<option value="oracle" {selected_external_db_type_oracle}>oracle</option>
					<option value="postgres" {selected_external_db_type_postgres}>postgres</option>
				</select>
			</td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_external_db_name}:</td>
			<td><input name="newsettings[external_db_name]" value="{value_external_db_name}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_external_db_user}:</td>
			<td><input name="newsettings[external_db_user]" value="{value_external_db_user}"></td>
		</tr>


		<tr class="row_on">
			<td>{lang_login_external_db_password}:</td>
			<td><input type ="password" name="newsettings[external_db_password]" value="{value_external_db_password}"></td>
		</tr>
		<tr class="row_on">
			<td colspan="2">&nbsp;<b>{lang_email_settings}</b></td>
		</tr>
		<tr class="row_on">
			<td>{lang_email_contract_messages}:</td>
			<td><input name="newsettings[email_contract_messages]" value="{value_email_contract_messages}"></td>
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
