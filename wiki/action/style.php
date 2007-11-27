<?php
// $Id: style.php,v 1.3 2003/03/03 14:16:23 ralfbecker Exp $

// This function emits the current template's stylesheet.

function action_style()
{
  header("Content-type: text/css");

  ob_start();

  require(TemplateDir . '/wiki.css');

  $size = ob_get_length();
  header("Content-Length: $size");
  ob_end_flush();
}

