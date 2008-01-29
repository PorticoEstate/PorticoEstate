<!-- BEGIN list -->
<br />
    <table class="padding" align="center">
      <tr>
        {left}
        <td class="center">{lang_showing}</td>
        {right}
      </tr>
    </table>
    <table class="basic" align="center">
      <thead>
      <tr>
        <td>{sort_title} </td>
        <td>{lang_edit}</td>
        <td>{lang_delete}</td>
        <td>{lang_enabled}</td>
      </tr>
      </thead>
      {rows}
    </table>
  
  {addbutton}
<!-- END list -->

<!-- BEGIN add -->
    <table class="basic" align="center">
      <tr>
        <td class="left">
          <form method="post" action="{new_action}">
            <input type="submit" value="{lang_add}" />
          </form>
        </td>
      </tr>
      <tr><td>{lang_note}</td></tr>
    </table>
<!-- END add -->

<!-- BEGIN row -->
      <tr>
        <td class="bg_color1">{name}</td>
        <td class="bg_color2" align="center">{edit}</td>
        <td class="bg_color1" align="center">{delete}</td>
        <td class="bg_color2" align="center">{status}</td>
      </tr>
<!-- END row -->

