<br>
<center><h2>{title}</h2></center>
<p>
<form method="POST" action="{action_url}">
  <input type="hidden" name="comic_id" value="{comic_id}">
  <table border="0" cellpadding="0" cellspacing="0" width="85%" align="center">
    <tr align=center>
      <td colspan=2 align=center>
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr bgcolor="{th_bg}" fgcolor="{th_text}" align="left">
            <td colspan=1 align=left><b>{layout_label}:</b></td>
            <td colspan=1 align=right>{template_label}:
              <select name="comic_template">
                {template_options}
              </select>
            </td>
          </tr>
	  {template_images}
        </table>
      </td>
    </tr>
    <tr bgcolor="{th_bg}" fgcolor="{th_text}" align="left">
      <td colspan=1 align="left">{option_label}:</td>
      <td colspan=1>&nbsp;</td>
    </tr>
    <tr>
      <td width="50%" colspan=1 align="center">{perpage_label}:
        <select name="perpage">
          {perpage_options}
        </select>
      </td>
      <td width="50%" colspan=1 align="center">{scale_label}:
        <input type="checkbox" {scale_checked} name="scale_enabled" value="1">
      </td>
    </tr>
    <tr>
      <td width="50%" colspan=1 align="center">{frontpage_label}:
        <select name="frontpage">
          {frontpage_options}
        </select>
      </td>
      <td width="50%" colspan=1 align="center">{fpscale_label}:
        <input type="checkbox" {fpscale_checked} name="fpscale_enabled" value="1">
      </td>
    </tr>
    <tr>
      <td width="50%" colspan=1 align="center">{censor_label}:
        <select name="censor_level">
          {censor_options}
        </select>
      </td>
    </tr>
    <tr bgcolor="{th_bg}" fgcolor="{th_text}" align="left">
      <td colspan=1 align="left">{comic_label}:</td>
      <td colspan=1>&nbsp;</td>
    </tr>
    <tr>
      <td colspan=2 align="left">
        <select name="data_ids[]" multiple size={comic_size}>
          {comic_options}
        </select>
      </td>
    </tr>
    <tr>
      <td colspan=1 align=left>
        <input type="submit" name="submit" value="{action_label}">
      </td>
      <td colspan=1 align=right>
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
