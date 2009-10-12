<!-- $Id: formactivity.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
<center>
{pref_message}<br>{message}
<table width="75%" border="0" cellspacing="2" cellpadding="2">
<form method="POST" name="activity_form" action="{actionurl}">
	<tr>
		<td>{lang_choose}</td>
		<td>{choose}</td>
	</tr>
	<tr>
		<td>{lang_act_number}:</td>
		<td><input type="text" name="values[number]" value="{num}" size="20" maxlength="20"></td>
	</tr>
	<tr>
		<td valign="top">{lang_descr}:</td>
		<td colspan="2"><textarea name="values[descr]" rows=4 cols=50 wrap="VIRTUAL">{descr}</textarea></td>
	</tr>
	<tr>
		<td>{lang_category}:</td>
		<td><select name="values[cat]"><option value="">{lang_none}</option>{cats_list}</select></td>
	</tr>
	<tr>
		<td>{lang_remarkreq}:</td>
		<td><select name="values[remarkreq]">{remarkreq_list}</select></td>
	</tr>
	<tr>
		<td>{lang_billperae}:&nbsp;{currency}</td>
		<td><input type="text" name="values[billperae]" value="{billperae}"></td>
	</tr>
	<tr>
		<td>{lang_minperae}</td>
		<td>{minperae}</td>
	</tr>
	<tr valign="bottom" height="50">
		<td><input type="submit" name="save" value="{lang_save}"></td>
		<td align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
</form>
</table>
</center>

<!-- END edit -->
