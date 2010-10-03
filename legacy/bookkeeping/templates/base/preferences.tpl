<!-- $Id: preferences.tpl 16496 2006-03-12 10:48:44Z skwashd $ -->
<script language="JavaScript">
	self.name="first_Window";
	function abook()
	{
		Window1=window.open('{addressbook_link}',"Search","width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes");
	}
</script>

<center>
{bill_message}
<form method="POST" name="app_form" action="{actionurl}">
<table width="97%" border="0" cellspacing="2" cellpadding="2">

<!-- BEGIN book -->
	<tr bgcolor="{row_off}">
		<td><input type="button" value="{lang_address}" onClick="abook();"></td>
		<td><input type="hidden" name="abid" value="{abid}">
			<input type="text" name="name" size="50" value="{name}" readonly></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_select_tax}:</td>
		<td><input type="text" name="prefs[tax]" value="{tax}" size="6" maxlength="6">&nbsp;%</td>
	</tr>
	<tr valign="bottom" height="50">
		<td align="left">
			<input type="submit" name="save" value="{lang_save}">
		</td>
		<td align="right">
			<input type="submit" name="done" value="{lang_done}"></td>
	</tr>
</table>
</form>
</center>
<!-- END all -->
