<div id="wf_run_activity_message">{wf_message}</div>
<table style="border: 1px solid black;margin:0 auto;">
<tr class="th">
	<td colspan="2" style="font-size: 120%; font-weight:bold; text-align:center">
		{lang_Activity_completed}
	</td>
</tr>
<tr>
	<td class="th">{lang_Process:}</td>
	<td>{wf_procname} {procversion}</td>
</tr>
<tr>
	<td class="th">{lang_Activity:}</td>
	<td>{actname}</td>
</tr>
</table>
<br />
<table style="border: 1px solid black;margin:0 auto;">
{report}
</table>


<!-- BEGIN report_row -->
<tr class="{row_class}">
	<td>{icon_type_report}</td>
	<td>{icon_report}</td>
	<td>{label_report}</td>
	<td>{comment_report}</td>
</tr>
<!-- END report_row -->
