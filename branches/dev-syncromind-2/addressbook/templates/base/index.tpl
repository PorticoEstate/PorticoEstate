<!-- BEGIN addressbook_header -->
{tabs}
<!--{principal_tabs_inc}-->

{lang_showing}
<br />{searchreturn}
{search_filter}

<table>
	<thead>
		<tr>
			{cols}
			<th width="5%">{lang_view}</th>
			<th width="5%">{lang_vcard}</th>
			<th width="5%">{lang_edit}</th>
			<th width="5%">{lang_owner}</th>
		</tr>
	</thead>
	<tbody>
<!-- END addressbook_header -->

<!-- BEGIN column -->
			<td>{col_data}&nbsp;</td>
<!-- END column -->

<!-- BEGIN row -->
		<tr class="{row_class}">
			{columns}
			<td><a href="{row_view_link}">{lang_view}</a></td>
			<td><a href="{row_vcard_link}">{lang_vcard}</a></td>
			<td>{row_edit}</td>
			<td>{row_owner}</td>
		</tr>
<!-- END row -->

<!-- BEGIN addressbook_footer -->
	</tbody>
</table>
<table>
  <tr>
	<td>
     <form action="{add_url}"    method="post"><td width="16%"><input type="submit" name="Add" value="{lang_add}"></td></form>
     <form action="{cat_cont_url}" method="post"><td width="16%"><input type="submit" name="Categorize" value="{lang_cat_cont}"></td></form>
     <form action="{vcard_url}"  method="post"><td width="16%"><input type="submit" name="AddVcard" value="{lang_addvcard}"></td></form>
     <form action="{import_url}" method="post"><td width="16%"><input type="submit" name="Import" value="{lang_import}"></td></form>
     <form action="{import_alt_url}" method="post"><td width="16%"><input type="submit" name="Import" value="{lang_import_alt}"></td></form>
     <form action="{export_url}" method="post"><td width="16%"><input type="submit" name="Export" value="{lang_export}"></td></form>
    </td>
   </tr>
</table>
<!-- END addressbook_footer -->
