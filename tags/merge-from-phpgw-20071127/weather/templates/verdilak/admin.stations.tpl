<br>
<center><h2>{title}</h2></center>
<p>
{matchs}
{station_table}
<form method=POST action="{action_url">
  <table border="0" cellpadding="0" cellspacing="0" width=85% align=center>
    <tr bgcolor=D3DCE3>
      <td align=left width=15%>
        <input type="submit" name="submit" value="Add">
      </td>
      <td width=10%>Station*:</td>
      <td width=10%>
        <input type="text" name="metar_station" value="" size=4 maxlength=4>
      </td>
      <td width=10%>City*:</td>
      <td colspan=2>
        <input type="text" name="metar_city" value="" size=40 maxlength=128>
      </td>
    </tr>
    <tr bgcolor=D3DCE3>
      <td>&nbsp;</td>
      <td width=10%>Forecast* Zone*:</td>
      <td>
        <input type="text" name="metar_forecast" value="" size=6 maxlength=6>
      </td>
      <td width=10%>Region*:</td>
      <td colspan=3 align="left">
        <select name="region_id" size=1>
          {region_options}
        </select>
      </td>
    </tr>
  </table>
</form>
<center>
  <form action="{done_url}">
    <input type="submit" name="done" value="{done_label}">
  </form>
</center>
