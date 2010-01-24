<?php
// $Id$

// Harvest script parameters and other variables.  We do this even if
// register_globals=on; this way, we force the variables to be defined.
// (Which is better form in case the admin has warnings cranked all the
// way up).

$HTTP_REFERER = isset($_SERVER['HTTP_REFERER'])
                ? $_SERVER['HTTP_REFERER'] : '';
$REMOTE_ADDR  = isset($_SERVER['REMOTE_ADDR'])
                ? $_SERVER['REMOTE_ADDR'] : '';

$action       = isset($_GET['action'])
                ? $_GET['action'] : '';
$page         = isset($_GET['page'])
                ? $_GET['page'] : '';
$ver1         = isset($_GET['ver1'])
                ? $_GET['ver1'] : '';
$ver2         = isset($_GET['ver2'])
                ? $_GET['ver2'] : '';
$find         = isset($_GET['find'])
                ? $_GET['find'] : '';
$version      = isset($_GET['version'])
                ? $_GET['version'] : '';
$full         = isset($_GET['full'])
                ? $_GET['full'] : '';
$min          = isset($_GET['min'])
                ? $_GET['min'] : '';
$days         = isset($_GET['days'])
                ? $_GET['days'] : '';

$Preview      = isset($_POST['Preview'])
                ? $_POST['Preview'] : '';
$Save         = isset($_POST['Save'])
                ? $_POST['Save'] : '';
$SaveAndContinue = isset($_POST['SaveAndContinue'])
                ? $_POST['SaveAndContinue'] : '';
$archive      = isset($_POST['archive'])
                ? $_POST['archive'] : '';
$auth         = isset($_POST['auth'])
                ? $_POST['auth'] : '';
$categories   = isset($_POST['categories'])
                ? $_POST['categories'] : '';
$cols         = isset($_POST['cols'])
                ? $_POST['cols'] : '';
$comment      = isset($_POST['comment'])
                ? $_POST['comment'] : '';
$days         = isset($_POST['days'])
                ? $_POST['days'] : $days;
$discard      = isset($_POST['discard'])
                ? $_POST['discard'] : '';
$document     = isset($_POST['document'])
                ? $_POST['document'] : '';
$hist         = isset($_POST['hist'])
                ? $_POST['hist'] : '';
$min          = isset($_POST['min'])
                ? $_POST['min'] : $min;
$nextver      = isset($_POST['nextver'])
                ? $_POST['nextver'] : '';
$rows         = isset($_POST['rows'])
                ? $_POST['rows'] : '';
$tzoff        = isset($_POST['tzoff'])
                ? $_POST['tzoff'] : '';
$user         = isset($_POST['user'])
                ? $_POST['user'] : '';
$referrer     = isset($_POST['referrer'])
                ? $_POST['referrer'] : '';

require('lib/init.php');
require('parse/transforms.php');

// To add an action=x behavior, add an entry to this array.  First column
//   is the file to load, second is the function to call, and third is how
//   to treat it for rate-checking purposes ('view', 'edit', or 'search').
$ActionList = array(
                'view' => array('action/view.php', 'action_view', 'view'),
                'edit' => array('action/edit.php', 'action_edit', 'view'),
                'save' => array('action/save.php', 'action_save', 'edit'),
                'diff' => array('action/diff.php', 'action_diff', 'search'),
                'find' => array('action/find.php', 'action_find', 'search'),
                'history' => array('action/history.php', 'action_history',
                                   'search'),
                'prefs'   => array('action/prefs.php', 'action_prefs', 'view'),
                'macro'   => array('action/macro.php', 'action_macro', 'search'),
                'rss'     => array('action/rss.php', 'action_rss', 'view'),
                'style'   => array('action/style.php', 'action_style', ''),
                'admin'   => array('action/admin.php','','')
              );

if(empty($action))
  { $action = 'view'; }
if(empty($page))
  { $page = $HomePage; }

// Confirm we have a valid page name.
if(!validate_page($page))
  { die($ErrorInvalidPage); }

// Don't let people do too many things too quickly.
if($ActionList[$action][2] != '')
  { $pagestore->rateCheck($ActionList[$action][2],$REMOTE_ADDR); }

// Dispatch the appropriate action.
if(!empty($ActionList[$action]))
{
  include($ActionList[$action][0]);
  if ($ActionList[$action][1])
    $ActionList[$action][1]();
}

// Expire old versions, etc.
$pagestore->maintain();

if (!$anonymous)
{
	$GLOBALS['phpgw']->common->phpgw_footer();
}
?>
