<p>
<form method="POST" action="{action_url}">
  <input type="hidden" name="data_id" value={data_id}>
  <table border="0" cellpadding="0" cellspacing="0" width="85%" align="center">
    <tr bgcolor={bg_color}>
      <td align="left" width="15%">{comic_label}:</td>
      <td colspan=2>
        <input type="text" name="comic_name" value="{comic_name}"
         size=45 maxlength=50>
      </td>
    </tr>
    <tr bgcolor={bg_color}>
      <td colspan=1 align=left>
        <input type="submit" name="submit" value="{action_label}">
      </td>
      <td colspan=1>&nbsp</td>
      <td colspan=1 align=right>
        <input type="reset" name="reset" value="{reset_label}">
      </td>
    </tr>
  </table>
</form>
