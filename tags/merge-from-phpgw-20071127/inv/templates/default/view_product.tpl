<!-- $Id: view_product.tpl 9224 2002-01-18 00:00:35Z ceb $ -->

{app_header}

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
    <hr noshade width="98%" align="center" size="1">
<center>
{pref_message}
<table width="98%" border="0" cellpadding="2" cellspacing="2">
	<tr bgcolor="{tr_color1}">
		<td width="31%" height="35" colspan="2"><div align="right"><b>{lang_num}:</b></div></td>
		<td width="24%" height="35">{num}</td>
		<td width="21%" height="35"><div align="right"><b>{lang_url}:</b></div></td>
		<td width="31%" height="35"><a href="{url}" target="_blank">{url}</a></td>
	</tr>
	<tr bgcolor="{tr_color3}">
		<td width="31%" height="35" colspan="2"><div align="right"><b>{lang_serial}:</b></div></td>
		<td width="24%" height="35">{serial}</td>
		<td width="21%" height="35"><div align="right"><b>{lang_ftp}:</b></div></td>
		<td width="25%" height="35"><a href="{ftp}" target="_blank">{ftp}</a></td>
	</tr>
	<tr bgcolor="{tr_color1}">
		<td width="31%" colspan="2" height="35"><div align="right"><b>{lang_name}:</b></div></td>
		<td width="24%" height="35">{name}</td>
		<td width="21%" height="35"><div align="right"><b>{lang_distributor}:</b></div></td>
		<td width="25%" height="35">{dist}</td>
	</tr>
	<tr bgcolor="{tr_color3}">
		<td width="31%" colspan="2" height="35"><div align="right"><b>{lang_description}:</b></div></td>
		<td height="35">{descr}</td>
		<td width="21%" height="35"><div align="right"><b>{lang_category}:</b></div></td>
		<td width="25%" height="35">{cat_name}</td>
	</tr>
	<tr>
		<td colspan="5" height="2">&nbsp;</td>     
	</tr>
	<tr bgcolor="{tr_color1}">
		<td width="31%" colspan="2" height="35"><div align="right"><b>{lang_cost}:</b>&nbsp;{currency}</div></td>
		<td width="24%" height="35">{cost}</td>
		<td width="21%" height="35"><div align="right"><b># {lang_in_stock}:</b></div></td>
		<td width="25%" height="35">{stock}</td>
	</tr>
	<tr bgcolor="{tr_color3}">
		<td width="31%" colspan="2" height="35"><div align="right"><b>{lang_price}:</b>&nbsp;{currency}</div></td>
		<td width="24%" height="35">{price}</td>
		<td width="21%" height="35"><div align="right"><b>{lang_min_stock}:</b></div></td>
		<td width="25%" height="35">{mstock}</td>
	</tr>
	<tr bgcolor="{tr_color1}">
		<td width="31%" colspan="2" height="35"><div align="right"><b>{lang_retail}:</b>&nbsp;{currency}</div></td>
		<td width="24%" height="35">{retail}</td>
		<td width="21%" height="35"><div align="right"><b>{lang_status}:</b></div></td>
		<td width="25%" height="35">{status}</td>
	</tr>
	<tr>
		<td colspan="5" height="2">&nbsp;</td>
	</tr>
	<tr bgcolor="{tr_color3}">
		<td width="31%" colspan="2" height="35"><div align="right"><b>{lang_purchase_date}:</b></div></td>
		<td width="24%" height="35">{pdate}</td>
	</tr>
	<tr bgcolor="{tr_color1}">
		<td width="31%" colspan="2" height="35"><div align="right"><b>{lang_selling_date}:</b></div></td>
		<td width="24%" height="35">{sdate}</td>
	</tr>
</table><br><br>

<!-- BEGIN done -->

<table width="50%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td height="50">
			<FORM method="POST" action="{done_action}">
			{hidden_vars}
			<input type="submit" name="done" value="{lang_done}">
			</form>
		</td>
	</tr>
</table>
</center>

<!-- END done -->

