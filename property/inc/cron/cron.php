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
 	* @version $Id: cron.php,v 1.2 2007/10/07 21:25:22 sigurdne Exp $
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
		echo date('Y/m/d H:i:s ') . " Nothing to execute\n";
		return;
	}

//	echo $function;

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'login',
		'noapi'      => True		// this stops header.inc.php to include phpgwapi/inc/function.inc.php
	);
	include($path_to_phpgroupware.'/header.inc.php');
	unset($GLOBALS['phpgw_info']['flags']['noapi']);

	$db_type = $GLOBALS['phpgw_domain'][$_GET['domain']]['db_type'];

	$GLOBALS['phpgw_info']['server']['sessions_type'] = 'db';

	include(PHPGW_API_INC.'/functions.inc.php');

	$data = array('function' => $function,'enabled'=>1);
	while ($argc > 3)
	{
		list($key,$value) = explode('=',$argv[3]);
		$data[$key] = $value;
		array_shift($argv);
		--$argc;
	}

	$num = ExecMethod('property.custom_functions.index',$data);
	// echo date('Y/m/d H:i:s ').$_GET['domain'].': '.($num ? "$num job(s) executed" : 'Nothing to execute')."\n";

	$GLOBALS['phpgw']->common->phpgw_exit();
