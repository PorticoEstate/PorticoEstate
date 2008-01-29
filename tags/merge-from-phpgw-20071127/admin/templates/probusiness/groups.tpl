<!-- BEGIN list -->
    <table class="basic" align="center">
      <tr>
        <td class="left">{left_next_matchs}</td>
        <td class="header" align="center">{lang_groups}</td>
        <td class="right">{right_next_matchs}</td>
      </tr>
    </table>
    <table class="basic" align="center">
      <thead>
      <tr>
        <td>{sort_name}</td>
        <td>{header_edit}</td>
        <td>{header_delete}</td>
      </tr>
      </thead>
      {rows}
    </table>
    <table align="center">
      <tr>
        <td class="left">
          <form method="post" action="{new_action}">{input_add}</form>
        </td>
        <td class="right">
          <form method="post" action="{search_action}">{input_search}</form>
        </td>
      </tr>
    </table>
  
<!-- END list -->

<!-- BEGIN row -->
	<tr>
		<td class="bg_color1">{group_name}</td>
		<td class="bg_color2" width="5%">{edit_link}</td>
		<td class="bg_color1" width="5%">{delete_link}</td>
	</tr>
<!-- END row -->

<!-- BEGIN row_empty -->
	<tr><td colspan="5" align="center">{message}</td></tr>
<!-- END row_empty -->

