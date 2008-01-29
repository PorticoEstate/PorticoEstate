<!-- $Id: list_events.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{message}
<table width="60%" border="0" cellpadding="2" cellspacing="2" align="center">
	<tr bgcolor="{th_bg}">
		<td>{lang_alarm}</td>
		<td>{lang_event}</td>
	</tr>

<!-- BEGIN event_list -->

	<tr bgcolor="{tr_color}">
		<td>{event_extra}</td>
		<td>{event_name}</td>
	</tr>

<!-- END event_list -->

	<tr height="15">
		<td>&nbsp;</td>
	</tr>
<form method="POST" action="{action_url}">
	<tr bgcolor="{row_on}">
		<td><input type="text" name="values[limit]" value="{limit}" size="3" maxlength="3">&nbsp;{lang_days}</td>
		<td>{lang_before}&nbsp;<select name="values[event_id_limit]">{event_select_limit}</select></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td><input type="text" name="values[percent]" value="{percent}" size="2" maxlength="2">&nbsp;%</td>
		<td>{lang_before}&nbsp;<select name="values[event_id_percent]">{event_select_percent}</select></td>
	</tr>
	<tr height="50" valign="bottom">
		<td><input type="submit" name="save" value="{lang_save}"></td>
		<td align="right"><input type="submit" name="done" value="{lang_done}"></td>
	<tr>
</form>
</table>
