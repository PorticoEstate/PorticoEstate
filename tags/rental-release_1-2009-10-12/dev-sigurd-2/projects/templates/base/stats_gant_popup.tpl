<!-- $Id: stats_gant_popup.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->

<html>
<head>
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
	<META HTTP-EQUIV="Expires" CONTENT="-1" />
	<META HTTP-EQUIV="Cache-Control" CONTENT="no-store, no-cache,max-age=0, must-revalidate" /> 
	<link rel="stylesheet" type="text/css" href="{css_file}">
	<link rel="stylesheet" type="text/css" media="all" href="{server_root}/phpgwapi/js/jscalendar/calendar-win2k-cold-1.css" title="win2k-col\d-1" />

	<script type="text/javascript" src="{server_root}/phpgwapi/js/jscalendar/calendar.js"></script>
	<script type="text/javascript" src="{jscal_setup_src}"></script>
</head>
<body>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<form method="POST" action="{action_url}">
	<input type="hidden" name="project_id" value="{project_id}" />
	<input type="hidden" name="start" value="{start}">
	<input type="hidden" name="end" value="{end}">
	<tr height="50">
		<td width="20%">&nbsp;</td>
		<td>{lang_start_date}:&nbsp;{sdate_select}</td>
		<td>{lang_end_date}:&nbsp;{edate_select}</td>
		<td align="right"><input type="submit" name="show" value="{lang_show_chart}" /></td>
		<td width="20%">&nbsp;</td>
	</tr>
	<tr>
	<map name="plus">

<!-- BEGIN map -->

		<AREA SHAPE="Rect" HREF="{gantt_url}" COORDS="{coords}" />

<!-- END map -->

	</map>
		<td colspan="5" align="center"><img usemap="#plus" src="{pix_src}" border="0" /></td>
	</tr>
	<tr>
		<td colspan="5" align="center"><input type="button" value="{lang_close_window}" onClick="window.close();" /></td>
	</tr>
	</form>
</table>

