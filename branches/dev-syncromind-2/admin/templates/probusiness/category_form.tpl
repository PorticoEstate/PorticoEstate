<!-- BEGIN form -->
  {message}<br />
  <form name="form" method="post" action="{action_url}">

      <table class="basic" align="center">
        <thead><tr><td colspan="2">&nbsp;</td></tr></thead>
        <tr class="bg_color1">
          <td>{lang_parent}</td>
          <td>
            <select name="new_parent">
              <option value="">{lang_none}</option>
              {category_list}
            </select>
          </td>
        </tr>
        <tr class="bg_color2">
          <td>{lang_name}:</td>
          <td><input name="cat_name" size="50" value="{cat_name}" /></td>
        </tr>
        <tr class="bg_color1">
          <td class="top">{lang_descr}:</td>
          <td colspan="2"><textarea name="cat_description" rows="4" cols="50" wrap="virtual">{cat_description}</textarea></td>
        </tr>
        <tr id="50" class="bottom">
          <td><input type="submit" name="save" value="{lang_save}" /></td>
          <td class="right"><input type="submit" name="cancel" value="{lang_cancel}" /></td>
        </tr>
      </table>
    
  </form>
<!-- END form -->
