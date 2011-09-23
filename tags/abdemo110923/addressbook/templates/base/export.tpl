
<!-- BEGIN import -->
<CENTER>
  <TABLE WIDTH=90%>
    <TR BGCOLOR="{navbar_bg}">
      <TD><B><FONT SIZE=+2 COLOR="{navbar_text}"><CENTER>{export_text}</CENTER></FONT></B>
      </TD>
    </TR>
    <TR>
      <TD>
        <FORM ENCTYPE="multipart/form-data" action="{action_url}" method="POST">
        <OL>
        <LI>Select the type of conversion:
        <SELECT NAME="conv_type">
        <OPTION VALUE="none">&lt;none&gt;</OPTION>
{conv}        </SELECT><P></LI>
        <LI>{filename}:<INPUT NAME="tsvfilename" VALUE="export.txt"></LI>
        <LI>{lang_cat}:{cat_link}</LI>
        <LI><INPUT NAME="download" TYPE="checkbox" checked>Download export file (Uncheck to debug output in browser)</LI>
<P>OpenOffice export only uses the following options:</P>
        <LI><INPUT NAME="both_types" TYPE="checkbox" checked>OpenOffice.org only - Include fields from both types of contacts? (Uncheck to only include fields from previous screen)</LI>
        <LI><INPUT NAME="sub_cats" TYPE="checkbox" checked>OpenOffice.org only - Include field for category names (only sub-categories if category is selected)</LI>
        </OL>
	<P></P>
        <INPUT NAME="convert" TYPE="submit" VALUE="{download}">
        <input type="hidden" name="sort" value="{sort}">
        <input type="hidden" name="order" value="{order}">
        <input type="hidden" name="filter" value="{filter}">
        <input type="hidden" name="query" value="{query}">
        <input type="hidden" name="start" value="{start}">
        </FORM>
      </TD>
    </TR>
    <TR>
      <TD>
        <FORM action="{cancel_url}" method="post">
        <input type="hidden" name="sort" value="{sort}">
        <input type="hidden" name="order" value="{order}">
        <input type="hidden" name="filter" value="{filter}">
        <input type="hidden" name="query" value="{query}">
        <input type="hidden" name="start" value="{start}">
        <INPUT type="submit" name="Cancel" value="{lang_cancel}">
        </FORM>
      </TD>
    </TR>
  </TABLE>
</CENTER>
<!-- END import -->
