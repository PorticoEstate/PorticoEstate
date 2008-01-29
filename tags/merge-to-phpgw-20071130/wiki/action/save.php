<?php
// $Id: save.php 15975 2005-05-15 12:55:31Z skwashd $

require(TemplateDir . '/save.php');
require('lib/category.php');
require('parse/save.php');

// Commit an edit to the database.
function action_save()
{
  global $pagestore, $comment, $categories, $archive;
  global $Save, $Preview, $SaveAndContinue, $page, $document, $nextver;
  global $MaxPostLen, $UserName, $SaveMacroEngine, $ErrorPageLocked;

  if(empty($Save))                      // Didn't click the save button.
  {
  	if(!empty($Preview)) {
	    include('action/preview.php');
	    action_preview();
	    return;
	}
  }

  $pagestore->lock();                   // Ensure atomicity.

  $pg = $pagestore->page($page);
  $pg->read();

  if(!isEditable($pg->mutable))         // Edit disallowed.
    { die($ErrorPageLocked); }

  if($pg->exists()                      // Page already exists.
     && $pg->version >= $nextver        // Someone has changed it.
     && $pg->hostname != gethostbyaddr($REMOTE_ADDR)  // Wasn't us.
     && !$archive)                      // Not editing an archive version.
  {
    $pagestore->unlock();
    include('action/conflict.php');
    action_conflict();
    return;
  }

  // Silently trim string to $MaxPostLen chars.

  $document = substr($document, 0, $MaxPostLen);
  $document = str_replace("\r", "", $document);

  $document = $GLOBALS['phpgw']->db->db_addslashes($document);

  $comment = $GLOBALS['phpgw']->db->db_addslashes($comment);

  $pg->text = $document; //$esc_doc;
  $pg->hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  $pg->username = $UserName;
  $pg->comment  = $comment;

  if($pg->exists)
    { $pg->version++; }
  else
    { $pg->version = 1; }
  $pg->write();

  if(!empty($categories))               // Editor asked page to be added to
  {                                     //   a category or categories.
    add_to_category($page, $categories);
  }

  if ((empty($Save)) && (!empty($SaveAndContinue))) {
	  header('Location: ' . editURL($page));
  } else {
	template_save(array('page' => $page,
                      'text' => $document));
  }

  // Process save macros (e.g., to define interwiki entries).
  parseText($document, $SaveMacroEngine, $page);

  $pagestore->unlock();                 // End "transaction".
}
?>
