<br />
<form {enctype} action="{action_url}" method="post">
    <table align="center">
<!-- BEGIN filename -->
      <tr><td colspan="2" class="header">&nbsp;</td></tr>
      <tr>
        <td class="bg_color1">{lang_csvfile}</td>
        <td class="bg_color2"><input name="csvfile" size="30" type="file" value="{csvfile}" /></td>
      </tr>
      <tr>
        <td class="bg_color1">{lang_fieldsep}</td>
        <td class="bg_color2"><input name="fieldsep" size=1 value="{fieldsep}" /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="convert" type="submit" value="{submit}" /></td>
      </tr>
<!-- END filename -->

<!-- BEGIN fheader -->
      <tr>
        <td><b>{lang_csv_fieldname}</b></td>
        <td><b>{lang_addr_fieldname}</b></td>
        <td><b>{lang_translation}</b></td>
      </tr>
<!-- END fheader -->

<!-- BEGIN fields -->
      <tr>
        <td>{csv_field}</td>
        <td>
          
            <select name="addr_fields[{csv_idx}]">
              {addr_fields}
            </select>
          
        </td>
        <td><input name="trans[{csv_idx}]" size=60 value="{trans}" /></td>
      </tr>
<!-- END fields -->

<!-- BEGIN ffooter -->
      <tr>
        <td rowspan="2" valign="middle"><br /><input name="convert" type="submit" value="{submit}" /></td>
        <td colspan="2"><br />
          {lang_start} <input name="start" type="text" size="5" value="{start}" /> &nbsp; &nbsp;
          {lang_max} <input name="max" type="text" size="3" value="{max}" />
        </td>
      </tr>
      <tr><td colspan="3"><input name="debug" type="checkbox" value="1" checked /> {lang_debug}</td></tr>
      <tr><td colspan=3>&nbsp;<p>{help_on_trans}</p></td></tr>
<!-- END ffooter -->

<!-- BEGIN imported -->
      <tr>
        <td colspan="2" align="center">
          {log}
          <p>{anz_imported}</p>
        </td>
      </tr>
<!-- END imported -->
    </table>
  {hiddenvars}
</form>

