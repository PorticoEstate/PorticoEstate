<!-- BEGIN columns_data -->
<td>{text}</td>
<!-- END columns_data -->

<!-- BEGIN input_detail_data -->
<tr bgcolor="{row_bgc}">
{columns}
</tr>
<!-- END input_detail_data -->

<!-- BEGIN input_detail_data_tmp -->
{tmp_detail}
<!-- END input_detail_data_tmp -->

<!-- BEGIN input_row_tmp  -->
{tmp_row}
<!-- END input_row_tmp -->

<!-- BEGIN block_row -->
<table border="0">
  <tr>
    <th style="border-bottom: solid black 2px; cursor: hand; cursor: pointer;"  colspan="6" onclick="showHide('{block_name}');">{block_name}</th>
  </tr>
</table>
<div id="{block_name}" style="display: none;">
<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
<tbody>
  {detail_body_set}
</tbody>
</table>
</div>
<!-- END block_row -->

<!-- BEGIN detail_body -->
<tr>
<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
  <tbody>
    <tr align="right">
      <td>
	<input type="submit" name="{detail_b_name}" value="{detail_b_value}">
      </td>
    </tr>

    <tr><th bgcolor="{th_bg}">{caption_detail}</th></tr>

    <tr>
      <td>

	{block_detail}

      </td>
    </tr>
  </tbody>
</table>
</tr>
<!-- END detail_body -->
