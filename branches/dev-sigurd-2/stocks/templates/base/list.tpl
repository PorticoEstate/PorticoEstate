<!-- $Id$ -->

{app_header}

<center>
<table width="60%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td align="left">
			<form method="POST" action="{actionurl}" name="form">
			<select name="country" onChange="this.form.submit();">{country_list}</select>
			<noscript>&nbsp;<input type="submit" name="submit" value="{lang_submit}"></noscript></form>
		</td>
	</tr>
</table>
<table width="60%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="30%">{lang_symbol}</td>
		<td width="40%">{lang_company}</td>
		<td width="40%">{lang_country}</td>
		<td width="10%" align="center">{h_lang_edit}</td>
		<td width="10%" align="center">{h_lang_delete}</td>
	</tr>

<!-- BEGIN stock_list -->

	<tr bgcolor="{tr_color}">
		<td>{ssymbol}</td>
		<td>{sname}</td>
		<td>{scountry}</td>
		<td align="center"><a href="{edit}">{lang_edit}</a></td>
		<td align="center"><a href="{delete}">{lang_delete}</a></td>
	</tr>

<!-- END stock_list -->

</table>
<table width="60%" border="0" cellspacing="2" cellpadding="2">
<form method="POST" action="{addurl}">
	<tr valign="bottom">
		<td><input type="submit" name="add" value="{lang_add}"></form></td>
	</tr>
<form method="POST" action="{doneurl}">
	<tr valign="bottom">
		<td><input type="submit" name="done" value="{lang_done}"></form></td>
	</tr>
</table>
