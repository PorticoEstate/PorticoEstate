<!-- $Id$ -->

<center>
<form method="POST" action="{action_url}">
{message}
<table width="85%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td class="bg_color" colspan="2"><b>{lang_b_config}</b></td>
	</tr>
	<tr class="row_on">
		<td>{lang_b_create}</td>
		<td>{b_create}</td>
	</tr>
	<tr class="row_off">
		<td>{lang_b_intval}:</td>
		<td><select name="values[b_intval]"><option value="">{lang_select_b_intval}</option>{intval_list}</select></td>
	</tr>
	<tr class="row_on">
		<td>{lang_config_path}:</td>
		<td><input type="text" name="values[script_path]" value="{script_path}"></td>
	</tr>
	<tr class="row_off">
		<td>{lang_versions}:</td>
		<td><input type="text" name="values[versions]" value="{versions}" size="3" maxlenght="3"></td>
	</tr>
	<tr>
		<td class="row_off" colspan="2"><b>{lang_b_data}</b></td>
	</tr>
	<tr class="row_on">
		<td>{lang_b_sql}:</td>
		<td>{b_sql}</td>
	</tr>
	<tr class="row_off">
		<td>{lang_b_ldap}:</td>
		<td>{b_ldap}</td>
	</tr>
	<tr class="row_on">
		<td>{lang_b_email}:</td>
		<td>{b_email}</td>
	</tr>
	<tr class="row_off">
		<td>{lang_b_type}:</td>
		<td><select name="values[b_type]"><option value="">{lang_select_b_type}</option>{type_list}</select></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="bg_color" colspan="2"><b>{lang_l_config}</b></td>
	</tr>
	<tr class="row_on">
		<td>{lang_l_save}</td>
		<td>{l_save}</td>
	</tr>
	<tr class="row_off">
		<td>{lang_path}:</td>
		<td><input type="text" name="values[l_path]" value="{l_path}"></td>
	</tr>
	<tr class="row_on">
		<td>{lang_l_websave}</td>
		<td>{l_websave}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="bg_color" colspan="2"><b>{lang_r_config}</b></td>
	</tr>
	<tr class="row_on">
		<td>{lang_r_save}</td>
		<td>{r_save}</td>
	</tr>
	<tr class="row_off">
		<td>{lang_r_host}:</td>
		<td>{r_host}</td>
	</tr>
	<tr class="row_on">
		<td>{lang_app}:</td>
		<td><select name="values[r_app]"><option value="">{lang_select_app}</option>{r_app_list}</select></td>
	</tr>
	<tr class="row_off">
		<td>{lang_r_ip}:</td>
		<td><input type="text" name="values[r_ip]" value="{r_ip}"></td>
	</tr>
	<tr class="row_on">
		<td>{lang_path}:</td>
		<td><input type="text" name="values[r_path]" value="{r_path}"></td>
	</tr>
	<tr class="row_off">
		<td>{lang_user}:</td>
		<td><input type="text" name="values[r_user]" value="{r_user}"></td>
	</tr>
	<tr class="row_on">
		<td>{lang_pwd}:</td>
		<td><input type="password" name="values[r_pwd]" value="{r_pwd}"></td>
	</tr>
</table>

<table width="85%" border="0" cellspacing="2" cellpadding="2">
	<tr valign="bottom" height="50">
		<td>
			<input type="submit" name="values[save]" value="{lang_save}"></form></td>
		<td align="right">
			<form method="POST" action="{cancel_url}">
			<input type="submit" name="cancel" value="{lang_cancel}"></form></td>
	</tr>
</table>
</center>
