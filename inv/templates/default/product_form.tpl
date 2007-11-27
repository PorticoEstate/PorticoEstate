<!-- $Id: product_form.tpl 9219 2002-01-17 04:56:37Z ceb $ -->

{app_header}

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">
<center>
{message}<br>{error}
<FORM name="product_form" action="{actionurl}" method="POST">
{hidden_vars}
<table>
	<tr width=100% align=center>
		<td>{lang_choose}</td>
		<td>&nbsp;{choose}</td>
	</tr>
</table>
<table width="98%" border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td width="31%" height="35" colspan="2"><div align="right">{lang_num}:</div></td>
		<td width="24%" height="35"><input name="values[num]" value="{num}"></td>
		<td width="21%" height="35"><div align="right">{lang_url}:</div></td>
		<td width="31%" height="35"><input name="values[url]" value="{url}"></td>
	</tr>
	<tr>
		<td width="31%" height="35" colspan="2"><div align="right">{lang_serial}:</div></td>
		<td width="24%" height="35"><input name="values[serial]" value="{serial}"></td>
		<td width="21%" height="35"><div align="right">{lang_ftp}:</div></td>
		<td width="25%" height="35"><input name="values[ftp]" value="{ftp}"></td>
	</tr>
	<tr>
		<td colspan="2"><div align="right">{lang_name}:</div></td>
		<td width="24%"><input name="values[name]" value="{short_name}"></td>
		<td width="21%"><div align="right">{lang_distributor}:</div></td>
		<td width="25%"><select name="values[dist]"><option value="">{lang_select_dist}</option>{dist_list}</select></td>
	</tr>
	<tr>
		<td colspan="2"><div align="right">{lang_category}:</div></td>
		<td width="24%"><select name="values[cat_id]"><option value="">{lang_select_cat}</option>{category_list}</select></td>
		<td width="21%"><div align="right">&nbsp;</div></td>
		<td width="25%">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2"><div align="right">{lang_description}:</div></td>
		<td><textarea name="values[descr]" rows="4" cols="50" wrap="VIRTUAL">{descr}</textarea></td>
		<td width="21%"><div align="right">&nbsp;</div></td>
		<td width="25%">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="5" height="2">&nbsp;</td>     
	</tr>
	<tr>
		<td colspan="2" height="15"><div align="right">&nbsp;</div></td>
		<td width="24%" height="15">&nbsp;</td>
		<td width="21%" height="15"><div align="right">{lang_room}:</div></td>
		<td width="25%" height="15"><select name="values[bin]"><option value="">{lang_select_room}</option>{room_list}</select></td>
	</tr>
	<tr>
		<td colspan="2" height="15"><div align="right">{lang_cost}:&nbsp;{currency}</div></td>
		<td width="24%" height="15"><input name="values[cost]" value="{cost}"></td>
		<td width="21%" height="15"><div align="right">{lang_x}{lang_in_stock}:</div></td>
		<td width="25%" height="15"><input name="values[stock]" value="{stock}"></td>
	</tr>
	<tr>
		<td colspan="2"><div align="right">{lang_price}:&nbsp;{currency}</div></td>
		<td width="24%"><input name="values[price]" value="{price}"></td>
		<td width="21%"><div align="right">{lang_min_stock}:</div></td>
		<td width="25%"><input name="values[mstock]" value="{mstock}"></td>
	</tr>
	<tr>
		<td colspan="2" height="2"><div align="right">{lang_retail}:&nbsp;{currency}</div></td>
		<td width="24%" height="2"><input name="values[retail]" value="{retail}" readonly></td>
		<td width="21%" height="2"><div align="right">{lang_status}:</div></td>
		<td width="25%" height="2"><select name="values[status]">{status_list}</select></td>
	</tr>
	<tr>
		<td colspan="5" height="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" height="2"><div align="right">{lang_purchase_date}:</div></td>
		<td colspan="2">{purchase_date_select}</td>
	</tr>
	<tr>
		<td colspan="2" height="2"><div align="right">{lang_selling_date}:</div></td>
		<td colspan="2">{selling_date_select}</td>
	</tr>
	<tr>                                                                                                                                                                                    
		<td colspan="2"><div align="right">{lang_note}:</div></td>
		<td><textarea name="values[note]" rows="4" cols="50" wrap="VIRTUAL">{product_note}</textarea></td>
		<td width="21%"><div align="right">&nbsp;</div></td>
		<td width="25%">&nbsp;</td>
	</tr>
</table>

<!-- BEGIN add -->

<table width=50% border="0" cellpadding="2" cellspacing="2"> 
	<tr>
		<td height="50">
			<input name="submit" type="submit" value="{lang_save}"></td>
			<td height="50"><input type="reset" name="reset" value="{lang_reset}"></form></td>
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
			<input type="submit" name="submit" value="{lang_save}"></form></td>
		<td height="50">
			{delete}</td>
		<td height="50">
			<form method="POST" action="{done_action}">
			{hidden_vars}
			<input type="submit" name="done" value="{lang_done}"></form></td>
	</tr>
</table>
</center>

<!-- END edit -->
