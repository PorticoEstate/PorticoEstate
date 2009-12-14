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
			<td colspan="2">&nbsp;<b>{lang_bookingfrontend_settings}</b></td>
		</tr>
		<tr class="row_on">
			<td>{lang_custom_login_url_parameter}:</td>
			<td><input name="newsettings[login_parameter]" value="{value_login_parameter}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_header_key}:</td>
			<td><input name="newsettings[header_key]" value="{value_header_key}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_header_regular_expression}:</td>
			<td><input name="newsettings[header_regular_expression]" value="{value_header_regular_expression}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_soap_client_location}:</td>
			<td><input name="newsettings[soap_location]" value="{value_soap_location}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_soap_client_uri}:</td>
			<td><input name="newsettings[soap_uri]" value="{value_soap_uri}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_soap_client_proxy_host}:</td>
			<td><input name="newsettings[soap_proxy_host]" value="{value_soap_proxy_host}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_soap_client_proxy_port}:</td>
			<td><input name="newsettings[soap_proxy_port]" value="{value_soap_proxy_port}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_soap_client_encoding}:</td>
			<td><input name="newsettings[soap_encoding]" value="{value_soap_encoding}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_soap_client_login}:</td>
			<td><input name="newsettings[soap_login]" value="{value_soap_login}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_soap_client_password}:</td>
			<td><input name="newsettings[soap_password]" value="{value_soap_password}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_login_soap_client_wsdl}:</td>
			<td><input name="newsettings[soap_wsdl]" value="{value_soap_wsdl}"></td>
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
