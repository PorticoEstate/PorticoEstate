<!-- $Id: list_products.tpl 9701 2002-03-11 11:04:57Z milosch $ -->

<center>
<table border="0" width="100%">
  <tr colspan="2">
      <td>{category_list}</td>
      <td valign="top" align="center">
      <table border="0" width="90%" cellpadding="2" cellspacing="2">
        <tr>
          <td colspan="6"align="left">
            <table border="0" width="100%">
              <tr>
              {left}
                <td align="center">{lang_showing}</td>
              {right}
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="6" align="right">
            <form method="post" action="{search_action}">
            <input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
            </form></td>
        </tr>
        <tr bgcolor="{th_bg}" colspan="6">
        <form method="POST" action="{action_url}">
          <td align="center" width="8%">{lang_choose}</td>
          <td align="center" width="8%">{lang_piece}</td>
          <td width="20%">{sort_id}</td>
          <td width="25%">{sort_name}</td>
          <td align="right" width="10%">{currency}&nbsp;{sort_retail}</td>
          <td align="center" width="8%">{lang_view}</td>
        </tr>

<!-- BEGIN listproducts -->

        <tr bgcolor="{tr_color}">
          <td align="center">{choose}</td>
          <td align="center">{piece}</td>
          <td>{id}</td>
          <td>{name}</td>
          <td align="right">{retail}</td>
          <td align="center"><a href="{view}">{lang_view}</a></td>
        </tr>

<!-- END listproducts -->
      </table>
      <table border="0">
        <tr valign="bottom">
          <td height="50"><input type="submit" name="Add" value="{lang_add}"></td>
        </tr>
        </form>
      </table>
    </td>
  </tr>
</table>
</center>
