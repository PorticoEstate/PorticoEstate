<!-- BEGIN form -->
<br />
<div class="center">
  {message}<br />
</div>
<table class="padding" align="center">
<form name="form" action="{actionurl}" method="post">
      <tr class="header"><td colspan="3">&nbsp;</td></tr>
      <tr class="bg_color1">
        <td colspan="2">{lang_parent}</td>
        <td>
          <select name="new_parent">
            <option value="">{lang_none}</option>
            {category_list}
          </select>
        </td>
      </tr>
      <tr class="bg_color2">
        <td colspan="2">{lang_name}</td>
        <td><input name="cat_name" size="50" value="{cat_name}" /></td>
      </tr>
      <tr class="bg_color1">
        <td colspan="2">{lang_descr}</td>
        <td colspan="2"><textarea name="cat_description" rows="4" cols="50" wrap="virtual">{cat_description}</textarea></td>
      </tr>
      <tr class="bg_color2">
        <td colspan="2">{lang_access}</td>
        <td colspan="2">{access}</td>
      </tr>
<!-- BEGINN data_row -->
      <tr>
        <td colspan="2">{lang_data}</td>
        <td>{td_data}</td>
      </tr>
<!-- END data_row -->

<!-- BEGIN add -->

      <tr id="height">
        <td colspan="2">
          <input type="submit" name="save" value="{lang_save}" />
</form>
        </td>
        <td class="right">
          <form method="post" action="{cancel_url}">
            <input type="submit" name="cancel" value="{lang_cancel}" />
          </form>
        </td>
      </tr>
    </table>

<!-- END add -->

<!-- BEGIN edit -->
      <tr class="bottom" id="50">
        <td>
          {hidden_vars}
          <input type="submit" name="save" value="{lang_save}" />
</form>
        </td>
        <td>
          <form method="post" action="{cancel_url}">
            <input type="submit" name="cancel" value="{lang_cancel}" />
          </form>
        </td>
        <td class="right"><div class="workaround">{delete}</div></td>
      </tr>
    </table>
<!-- END edit -->
<!-- END form -->

