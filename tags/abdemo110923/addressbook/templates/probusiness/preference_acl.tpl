  {errors}
  {title}
  <table class="padding">
    <tr>
      {nml}
      <td>
				<form method="post" action="{action_url}">
				 {common_hidden_vars}
					<div class="center">
						<input type="text" name="query" value="{search_value}" />
						<input type="submit" name="search" value="{search}" />
					</div>
				</form>
      </td>
      {nmr}
    </tr>
  </table>
<form method="post" action="{action_url}">
    <table align="center">
      {row}
    </table>
  {common_hidden_vars_form}
  <input type="hidden" name="processed" value="{processed}" />
  <div class="center"><input type="submit" name="submit" value="{submit_lang}" /></div>
</form>
