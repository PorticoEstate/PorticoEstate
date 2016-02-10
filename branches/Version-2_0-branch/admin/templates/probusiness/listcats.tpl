    <table class="basic" align="center">
      <tr>
        <td colspan="5" align="center">
          <table class="basic">
            <tr>
              {left}
              <td align="center" class="header">{lang_showing}</td>
              {right}
            </tr>
          </table>
        </td>
      </tr>
<!-- BEGIN search -->
      <tr>
        <td colspan="5" align="right">
          <form method="post" action="{action_nurl}">
            <input type="text" name="query" />&nbsp;
            <input type="submit" name="search" value="{lang_search}" />
          </form>
        </td>
      </tr>
<!-- END search -->
      <tr class="header">
        <td width="20%">{sort_name}</td>
        <td width="32%">{sort_description}</td>
        <td width="8%" align="center">{lang_sub}</td>
        <td width="8%" align="center">{lang_edit}</td>
        <td width="8%" align="center">{lang_delete}</td>
      </tr>
<!-- BEGIN cat_list -->
      <tr>
        <td class="bg_color1">{name}</td>
        <td class="bg_color2">{descr}</td>
        <td class="bg_color1" align="center">{add_sub}</a></td>
        <td class="bg_color2" align="center">{edit}</a></td>
        <td class="bg_color1" align="center">{delete}</a></td>
      </tr>
<!-- END cat_list -->
      <tr valign="bottom" height="50">
        <form method="POST" action="{action_url}">
<!-- BEGIN add -->
          <td colspan="2">
            <input type="submit" name="add" value="{lang_add}" />
          </td>
<!-- END add -->
          <td colspan="3" align="right">
            <input type="submit" name="done" value="{lang_done}" />
          </td>
        </form>
      </tr>
    </table>
  

