<?php
// $Id$

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

