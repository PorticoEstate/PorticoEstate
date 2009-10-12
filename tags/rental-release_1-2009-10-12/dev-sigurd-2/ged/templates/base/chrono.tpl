<div id="ged_top_menu">{top_link} {up_link} {update_folder} {add_folder} {delete_folder} {add_file} {edit_file} {update_file} {refuse_file} {accept_file} {submit_file} {deliver_file} {reject_file} {approve_file} {delete_file} {change_acl} {search} {stats} {chrono}</div>
<h1>{lang_chrono_title}</h1>
<table>
<!-- BEGIN type_block -->
<tr>
<td colspan="4"><h2>{doc_type}</h2></td>
</tr>
<tr class="row_off">
<td>No</td><td>reference</td><td>version</td><td>name</td><td>author</td><td>date</td>
</tr>
<!-- BEGIN chrono_block -->
<tr class="{row_class}">
<td>{no}<td><a href="{browse_link}" title="{description}">{reference}</a></td><td><a href="{browse_link}">{version_label} <img src="{status_image}" /></a></td><td><a href="{browse_link}">{name}</a></td><td>{author}</td><td>{date}</td>
</tr>
<!-- END chrono_block -->
<tr>
<td>&nbsp;</td>
</tr>
<!-- END type_block -->
</table>
<a href="{export_csv_link}" >{lang_export_csv}</a>