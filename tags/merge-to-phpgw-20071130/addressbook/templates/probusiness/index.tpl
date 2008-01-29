<!-- BEGIN addressbook_header -->
  <table class="basic" align="left">
    <tr>
      {principal_tabs_inc}
    </tr>
  </table>
  <br /><br /><br />
  <div align="center">
  {lang_showing}<br />
  {searchreturn}
  {search_filter}
  </div>

  <table class="padding" align="center" style="border: 2px solid #FFFFFF">
      <tr class="header">
        {cols}
        <td></td>
        <td></td>
        <td></td>
        <td><img src="addressbook/templates/probusiness/images/single.png" title="{lang_owner}" /></td>
      </tr>
<!-- END addressbook_header -->

<!-- BEGIN column -->
			<td class="bg_color1">{col_data}</td>
<!-- END column -->

<!-- BEGIN row -->
      <tr class="bg_view">
        {columns}
        <td class="bg_color2"><a href="{row_view_link}"><img src="phpgwapi/templates/default/images/view.png" title="{lang_view}" /></a></td>
        <td class="bg_color2"><a href="{row_vcard_link}"><img src="addressbook/templates/probusiness/images/newcntc.png" title="{lang_vcard}" /></a></td>
        <td class="bg_color2">{row_edit}</td>
        <td class="bg_color2"><img src="addressbook/templates/probusiness/images/single.png" title="{row_owner}" /></td>
      </tr>
<!-- END row -->

<!-- BEGIN addressbook_footer -->
  </table>
	<br />
	<form action="{add_url}" method="post">
		<input type="submit" name="Add" value="{lang_add}" />
	</form>
<!-- END addressbook_footer -->

