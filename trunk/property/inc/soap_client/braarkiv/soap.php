<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @package soap
	* @subpackage communication
 	* @version $Id: soap.php 6682 2010-12-20 09:57:35Z sigurdne $
	*/


	/*
		Example testurl:
		http://localhost/~sn5607/savannah_trunk/property/inc/soap_client/braarkiv/soap.php?domain=default&location_id=54&section=BraArkiv
	*/

	$GLOBALS['phpgw_info'] = array();

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_Template_class'	=> true,
		'currentapp'				=> 'login',
		'noheader'					=> true,
		'noapi'						=> true		// this stops header.inc.php to include phpgwapi/inc/function.inc.php
	);

	/**
	* Include phpgroupware header
	*/

	require_once '../../../../header.inc.php';

	unset($GLOBALS['phpgw_info']['flags']['noapi']);

	$GLOBALS['phpgw_info']['message']['errors'] = array();
	$system_name = $GLOBALS['phpgw_info']['server']['system_name'];

	if(!isset($_GET['domain']) || !$_GET['domain'])
	{
		$GLOBALS['phpgw_info']['message']['errors'][] = "{$system_name}::domain not given as input";
	}
	else
	{
		$_REQUEST['domain'] = $_GET['domain'];
		$_domain_info = isset($GLOBALS['phpgw_domain'][$_GET['domain']]) ? $GLOBALS['phpgw_domain'][$_GET['domain']] : '';
		if(!$_domain_info)
		{
			$GLOBALS['phpgw_info']['message']['errors'][] = "{$system_name}::not a valid domain";
		}
		else
		{
			$GLOBALS['phpgw_domain'] = array();
			$GLOBALS['phpgw_domain'][$_GET['domain']] = $_domain_info;
		}
	}

	require_once PHPGW_API_INC.'/functions.inc.php';



	$location_id	= phpgw::get_var('location_id', 'int');
	$section	= phpgw::get_var('section', 'string');
	$bygningsnr = (int) phpgw::get_var('bygningsnr', 'int');

	$c	= CreateObject('admin.soconfig',$location_id);


	$login = $c->config_data[$section]['anonymous_user'];
	$passwd = $c->config_data[$section]['anonymous_pass'];
	$location_url = 'http://braarkiv.adm.bgo/service/services.asmx';//$c->config_data['common']['location_url'];

//_debug_array($_REQUEST);
//_debug_array($c->config_data[$section]);

	$_POST['submitit'] = "";

	$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);

	if(!$GLOBALS['sessionid'])
	{
		$lang_denied = lang('Anonymous access not correctly configured');
		if($GLOBALS['phpgw']->session->reason)
		{
			$lang_denied = $GLOBALS['phpgw']->session->reason;
		}
		$GLOBALS['phpgw_info']['message']['errors'][] = "{$system_name}::{$lang_denied}";
	}
	
	if($GLOBALS['phpgw_info']['message']['errors'])
	{
		_debug_array($GLOBALS['phpgw_info']['message']['errors']);
		$GLOBALS['phpgw']->common->phpgw_exit();	
	}

	/**
	* @global object $GLOBALS['server']
	*/

	require_once 'services.php';

	$options=array();
	$options['soap_version']	= SOAP_1_2;
	$options['location']		= $location_url;
	$options['uri']				= $location_url;
	$options['trace']			= 1;
	//	$options['proxy_host']		= $this->pswin_param['proxy_host'];
	//	$options['proxy_port']		= $this->pswin_param['proxy_port'];
	$options['encoding']		= 'iso-8859-1';//'UTF-8';

	$wdsl = null;
	$wdsl = 'http://braarkiv.adm.bgo/service/services.asmx?WSDL';

	$Services = new Services($wdsl, $options);
	
	$Login = new Login();
	
	$Login->userName = 'hb776';
	$Login->password = 'hb776';

	$LoginResponse = $Services->Login($Login);
_debug_array($LoginResponse);
	$secKey = $LoginResponse->LoginResult;


	$searchDocument = new searchDocument();
	$searchDocument->secKey = $secKey;
	$searchDocument->baseclassname = 'Eiendomsarkiver';
	$searchDocument->classname = 'Eiendomsarkiv';
	$searchDocument->where = "bygningsnr = {$bygningsnr}";// AND Regdato > '2006-01-25'";
//	$searchDocument->where = "Regdato > '2006-01-25'";
	$searchDocument->maxhits = '1';

_debug_array($searchDocument);

	$searchDocumentResponse = $Services->searchDocument($searchDocument);

	$searchDocumentResult = $searchDocumentResponse->searchDocumentResult;





/*

	$searchAndGetDocumentsWithVariants = new searchAndGetDocumentsWithVariants();

	$searchAndGetDocumentsWithVariants->secKey = $secKey;
	$searchAndGetDocumentsWithVariants->baseclassname = 'Eiendomsarkiver';
//	$searchAndGetDocumentsWithVariants->classname = 'Eiendomsarkiv';
//	$searchAndGetDocumentsWithVariants->where = "bygningsnr = {$bygningsnr}";// AND Regdato > '2006-01-25'";
	$searchAndGetDocumentsWithVariants->maxhits = '1';


_debug_array($searchAndGetDocumentsWithVariants);

	$searchAndGetDocumentsWithVariantsResponse = $Services->searchAndGetDocumentsWithVariants($searchAndGetDocumentsWithVariants);

	$searchAndGetDocumentsWithVariantsResult = $searchDocumentResponse->searchAndGetDocumentsWithVariantsResult;

_debug_array($searchAndGetDocumentsWithVariantsResponse);
*/





	$GLOBALS['phpgw']->common->phpgw_exit();
