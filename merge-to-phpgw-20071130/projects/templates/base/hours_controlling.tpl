<!-- $Id: hours_controlling.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<style type="text/css"><!--
.table_cell_hover
 {
  background-color: #aaffcc;
 }
// -->
</style>
<script language="JavaScript" type="text/javascript"><!--
var table_cell_classes = Array();


function openhourview( projectid, day )
 {
  window.open('{view_hours_link}&project_id=' + projectid + '&day=' + day, '_self', '');
 }


function table_line_row_hover(line, row, status)
 {
  var table_cell_id = '';

  for(var l = 0; l <= line; ++l)
   {
    table_cell_id = 'L' + l + '_R' + row;
    set_cell_status(table_cell_id, status);
   }
  for(var r = 0; r < row; ++r)
   {
    table_cell_id = 'L' + line + '_R' + r;
    set_cell_status(table_cell_id, status);
   }
 }


function set_cell_status(table_cell_id, status)
 {
  var table_cell;

  table_cell = document.getElementById(table_cell_id);
  if (!table_cell)
   {
    return;
   }
  if (status == true)
   {
    table_cell_classes[table_cell_id] = table_cell.className;
    table_cell.className = 'table_cell_hover';
   }
  else
   {
    table_cell.className = table_cell_classes[table_cell_id];
   }
 }
// -->
</script>
<form method="post" action="{action_url}">
  <table cellspacing="2" cellpadding="2">
	<tr bgcolor="{th_bg}" style="vertical-align:top">
		<td align="center">{lang_employee}</td>
		<td align="center">{lang_start_date}</td>
		<td align="center">{lang_end_date}</td>
		<td align="center">&nbsp;</td>
		<td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td align="center">&nbsp;</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td style="white-space:nowrap;">{l_employee}</td>
		<td align="center" style="white-space:nowrap;">&nbsp;{sdate_select}&nbsp;</td>
		<td align="center" style="white-space:nowrap;">&nbsp;{edate_select}&nbsp;</td>
		<td style="white-space:nowrap;"><input type="submit" name="view" value="{l_view_sheet}"></td>
		<td>&nbsp;</td>
		<td align="right" style="white-space:nowrap;"><input type="submit" name="export" value="{l_export_sheet}">&nbsp;<input type="submit" name="import" value="{l_import_sheet}"></td>
	</tr>
  </table>
</form>
<br />
<table>
  <thead>
    <tr style="background-color:{th_bg_theme}">
      <!-- BEGIN blk_row_title0 -->
      <td id="L{cell_line}_R{cell_row}" style="font-weight:bold">{l_rowTitles}</td>
      <!-- END blk_row_title0 -->
      <!-- BEGIN matrix_day -->
      <td id="L{cell_line}_R{cell_row}" {holidaystyle} style="border-right:1px solid #FFFFFF; width:80px; text-align:center">{date}</td>
      <!-- END matrix_day -->
      <td id="L{total_cell_line}_R{total_cell_row}" style="border-right:1px solid #FFFFFF; width:80px; text-align:right">{l_total}</td>
	</tr>
  </thead>
  <tfoot>
    <tr style="font-size:10px; background-color:{theme_bg}">
      <td id="L{booked2_cell_line}_R{booked2_cell_row}" colspan="{rowtitles}" style="white-space:nowrap; font-weight:bold">{l_overtime}</td>
      <!-- BEGIN daytotal2 -->
      <td id="L{cell_line}_R{cell_row}" style="border-right:1px solid #FFFFFF; text-align:right" onmouseover="table_line_row_hover({cell_line},{cell_row},true);" onmouseout="table_line_row_hover({cell_line},{cell_row},false);" title="{content_tooltip}">{format_minutes}</td>
      <!-- END daytotal2 -->
      <td style="border-right:1px solid #FFFFFF; font-weight:bold; text-align:right">{booked_total2}</td>
    </tr>
    <tr style="background-color:{theme_th_bg}">
      <td id="L{booked_cell_line}_R{booked_cell_row}" colspan="{rowtitles}" style="white-space:nowrap; font-weight:bold">{l_total}</td>
      <!-- BEGIN daytotal -->
      <td id="L{cell_line}_R{cell_row}" style="border-right:1px solid #FFFFFF; text-align:right" onmouseover="table_line_row_hover({cell_line},{cell_row},true);" onmouseout="table_line_row_hover({cell_line},{cell_row},false);" title="{content_tooltip}">{format_minutes}</td>
      <!-- END daytotal -->
      <td style="border-right: 1px solid #FFFFFF; font-weight: bold; text-align: right">{booked_total}</td>
    </tr>
  </tfoot>
  <tbody>
    <!-- BEGIN body_row -->
    <tr style="{row_color}">
      <!-- BEGIN row_title -->
      <td id="L{cell_line}_R{cell_row}" style="white-space:nowrap; padding-right:10px"><a href="{matrix_link}">{pnumber}{enddate}{title}</a></td>
      <!-- END row_title -->
      <!-- BEGIN content_cell -->
      <td id="L{cell_line}_R{cell_row}" style="border-right:1px solid #FFFFFF; cursor:pointer; text-align:right;" onclick="openhourview({matrix_value});" onmouseover="table_line_row_hover({cell_line},{cell_row},true);" onmouseout="table_line_row_hover({cell_line},{cell_row},false);" title="{content_tooltip}">{content_value}</td>
      <!-- END content_cell -->
      <td id="L{cell_line}_R{cell_row}" style="border-right:1px solid #FFFFFF; font-weight:bold; text-align:right" onmouseover="table_line_row_hover({cell_line},{cell_row},true);" onmouseout="table_line_row_hover({cell_line},{cell_row},false);" title="{content_tooltip}">{row_total_value}</td>
    </tr>
    <!-- END body_row -->
  </tbody>
</table>


