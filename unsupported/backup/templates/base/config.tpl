

<!-- BEGIN header -->

<form method="POST" action="{action_url}">
<table border="0" align="center" cellpadding="2" cellspacing="2">
	<tr class="th">
		<td colspan="2">&nbsp;<b>{title}</b></td>
	</tr>

<!-- END header -->

<!-- BEGIN body -->

	<tr class="row_on">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr class="row_off">
		<td colspan="2">&nbsp;<b>{lang_backup} {lang_settings} - {lang_program_locations}</b></td>
	</tr>
	<tr class="row_on">
		<td>{lang_php_cgi}:</td>
		<td><input name="newsettings[php_cgi]" value="{value_php_cgi}"></td>
	</tr>
	<tr class="row_off">
		<td>{lang_tar}:</td>
		<td><input name="newsettings[tar]" value="{value_tar}"></td>
	</tr>
	<tr class="row_on">
		<td>{lang_bzip2}:</td>
		<td><input name="newsettings[bzip2]" value="{value_bzip2}"></td>
	</tr>
	<tr class="row_off">
		<td>{lang_zip}:</td>
		<td><input name="newsettings[zip]" value="{value_zip}"></td>
	</tr>
	<tr class="row_on">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr class="row_off">
		<td colspan="2">&nbsp;<b>{lang_backup} {lang_settings} - {lang_directory_locations}</b></td>
	</tr>
	<tr class="row_on">
		<td>{lang_mysql}:</td>
		<td><input name="newsettings[mysql]" value="{value_mysql}"></td>
	</tr>
	<tr class="row_off">
		<td>{lang_pgsql}:</td>
		<td><input name="newsettings[pgsql]" value="{value_pgsql}"></td>
	</tr>
	<tr class="row_on">
		<td>{lang_ldap}:</td>
		<td><input name="newsettings[ldap]" value="{value_ldap}"></td>
	</tr>
	<tr class="row_off">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr class="row_on">
		<td colspan="2">&nbsp;<b>{lang_backup} {lang_settings} - {lang_directory_names}</b></td>
	</tr>
	<tr class="row_off">
		<td>{lang_ldap_database}:</td>
		<td><input name="newsettings[ldap_in]" value="{value_ldap_in}"></td>
	</tr>
	<tr class="row_on">
		<td>{lang_maildir}:</td>
		<td><input name="newsettings[maildir]" value="{value_maildir}"></td>
	</tr>
	<tr class="row_off">
		<td colspan="2">&nbsp;</td>
	</tr>

<!-- END body -->

<!-- BEGIN footer -->

	<tr class="th">
		<td colspan="2">&nbsp;</td>
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
