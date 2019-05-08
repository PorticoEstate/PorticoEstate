<!-- BEGIN list -->
<p><b>{lang_installed}</b><hr><p>

<p>
<div align="center">
	<table class="pure-table">
		<tr class="bg_color">
			{left}
			<td align="center">{lang_showing}</td>
			{right}
		</tr>
	</table>

	<table class="pure-table pure-table-bordered">
		<thead>
			<tr>
				<th> {sort_title} </th>
				<th>{lang_edit}</th>
				<th>{lang_delete}</th>
				<th>{lang_enabled}</th>
			</tr>
		</thead>

		<tbody>
			{rows}
		</tbody>
	</table>

	<table class="pure-table">
		<tr>
			<td align="left">
				<form method="POST" action="{new_action}">
					<input type="submit" value="{lang_add}">
				</form>
			</td>
			<td>
				{lang_note}
			</td>
		</tr>
	</table>
</div>
<!-- END list -->

<!-- BEGIN row -->
<tr>
	<td>{name}</td>
	<td width="5%">{edit}</td>
	<td width="5%">{delete}</td>
	<td width="5%">{status}</td>
</tr>
<!-- END row -->
