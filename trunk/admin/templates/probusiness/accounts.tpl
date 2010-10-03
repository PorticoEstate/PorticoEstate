<!-- BEGIN list -->
<table class="basic">
	<tr>
  	<td class="left">{left_next_matchs}</td>
		<td class="center">{lang_user_accounts}</td>
		<td class="right">{right_next_matchs}</td>
	</tr>
</table>
<table class="padding" align="center">
	<tr class="header">
		<td>{lang_loginid}</td>
		<td>{lang_lastname}</td>
		<td>{lang_firstname}</td>
		<td>{lang_edit}</td>
		<td>{lang_delete}</td>
		<td>{lang_view}</td>
	</tr>
	{rows}
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td><form method="post" action="{actionurl}">{input_add}</form>
		<td colspan="5"><form method="post" action="{accounts_url}">{input_search}&nbsp;</form></td>
	</tr>
	<tr>
		<td colspan="6"></td>
  </tr>		
</table>
<!-- END list -->

<!-- BEGIN row -->
        <tr>
          <td class="bg_color1">{row_loginid}</td>
          <td class="bg_color2">{row_lastname}</td>
          <td class="bg_color1">{row_firstname}</td>
          <td class="bg_color2">{row_edit}</td>
          <td class="bg_color1">{row_delete}</td>
          <td class="bg_color2">{row_view}</td>
        </tr>
<!-- END row -->

<!-- BEGIN row_empty -->
        <tr><td colspan="5" class="center">{message}</td></tr>
<!-- END row_empty -->

