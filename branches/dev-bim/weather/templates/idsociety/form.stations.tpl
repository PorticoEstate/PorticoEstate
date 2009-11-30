<p>
<form method="POST" action="{action_url}">
  <input type="hidden" name="metar_id" value={metar_id}>
  <table border="0" cellpadding="0" cellspacing="0" width="85%" align="center">
    <tr bgcolor={bg_color}>
      <td width="10%">{station_label}:</td>
      <td>
        <input type="text" name="metar_station" value="{metar_station}"
         size=4 maxlength=4>
      </td>
      <td width="10%">{city_label}:</td>
      <td colspan=2>
        <input type="text" name="metar_city" value="{metar_city}"
         size=35 maxlength=128>
      </td>
    </tr>
    <tr bgcolor={bg_color}>
      <td width="10%">{forecast_label}:</td>
      <td>
        <input type="text" name="metar_forecast" value="{metar_forecast}"
         size=6 maxlength=6>
      </td>
      <td width="10%">{region_label}:</td>
      <td colspan=2 align="left">
        <select name="region_id" size=1>
          {region_options}
        </select>
      </td>
    </tr>
    <tr bgcolor={bg_color}>
      <td colspan=1 align=left>
        <input type="submit" name="submit" value="{action_label}">
      </td>
      <td colspan=3>&nbsp</td>
      <td colspan=1 align=right>
        <input type="reset" name="reset" value="{reset_label}">
      </td>
    </tr>
  </table>
</form>
