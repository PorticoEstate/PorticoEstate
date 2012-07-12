<!-- begin filter_list.tpl -->
{widget_toolbar}
<p align="center">
  <b class="header">{page_title}</b>
</p>
<hr />
  <table class="basic" align="center">
    <tr class="header">
      <td class="left">{filter_name_header}</td>
      <td class="center">{lang_move_up}</td>
      <td class="center">{lang_move_down}</td>
      <td class="center">{lang_edit}</td>
      <td class="center">{lang_delete}</td>
    </tr>
<!-- BEGIN B_filter_list_row -->
    <tr class="bg_view">
      <td class="left">{filter_identity}</td>
      <td class="center">{move_up_href}</td>
      <td class="center">{move_down_href}</td>
      <td class="center">{edit_href}</td>
      <td class="center">{delete_href}</td>
    </tr>
<!-- END B_filter_list_row -->
    <tr><td colspan="5">&nbsp;</td></tr>
    <tr class="center"><td colspan="5" class="bg_color2">{add_new_filter_href}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{done_href}</td></tr>
  </table>
<br />
<hr />
<br />
  <table class="padding" align="center">
    <tr class="center">
      <td class="header" colspan="2">
        {lang_test_or_apply}
      </td>
    </tr>
    <tr class="center">
      <td class="bg_color1">{test_all_filters_href}</td>
      <td class="bg_color2">{run_all_filters_href}</td>
    </tr>
  </table>
<p>&nbsp;</p>
<!-- end filter_list.tpl -->

