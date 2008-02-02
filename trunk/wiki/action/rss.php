<?php
// $Id$

require(TemplateDir . '/rss.php');
require('parse/html.php');
require('parse/macros.php');

function action_rss()
{
  global $pagestore, $min, $days;

  $itemseq  = '';
  $itemdesc = '';

  if($min == 0)  { $min = 10; }
  if($days == 0) { $days = 2; }

  $pages = $pagestore->allpages();

  usort($pages, 'catSort');
  $now = time();

  for($i = 0; $i < count($pages); $i++)
  {
    $editTime = mktime(substr($pages[$i][0], 8, 2),
                       substr($pages[$i][0], 10, 2),
                       substr($pages[$i][0], 12, 2),
                       substr($pages[$i][0], 4, 2),
                       substr($pages[$i][0], 6, 2),
                       substr($pages[$i][0], 0, 4));
    if($days >= 0 && ($now - $editTime) > $days * 24 * 60 * 60 && $i >= $min)
      { break; }

    $itemseq = $itemseq .
               '                <rdf:li rdf:resource="' .
               viewURL($pages[$i][1], $pages[$i][7]) . '" />' . "\n";
    $itemdesc = $itemdesc .
                '    <item rdf:about="' . viewURL($pages[$i][1], $pages[$i][7]) . '">' . "\n" .
                '        <title>' . $pages[$i][1] . '</title>' . "\n" .
                '        <link>' . viewURL($pages[$i][1]) . '</link>' . "\n" .
                '        <description>' . $pages[$i][5] . '</description>' . "\n" .
                '        <dc:date>' . html_gmtime($pages[$i][0]) . '</dc:date>' . "\n" .
                '        <dc:contributor>' . "\n" .
                '            <rdf:Description wiki:host="' . $pages[$i][2] . '"'. ($pages[$i][3] == '' ? '' : (' link="' . viewURL($pages[$i][3]) . '"')) . '>' . "\n" .
                ($pages[$i][3] == '' ? '' : ('                <rdf:value>' . $pages[$i][3] . '</rdf:value>' . "\n")) .
                '            </rdf:Description>' . "\n" .
                '        </dc:contributor>' . "\n" .
                '        <wiki:status>updated</wiki:status>' . "\n" .
                '        <wiki:importance>major</wiki:importance>' . "\n" .
                '        <wiki:diff>' . historyURL($pages[$i][1]) . '</wiki:diff>' . "\n" .
                '        <wiki:version>' . $pages[$i][7] . '</wiki:version>' . "\n" .
                '        <wiki:history>' . historyURL($pages[$i][1]) . '</wiki:history>' . "\n" .
                '    </item>' . "\n";
  }

  template_rss(array('itemseq'  => $itemseq,
                     'itemdesc' => $itemdesc));
}

?>
