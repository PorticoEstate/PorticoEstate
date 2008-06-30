<?php
// $Id$

// Master parser for 'Tavi.
function parseText($text, $parsers, $object_name)
{
  global $Entity, $ParseObject;

  $old_parse_object = $ParseObject;
  $ParseObject = $object_name;          // So parsers know what they're parsing.

  $count  = count($parsers);
  $result = '';

  // Run each parse element in turn on each line of text.

  foreach(explode("\n", $text) as $line)
  {
    $line = $line . "\n";
    for($i = 0; $i < $count; $i++)
      { $line = $parsers[$i]($line); }

    $result = $result . $line;
  }

  // Some stateful parsers need to perform final processing.

  $line = '';
  for($i = 0; $i < $count; $i++)
    { $line = $parsers[$i]($line); }

  $ParseObject = $old_parse_object;

  return $result . $line;
}

?>
