<div id="ged_top_menu">{top_link} {up_link} {update_folder} {add_folder} {delete_folder} {add_file} {edit_file} {update_file} {refuse_file} {accept_file} {submit_file} {deliver_file} {reject_file} {approve_file} {delete_file} {change_acl} {search} {stats}</div>
<br/>
<div align=left>
<form name="period" method="GET" action="{action_filer}">
<input name="menuaction" type="hidden" value="{menuaction}">
<input name="sessionid" type="hidden" value="{sessionid}">
<input name="click_history" type="hidden" value="{click_history}">
{jscal_start}
{jscal_end}
<input type="submit" name="ok" value="filter"/>
</form>
<!-- BEGIN ged_projects -->
<h2>Project : {project_name}</h3>
<img src="{test_graph_link}" />
<h3>Documents delivered during period : {count_delivered}</h2>
<table cellspacing="0" cellpadding="0" width="70%">
<!-- BEGIN delivered_block -->
<tr>
<td width="20" height="16" valign="bottom">
<img height="16" src="{status_image}">
</td>
<td valign="top"><a href="{search_link}" style="font-size: 10pt;"><b>{name}</b> [{reference}] {version}</a>
</td>
</tr>
<tr>
<td></td>
<td>
<span style="font-size: 8pt;">{description} - {descriptionv}</span>
</td>
</tr>
<tr height="5px">
<td></td>
</tr>
<!-- END delivered_block -->
</table>

<h3>Documents approved during period : {count_approved}</h2>
<table cellspacing="0" cellpadding="0" width="70%">
<!-- BEGIN accepted_block -->
<tr>
<td width="20" height="16" valign="bottom">
<img height="16" src="{status_image}">
</td>
<td valign="top"><a href="{search_link}" style="font-size: 10pt;"><b>{name}</b> [{reference}] {version}</a>
</td>
</tr>
<tr>
<td></td>
<td>
<span style="font-size: 8pt;">{description} - {descriptionv}</span>
</td>
</tr>
<tr height="5px">
<td></td>
</tr>
<!-- END accepted_block -->
</table>


<h3>Documents refused during period : {count_rejected}</h2>
<table cellspacing="0" cellpadding="0" width="70%">
<!-- BEGIN refused_block -->
<tr>
<td width="20" height="16" valign="bottom">
<img height="16" src="{status_image}">
</td>
<td valign="top"><a href="{search_link}" style="font-size: 10pt;"><b>{name}</b> [{reference}] {version}</a>
</td>
</tr>
<tr>
<td></td>
<td>
<span style="font-size: 8pt;">{description} - {descriptionv}</span>
</td>
</tr>
<tr height="5px">
<td></td>
</tr>
<!-- END refused_block -->
</table>
<!-- END ged_projects -->

</div>
