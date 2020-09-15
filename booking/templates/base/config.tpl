<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered">
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
			<td><input name="newsettings[external_site_address]" value="{value_external_site_address}"/></td>
		</tr>
		<tr class="row_off">
			<td>{lang_custom_email_sender}:{lang_example}: noreply&lt;noreply@Bergen.Kommune.no&gt;</td>
			<td><input name="newsettings[email_sender]" value="{value_email_sender}"/></td>
		</tr>
		<tr class="row_off">
			<td>Reply to:</td>
			<td><input name="newsettings[email_reply_to]" value="{value_email_reply_to}"/></td>
		</tr>
		<tr class="row_on">
			<td>{lang_logopath_frontend}:{lang_example}: /phpgwapi/templates/bkbooking/images/bergen_logo.png</td>
			<td><input name="newsettings[logopath_frontend]" value="{value_logopath_frontend}"/></td>
		</tr>
		<tr class="row_off">
			<td>{lang_image_maxheight}:{lang_example}: 300</td>
			<td><input name="newsettings[image_maxheight]" value="{value_image_maxheight}"/></td>
		</tr>
		<tr class="row_on">
			<td>{lang_image_maxwidth}:{lang_example}: 300</td>
			<td><input name="newsettings[image_maxwidth]" value="{value_image_maxwidth}"/></td>
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
				<select name="newsettings[e_lock_request_method]">
					{hook_request_method}
				</select>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_proxy}:</td>
			<td>
				<input name="newsettings[proxy]" value="{value_proxy}"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_e_lock_webservice}:</td>
			<td>
				<input name="newsettings[e_lock_webservice]" value="{value_e_lock_webservice}"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_e_lock_login}:</td>
			<td>
				<input name="newsettings[e_lock_login]" value="{value_e_lock_login}"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_e_lock_password}:</td>
			<td>
				<input name="newsettings[e_lock_password]" value="{value_e_lock_password}"/>
			</td>
		</tr>
		<tr class="row_off">
			<td>{lang_participant_limit}:</td>
			<td>
				<input name="newsettings[participant_limit]" value="{value_participant_limit}"/>
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
				<input type="submit" name="submit" value="{lang_submit}">
				<input type="submit" name="cancel" value="{lang_cancel}">
			</td>
		</tr>
	</table>
</form>
<!-- END footer -->
