<!-- $Id: preferences.tpl 9211 2002-01-17 01:49:04Z ceb $ -->
<script language="JavaScript">
	self.name="first_Window";
	function abook()
	{
		Window1=window.open('{addressbook_link}',"Search","width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes");
    }
</script>
<br><br>
<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
<form method="POST" name="order_form" action="{actionurl}">
<table width="80%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td><input type="button" value="{lang_address}" onClick="abook();"></td>
		<td><input type="hidden" name="abid" value="{abid}">
			<input type="text" name="name" size="50" value="{name}" readonly>&nbsp;&nbsp;&nbsp;{lang_select}</td>
	</tr>
	<tr>
		<td>{lang_print_format}</td>
		<td><select name="prefs[print_format]">{print_format}</select></td>
	</tr>
	<tr>
		<td>{lang_def_cat}</td>
		<td><select name="prefs[cat_id]"><option value="">{lang_select_def_cat}</option>{category_list}</select></td>
	</tr>
	<tr valign="bottom">
		<td height="50">
			<input type="submit" name="submit" value="{lang_save}">
			</form>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
</center>
