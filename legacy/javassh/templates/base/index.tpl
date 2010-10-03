		<form method="POST" action="{action}" target="console" 
			onSubmit="window.open('','console','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=620,height=450');return true;">
    <table summary="javassh login" align="center" width="60%" cellspacing="0">
      <tr class="th">
        <td colspan="2" align="center">
          <h3>{lang_javassh_connect}</h3>
        </td>
      </tr>
      <tr class="rowon">
        <td align="right" width="50%">
          <b>{lang_server}: </b>
        </td>
       <td align="left" width="50%">
          <select name="server">
				<!-- BEGIN server -->
            <option value="{server_id}" {selected}>{server_name}</option>
				<!-- END server -->
          </select> 
        </td>
      </tr>
<!--
      <tr class="rowoff">
        <td align="right" width="50%">
          <b>{lang_username}: </b>
        </td>
        <td align="left" width="50%">
          <input type="text" name="user" size="20" value="{user_val}"> 
        </td>
      </tr>
      <tr class="rowon">
        <td align="right" width="50%">
          <b>{lang_password}: </b>
        </td>
        <td align="left" width="50%">
          <input type="password" name="pass" size="20" value="{pass_val}"> 
        </td>
      </tr>
-->
      <tr class="rowoff">
        <td colspan="2" align="center">
          <input type="submit" value="{lang_connect}">&nbsp;&nbsp;
			  <input type="reset" value="{lang_clear}">
        </td>
      </tr>
    </table>
		</form>
<p>&nbsp;</p>