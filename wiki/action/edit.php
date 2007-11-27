<?php
// $Id: edit.php 12052 2003-03-18 22:57:53Z ralfbecker $

require('parse/macros.php');
require('parse/html.php');
require(TemplateDir . '/edit.php');
require('parse/main.php');
require(TemplateDir . '/preview.php');

// Edit a page (possibly an archive version).
function action_edit()
{
  global $page, $pagestore, $ParseEngine, $version, $ErrorPageLocked, $EditWithPreview,$anonymous;

  $pg = $pagestore->page($page);
  $pg->read();

  if(!isEditable($pg->mutable))
    { die($ErrorPageLocked); }

  $archive = 0;
  if($version != '')
  {
    $pg->version = $version;
    $pg->read();
    $archive = 1;
  }
  if ($EditWithPreview)
  {
    template_preview(array('page'      => $page,
                           'text'      => $pg->text,
                           'html'      => parseText($pg->text,$ParseEngine,$page),
                           'timestamp' => $pg->time,
                           'nextver'   => $pg->version + 1,
                           'archive'   => $archive));
  }
  else
  {
    template_edit(array('page'      => $page,
                        'text'      => $pg->text,
                        'timestamp' => $pg->time,
                        'nextver'   => $pg->version + 1,
                        'archive'   => $archive));
  }
}
?>
