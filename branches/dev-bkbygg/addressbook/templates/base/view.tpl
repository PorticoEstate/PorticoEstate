<!-- BEGIN view_header -->
<table id="contacts_view">
<!-- END view_header -->
<!-- BEGIN view_row -->
	<tr class="{tr_class}">
		<td class="contacts_v_label">{display_col}</td>
		<td class="contacts_v_val">{ref_data}</td>
	</tr>
<!-- END view_row -->
{cols}
<!-- BEGIN view_footer -->
	<tr class="{owner_class}">
		<td class="contacts_v_label">{lang_owner}</td>
		<td class="contacts_v_val">{owner}</td>
	</tr>
	<tr class="{access_class}">
		<td class="contacts_v_label">{lang_access}</td>
		<td class="contacts_v_val">{access}</td>
	</tr>
	<tr class="{cat_class}">
		<td class="contacts_v_label">{lang_category}</td>
		<td class="contacts_v_val">{catname}</td>
	</tr>
</table>
<!-- END view_footer -->
<!-- BEGIN view_buttons -->
	<div class="button_group">
		{edit_button}
		{copy_button}
		{vcard_button}
		{done_button}
	</div>
<!-- END view_buttons -->
