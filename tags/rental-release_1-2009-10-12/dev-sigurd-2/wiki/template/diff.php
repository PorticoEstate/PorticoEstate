<?php
// $Id$

require_once(TemplateDir . '/common.php');

// The diff template is passed an associative array with the following
// elements:
//
//   page      => A string containing the name of the wiki page being viewed.
//   diff_html => A string containing the XHTML markup for the differences.
//   html      => A string containing the XHTML markup for the page itself.
//   editable  => An integer.  Will be nonzero if user is allowed to edit page.
//   timestamp => Timestamp of last edit to page.

function template_diff($args)
{
  template_common_prologue(array('norobots' => 1,
                                 'title'    => 'Differences in ' . $args['page'],
                                 'heading'  => 'Differences in ',
                                 'headlink' => $args['page'],
                                 'headsufx' => '',
                                 'toolbar'  => 1));
?>
<div id="body">
<strong>Difference between versions:</strong><br /><br />
<?php print $args['diff_html']; ?>
<hr />
<?php print $args['html']; ?>
</div>
<?php
  template_common_epilogue(array('twin'      => $args['page'],
                                 'edit'      => $args['page'],
                                 'editver'   => $args['editable'] ? 0 : -1,
                                 'history'   => $args['page'],
                                 'timestamp' => $args['timestamp'],
                                 'nosearch'  => 0));
}
?>
