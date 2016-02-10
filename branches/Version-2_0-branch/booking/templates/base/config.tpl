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
			<td>{lang_external_host_address}:Example: https://www.bergen.kommune.no</td>
			<td><input name="newsettings[external_site_address]" value="{value_external_site_address}"></td>
		</tr>
		<tr class="row_off">
			<td>{lang_custom_email_sender}:example: noreply&lt;noreply@Bergen.Kommune.no&gt;</td>
			<td><input name="newsettings[email_sender]" value="{value_email_sender}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_logopath_frontend}:example: /phpgwapi/templates/bkbooking/images/bergen_logo.png</td>
			<td><input name="newsettings[logopath_frontend]" value="{value_logopath_frontend}"></td>
		</tr>
		<tr class="row_off">
			<td>{lang_image_maxheight}:example: 300</td>
			<td><input name="newsettings[image_maxheight]" value="{value_image_maxheight}"></td>
		</tr>
		<tr class="row_on">
			<td>{lang_image_maxwidth}:example: 300</td>
			<td><input name="newsettings[image_maxwidth]" value="{value_image_maxwidth}"></td>
		</tr>
		<tr class="row_off">
			<td>{lang_support_email_address}:</td>
			<td>
				<input name="newsettings[support_address]" value="{value_support_address}" size="40">
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
