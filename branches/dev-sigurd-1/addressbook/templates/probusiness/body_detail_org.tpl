<!-- BEGIN columns_data -->
    <td>{text}</td>
<!-- END columns_data -->

<!-- BEGIN input_detail_data -->
  <tr>
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
<table>
  <tr><th style="border-bottom: solid black 2px; cursor: hand; cursor: pointer;"  colspan="6" onclick="showHide('{block_name}');">{block_name}</th></tr>
</table>
  <table class="basic" align="center">
    <tbody>
      {detail_body_set}
    </tbody>
  </table>
<!-- END block_row -->

<!-- BEGIN detail_body -->
  <tr>
    <td>
      <table class="basic">
        <tbody>
          <tr><td><input type="submit" name="{detail_b_name}" value="{detail_b_value}" /></td></tr>
          <tr><th>{caption_detail}</th></tr>
          <tr><td>{block_detail}</td></tr>
        </tbody>
      </table>
    </td>
  </tr>
<!-- END detail_body -->

