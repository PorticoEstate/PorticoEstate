    <form method="POST" action="{action}">
		  <input type="hidden" name="id" value="{server_id}">
      <table cellspacing="0" width="50%">
        <tr class="th">
          <td colspan="2">
            {lang_edit_server}
          </td>
        </tr>
        <tr class="rowon">
          <td width="50%" align="right">
            <b>{lang_host}:</b>
          </td>
          <td width="50%" align="left">
            <input type="text" name="host" value="{host}">
          </td>
        </tr>
        <tr class="rowoff">
          <td width="50%" align="right">
            <b>{lang_port}:</b>
          </td>
          <td width="50%" align="left">
            <input type="text" name="port" value="{port}">
          </td>
        </tr>
        <tr class="rowon">
          <td width="50%" align="right">
            <b>{lang_protocol}:</b>
          </td>
          <td width="50%" align="left">
            <select name="protocol">
              <option {selected_ssh}>ssh</option>
              <option {selected_telnet}>telnet</option>
            </select> 
          </td>
        </tr>
        <tr class="rowoff">
          <td colspan="2" align="center">
            <input type="submit" name="save" value="{lang_save}">&nbsp;&nbsp;
				<input type="button" name="cancel" value="{lang_done}" 
					onClick="window.location='{url_done}';"> 
          </td>
        </tr>
      </table>
    </form>
		<br />
