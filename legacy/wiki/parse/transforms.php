<?php
// $Id$

//Portions of this script written by Dave Hall - skwashd at phpgroupware.org
//Portions of this file are Copyright (c) 2004 The Free Software Foundation Inc

// The main parser components.  Each of these takes a line of text and scans it
//   for particular wiki markup.  It converts markup elements to
//   $FlgChr . x . $FlgChr, where x is an index into the global array $Entity,
//   which contains descriptions of each markup entity.  Later, these will be
//   converted back into HTML  (or, in the future, perhaps some other
//   representation such as XML).

function parse_noop($text)
{
  return $text;
}

// The following function "corrects" for PHP's odd preg_replace behavior.
// Back-references have backslashes inserted before certain quotes
// (specifically, whichever quote was used around the backreference); this
// function removes remove those backslashes.

function q1($text)
  { return str_replace('\\"', '"', $text); }

function validate_page($page)
{
  global $FlgChr;

  $p = parse_wikiname($page, 1);
  if(preg_match('/^' . $FlgChr . '\\d+' . $FlgChr . '$/', $p))
    { return 1; }
  $p = parse_freelink('((' . $page . '))', 1);
  if(preg_match('/^' . $FlgChr . '\\d+' . $FlgChr . '$/', $p))
    { return 2; }
  return 0;
}

function parse_elem_flag($text)
{
  global $FlgChr;

  // Hide element flags (0xFF) from view.
  return preg_replace('/' . $FlgChr . '/e', "new_entity(array('raw', '$FlgChr'))", $text, -1);
}

function new_entity($array)
{
  global $Entity, $FlgChr;

  $Entity[count($Entity)] = $array;
  return $FlgChr . (count($Entity) - 1) . $FlgChr;
}

function parse_wikiname($text, $validate = 0)
{
  global $LinkPtn, $EnableWikiLinks;

  if(!$EnableWikiLinks) { return $text; }

  if($validate)
    { $ptn = "/(^|[^A-Za-z])($LinkPtn)(())(\"\")?/e"; }
  else
    { $ptn = "/(^|[^A-Za-z])(!?$LinkPtn)((\#[A-Za-z]([-A-Za-z0-9_:.]*[-A-Za-z0-9_])?)?)(\"\")?/e"; }

  return preg_replace($ptn,
                      "q1('\\1').wikiname_token(q1('\\2'),'\\4')",
                      $text, -1);
}

function wikiname_token($name, $anchor)
{
  if($name[0] == '!')                   // No-link escape sequence.
    { return substr($name, 1); }        // Trim leading '!'.
  return new_entity(array('ref', $name, $name, '', $anchor, $anchor));
}

function parse_freelink($text, $validate = 0)
{
  global $EnableFreeLinks;

  if(!$EnableFreeLinks) { return $text; }

  if($validate)
  {
    $ptn = "/\\(\\(([-A-Za-z0-9 _+\\/.,;:!?'\"\\[\\]\\{\\}&\xc0-\xff]+)()()\\)\\)/e";
  }
  else
  {
    $ptn = "/\\(\\(([-A-Za-z0-9 _+\\/.,;:!?'\"\\[\\]\\{\\}&\xc0-\xff]+)(\|[-A-Za-z0-9 _+\\/.,;:!?'\"\\[\\]\\{\\}&\xc0-\xff]+)?(\#[A-Za-z][-A-Za-z0-9_:.]*)?()\\)\\)/e";
  }

  return preg_replace($ptn,
                      "freelink_token(q1('\\1'), q1('\\2'), '\\3', '')",
                      $text, -1);
}

function freelink_token($link, $appearance, $anchor, $anchor_appearance)
{
  if($appearance == '')
    { $appearance = $link; }
  else
    { $appearance = substr($appearance, 1); }   // Trim leading '|'.
  return new_entity(array('ref', $link, $appearance, '',
                          $anchor, $anchor_appearance));
}

function parse_interwiki($text)
{
  global $InterwikiPtn;

  return preg_replace("/(^|[^A-Za-z])($InterwikiPtn)(\$|[^\\/=&~A-Za-z0-9])/e",
                      "q1('\\1').interwiki_token(q1('\\3'),q1('\\4')).q1('\\6')",
                      $text, -1);
}

function interwiki_token($prefix, $ref)
{
  global $pagestore;

  if(($url = $pagestore->interwiki($prefix)) != '')
  {
    return new_entity(array('interwiki', $url . $ref, $prefix . ':' . $ref));
  }

  return $prefix . ':' . $ref;
}

function parse_hyperlink_ref($text)
{
  global $UrlPtn;

  return preg_replace("/\\[($UrlPtn)]/Ue",
                      "url_token(q1('\\1'), '')", $text, -1);
}

function parse_hyperlink_description($text)
{
  global $UrlPtn;

  return preg_replace("/\\[($UrlPtn) ([^]]+)]/e",
                      "url_token(q1('\\1'), q1('[\\4]'))", $text, -1);
}

function parse_hyperlink($text)
{
  global $UrlPtn;

  return preg_replace("/(^|[^A-Za-z])($UrlPtn)(\$|[^\\/?=&~A-Za-z0-9])/e",
                      "q1('\\1').url_token(q1('\\2'), q1('\\2')).q1('\\5')", $text, -1);
}

function url_token($value, $display)
{
  static $count = 1;

  if($display == '')
    { $display = '[' . $count++ . ']'; }

  return new_entity(array('url', $value, $display));
}

function parse_macros($text)
{
  return preg_replace('/\\[\\[([^] ]+( [^]]+)?)]]/e',
                      "macro_token(q1('\\1'), q1('\\3'))", $text, -1);
}

function macro_token($macro, $trail)
{
  global $ViewMacroEngine;

  $cmd  = strtok($macro, ' ');
  $args = strtok('');

  if($ViewMacroEngine[$cmd] != '')
    { return new_entity(array('raw', $ViewMacroEngine[$cmd]($args))); }
  else
    { return '[[' . $macro . ']]' . ($trail == "\n" ? $trail : ''); }
}

function parse_transclude($text)
{
  $text2 = preg_replace('/%%([^%]+)%%/e',
                        "transclude_token(q1('\\1'))", $text, -1);
  if($text2 != $text)
    { $text2 = str_replace("\n", '', $text2); }
  return $text2;
}

function transclude_token($text)
{
  global $pagestore, $ParseEngine, $ParseObject;
  static $visited_array = array();
  static $visited_count = 0;

  if(!validate_page($text))
    { return '%%' . $text . '%%'; }

  $visited_array[$visited_count++] = $ParseObject;
  for($i = 0; $i < $visited_count; $i++)
  {
    if($visited_array[$i] == $text)
    {
      $visited_count--;
      return '%%' . $text . '%%';
    }
  }

  $pg = $pagestore->page($text);
  $pg->read();
  if(!$pg->exists)
  {
    $visited_count--;
    return '%%' . $text . '%%';
  }

  $result = new_entity(array('raw', parseText($pg->text, $ParseEngine, $text)));
  $visited_count--;
  return $result;
}

function parse_bold($text)
{
  return preg_replace("/'''(()|[^'].*)'''/Ue", "pair_tokens('bold', q1('\\1'))",
                      $text, -1);
}

function parse_italic($text)
{
  return preg_replace("/''(()|[^'].*)''/Ue", "pair_tokens('italic', q1('\\1'))",
                      $text, -1);
}

function parse_teletype($text)
{
  return preg_replace("/{{({*?.*}*?)}}/Ue",
                      "pair_tokens('tt', q1('\\1'))", $text, -1);
}

function pair_tokens($type, $text)
{
  global $Entity, $FlgChr;

  $Entity[count($Entity)] = array($type . '_start');
  $Entity[count($Entity)] = array($type . '_end');

  return $FlgChr . (count($Entity) - 2) . $FlgChr . $text .
         $FlgChr . (count($Entity) - 1) . $FlgChr;
}

function parse_newline($text)
{
  static $last = array('', '');

  // More than two consecutive newlines fold into two newlines.

  if($last[0] == "\n" && $last[1] == "\n" && $text == "\n")
    { return ''; }
  $last[0] = $last[1];
  $last[1] = $text;

  return preg_replace("/\\n(\\r)?/e", "new_entity(array('newline'))",
                      $text, -1);
}

function parse_horiz($text)
{
  return preg_replace("/-{4,}(\\n(\\r)?)?/e", "new_entity(array('hr'))",
                      $text, -1);
}

function parse_nowiki($text)
{
  return preg_replace("/```(.*)```/Ue",
                      "new_entity(array('nowiki', parse_elements(q1('\\1'))))",
                      $text, -1);
}

function parse_code($text)
{
  global $Entity, $FlgChr;
  static $in_code = 0;
  static $buffer  = '';

  if($in_code)
  {
    if($text == "&lt;/code>\n")
    {
      $Entity[count($Entity)] = array('code', $buffer);
      $buffer  = '';
      $in_code = 0;
      return $FlgChr . (count($Entity) - 1) . $FlgChr;
    }

    $buffer = $buffer . parse_elements($text);
    return '';
  }
  else
  {
    if($text == "&lt;code>\n")
    {
      $in_code = 1;
      return '';
    }

    return $text;
  }
}

function parse_raw_html($text)
{
  global $Entity, $FlgChr;
  static $in_html = 0;
  static $buffer  = '';

  if($in_html)
  {
    if(strtolower($text) == "</html>\n")
    {
      $Entity[count($Entity)] = array('raw', $buffer);
      $buffer  = '';
      $in_html = 0;
      return $FlgChr . (count($Entity) - 1) . $FlgChr;
    }
    // the following str-replace gards agains css or script in the html
    $buffer .=  parse_elements(str_replace(array('<script','</script'),array('&lt;script','&lt;/script'), preg_replace("' on(.*?)=\"(.*?)\"'i", '', $text)));
    return '';
  }
  else
  {
    if(strtolower($text) == "<html>\n")
    {
      $in_html = 1;
      return '';
    }

    return $text;
  }
}

function parse_indents($text)
{
  global $MaxNesting;
  static $last_prefix = '';             // Last line's prefix.
  static $steal = '';                   // Stealing the next line?

// Locate the indent prefix characters.

  preg_match('/^([:\\*#]*)(;([^:]*):)?(.*\\n?$)/', $text, $result);

  if($result[2] != '')                  // Definition list.
    { $result[1] = $result[1] . ';'; }

// No list on last line, no list on this line.  Bail out:

  if($steal == '' && $last_prefix == '' && $result[1] == '')
    { return $text; }                   // Common case fast.

// Remember lengths of strings.

  $last_len   = strlen($last_prefix);
  $prefix_len = strlen($result[1]);

  if($steal == '')                      // Not slurping up line.
  {
    $text = $result[4];

    $fixup = '';

// Loop through and look for prefix characters in common with the
// previous line.

    for($i = 0;
        $i < $MaxNesting && ($i < $last_len || $i < $prefix_len);
        $i++)
    {
      // If equal, continue.
      if($i < $last_len && $i < $prefix_len     // Neither past end.
         && $last_prefix[$i] == $result[1][$i]) // Equal content.
        { continue; }

      // If we've gone deeper than the previous line, we're done.
      if($i >= $last_len)
        { break; }

      // If last line goes further than us, end its dangling lists.
      if($i >= $prefix_len                      // Current line ended.
         || $last_prefix[$i] != $result[1][$i]) // Or not, but they differ.
      {
        for($j = $i; $j < $MaxNesting && $j < $last_len; $j++)
        {
          $fixup = entity_listitem($last_prefix[$j], 'end')
                   . entity_list($last_prefix[$j], 'end')
                   . $fixup;
        }
        break;
      }
    }

// End the preceding line's list item if we're starting another one
// at the same level.

    if($i > 0 && $i >= $prefix_len)
      { $fixup = $fixup . entity_listitem($last_prefix[$i - 1], 'end'); }

// Start fresh new lists for this line as needed.
// We start all but the last one as *indents* (definition lists)
// instead of what they really may appear as, since their function is
// really just to indent.

    for(; $i < $MaxNesting - 1 && $i + 1 < $prefix_len; $i++)
    {
      $result[1][$i] = ':';             // Pretend to be an indent.
      $fixup = $fixup
               . entity_list(':', 'start')
               . entity_listitem(':', 'start');
    }
    if($i < $prefix_len)                // Start the list itself.
    {
      $fixup = $fixup
               . entity_list($result[1][$i], 'start');
    }

// Start the list *item*.

    if($result[2] != '')                // Definition list.
    {
      $fixup = $fixup
               . new_entity(array('term_item_start'))
               . $result[3]
               . new_entity(array('term_item_end'));
    }

    if($result[1] != '')
      { $text = entity_listitem(substr($result[1], -1), 'start') . $text; }

    $text = $fixup . $text;

    $last_prefix = $result[1];
  }

  if($steal != '' || $result[1] != '')
  {
// Check if a previous line used a trailing '\' to "steal" us;
// i.e., to insert a new line while continuing with the same list item.

    if($steal == '')
      { $steal = substr($result[1], -1); }

// Check if *we* have a trailing '\' to "steal" the next line.  If not,
// end ourselves right here.

    if(preg_match('/(^|[^\\\\])(\\\\\\\\)*\\\\$/', $text))
      { $text = preg_replace('/\\\\$/', "\n", $text); }
    else
    {
      $text = str_replace("\n", '', $text);
      $steal = '';
    }
  }

  return $text;
}

function entity_list($type, $fn)
{
  if($type == '*')
    { return new_entity(array('bullet_list_' . $fn)); }
  else if($type == ':' || $type == ';')
    { return new_entity(array('indent_list_' . $fn)); }
  else if($type == '#')
    { return new_entity(array('numbered_list_' . $fn)); }
}

function entity_listitem($type, $fn)
{
  if($type == '*')
    { return new_entity(array('bullet_item_' . $fn)); }
  else if($type == ':' || $type == ';')
    { return new_entity(array('indent_item_' . $fn)); }
  else if($type == '#')
    { return new_entity(array('numbered_item_' . $fn)); }
}

function parse_heading($text)
{
  global $MaxHeading;

  if(!preg_match('/^\s*(=+) (.*) (=+)\s*$/', $text, $result))
    { return $text; }

  if(strlen($result[1]) != strlen($result[3]))
    { return $text; }

  if(($level = strlen($result[1])) > $MaxHeading)
    { $level = $MaxHeading; }

  return new_entity(array('head_start', $level)) .
         $result[2] .
         new_entity(array('head_end', $level));
}

function parse_htmlisms($text)
{
  $text = str_replace('&', '&amp;', $text);
  $text = str_replace('<', '&lt;', $text);
  return $text;
}

function parse_elements($text)
{
  global $FlgChr;
  return preg_replace("/$FlgChr(\\d+)$FlgChr/e", "generate_element(q1('\\1'))", $text, -1);
}

function generate_element($text)
{
  global $Entity, $DisplayEngine;

  if(function_exists('call_user_func_array'))
  {
    return call_user_func_array($DisplayEngine[$Entity[$text][0]],
                                array_slice($Entity[$text], 1));
  }
  else
  {
    return $DisplayEngine[$Entity[$text][0]]($Entity[$text][1],
                                             $Entity[$text][2],
                                             $Entity[$text][3],
                                             $Entity[$text][4],
                                             $Entity[$text][5]);
  }
}

function parse_diff_skip($text)
{
  if(preg_match('/^--+/', $text))
    { return ''; }
  if(preg_match('/^\\\\ No newline/', $text))
    { return ''; }
  return $text;
}

function parse_diff_color($text)
{
  static $in_old = 0;
  static $in_new = 0;

  if(strlen($text) == 0)
    { $this_old = $this_new = 0; }
  else
  {
    $this_old = ($text[0] == '<');
    $this_new = ($text[0] == '>');
  }

  if($this_old || $this_new)
    { $text = substr($text, 2); }

  if($this_old && !$in_old)
    { $text = new_entity(array('diff_old_start')) . $text; }
  else if($this_new && !$in_new)
    { $text = new_entity(array('diff_new_start')) . $text; }

  if($in_old && !$this_old)
    { $text = new_entity(array('diff_old_end')) . $text; }
  else if($in_new && !$this_new)
    { $text = new_entity(array('diff_new_end')) . $text; }

  $in_old = $this_old;
  $in_new = $this_new;

  return $text;
}

function parse_diff_message($text)
{
  global $FlgChr;

  $text = preg_replace('/(^(' . $FlgChr . '\\d+' . $FlgChr . ')?)\\d[0-9,]*c[0-9,]*$/e',
                       "q1('\\1').new_entity(array('diff_change'))", $text, -1);
  $text = preg_replace('/(^(' . $FlgChr . '\\d+' . $FlgChr . ')?)\\d[0-9,]*a[0-9,]*$/e',
                       "q1('\\1').new_entity(array('diff_add'))", $text, -1);
  $text = preg_replace('/(^(' . $FlgChr . '\\d+' . $FlgChr . ')?)\\d[0-9,]*d[0-9,]*$/e',
                       "q1('\\1').new_entity(array('diff_delete'))", $text, -1);

  return $text;
}

function parse_table($text)
{
  static $in_table = 0;

  $pre = '';
  $post = '';
  if(preg_match('/^(\|\|)+.*(\|\|)\s*$/', $text))  // Table.
  {
    if(!$in_table)
    {
      $pre = html_table_start();
      $in_table = 1;
    }
    $text = preg_replace('/^((\|\|)+)(.*)\|\|\s*$/e',
                         "new_entity(array('raw',html_table_row_start().html_table_cell_start(strlen('\\1')/2))).".
                         "q1('\\3').new_entity(array('raw',html_table_cell_end().html_table_row_end()))",
                         $text, -1);
    $text = preg_replace('/((\|\|)+)/e',
                         "new_entity(array('raw',html_table_cell_end().html_table_cell_start(strlen('\\1')/2)))",
                         $text, -1);
  }
  else if($in_table)                    // Have exited table.
  {
    $in_table = 0;
    $pre = html_table_end();
  }

  if($pre != '')
    { $text = new_entity(array('raw', $pre)) . $text; }
  if($post != '')
    { $text = $text . new_entity(array('raw', $post)); }

  return $text;
}

?>
