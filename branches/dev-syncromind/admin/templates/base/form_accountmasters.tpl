<!-- $Id$ -->
<script language="JavaScript">
	self.name="first_Window";
	function accounts_popup()
	{
		Window1=window.open('{accounts_link}',"Search","width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes");
	}
</script>

<center>
{error_message}
<table border="0" width="80%" cellpadding="2" cellspacing="2">
<form method="POST" action="{action_url}" name="app_form">
	<tr>
		<td valign="top">{lang_select_addressmasters}:</td>
		<td>{accounts}</td>
	</tr>
	<tr height="50" valign="bottom">
		<td><input type="submit" name="save" value="{lang_save}"></td>
		<td align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
</form>
</table>
</center>

<!-- BEGIN select -->

		<table>
			<tr>
				<td valign="top">{lang_select_users}:</td>
				<td>
					<select name="account_addressmaster[]" multiple size="{u_select_size}">
             			{user_list}
            		</select>
				</td>
			</tr>
			<tr>
				<td valign="top">{lang_select_groups}:</td>
				<td>
					<select name="group_addressmaster[]" multiple size="{g_select_size}">
             			{group_list}
            		</select>
				</td>
			</tr>
		</table>

<!-- END select -->

<!-- BEGIN popwin -->

		<table>
			<tr>
				<td>
					<select name="account_addressmaster[]" multiple size="{select_size}">{account_list}</select>
				</td>
				<td valign="top">
        			<input type="button" value="{lang_open_popup}" onClick="accounts_popup()">
					<input type="hidden" name="accountid" value="{accountid}">
				</td>
			</tr>
		</table>

<!-- END popwin -->
