<?php
// $Id$

// The save template is passed an associative array with the following
// elements:
//
//   page      => A string containing the name of the wiki page being saved.
//   text      => A string containing the wiki markup for the given page.

function template_save($args)
{
// You might use this to put up some sort of "thank-you" page like Ward
//   does in WikiWiki, or to display a list of words that fail spell-check.
// For now, we simply redirect to the view action for this page.

  header('Location: ' . viewURL($args['page']));
}
?>
