<br>
<center><h2>{title}</h2></center>
<p>
<form method="POST" action="{action_url}">
  <input type="hidden" name="weather_id" value="{weather_id}">
  <table border="0" cellpadding="0" cellspacing="0" width="85%" align="center">
    <tr colspan=4 align="center">
      <td colspan=4 align="center">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <th colspan=3 bgcolor="{th_bg}" fgcolor="{th_text}" align="left">
            <td align="left"><b>{layout_label}</b></td>
            <td align="right">{template_label}:</td>
	    <td align="left">
              <select name="template_id">
                {template_options}
              </select>
            </td>
          </th>
          {template_images}
        </table>
      </td>
    </tr>
    <tr align="center">
      <td colspan=4 align="center">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <th colspan=4 bgcolor="{th_bg}" fgcolor="{th_text}" align="left">
            <td colspan=4 align="left"><b>{sticker_label}</b></td>
          </th>
          <tr>
            <td colspan=2 valign="center" align="center">{wunder_label}:
              <input type="checkbox" name="wunderground_enabled"
               {wunder_checked} value="1">
            </td>
            <td colspan=2 valign="center" align="center">{sticker_src_label}:
              <select name="sticker_source" size=1>
                {sticker_options}
              </select>
            </td>
          </tr>
          <tr>
            <td colspan=2 valign="center" align="center">{tenable_label}:
              <input type="checkbox" name="title_enabled"
               {title_checked} value="1">
            </td>
            <td colspan=2 valign="center" align="center">{fpenable_label}:
              <input type="checkbox" name="frontpage_enabled"
               {fpage_checked} value="1">
            </td>
          </tr>
          <tr>
            <td colspan=2 valign="center" align="center">{tsize_label}:
              <select name="title_size" size=1>
                {tsize_options}
              </select>
            </td>
            <td colspan=2 valign="center" align="center">{fpsize_label}:
              <select name="frontpage_size" size=1>
                {fpsize_options}
              </select>
            </td>
          </tr>
          <tr>
            <td colspan=2 align="center" valign="top">
              {tmetar_label}:
            </td>
            <td colspan=2 align="center" valign="top">
              {fpmetar_label}:
            </td>
          </tr>
          <tr>
            <td colspan=2 align="center" valign="top">
              <select name="title_metar" size=7>
                {tmetar_options}
              </select>
            </td>
            <td colspan=2 align="center" valign="top">
              <select name="frontpage_metar" size=7>
                {fpmetar_options}
              </select>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr align="center">
      <td colspan=4 align="center">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <th colspan=2 bgcolor="{th_bg}" fgcolor="{th_text}" align="left">
            <td colspan=2 align="left"><b>{links_label}</b></td>
          </th>
          <tr>
            <td colspan=1 valign="center" align="center">{lenable_label}:
              <input type="checkbox" name="links_enabled"
               {links_checked} value="1">
            </td>
            <td colspan=1 valign="center" align="center">{lavail_label}:<br>
              <select name="links[]" multiple size=3>
                {link_options}
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <table>
                <tr align="center">
                  <td align=right>{city_label}:</td>
                  <td>
                    <input type="text" name="city"
                     value="{city}" maxlength=50>
                  </td>
                </tr>
                <tr align="center">
                  <td align=right>{country_label}:</td>
                  <td>
                    <input type="text" name="country"
                     value="{country}" maxlength=50>
                  </td>
                </tr>
              </table>
            </td>
            <td>
              <table>
                <tr>
                  <td align="right">{gstation_label}:</td>
                  <td>
                    <input type="text" name="gstation"
                     value="{gstation}" maxlength=50>
                  </td>
                </tr>
                <tr>
                  <td valign="center" align="right">{state_label}:</td>
                  <td align="left">
                    <select name="stateid" size=3>
                      {state_options}
                    </select>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr align="center">
      <td colspan=4 align="center">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <th colspan=4 bgcolor="{th_bg}" fgcolor="{th_text}" align="left">
            <td colspan=4 align="left"><b>{remote_label}</b></td>
          </th>
          <tr>
            <td colspan=2 valign="center" align="center">{fenable_label}:
              <input type="checkbox" name="forecasts_enabled"
               {forecast_checked} value="1">
            </td>
            <td colspan=2 valign="center" align="center">{oenable_label}:
              <input type="checkbox" name="observations_enabled"
               {observation_checked} value="1">
            </td>
          </tr>
          <tr>
            <td colspan=4 align="center">{mavail_label}:</td>
          </tr>
          <tr>
            <td colspan=4 align="center">
              <select name="metars[]" multiple size=12>
                {metar_options}
              </select>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td colspan=2 align="left">
        <input type="submit" name="submit" value="{action_label}">
      </td>
      <td colspan=2 align="right">
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



