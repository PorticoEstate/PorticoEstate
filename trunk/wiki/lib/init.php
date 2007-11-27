<?php
// $Id: init.php 16913 2006-07-25 00:02:21Z skwashd $

// General initialization code.

require('lib/defaults.php');
require('config.php');		// this has to go into a admin-page

$sessionid = (isset($_GET['sessionid']) ? $_GET['sessionid']
		:(isset($_COOKIE['sessionid']) ? $_COOKIE['sessionid'] :'')
	);
if(empty($sessionid))
{
	$phpsessid = ini_get('session.name');
	$sessionid = (isset($_GET[$phpsessid]) ? $_GET[$phpsessid]
			:(isset($_COOKIE[$phpsessid]) ? $_COOKIE[$phpsessid] :'')
		);
 }

if ($sessionid || !(AnonymousSession == 'readonly' || AnonymousSession == 'editable'))
{
	$GLOBALS['phpgw_info']['flags']['noheader'] = True;
	
	include('../header.inc.php');
}
else
{
	$login  = AnonymousUser;
	$passwd = AnonymousPasswd;

	$GLOBALS['phpgw_info']['flags'] = array(
		'disable_Template_class' => True,
		'login' => True,
		'currentapp' => 'login',
		'noheader'  => True
	);
	include('../header.inc.php');

	if (! $GLOBALS['phpgw']->session->verify())
	{
		$login  = AnonymousUser;
		$passwd = AnonymousPasswd;

		$sessionid = $GLOBALS['phpgw']->session->create($login,$passwd,'text');
	}
	if (!$sessionid) {
		echo "<p>Can't create session for user '".AnonymousUser."' !!!</p>\n";
	}
	else
	{	
		$GLOBALS['phpgw']->redirect_link('/wiki/index.php',$_SERVER['QUERY_STRING']);
	}
	$GLOBALS['phpgw']->common->phpgw_exit();
}

$UserName = ExecMethod('phpgwapi.accounts.id2name',get_account_id());
$anonymous = $UserName == AnonymousUser;
// echo "<p>user='$UserName', AnonymousUser='$AnonymousUser', anonymous=".($anonymous?'True':'False').", action='$action', Preview='$Preview'</p>\n";
if (!($action == 'save' && !$Preview) && $action != 'admin' && !($action == 'prefs' && $Save))
{
	$GLOBALS['phpgw_info']['flags']['nonavbar'] = $anonymous;
	$GLOBALS['phpgw']->common->phpgw_header(!$anonymous);
}

/*!
@function isEditable
@abstract check if a page is editable for the user
@syntax isEditable($page_mutable=True)
@param $page_mutable Setting of the page in the db, independent of user
*/
function isEditable($page_mutable=True)
{
	global $anonymous;
	
	return $GLOBALS['phpgw_info']['user']['apps']['admin'] ||	// always editable for admins or
	       // only editable if set in the db AND (user is no anonymous or the anonymous sessions are editable)
	       $page_mutable && (!$anonymous || AnonymousSession == 'editable');
}

$WikiLogo = $GLOBALS['phpgw_info']['server']['webserver_url'] . '/phpgwapi/templates/base/images/logo.png';

require('lib/url.php');
require('lib/messages.php');

$pagestore = CreateObject('wiki.sowiki');

$FlgChr = chr(255);                     // Flag character for parse engine.

$Entity = array();                      // Global parser entity list.

// Strip slashes from incoming variables.

if(get_magic_quotes_gpc())
{
  $document = stripslashes($document);
  $categories = stripslashes($categories);
  $comment = stripslashes($comment);
  $page = stripslashes($page);
}

// Read user preferences from cookie.

$prefstr = isset($_COOKIE[$CookieName])
           ? $_COOKIE[$CookieName] : '';

if(!empty($prefstr))
{
  if(ereg("rows=([[:digit:]]+)", $prefstr, $result))
    { $EditRows = $result[1]; }
  if(ereg("cols=([[:digit:]]+)", $prefstr, $result))
    { $EditCols = $result[1]; }
  if(ereg("user=([^&]*)", $prefstr, $result))
    { $UserName = urldecode($result[1]); }
  if(ereg("days=([[:digit:]]+)", $prefstr, $result))
    { $DayLimit = $result[1]; }
  if(ereg("auth=([[:digit:]]+)", $prefstr, $result))
    { $AuthorDiff = $result[1]; }
  if(ereg("min=([[:digit:]]+)", $prefstr, $result))
    { $MinEntries = $result[1]; }
  if(ereg("hist=([[:digit:]]+)", $prefstr, $result))
    { $HistMax = $result[1]; }
  if(ereg("tzoff=([[:digit:]]+)", $prefstr, $result))
    { $TimeZoneOff = $result[1]; }
}

#if($Charset != '')
#  { header("Content-Type: text/html; charset=$Charset"); }

?>
