<div style="float: right"><img src="{current_version_status_image}"/></div>
<div class="ged_title">{name} [{reference}] v{current_version}</div>
<div class="ged_file_description">{description}<br/>
{current_version_description}</div>
<div class="ged_file_metadata">Owned by : {owner} - Creation : {creation_date} - Version : {current_version_date} by {current_version_creator} -
{lang_current_version_expiration_date} : {current_version_expiration_date}
</div>

<div style="float: right"><a href="{download_all_link}" ><img style="{border: none;}" src="{image_download-32}"/></a></div>
<div class="ged_title">Linking to :</div>
<table id="ged_file_relations" cellspacing="0" cellpadding"0">
<!-- BEGIN relations_list -->
<tr class="{relation_status_oe}">
	<td><img src="{relation_status_image}"/></td>
	<td><a href="{relation_link}" >{relation_name} [{relation_reference}] v{relation_version}</a></td>
	<td>{relation_type}</td>
</tr>
<!-- END relations_list -->
</table>

<div class="ged_title">Linked by :</div>
<table id="ged_file_relations2" cellspacing="0" cellpadding"0">
<!-- BEGIN relations_list2 -->
<tr class="{relation_status_oe}">
	<td><img src="{relation_status_image}"/></td>
	<td><a href="{relation_link}" >{relation_name} [{relation_reference}] v{relation_version}</a></td>
	<td>{relation_type}</td>
</tr>
<!-- END relations_list2 -->
</table>

<div class="ged_title">Versions :</div>
<table id="ged_file_versions" cellspacing="0" cellpadding"0">
<!-- BEGIN versions_list -->
<tr class="{file_version_status_oe}">
	<td rowspan="2"><a href="{show_version_link}#ged_file_relations"><img style="{border: none;}" src="{version_status_image}"/></a></td>
	<td ><em>{version}</em></td>
	<td>{version_description}</td>
	<td align="right">
		<a href="{download_file_link}" target="{download_file_target}">{lang_download}</a>
		<a href="{view_file_link}" target="{view_file_target}">{lang_view}</a>
	</td>
</tr>
<tr class="{file_version_status_oe}">
	<td colspan="3" class="ged_file_metadata">Created by {version_creator} on {version_creation_date}</td>	
</tr>
<!-- END versions_list -->
</table>