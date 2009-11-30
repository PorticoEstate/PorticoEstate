<!-- $Id$ -->

{app_header}

<center>
<table width="69%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td align="left">
			<form method="POST" action="{actionurl}" name="form">
			<select name="country" onChange="this.form.submit();">{country_list}</select>
			<noscript>&nbsp;<input type="submit" name="submit" value="{lang_submit}"></noscript></form>
		</td>
	</tr>
</table>
<table border="0" with="69%" cellpadding="2" cellspacing="2">
	<tr>
		<td align="center">{quotes}</td>
	</tr>
</table>
</center>
