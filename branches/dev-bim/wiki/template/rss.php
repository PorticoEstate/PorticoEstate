<?php
// $Id$

// The RSS template is passed an associative array with the following
// elements:
//
//   itemseq   => A string containing the rdf:li elements for the syndication.
//   itemdesc  => A string containing the item elements for the syndication.

function template_rss($args)
{
  global $ScriptBase, $WikiName, $MetaDescription, $InterWikiPrefix;
  global $Charset;

  header('Content-type: text/plain');
?>
<?php print '<?xml '; ?>version="1.0" encoding="<?php print $Charset; ?>"?>
<rdf:RDF
     xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns="http://purl.org/rss/1.0/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:wiki="http://purl.org/rss/1.0/modules/wiki/"
>
    <!--
        Add a "days=nnn" URL parameter to get nnn days of information
        (the default is 2).  Use days=-1 to show entire history.
        Add a "min=nnn" URL parameter to force a minimum of nnn entries
        in the output (the default is 10).
    -->
    <channel rdf:about="<?php print $ScriptBase; ?>">
        <title><?php print $WikiName; ?></title>
        <link><?php print $ScriptBase; ?></link>
        <description><?php print $MetaDescription; ?></description>
        <wiki:interwiki>
            <rdf:Description link="<?php print $ScriptBase . '?'; ?>">
                <rdf:value><?php print $InterWikiPrefix; ?></rdf:value>
            </rdf:Description>
        </wiki:interwiki>
        <items>
            <rdf:Seq>
<?php
  print $args['itemseq'];
?>
            </rdf:Seq>
        </items>
    </channel>

<?php
  print $args['itemdesc'];
?>

</rdf:RDF>
<?php
}
?>
