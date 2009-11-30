<?php
// $Id$

require(TemplateDir . '/conflict.php');

// Conflict editor.  Someone accidentally almost overwrote something someone
//   else just saved.
function action_conflict()
{
  global $pagestore, $page, $document, $ParseEngine;

  $pg = $pagestore->page($page);
  $pg->read();

  template_conflict(array('page'      => $page,
                          'text'      => $pg->text,
                          'html'      => parseText($pg->text,
                                                   $ParseEngine, $page),
                          'usertext'  => $document,
                          'timestamp' => $pg->time,
                          'nextver'   => $pg->version + 1));
}
?>
