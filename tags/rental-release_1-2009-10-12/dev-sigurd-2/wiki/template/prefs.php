<?php
// $Id$

require('parse/html.php');
require_once(TemplateDir . '/common.php');

function template_prefs()
{
  global $PrefsScript, $HTTP_REFERER, $HistMax, $TimeZoneOff;
  global $AuthorDiff, $EditRows, $EditCols, $UserName, $DayLimit, $MinEntries;

  template_common_prologue(array('norobots' => 1,
                                 'title'    => 'Preferences',
                                 'heading'  => 'Preferences',
                                 'headlink' => '',
                                 'headsufx' => '',
                                 'toolbar'  => 0));
?>
<div id="body">
<form action="<?php print $PrefsScript; ?>" method="post">
<div class="form">
  <input type="hidden" name="referrer" value="<?php print $HTTP_REFERER; ?>" />

<!--  <strong>User name</strong><br /><br />

  This feature displays your name on RecentChanges to the right
  of pages you edit.  If left blank, your IP address will be
  displayed instead.<br /><br />
  <input type="text" name="user" value="-->
Your username is "<?php print $UserName; ?>".<br><br> 
<!--" /><br />
<hr align=left width=99% />-->

  <strong>Edit box</strong><br /><br />
  Rows: <input type="text" name="rows" value="<?php print $EditRows; ?>" /><br />
  Columns: <input type="text" name="cols" value="<?php
    print $EditCols; ?>" /><br />
  <hr align=left width=99% />

  <strong>History lists</strong><br /><br />
  Enter here the maximum number of entries to display in a document's history
  list.<br /><br />
  <input type="text" name="hist" value="<?php print $HistMax; ?>" /><br /><br />

  <strong>RecentChanges</strong><br /><br />
  Choose your current time here, so the server may figure out what time zone
  you are in.<br /><br />
  <select name="tzoff">
<?php
  for($i = -23.5 * 60; $i <= 23.5 * 60; $i += 30)
  {
?>
<option value="<?php print $i; ?>"<?php if($i == $TimeZoneOff) { print ' selected="selected"'; } ?>><?php
    print date('Y-m-d H:i', time() + $i * 60);
?></option>
<?php
  }
?>
  </select><br /><br />
  Enter here the number of days of edits to display on RecentChanges or any
  other subscription list.  Set this to zero if you wish to see all pages in
  RecentChanges, regardless of how recently they were edited.<br /><br />
  <input type="text" name="days" value="<?php print $DayLimit; ?>" /><br /><br />
  <em>But</em> display at least this many entries in RecentChanges and other
  subscription lists:<br /><br />
  <input type="text" name="min" value="<?php print $MinEntries; ?>" /><br /><br />
  <input type="checkbox" name="auth"<?php
    if($AuthorDiff) { print ' checked="checked"'; } ?> />
  History display should show <em>all</em> changes made by the latest
  author.  Otherwise, show only the last change made.<br />

  <hr align=left width=99% /><br />
  <input type="submit" name="Save" value="Save" />
</div>
</form>
</div>
<?php
  template_common_epilogue(array('twin'      => '',
                                 'edit'      => '',
                                 'editver'   => 0,
                                 'history'   => '',
                                 'timestamp' => '',
                                 'nosearch'  => 1));
}
?>
