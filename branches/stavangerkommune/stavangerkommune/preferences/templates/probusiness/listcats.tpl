<br />
  <table class="basic" align="center">
    <tr>
      <td colspan="3" class="left">
        <table align="center">
          <tr class="center">
            {left}
            <td class="header">{lang_showing}</td>
            {right}
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td colspan="4" class="center">
        <form method="post" action="{actionurl}">
          <input type="text" name="query" />&nbsp;
          <input type="submit" name="search" value="{lang_search}" />
        </form>
      </td>
    </tr>
  </table>
  
    <table class="basic" align="center">
            <thead>
              <tr>
                <td>{sort_name}</td>
                <td>{sort_description}</td>
                {sort_data}
                <td align="center">{lang_app}</td>
                <td align="center">{lang_sub}</td>
                <td align="center">{lang_edit}</td>
                <td align="center">{lang_delete}</td>
              </tr>
            </thead>
<!-- BEGIN cat_list -->
            <tr>
              <td class="bg_color1">{name}</td>
              <td class="bg_color2">{descr}</td>
              {td_data}
              <td class="bg_color1" class="center"><a href="{app_url}">{lang_app}</a></td>
              <td class="bg_color2" class="center"><a href="{add_sub}">{lang_sub_entry}</a></td>
              <td class="bg_color1" class="center"><a href="{edit}">{lang_edit_entry}</a></td>
              <td class="bg_color2" class="center"><a href="{delete}">{lang_delete_entry}</a></td>
            </tr>
<!-- END cat_list -->

<!-- BEGINN add   -->
	</table>
    <table class="basic">
      <tr>
        <td class="left">
          <form method="post" action="{add_action}">
            <input type="submit" value="{lang_add}" />
          </form>
        </td>
        <td class="right">
          <form method="post" action="{doneurl}">
            <input type="submit" name="done" value="{lang_done}" />
          </form>
        </td>
      </tr>
    </table>
  
<!-- END add -->

