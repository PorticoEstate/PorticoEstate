<!-- $Id: preferences.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
<br/><br/><br/><br/>
<center>
<table border="0" cellspacing="2" cellpadding="2">
<form method="POST" name="app_form" action="{action_url}">
	<tr class="bg_color2">
		<td valign="top">{lang_select_columns}:</td>
		<td><select size="8" name="prefs[cols][]" multiple>{column_select}</select></td>
	</tr>
	<tr class="bg_color1">
		<td valign="top">{lang_select_cs_columns}:</td>
		<td>
			<select size="8" name="prefs[cscols][]" multiple>
				{column_cs_select}
			</select>
		</td>
	</tr>
	<tr class="bg_color2">
		<td>{worktime_statusmail_desc}:</td>
		<td><input type="checkbox" name="prefs[send_status_mail]" value="True"{send_status_mail_checked}"></td>
	</tr>
	<tr class="bg_color1">
		<td valign="top">{lang_show_projects_on_mainscreen}:</td>
		<td><input type="checkbox" name="prefs[mainscreen_showevents]" value="True"{mainscreen_checked}"></td>
	</tr>
	<tr valign="bottom" height="50">
		<td>
			<input type="submit" name="save" value="{lang_save}">
		</td>
		<td align="right">
			<input type="submit" name="done" value="{lang_done}"></td>
	</tr>
</form>
</table>

