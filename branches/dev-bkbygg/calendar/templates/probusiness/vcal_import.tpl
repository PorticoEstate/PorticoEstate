<!-- vcardin form -->
 {vcal_header}
  <form ENCTYPE="multipart/form-data" method="POST" action="{action_url}">
    <table>
      <tr><td class="header" colspan="2">&nbsp;</td></tr>
      <tr class="bg_view">
        <td>{ical_lang}: <input type="file" name="uploadedfile" /></td>
        <td><input type="submit" name="action" value="{load_vcal}" /></td>
      </tr>
    </table>
  </form>