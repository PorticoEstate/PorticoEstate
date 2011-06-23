#!/usr/bin/php -q
<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default forward_mail_as_sms user=<username> cellphone=<phonenumber>
	 */

	$path_to_phpgroupware = dirname(__FILE__) . '/../../..';	// need to be adapted if this script is moved somewhere else
	$_SERVER['DOCUMENT_ROOT'] = isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] ? $_SERVER['DOCUMENT_ROOT'] : '/usr/local/apache2/htdocs';

	$_GET['domain'] = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'default';

	if(!$function = $_SERVER['argv'][2])
	{
		echo "Nothing to execute\n";
		return;
	}

//	echo $function;

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'login',
		'noapi'      => true		// this stops header.inc.php to include phpgwapi/inc/function.inc.php
	);
	include($path_to_phpgroupware.'/header.inc.php');
	unset($GLOBALS['phpgw_info']['flags']['noapi']);

//	$db_type = $GLOBALS['phpgw_domain'][$_GET['domain']]['db_type'];

	$GLOBALS['phpgw_info']['server']['sessions_type'] = 'db';

	$_domain_info = isset($GLOBALS['phpgw_domain'][$_GET['domain']]) ? $GLOBALS['phpgw_domain'][$_GET['domain']] : '';
	if(!$_domain_info)
	{
		echo "not a valid domain\n";
		die();
	}
	else
	{
		$GLOBALS['phpgw_domain'] = array();
		$GLOBALS['phpgw_domain'][$_GET['domain']] = $_domain_info;
	}

	include(PHPGW_API_INC.'/functions.inc.php');

	$data = array('function' => $function,'enabled'=>1);
	while ($argc > 3)
	{
		list($key,$value) = explode('=',$argv[3]);
		$data[$key] = $value;
		array_shift($argv);
		--$argc;
	}

	$destroy_session = false;
	if(!isset($GLOBALS['phpgw']->session->sessionid) || !$GLOBALS['phpgw']->session->sessionid)
	{
		$GLOBALS['phpgw']->session->sessionid = md5($GLOBALS['phpgw']->common->randomstring(10));
		$destroy_session = true;
	}

	$GLOBALS['phpgw_info']['user']['apps']['admin'] = true;
	$GLOBALS['phpgw_info']['user']['domain'] = $_GET['domain'];
	$GLOBALS['phpgw_info']['user']['account_id'] = -1;
	$GLOBALS['phpgw_info']['user']['account_lid'] = 'cron_job';

	$num = ExecMethod('property.custom_functions.index',$data);
	// echo date('Y/m/d H:i:s ').$_GET['domain'].': '.($num ? "$num job(s) executed" : 'Nothing to execute')."\n";

	if($destroy_session)
	{
		$GLOBALS['phpgw']->session->destroy($GLOBALS['phpgw']->session->sessionid, true);
	}

	$GLOBALS['phpgw']->common->phpgw_exit();
