<?php
// $Id$

require_once(TemplateDir . '/common.php');

// The conflict template is passed an associative array with the following
// elements:
//
//   page      => A string containing the name of the wiki page being edited.
//   text      => A string containing the wiki markup of the version that was
//                saved while the user was editing the page.
//   html      => A string containing the XHTML markup of the version of the
//                page that was saved while the user was editing the page.
//   usertext  => A string containing the wiki markup of the text the user
//                tried to save.
//   timestamp => Timestamp of last edit to page.
//   nextver   => An integer; the expected version of this document when saved.

function template_conflict($args)
{
  global $EditRows, $EditCols, $UserName, $PrefsScript;

  template_common_prologue(array('norobots' => 1,
                                 'title'    => 'Editing ' . $args['page'],
                                 'heading'  => 'Editing ',
                                 'headlink' => $args['page'],
                                 'headsufx' => '',
                                 'toolbar'  => 1));
?>
<div id="body">
<p class="warning">
  Warning: since you started editing, this document has been changed by someone
  else.  Please merge your edits into the current version of this document.
</p>
<h1>Current Version</h1>
<form method="post" action="<?php print saveURL($args['page']); ?>">
<div class="form">
  <input type="submit" name="Save" value="Save" />
  <input type="submit" name="Preview" value="Preview" />
<?php
  if($UserName != '')
    { print 'Your user name is ' . html_ref($UserName, $UserName); }
  else
  {
?>  Visit <a href="<?php print $PrefsScript; ?>">Preferences</a> to set your
user name<?php
  }
?><br />
  <input type="hidden" name="nextver" value="<?php print $args['nextver']; ?>" />
  <textarea name="document" rows="<?php
    print $EditRows; ?>" cols="<?php
    print $EditCols; ?>" wrap="virtual"><?php
  print str_replace('<', '&lt;', str_replace('&', '&amp;', $args['text']));
?></textarea><br />
  Summary of change:
  <input type="text" name="comment" size="40" value="" /><br />
  Add document to category:
  <input type="text" name="categories" size="40" value="" />
<hr />
<h1>Your changes</h1>
  <textarea name="discard" rows="<?php
    print $EditRows; ?>" cols="<?php
    print $EditCols; ?>" wrap="virtual"><?php
  print str_replace('<', '&lt;', str_replace('&', '&amp;', $args['usertext']));
?></textarea><br />
</div>
</form>
<h1>Preview of Current Version</h1>
<?php
  print $args['html'];
?>
</div>
<?php
  template_common_epilogue(array('twin'      => $args['page'],
                                 'edit'      => '',
                                 'editver'   => 0,
                                 'history'   => $args['page'],
                                 'timestamp' => $args['timestamp'],
                                 'nosearch'  => 0));
}
?>
