    <table class="basic" align="center">
      <tr>
        <td colspan="6" class="center">
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
        <td colspan="6" class="right">
          <form method="post" action="{actionurl}">
            <input type="text" name="query" />&nbsp;
            <input type="submit" name="search" value="{lang_search}" />
          </form>
        </td>
      </tr>
<!-- END search -->
      <tr class="header">
        <td>{sort_name}</td>
        <td>{sort_url}</td>
        <td>{sort_mode}</td>
        <td>{sort_security}</td>
        <td class="center">{lang_edit}</td>
        <td class="center">{lang_delete}</td>
      </tr>
<!-- BEGIN server_list -->
      <tr>
        <td class="bg_color1">{server_name}</td>
        <td class="bg_color2">{server_url}</td>
        <td class="bg_color1">{server_mode}</td>
        <td class="bg_color2">{server_security}&nbsp;</td>
        <td class="bg_color1" align="center">{edit}</td>
        <td class="bg_color2" align="center">{delete}</td>
      </tr>
<!-- END server_list -->
      <tr class="bottom">
<!-- BEGIN add   -->
        <td>
          <form method="post" action="{add_action}">
            <input type="submit" name="add" value="{lang_add}" />
          </form>
        </td>
<!-- END add -->
        <td>
          <form method="post" action="{doneurl}">
            <input type="submit" name="done" value="{lang_done}" />
          </form>
        </td>
      </tr>
    </table>

