<div id="ged_top_menu">{top_link} {up_link} {update_folder} {add_folder} {delete_folder} {add_file} {edit_file} {update_file} {refuse_file} {accept_file} {submit_file} {deliver_file} {reject_file} {approve_file} {delete_file} {change_acl} {search} {stats}</div>
<br/>
<div align=center>
<form name="search" action="{action_search}" method="get">
<input name="menuaction" type="hidden" value="{menuaction}">
<input name="sessionid" type="hidden" value="{sessionid}">
<input name="click_history" type="hidden" value="{click_history}">
<input name="{search_query_field}" type="text" size="50" value="{search_query_value}"> <input name="{do_search_command}" type="submit" value="{do_search_value}">
</form>
<table cellspacing="0" cellpadding="0" width="70%">
<!-- BEGIN search_results_block -->
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
<!-- END search_results_block -->
</table>
</div>
