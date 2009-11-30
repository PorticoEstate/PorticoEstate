<?php
// $Id$

require_once(TemplateDir . '/common.php');

// The view template is passed an associative array with the following
// elements:
//
//   page      => A string containing the name of the wiki page being viewed.
//   html      => A string containing the XHTML rendering of the wiki page.
//   editable  => An integer.  Will be nonzero if user is allowed to edit page.
//   timestamp => Timestamp of last edit to page.
//   archive   => An integer.  Will be nonzero if this is not the most recent
//                version of the page.
//   version   => Version number of page version being viewed.

function template_view($args)
{
  template_common_prologue(array('norobots' => $args['archive'],
                                 'title'    => $args['page'],
                                 'heading'  => '',
                                 'headlink' => $args['page'],
                                 'headsufx' => $args['archive'] ?
                                                 ' (' . html_timestamp($args['timestamp']) . ')'
                                                 : '',
                                 'toolbar'  => 1));
/*
  template_common_epilogue(array('twin'      => $args['page'],
                                 'edit'      => $args['page'],
                                 'editver'   => !$args['editable'] ? -1
                                                : ($args['archive']
                                                   ? $args['version'] : 0),
                                 'history'   => $args['page'],
                                 'timestamp' => $args['timestamp'],
                                 'nosearch'  => 0));
*/
?>
<div id="body">
<?php print $args['html']; ?>
</div>
<?php
template_common_epilogue(array('twin'      => $args['page'],
                                 'edit'      => $args['page'],
                                 'editver'   => !$args['editable'] ? -1
                                                : ($args['archive']
                                                   ? $args['version'] : 0),
                                 'history'   => $args['page'],
                                 'timestamp' => $args['timestamp'],
                                 'nosearch'  => 1));
}
?>
