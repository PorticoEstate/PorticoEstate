<!-- $Id: delete.tpl 9883 2002-04-05 23:35:03Z ceb $ -->

{app_header}

<center>
<table border="0" cellpadding="2" cellspacing="2">
<form method="POST" action="{action_url}">
	<tr>
		<td align="center">{deleteheader}</td>
	</tr>
	<tr>
		<td>
			<table border="0" width="100%">
				<tr>
					<td align="center">
						{hidden_vars}
						<input type="submit" name="confirm" value="{lang_yes}"></form></td>
					<td align="center"><a href="{nolink}">{lang_no}</a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</center>
