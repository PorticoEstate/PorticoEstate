<h2>{title_fields}</h2>
<table border="0" width="100%">
	<tr>
		{left}
		<td align="center">{lang_showing}</td>
		{right}
	</tr>
</table>

<form method="post" action="{actionurl}"><input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}"></form>

<table id="contacts_fields_list">
	<thead>
		<tr>
			<td>{sort_field}</td>
			<td>{lang_edit}</td>
			<td>{lang_delete}</td>
		</tr>
	</thead>
	<tbody>
	<!-- BEGIN field_list -->
		<tr class="{tr_class}">
			<td>{cfield}</td>
			<td><a href="{edit}">{lang_edit_entry}</a></td>
			<td><a href="{delete}">{lang_delete_entry}</a></td>
		</tr>
	<!-- END field_list -->  
	</tbody>
</table>

<!-- BEGIN add   -->
<div class="btngrp">
	<form method="post" action="{add_action}">
		<button type="submit" name="add" value="1">{lang_add}</button>
	</form>
	<form method="post" action="{doneurl}">
		<button type="submit" name="done" value="1">{lang_done}</button>
	</form>
</div>
<!-- END add -->
