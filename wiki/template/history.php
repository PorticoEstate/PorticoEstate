<?php
// $Id$

require_once(TemplateDir . '/common.php');

// The history template is passed an associative array with the following
// elements:
//
//   page      => A string containing the name of the wiki page.
//   history   => A string containing the XHTML markup for the history form.
//   diff      => A string containing the XHTML markup for the changes made.

function template_history($args)
{
  global $DiffScript;

  template_common_prologue(array('norobots' => 1,
                                 'title'    => 'History of ' . $args['page'],
                                 'heading'  => 'History of ',
                                 'headlink' => $args['page'],
                                 'headsufx' => '',
                                 'toolbar'  => 1));
?>
<div id="body">
  <form method="get" action="<?php print $DiffScript; ?>">
  <div class="form">
    <input type="hidden" name="action" value="diff" />
    <input type="hidden" name="sessionid" value="<?php echo $GLOBALS['phpgw_info']['user']['sessionid']; ?>" />
    <input type="hidden" name="kp3" value="<?php echo $GLOBALS['phpgw_info']['user']['kp3']; ?>" />
    <input type="hidden" name="page" value="<?php print $args['page']; ?>" />
<table border="0">
  <tr><td><strong>Older</strong></td>
      <td><strong>Newer</strong></td><td></td></tr>
<?php
  print $args['history'];

?>
  <tr><td colspan="3">
    <input type="submit" value="Compute Difference" /></td></tr>
</table>
  </div>
  </form>
<hr /><br />

<strong>Changes by last author:</strong><br /><br />

<?php print $args['diff']; ?>
</div>
<?php
  template_common_epilogue(array('twin'      => $args['page'],
                                 'edit'      => '',
                                 'editver'   => 0,
                                 'history'   => '',
                                 'timestamp' => '',
                                 'nosearch'  => 0));
}
?>
