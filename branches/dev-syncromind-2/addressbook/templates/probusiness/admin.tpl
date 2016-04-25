<!-- BEGIN admin.tpl -->
<p><b>{lang_admin}:</b></p>
<hr />
<p></p>
<form method="post" action="{action_url}">
  <table class="addressbookAdmin">
    <tr><td colspan="3">{lang_countrylist} <input type="checkbox" name="usecountrylist"{countrylist} /></td></tr>
    <tr><td colspan="5" class="center"><input type="submit" name="submit" value="{lang_submit}" /></td></tr>
  </table>
</form>

