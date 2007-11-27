#!/usr/bin/php -q
<?php
	/**
	* Timed Asynchron Services - cron-job like timed calls of phpGroupWare methods
	* @author Ralf Becker <RalfBecker@outdoor-training.de>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage cron
	* @version $Id: asyncservices.php 15874 2005-04-28 08:11:59Z powerstat $
	*/

	$path_to_phpgroupware = dirname(__FILE__) . '/../..';	// need to be adapted if this script is moved somewhere else
	$_GET['domain'] = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'default';

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'login',
		'noapi'      => True		// this stops header.inc.php to include phpgwapi/inc/function.inc.php
	);
	
	
	/**
	* Include phpgroupware header
	*/
	include($path_to_phpgroupware.'/header.inc.php');


	unset($GLOBALS['phpgw_info']['flags']['noapi']);

	$db_type = $GLOBALS['phpgw_domain'][$_GET['domain']]['db_type'];
	if (!extension_loaded($db_type) && !dl($db_type.'.so'))
	{
		echo "Extension '$db_type' is not loaded and can't be loaded via dl('$db_type.so') !!!\n";
	}
	
	$GLOBALS['phpgw_info']['server']['sessions_type'] = 'db';

	/**
	* Include API functions
	*/
	include(PHPGW_API_INC.'/functions.inc.php');
	
	$num = ExecMethod('phpgwapi.asyncservice.check_run','crontab');
	// if the following comment got removed, you will get an email from cron for every check performed
	//echo date('Y/m/d H:i:s ').$_GET['domain'].': '.($num ? "$num job(s) executed" : 'Nothing to execute')."\n";

	$GLOBALS['phpgw']->common->phpgw_exit();
