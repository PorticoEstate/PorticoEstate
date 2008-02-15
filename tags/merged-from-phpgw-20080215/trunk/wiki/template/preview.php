<?php
// $Id$

require_once(TemplateDir . '/common.php');

// The preview template is passed an associative array with the following
// elements:
//
//   page      => A string containing the name of the wiki page being viewed.
//   text      => A string containing the wiki markup of the wiki page.
//   html      => A string containing the XHTML rendering of the wiki page.
//   timestamp => Timestamp of last edit to page.
//   nextver   => An integer; the expected version of this document when saved.
//   archive   => An integer.  Will be nonzero if this is not the most recent
//                version of the page.

function template_preview($args)
{
  global $EditRows, $EditCols, $categories, $UserName, $comment, $PrefsScript, $EditWithPreview;
  
  $title = $EditWithPreview ? 'Editing' : 'Previewing';

  template_common_prologue(array('norobots' => 1,
                                 'title'    => $title . ' ' . $args['page'],
                                 'heading'  => $title . ' ',
                                 'headlink' => $args['page'],
                                 'headsufx' => '',
                                 'toolbar'  => 1,
                                 'nosearch'  => 0));
?>
<div id="body">
<form method="post" name="editform" action="<?php print saveURL($args['page']); ?>">
<div class="form">
  <input type="submit" name="Save" value="Save" />
  <input type="submit" name="Preview" value="Preview" />
  <input type="submit" name="SaveAndContinue" value="Save & Continue" />
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
<?php  if($args['archive'])
    {?>
  <input type="hidden" name="archive" value="1" />
<?php  }?>
  <textarea name="document" rows="<?php
    print $EditRows; ?>" cols="<?php
    print $EditCols; ?>" wrap="virtual"><?php
  print str_replace('<', '&lt;', str_replace('&', '&amp;', $args['text']));
?></textarea><br />
  Summary of change:
  <input type="text" name="comment" size="40" value="<?php
    print $comment; ?>" /><br />
  Add document to category:
  <input type="text" name="categories" size="40" value="<?php
    print $categories; ?>" />
</div>
</form>
<?php global $EditWithPreview;
  if (!$EditWithPreview) {
    echo "<h1>Preview</h1>\n";
  }
?>
<hr align=left width=99% />
<?php print $args['html']; ?>
</div>
<?php
  template_common_epilogue(array('twin'      => $args['page'],
                                 'edit'      => '',
                                 'editver'   => 0,
                                 'history'   => $args['page'],
                                 'timestamp' => $args['timestamp'],
                                 'nosearch'  => 1));
}
?>
