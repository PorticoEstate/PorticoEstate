<br>
<center><h2>{title}</h2></center>
<p>
<form method="POST" action="{action_url}">
  <table border="0" cellpadding="0" cellspacing="0" width="95%" align="center">
    <tr>
      <td width="33%" colspan=1 align="center">{gdlib_label}:
        <input type="checkbox" {gdlib_checked} name="gdlib_enabled" value="1">
      </td>
      <td width="33%" colspan=1 align="right">{imagetype_label}:</td>
      <td width="33%" colspan=1 align="left">
        <select name="gdtype">
          {image_options}
        </select>
      </td>
    </tr>
    <tr>
      <td width="33%" colspan=1 align="center">{imgsrc_label}:
        <select name="image_source">
          {imgsrc_options}
        </select>
      </td>
      <td width="33%" colspan=1 align="center">{remote_label}:
        <input type="checkbox" {remote_checked} name="remote_enabled" value="1">

      </td>
      <td width="33%" colspan=1 align="center">{filesize_label}:
        <input type="text" name="filesize" value="{filesize}" size=7 maxlength=7
>
      </td>
    </tr>
    <tr>
      <td colspan=1 align=right>
        <input type="submit" name="submit" value="{action_label}">
      </td>
      <td colspan=1>&nbsp</td>
      <td colspan=1 align=left>
        <input type="reset" name="reset" value="{reset_label}">
      </td>
    </tr>
  </table>
</form>
<center>
  <form method="POST" action="{done_url}">
    <input type="submit" name="done" value="{done_label}">
  </form>
</center>
