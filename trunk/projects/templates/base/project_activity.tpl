<!-- $Id: project_activity.tpl,v 1.2 2006/12/05 19:40:45 sigurdne Exp $ -->
{app_header}
<div class="projects_content"></div>
<table cellspacing="2" cellpadding="2">
	<form method="POST" action="{action_url}">
	<tr bgcolor="{th_bg}">
		<td>{l_project}</td>
		<td>{lang_start_date}:&nbsp;{sdate_select}</td>
		<td>{lang_end_date}:&nbsp;{edate_select}</td>
		<td>{l_budget_modus}</td>
		<td>
		<select name="budgetmodus" size="1">
	    <option value="h">{l_hour}</option>
	    <option value="m" {selected}>{l_monetary}</option>
	  </select>
		<td><input type="submit" name="view" value="{l_update_view}"></td>
	</tr>
</form>
</table>
<br />
<table cellspacing="0" cellpadding="0" align="center" style="border: 2px solid #FFFFFF; empty-cells: show" >
	
	{tableContent}
	
	</tbody>
	<tfoot>
		<tr style="background-color: #d3dce3">
			<td>
			{l_total}
			</td>
			{tableContent2}
			<td style="background-color: #d3dce3"></td>
		</tr>
	</tfoot>
</table>		
			

<!-- BEGIN EntryDetails -->
<!--
<table width="100%">
	<tr>
		<td>
			{l_entryDetails_date}
		</td>
		<td align=right>
			{entryDetails_date}
		</td>
	</tr>
	<tr>
		<td>
			{l_entryDetails_begin}
		</td>
		<td align=right>
			{entryDetails_begin}
		</td>
	</tr>
	<tr>
		<td>
			{l_entryDetails_end}
		</td>
		<td align=right>
			{entryDetails_end}
		</td>
	</tr>
	<tr>
		<td>
			{l_entryDetails_hours}
		</td>
		<td align=right>
			{entryDetails_hours}
		</td>
	</tr>
	<tr>
		<td>
			{l_entryDetails_status}
		</td>
		<td align=right>
			{entryDetails_status}
		</td>
	</tr>
</table>
-->
<!-- END EntryDetails -->

<script>
function show_details(id)
{
	document.getElementById(id).style.display = 'block';
}

function hide_details(id)
{
	document.getElementById(id).style.display = 'none';
}

// Tooltip handling
var tt_db = (document.compatMode && document.compatMode != 'BackCompat') ? document.documentElement : document.body? document.body : null;

tt_Init();
</script>