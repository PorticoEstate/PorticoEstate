<!-- BEGIN form -->
  <table class="basic" align="center">
    <tr><td colspan="1" align="center" class="header"><b>{title_fields}<b/></td></tr>
  </table>
{message}
  <form name="form" action="{actionurl}" method="post">
    <table class="basic" align="center">
      <tr>
        <td class="bg_color2">{lang_name}:</td>
        <td class="bg_color2"><input name="field_name" size="50" value="{field_name}" /></td>
      </tr>
    </table>
<!-- BEGIN add -->
  <table class="basic" align="left">
    <tr>
      <td class="center">
        {hidden_vars}
        <input type="submit" name="submit" value="{lang_add}" />
      </td>
      <td class="center">
      	<br />
        <input type="reset" name="reset" value="{lang_reset}" />
</form>
      </td>
      <td class="right">
        <form method="post" action="{doneurl}">
          {hidden_vars}
           <br />
          <input type="submit" name="done" value="{lang_done}" />
</form>
      </td>
    </tr>
  </table>
</form>
<!-- END add -->

<!-- BEGIN edit -->
  <table class="basic" align="center">
    <tr class="bottom">
      <td class="center">
        {hidden_vars}
        <input type="submit" name="submit" value="{lang_edit}" />
</form>
      </td>
      <td class="center">
        <form method="post" action="{deleteurl}">
          {hidden_vars}
          <input type="submit" name="delete" value="{lang_delete}" />
        </form>
      </td>
      <td class="center">
        <form method="post" action="{doneurl}">
          {hidden_vars}
          <input type="submit" name="done" value="{lang_done}" />
        </form>
      </td>
    </tr>
  </table>
<!-- END edit -->
<!-- END form -->

