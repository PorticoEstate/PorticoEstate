<!-- BEGIN import -->
    <table class="basic">
      <tr class="header">
      	<td class="center"><b>{export_text}</b></td>
      </tr>
      <tr>
        <td>
          <form  method="post" action="{action_url}" enctype="multipart/form-data">
            <ol>
              <li class="bg_color2">Select the type of conversion:
                <select name="conv_type">
                  <option value="none">&lt;none&gt;</option>
                  {conv}
                </select>
                <br /><br />
              </li>
              <li class="bg_color2">{filename}:<input name="tsvfilename" value="export.txt" /></li>
              <li class="bg_color2">{lang_cat}:{cat_link}</li>
              <li class="bg_color2"><input name="download" type="checkbox" checked="checked" />Download export file (Uncheck to debug output in browser)</li>
              <P class="header">OpenOffice export only uses the following options:</P>
        			<li class="bg_color2"><INPUT NAME="both_types" TYPE="checkbox" checked>OpenOffice.org only - Include fields from both types of contacts? (Uncheck to only include fields from previous screen)</li>
        			<li class="bg_color2"><INPUT NAME="sub_cats" TYPE="checkbox" checked>OpenOffice.org only - Include field for category names (only sub-categories if category is selected)</li>
            </ol>
						<P></P>
            <input name="convert" type="submit" value="{download}" />
            <input type="hidden" name="sort" value="{sort}" />
            <input type="hidden" name="order" value="{order}" />
            <input type="hidden" name="filter" value="{filter}" />
            <input type="hidden" name="query" value="{query}" />
            <input type="hidden" name="start" value="{start}" />
          </form>
        </td>
      </tr>
      <tr>
        <td>
          <form action="{cancel_url}" method="post">
            <input type="hidden" name="sort" value="{sort}" />
            <input type="hidden" name="order" value="{order}" />
            <input type="hidden" name="filter" value="{filter}" />
            <input type="hidden" name="query" value="{query}" />
            <input type="hidden" name="start" value="{start}" />
            <input type="submit" name="Cancel" value="{lang_cancel}" />
          </form>
        </td>
      </tr>
    </table>
  
<!-- END import -->

