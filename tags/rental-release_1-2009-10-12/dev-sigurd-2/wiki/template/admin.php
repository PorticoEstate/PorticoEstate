<?php
// $Id$

require_once(TemplateDir . '/common.php');

// The admin template is passed an associative array with the following
// elements:
//
//   html      => A string containing the XHTML markup of the form to be
//                displayed.

function template_admin($args)
{
  template_common_prologue(array('norobots' => 1,
                                 'title'    => 'Administration',
                                 'heading'  => 'Administration',
                                 'headlink' => '',
                                 'headsufx' => '',
                                 'toolbar'  => 0));
?>
<div id="body">
<?php print $args['html']; ?>
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
