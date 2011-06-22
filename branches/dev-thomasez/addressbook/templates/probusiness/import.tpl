<!-- BEGIN import -->
  <table class="basic">
    <tr><td class="header"><b><div class="center">{import_text}</b></td></tr>
    <tr>
      <td>
        <table class="basic">
          <tr>
            <td>
              <form method="post" action="{action_url}" enctype="multipart/form-data">
                  <ol>
                    <li class="bg_color1">In Outlook, wähle den Kontakt Ordner aus <b>Importieren und Exportieren...</b> aus dem <b>Datei</b>
                      Menu und exportiere die Kontakte in eine Kommagetrennte Text (CSV) Datei.<br />
                      Oder, in Palm Desktop 4.0 oder höher, besuchen sie Ihr Adressbuch aus und wählen <b>Export</b> von dem <b>Datei</b> Menu aus.
                      Die Datei wird in dem VCard Format exportiert.
                    </li>
                    <li class="bg_color1">Geben Sie den Pfad für die zu exportierende Datei hier ein:
                      <input name="tsvfile" size="48" type="file" value="{tsvfilename}" /><br />
                    </li>
                    <li class="bg_color1">Wähle die Art der Konvertierung:
                     <select name="conv_type">
                        <option value="none">&lt;none&gt;</option>
                        {conv}
                      </select>
                    </li>
                    <li class="bg_color1">{lang_cat}:{cat_link}</li>
                    <li class="bg_color1"><input name="private" type="checkbox" value="private" ckecked="checked" /> Die Einträge als Privat markieren</li>
                    <li class="bg_color1"><input name="download" type="checkbox" value="{debug}" ckecked="checked" /> Fehlerausgabe in Browser</li>
                  </ol>
                <input name="convert" type="submit" value="{download}" />
                <input type="hidden" name="sort" value="{sort}" />
                <input type="hidden" name="order" value="{order}" />
                <input type="hidden" name="filter" value="{filter}" />
                <input type="hidden" name="query" value="{query}" />
                <input type="hidden" name="start" value="{start}" />
              </form>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>
        <div class="left">
          <form action="{cancel_url}" method="post">
            <input type="hidden" name="sort" value="{sort}" />
            <input type="hidden" name="order" value="{order}" />
            <input type="hidden" name="filter" value="{filter}" />
            <input type="hidden" name="query" value="{query}" />
            <input type="hidden" name="start" value="{start}" />
            <input type="submit" name="Cancel" value="{lang_cancel}" />
          </form>
        </div>
      </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
<!-- END import -->
