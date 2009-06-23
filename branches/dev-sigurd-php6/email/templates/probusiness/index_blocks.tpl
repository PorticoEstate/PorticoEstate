<!-- BEGIN B_mlist_form_init -->
<form name="{frm_delmov_name}" action="{frm_delmov_action}" method="post">
  <input type="hidden" name="what" value="delall" />
  <!-- input type="hidden" name="folder" value=" { current_folder } " -->
  <input type="hidden" name="sort" value="{current_sort}" />
  <input type="hidden" name="order" value="{current_order}" />
  <input type="hidden" name="start" value="{current_start}" />
<!-- END B_mlist_form_init -->

<!-- BEGIN B_arrows_form_table -->
<table class="basic" align="center">
  <tr>
    <td>
			{first_page}
    <td>
			{prev_page}
    </td>
    <td class="right">
			{next_page}
    </td>
    <td class="right">
			{last_page}
    </td>
  </tr>
</table>
<!-- END B_arrows_form_table -->

<!-- BEGIN B_stats_layout2 -->
<table class="basic" align="center" style="text-align: center; font-style: bold">
  <tr class="header">
    <td>{stats_folder}</td>
    <td>{stats_new}&nbsp;&nbsp;{lang_new}</td>
    <td>{stats_saved}&nbsp;&nbsp;{lang_total}</td>
	{form_get_size_opentag}
		<td>{stats_size_or_button}&nbsp;{lang_size}</td>
	{form_get_size_closetag}
    <td>{stats_first}&nbsp;{stats_to_txt}&nbsp;{stats_last}</td>
  </tr>
</table>
<!-- END B_stats_layout2 -->

<!-- BEGIN B_stats_layout1 -->
<table class="basic">
  <tr class="header">
    <td>
      <table>
        <tr>
          {form_get_size_opentag}
          <td>
            &nbsp;<strong>{stats_folder}</strong><br />
            
              &nbsp;&nbsp;&nbsp;{stats_new}&nbsp;&nbsp;:&nbsp;&nbsp;{lang_new2}<br />
              &nbsp;&nbsp;&nbsp;{stats_saved}&nbsp;&nbsp;:&nbsp;&nbsp;{lang_total2}<br />
              &nbsp;&nbsp;&nbsp;{stats_size_or_button}&nbsp;&nbsp;&nbsp;&nbsp;{lang_size2}
            
          </td>
          {form_get_size_closetag}
          <td align="right">
            <table>
              <tr>
                {form_folder_switch_opentag}
                <td>{folder_switch_combobox}</td>
                <td>&nbsp;&nbsp;{folders_btn}</td>
                {form_folder_switch_closetag}
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!-- END B_stats_layout1 -->

<!-- BEGIN B_mlist_block -->
  <tr class="bg_color1">
    <td align="center">
      {V_mlist_form_init}
      <input type="checkbox" name="delmov_list[]" value="{mlist_msg_num}" />
    </td>
    <td align="center">{mlist_attach}</td>
    <td align="left" class="no_wrap">{open_newbold}{mlist_from} {mlist_from_extra}{close_newbold}</td>
    <td align="left">{open_newbold}<a href="{mlist_subject_link}">{mlist_subject}</a>{close_newbold}</td>
    <td align="center">{mlist_date}</td>
    <td align="center">{mlist_size}</td>
  </tr>
<!-- END B_mlist_block -->

<!-- BEGIN B_mlist_submit_form -->
<form action="{mlist_submit_form_action}" method="post">
  {mlist_hidden_vars}
  <p>Pass off to mlist class <input type="submit" name="submit" value="Submit to mlist" /> to navigate all results.</p>
</form>
<!-- END B_mlist_submit_form -->

