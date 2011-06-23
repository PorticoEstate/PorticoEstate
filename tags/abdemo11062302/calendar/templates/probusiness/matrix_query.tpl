<!-- BEGIN matrix_query -->
  <form action="{action_url}" method="post" name="matrixform">
    <table border="0" class="basic" align="center">
      <tr>
				<td colspan="2" style="text-align: center" class="header">
					<b>{title}</b>
				</td>
      </tr>
     {rows}
      <tr style="vertical-align: top">
        <td style="padding-top: 8px">
					<input type="submit" value="{submit_button}" />
</form>
				</td>
				<td style="padding-top: 8px">
					{cancel_button}
				</td>
      </tr>
    </table>
<!-- END matrix_query -->

<!-- BEGIN list -->
  <tr class="bg_view">
    <td class="top">
    	<b>&nbsp;{field}:</b>
    </td>
    <td style="vertical-align: top; width: 100%">{data}</td>
  </tr>
<!-- END list -->
