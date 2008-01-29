<!-- BEGIN form -->
  <form method="post" action="{action_url}">
    {hidden_vars}
      <table class="padding" align="center">
        <tr class="center">
          <td class="header" colspan="2">
            <b>{cat_name}</b>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="bg_color1">
            <div class="center">{messages}</div>
          </td>
        </tr>
        <tr><td class="center" colspan="2">{lang_subs}&nbsp;{subs}</td></tr>
        <tr>
<!-- BEGIN delete -->
          <td class="center"><input type="submit" name="confirm" value="{lang_yes}" /></td>
          <td class="center"><input type="submit" name="cancel" value="{lang_no}" /></td>
<!-- END delete -->
<!-- BEGIN done -->
          <td class="center"><input type="submit" name="cancel" value="{lang_ok}" /></td>
<!-- END done -->
        </tr>
      </table>
  </form>

<!-- END form -->

