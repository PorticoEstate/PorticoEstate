<!-- $Id$ -->
<center>
<table width="50%" border="0" cellspacing="2" cellpadding="2">
	<form method="POST" action="{action_url}">
	<tr bgcolor="{tr_color1}">
		<td>{lang_display}:</td>
		<td align="right">{mainscreen}</td>
	</tr>
	<tr bgcolor="{tr_color2}">
		<td>{lang_def_country}:</td>
		<td align="right"><select name="prefs[country]">{country_list}</select></td>
	</tr>
	<tr valign="bottom" height="50">
		<td><input type="submit" name="prefs[save]" value="{lang_save}"></form></td>
		<form method="POST" action="{cancel_url}">
		<td align="right"><input type="submit" name="cancel" value="{lang_cancel}"></form></td>
	</tr>
</table>
</center>
