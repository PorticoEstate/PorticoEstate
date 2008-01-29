<!-- BEGIN sitemgr_prefs -->

	<b>{setup_instructions}</b>
	<p>{lang_subdir}
	</p>
	<p>{lang_first_directory}
	</p>
	<p>{lang_second_directory}
	</p>
	<p>{lang_edit_config_inc}
	<p>
	<hr>
	<b>{options}</b>
	</p>
	<p>
	<form action="{formaction}" method="post">
<center>
<table border="0" width="90%" cellspacing="8">
<!-- BEGIN PrefBlock -->
	<tr>
		<td>
			<table border="1" cellpadding="5" cellspacing="0" width="100%">
			<tr><td>
			<table border="0" cellpadding="1" cellspacing="0" width="100%">
				<tr>
					<td width="50%" valign="top">
						<b>{pref-title}</b><br>
						{pref-input}
					</td>
					<td width="50%" valign="bottom">
						<i>{pref-note}</i>
					</td>
				</tr>
			</table>
			</td></tr>
			</table>
		</td>
	</tr>
<!-- END PrefBlock -->
</table>
</center>

	<input type="submit" name="btnSave" value="{lang_save}">
	</form>
<!-- END sitemgr_prefs -->
