<!-- BEGIN header -->
<form method="POST" action="{action_url}">
<table border="0" align="center" width="85%">
   <tr class="th">
    <td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
   </tr>
<!-- END header -->
<!-- BEGIN body -->
   <tr class="row_on">
    <td colspan="2">&nbsp;</td>
   </tr>
   <tr class="row_off">
    <td colspan="2">&nbsp;<b>{lang_rental}</b></td>
   </tr>
   <tr class="row_on">
    <td>{lang_area_suffix}:</td>
    <td><input name="newsettings[area_suffix]" value="{value_area_suffix}"></td>
   </tr>
   <tr class="row_off">
    <td>{lang_currency_prefix}:</td>
    <td><input name="newsettings[currency_prefix]" value="{value_currency_prefix}"></td>
   </tr>
   <tr class="row_on">
    <td>{lang_currency_suffix}:</td>
    <td><input name="newsettings[currency_suffix]" value="{value_currency_suffix}"></td>
   </tr>

<!-- END body -->
<!-- BEGIN footer -->
  <tr class="th">
    <td colspan="2">
&nbsp;
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="submit" name="submit" value="{lang_submit}">
      <input type="submit" name="cancel" value="{lang_cancel}">
    </td>
  </tr>
</table>
</form>
<!-- END footer -->
