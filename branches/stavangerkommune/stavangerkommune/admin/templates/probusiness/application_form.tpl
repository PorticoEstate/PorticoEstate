<!-- BEGIN form -->
{error}
<br />
<form method="post" action="{form_action}">
  {hidden_vars}
    <table class="basic" align="center">
      <thead><tr><td colspan="3">&nbsp;</td></tr></thead>
      {rows}
      <tr>
        <td class="left"><input type="submit" name="save" value="{lang_save_button}" /></td>
        <td class="left"><input type="submit" name="cancel" value="{lang_cancel_button}" /></td>
        <td class="right">&nbsp;
<!-- BEGIN delete_button -->
          <input type="submit" name="delete" value="{lang_delete_button}" />
<!-- END delete_button -->
        </td>
      </tr>
    </table>
</form>
<!-- END form -->

<!-- BEGIN row -->
      <tr>
        <td class="bg_color2">
        	<div class="left">
        		{label}
        	</div>
        </td>
        <td class="bg_color1" colspan="2">
        	<div class="left">
        		{value}
        	</div>
        </td>
      </tr>
<!-- END row -->
