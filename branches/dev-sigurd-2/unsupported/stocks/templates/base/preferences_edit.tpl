<!-- $Id$ -->

{app_header}

<center>
<form method="POST" name="preferences_edit" action="{add_url}">
{hidden_vars}
<table border="0" cellspacing="2" cellpadding="2" width="40%">
	<tr bgcolor="{tr_color1}">
		<td>{lang_symbol}:</td>
		<td align="right"><input type="text" name="values[symbol]" value="{symbol}"></td>
	</tr>
	<tr bgcolor="{tr_color2}">
		<td>{lang_company}:</td> 
		<td align="right"><input type="text" name="values[name]" value="{name}"></td>
	</tr>
	<tr bgcolor="{tr_color1}">
		<td>{lang_country}:</td> 
		<td align="right"><select name="values[country]">{country_list}</select></td>
	</tr>

<!-- BEGIN edit -->

	<tr valign="bottom" height="50">
		<td>
			<input type="submit" name="values[save]" value="{lang_save}">
		</td>
		</form>
		<form method="post" action="{cancel_url}">
		<td align="right">
			<input type="submit" name="cancel" value="{lang_cancel}">
		</td>
	</tr>
</table>
</center>
         
<!-- END edit -->
