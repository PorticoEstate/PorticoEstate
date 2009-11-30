<?php
// $Id$

// Compute the difference between two sets of text.
function diff_compute($text1, $text2)
{
  global $TempDir, $DiffCmd, $ErrorCreatingTemp, $ErrorWritingTemp;

  //$num = rand();                // Comment if running on Windows.
  // $num = rand();                     // Uncomment if running on Windows.
  $num = strncmp(PHP_OS,'WIN',3) ? posix_getpid() : rand();

  $temp1 = $TempDir . '/wiki_' . $num . '_1.txt';
  $temp2 = $TempDir . '/wiki_' . $num . '_2.txt';

  if(!($h1 = fopen($temp1, 'w')) || !($h2 = fopen($temp2, 'w')))
    { die($ErrorCreatingTemp); }

  if(fwrite($h1, $text1) < 0 || fwrite($h2, $text2) < 0)
    { die($ErrorWritingTemp); }

  fclose($h1);
  fclose($h2);

  $diff = `$DiffCmd $temp1 $temp2`;

  unlink($temp1);
  unlink($temp2);

  return $diff;
}

// Parse diff output into nice HTML.
function diff_parse($text)
{
  global $DiffEngine;

  return parseText($text, $DiffEngine, '');
}

?>
