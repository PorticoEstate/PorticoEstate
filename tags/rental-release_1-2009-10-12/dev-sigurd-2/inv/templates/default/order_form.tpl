<!-- $Id: order_form.tpl 5679 2001-06-10 03:19:15Z bettina $ -->
<script language="JavaScript">
	self.name="first_Window";
	function abook()
	{
		Window1=window.open('{addressbook_link}',"Search","width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes");
	}
</script>
<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
{message}
<form method="POST" name="order_form" action="{actionurl}">
<table width="85%" border="0" cellspacing="2" cellpadding="2" align="center">
	<tr>
		<td width="30%">&nbsp;</td>
		<td>{lang_choose}&nbsp;{choose}</td>
	</tr>
	<tr>
		<td align="right">{lang_num}:</td>
		<td><input type="text" name="num" value="{num}"></td>
	</tr>
	<tr>
		<td align="right">{lang_descr}:</td>
		<td align="left"><textarea name="descr" rows=4 cols=50 wrap="VIRTUAL">{descr}</textarea></td>
	</tr>
	<tr>
		<td align="right">{lang_status}:</td>
		<td align="left"><select name="status">{status_list}</select></td>
	</tr>
	<tr>
		<td align="right"><input type="button" value="{lang_customer}" onClick="abook();"></td>
		<td><input type="hidden" name="abid" value="{abid}">
			<input type="text" name="name" size="50" value="{name}" readonly>&nbsp;&nbsp;&nbsp;{lang_select}</td>
	</tr>
	<tr>
		<td align="right">{lang_date}:</td>
		<td>{date_select}</td>
	</tr>
	<tr>
		<td align="right">{lang_access}:</td>
		<td>{access}</td>
	</tr>
</table>

<!-- BEGIN add -->

<table width="50%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td height="50">
			{hidden_vars}
			<input type="submit" name="submit" value="{lang_add}"></td>
		<td height="50">
			<input type="reset" name="reset" value="{lang_reset}"></form></td>
		<td height="50">
			<form method="POST" action="{done_action}">
			{hidden_vars}
			<input type="submit" name="done" value="{lang_done}"></form></td>
	</tr>
</table>
</center>

<!-- END add -->

<!-- BEGIN edit -->

<table width="50%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td height="50">
			{hidden_vars}
			<input type="submit" name="submit" value="{lang_edit}"></form></td>
		<td height="50">
			{hidden_vars}
			{delete}</td>
		<td height="50">
			<form method="POST" action="{done_action}">
			{hidden_vars}
			<input type="submit" name="done" value="{lang_done}"></form></td>
	</tr>
</table>
</center>

<!-- END edit -->
