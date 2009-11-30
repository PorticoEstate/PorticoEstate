<!-- $Id$ -->
<table border="0" cellspacing="2" cellpadding="2" width="80%">
		<tr>
			<td colspan="3" align=left>
				<table border="0" width="100%">
					<tr>
					{left}
						<td align="center">{lang_showing}</td>
					{right}
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="3" align=right>
				<form method="post" action="{actionurl}">
				<input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
				</form></td>
		</tr>
</table>
<table border="0" cellspacing="2" cellpadding="2" width="80%">
	<thead>
		<tr>
			<th>{sort_name}</td>
			<th>{sort_description}</td>
			{sort_data}
			<th align="center">{lang_app}</th>
			<th align=center>{lang_sub}</th>
			<th align=center>{lang_edit}</th>
			<th align=center>{lang_delete}</th>
		</tr>
	</thead>
	<tbody>

<!-- BEGIN cat_list -->
	<tr class="{tr_class}">
		<td>{name}</td>
		<td>{descr}</td>
		{td_data}
		<td align="center"><a href="{app_url}">{lang_app}</a></td>
		<td align="center"><a href="{add_sub}">{lang_sub_entry}</a></td>
		<td align="center"><a href="{edit}">{lang_edit_entry}</a></td>
		<td align="center"><a href="{delete}">{lang_delete_entry}</a></td>
	</tr>
<!-- END cat_list -->

	</tbody>
</table>

<!-- BEGIN add   -->
<div class="button_group">
	<form method="POST" action="{add_action}">
		<input type="submit" value="{lang_add}">
	</form>
	<form method="POST" action="{doneurl}">
		<input type="submit" name="done" value="{lang_done}">
	</form>
</div>
<!-- END add -->

<!-- BEGIN data_column -->
	<td>{data}</td>
<!-- END data_column -->
