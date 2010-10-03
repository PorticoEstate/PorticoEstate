<!-- $Id: stats_gant.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->

<script language="JavaScript">
	self.name="first_Window";
	function gantt_chart()
	{
		Window1=window.open('{gantt_link}',"Search","width=1024,height=768,toolbar=no,scrollbars=yes,resizable=yes");
	}
</script>

{app_header}
<div class="projects_content"></div>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<form method="POST" action="{action_url}">
	<input type="hidden" name="project_id" value="{project_id}">
	<input type="hidden" name="start" value="{start}">
	<input type="hidden" name="end" value="{end}">
	<tr bgcolor="{th_bg}">
		<td align="center" width="30%" nowrap="nowrap">{lang_start_date}:&nbsp;{sdate_select}</td>
		<td align="center" width="30%" nowrap="nowrap">{lang_end_date}:&nbsp;{edate_select}</td>
		<td align="center" width="40%" nowrap="nowrap"><input type="submit" name="show" value="{lang_show_chart}"></td>
	</tr>
	<tr height="5">
		<td colspan="3"></td>
	</tr>
	<tr>
		<map name="plus">

<!-- BEGIN map -->

			<area shape="Rect" href="{gantt_url}" coords="{coords}" />

<!-- END map -->

		</map>
		<td colspan="3" align="center"><img usemap="#plus" src="{pix_src}" border="0"><hr></td>
	</tr>
	<tr height="30">
		<td colspan="3" align="right"><input type="button" name="gantt_popup" value="{lang_show_gantt_in_new_window}" onclick="gantt_chart();"></td>
	</tr>
	</form>
</table>
