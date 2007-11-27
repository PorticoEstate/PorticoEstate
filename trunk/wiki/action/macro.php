<?php
// $Id: macro.php,v 1.3 2003/03/03 14:16:23 ralfbecker Exp $

require('parse/macros.php');
require('parse/html.php');

// Execute a macro directly from the URL.
function action_macro()
{
  global $ViewMacroEngine, $macro, $parms;

  if(!empty($ViewMacroEngine[$macro]))
  {
    print $ViewMacroEngine[$macro]($parms);
  }
}
?>
