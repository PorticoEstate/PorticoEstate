<!-- BEGIN many_actions -->
<td>
<table border="0" align="center" width="80%">
  <tbody>
  	<th bgcolor="{th_bg}">{lang_general_data}</th>
    <tr>
      <td>
		<table border="0" align="center" width="100%">
		<tbody>
			<tr bgcolor="{row_on}">
			<td width="45%">{lang_person}{person}</td>
			<td width="10%"></td>
			<td width="45%">{lang_defaul}{options}</td>
			</tr>
		</tbody>
		</table>
	  </td>
    </tr>

    <tr>
      <td>

		<table border="0" align="center" width="100%">
		<tbody align="center">
			<tr bgcolor="{row_on}">
				<td width="45%">{lang_title_left}</td>
				<td width="10%"></td>
				<td width="45%">{lang_title_rigth}</td>
			</tr>
			<tr bgcolor="{row_off}">
			<td width="45%">
				{options_left}
			</td>
			<td width="10%">
				<table border="0" align="center">
				<tbody align="center">
					<tr>
					<td>
						<input type="button" onClick="move('{all_opt}','{my_opt}',
									'{current_opt}','{my_opt}')" value=">>">
					</td>
					</tr>
					<tr>
					<td>
						<input type="button" onClick="move('{my_opt}','{all_opt}',
									'{current_opt}','{my_opt}')" value="<<">
					</td>
					</tr>
				</tbody>
				</table>
			</td>
			<td width="45%">
				{options_rigth}
			</td>
			</tr>
		</tbody>
		</table>
	  </td>
    </tr>
  </tbody>
</table>
</td>
<!-- END many_actions -->