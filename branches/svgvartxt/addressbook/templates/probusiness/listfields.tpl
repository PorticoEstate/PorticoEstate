	<table class="basic">
    <tr><td colspan="6" class="center"><b>{title_fields}<b/></td></tr>
    <tr>
      <td colspan="6" class="left">
        <table class="basic">
          <tr>
            {left}
            <td class="center">{lang_showing}</td>
            {right}
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="right">
        <form method="post" action="{actionurl}">
          <input type="text" name="query" />&nbsp;
          <input type="submit" name="search" value="{lang_search}" />
        </form>
      </td>
    </tr>
    <tr>
      <td id="16">{sort_field}</td>
      <td id="8" class="center">{lang_edit}</td>
      <td id="8" class="center">{lang_delete}</td>
    </tr>
<!-- BEGIN field_list -->
    <tr>
      <td>{cfield}</td>
      <td class="center"><a href="{edit}">{lang_edit_entry}</a></td>
      <td class="center"><a href="{delete}">{lang_delete_entry}</a></td>
    </tr>
<!-- END field_list -->

<!-- BEGIN add   -->
    <tr class="bottom">
      <td>
        <form method="post" action="{add_action}">
          <input type="submit" name="add" value="{lang_add}" />
        </form>
      </td>
    </tr>
    <tr class="bottom">
      <td>
        <form method="post" action="{doneurl}">
          <input type="submit" name="done" value="{lang_done}" />
        </form>
      </td>
    </tr>
<!-- END add -->
  </table>

