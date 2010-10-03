  {errors}
  {title}
<table class="basic" align="center">
  <tr>
    {nml}
    <td>
      <div class="center">
        <form method="post" action="{action_url}">
          {common_hidden_vars}
          <input type="text" name="query" value="{search_value}" />
          <input type="submit" name="search" value="{search}" />
        </form>
      </div>
    </td>
    {nmr}
  </tr>
</table>
<form method="post" action="{action_url}">
    <table class="basic" align="center">
      {row}
    </table>
  {common_hidden_vars_form}
  <input type="hidden" name="processed" value="{processed}" />
  <div class="center">
    <input type="submit" name="submit" value="{submit_lang}" />
  </div>
</form>

