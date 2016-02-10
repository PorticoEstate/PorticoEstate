<?php
	/**************************************************************************\
	* eGroupWare xmlhttp server                                                *
	* http://www.egroupware.org                                                *
	* This file written by Lars Kneschke <lkneschke@egroupware.org>            *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License.              *
	\**************************************************************************/

	/* $Id$ */

	require_once('./inc/xajax.inc.php');

	/**
	 * callback if the session-check fails, redirects via xajax to login.php
	 * 
	 * @param array &$anon_account anon account_info with keys 'login', 'passwd' and optional 'passwd_type'
	 * @return boolean/string true if we allow anon access and anon_account is set, a sessionid or false otherwise
	 */
	function xajax_redirect(&$anon_account)
	{
		// now the header is included, we can set the charset
		$GLOBALS['xajax']->setCharEncoding('utf-8');
		define('XAJAX_DEFAULT_CHAR_ENCODING','utf-8');

		$response = new xajaxResponse();
		$response->addScript("location.href='".$GLOBALS['phpgw_info']['server']['webserver_url'].'/login.php?cd=10'."';");

		header('Content-type: text/xml; charset='.'utf-8');
		echo $response->getXML();
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	function doXMLHTTP()
	{
		$numargs = func_num_args(); 
		if($numargs < 1) 
			return false;

		$argList	= func_get_args();
		$arg0		= array_shift($argList);
	
		if(get_magic_quotes_gpc()) {
			foreach($argList as $key => $value) {
				if(is_array($value)) {
					foreach($argList as $key1 => $value1) {
						$argList[$key][$key1] = stripslashes($value1);
					}
				} else {
					$argList[$key] = stripslashes($value);
				}
			}
		}
		//error_log("xajax_doXMLHTTP('$arg0',...)");

		@list($appName, $className, $functionName, $handler) = explode('.',$arg0);
		
		$GLOBALS['phpgw_info'] = array(
			'flags' => array(
				'currentapp'			=> $appName,
				'noheader'			=> True,
			)
		);
		include('./../header.inc.php');

		// now the header is included, we can set the charset
		$GLOBALS['xajax']->setCharEncoding('utf-8');
		define('XAJAX_DEFAULT_CHAR_ENCODING','utf-8');

		switch($handler)
		{
			case '/etemplate/process_exec':
				$_GET['menuaction'] = $appName.'.'.$className.'.'.$functionName;
				$appName = $className = 'etemplate';
				$functionName = 'process_exec';
				$arg0 = 'etemplate.etemplate.process_exec';

				$argList = array(
					$argList[0]['etemplate_exec_id'],
					$argList[0]['submit_button'],
					$argList[0],
					'xajaxResponse',
				);
				error_log("xajax_doXMLHTTP() /etemplate/process_exec handler: arg0='$arg0', menuaction='$_GET[menuaction]'");
				break;
			case 'etemplate':	// eg. ajax code in an eTemplate widget
				$arg0 = ($appName = 'etemplate').'.'.$className.'.'.$functionName;
				break;
		}
		if(substr($className,0,4) != 'ajax' && $arg0 != 'etemplate.etemplate.process_exec' && substr($functionName,0,4) != 'ajax' ||
			!preg_match('/^[A-Za-z0-9_]+\.[A-Za-z0-9_]+\.[A-Za-z0-9_]+$/',$arg0))
		{
			// stopped for security reasons
			error_log($_SERVER['PHP_SELF']. ' stopped for security reason. '.$arg0.' is not valid. class- or function-name must start with ajax!!!');
			exit;
		}
		$ajaxClass =& CreateObject($appName.'.'.$className);
		
		$translation = CreateObject('felamimail.translation');
		if($argList)
		{
			$argList = $translation->convert($argList);
		}

		return call_user_func_array(array(&$ajaxClass, $functionName), $argList );
	}

	$xajax = new xajax($_SERVER['PHP_SELF']);
	$xajax->registerFunction('doXMLHTTP');	
	$xajax->processRequests();
