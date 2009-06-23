<!-- $Id: del_listproducts.tpl 5941 2001-06-15 21:47:31Z bettina $ -->
<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>                                                                                                                                                     
<hr noshade width="98%" align="center" size="1">                                                                                                                                                  
<center>
{message} 
{error}
<form method="POST" action="{actionurl}">
<table border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td>{lang_choose}</td>
		<td>{choose}</td>
	</tr>
	<tr>
		<td>{title_delivery_num}:</td>
		<td><input type=text name="delivery_num" value="{delivery_num}"></td>
	</tr>
	<tr>
		<td>{title_descr}:</td>
		<td>{descr}</td>
	</tr>
	<tr>
		<td>{title_customer}:</td>
		<td>{customer}</td>
	</tr>
	<tr>
		<td>{lang_delivery_date}:</td>
		<td>{date_select}</td>
	</tr>
</table><br><br> 
<table width="70%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}">
		<td width="5%" bgcolor="{th_bg}" align="right">{h_lang_pos}</td>
		<td width="5%" bgcolor="{th_bg}" align="right">{h_lang_piece}</td>
		<td width="20%" bgcolor="{th_bg}">{h_lang_id}</td>
		<td width="20%" bgcolor="{th_bg}">{h_lang_serial}</td>
		<td width="20%" bgcolor="{th_bg}">{h_lang_name}</td>
	</tr>

<!-- BEGIN product_list -->

	<tr bgcolor="{tr_color}">
		<td align="right">{pos}</td>
		<td align="right">{piece}</td>
		<td>{product_id}</td>
		<td>{serial}</td>
		<td>{product_name}</td>
	</tr>

<!-- END product_list -->

</table>
<table width="70%" border="0" cellpadding="2" cellspacing="2">
	<tr>
		{hidden_vars}
		<td>{create}</td>
		</form>
		<td><a href="{print_delivery}" target="_blank">{lang_print_delivery}</a></td>
		<td><a href="{list_delivery}">{lang_list_delivery}</a></td>
	</tr>
</table>
</center>
