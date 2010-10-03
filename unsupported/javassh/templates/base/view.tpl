    <form method="POST" action="{url_done}">
		  <input type="hidden" name="id" value="{server_id}">
      <table cellspacing="0" width="50%">
        <tr class="th">
          <td colspan="2">
            {lang_view_server}
          </td>
        </tr>
        <tr class="rowon">
          <td width="50%" align="right">
            <b>{lang_host}:</b>
          </td>
          <td width="50%" align="left">{host}</td>
        </tr>
        <tr class="rowoff">
          <td width="50%" align="right">
            <b>{lang_port}:</b>
          </td>
          <td width="50%" align="left">{port}</td>
        </tr>
        <tr class="rowon">
          <td width="50%" align="right">
            <b>{lang_protocol}:</b>
          </td>
          <td width="50%" align="left">{protocol}</td>
        </tr>
        <tr class="rowoff">
          <td colspan="2" align="center">
            <input type="submit" name="cancel" value="{lang_done}">
          </td>
        </tr>
      </table>
    </form>
		<br />
