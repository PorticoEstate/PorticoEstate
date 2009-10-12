<!-- $Id: listactivities.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<center>
<table border="0" width="85%" cellpadding="2" cellspacing="2">
	<tr colspan="6">
		<td colspan="6">
			<table border="0" width="100%">
				<tr>
				{left}
					<td align="center">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<tr colspan="6">
		<td width="33%" align="left">
			<form method="POST" name="cat" action="{cat_action}">
				<select name="cat_id" onChange="this.form.submit();">
				<option value="none">{lang_select_category}</option>
				{categories_list}
				</select>
				<noscript><input type="submit" name="cats" value="{lang_select}"></noscript>
			</form>
		</td>
		<td width="33%" align="center">&nbsp;</td>
		<td width="33%" align="right"><form method="POST" name="query" action="{search_action}">{search_list}</form></td>
	</tr>
</table>
{pref_message}
<table border="0" width="85%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td width="8%" bgcolor="{th_bg}">{sort_num}</td>
		<td width="30%" bgcolor="{th_bg}">{sort_descr}</td>
		<td width="10%" bgcolor="{th_bg}" align="right">{currency}&nbsp;{sort_billperae}</td>
		{sort_minperae}
		<td width="8%" bgcolor="{th_bg}" align="center">{lang_edit}</td>
		<td width="8%" bgcolor="{th_bg}" align="center">{lang_delete}</td>
	</tr>

<!-- BEGIN activities_list -->

	<tr bgcolor="{tr_color}">
		<td>{num}</td>
		<td>{descr}</td>
		<td align="right">{billperae}</td>
		{minperae}
		<td align="center"><a href="{edit}">{lang_edit}</a></td>
		<td align="center"><a href="{delete}">{lang_delete}</a></td>
	</tr>

<!-- END activities_list -->

</table>
<table border="0" width="85%" cellpadding="2" cellspacing="2">
	<tr valign="bottom">
		<td align="left"><form method="POST" action="{add_url}">
			<input type="submit" name="Add" value="{lang_add}"></form></td>
	</tr>
</table>
</center>
