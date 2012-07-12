<?php
	/**
	* phpGroupWare
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2000-2009 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @package xmlrpc
	* @subpackage communication
 	* @version $Id$
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

	require_once 'header.inc.php';

	unset($GLOBALS['phpgw_info']['flags']['noapi']);
	$GLOBALS['phpgw_info']['flags']['authed'] = false;
	$GLOBALS['phpgw_info']['message']['errors'] = array();

	if(!isset($_GET['domain']) || !$_GET['domain'])
	{
		$GLOBALS['phpgw_info']['message']['errors'][] = 'domain not given as input';
	}
	else
	{
		$_REQUEST['domain'] = $_GET['domain'];
		$_domain_info = isset($GLOBALS['phpgw_domain'][$_GET['domain']]) ? $GLOBALS['phpgw_domain'][$_GET['domain']] : '';
		if(!$_domain_info)
		{
			$GLOBALS['phpgw_info']['message']['errors'][] = 'not a valid domain';
		}
		else
		{
			$GLOBALS['phpgw_domain'] = array();
			$GLOBALS['phpgw_domain'][$_GET['domain']] = $_domain_info;
		}
	}

	include(PHPGW_API_INC.'/functions.inc.php');


	/**
	* Include the XMLRPC specific functions
	*/
	require_once PHPGW_API_INC . '/xmlrpc/lib/xmlrpc.inc';
	require_once PHPGW_API_INC . '/xmlrpc/lib/xmlrpcs.inc';
	require_once PHPGW_API_INC . '/xmlrpc/lib/xmlrpc_wrappers.inc';

//	include_once(PHPGW_API_INC . '/xml_functions.inc.php');

	// If XML-RPC isn't enabled in PHP, return an XML-RPC response stating so
	if (! function_exists('xmlrpc_server_create'))
	{
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<methodResponse>\n";
		echo "<fault>\n";
		echo " <value>\n";
		echo "  <struct>\n";
		echo "   <member>\n";
		echo "    <name>faultString</name>\n";
		echo "    <value>\n";
		echo "     <string>XML-RPC support NOT enabled in PHP installation</string>\n";
		echo "    </value>\n";
		echo "   </member>\n";
		echo "   <member>\n";
		echo "    <name>faultCode</name>\n";
		echo "    <value>\n";
		echo "     <int>1005</int>\n";
		echo "    </value>\n";
		echo "   </member>\n";
		echo "  </struct>\n";
		echo " </value>\n";
		echo "</fault>\n";
		echo "</methodResponse>\n";

		exit;
	}


	// Return all PHP errors as faults

	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	set_error_handler('xmlrpc_custom_error');

	$headers = getallheaders();

	$GLOBALS['xmlrpc_server'] = xmlrpc_server_create();
	if ( isset($_SERVER['HTTP_RAW_POST_DATA']) )
	{
		$request_xml = $_SERVER['HTTP_RAW_POST_DATA'];
	}
	else
	{
		$request_xml = implode("\r\n", file('php://input'));
	}

	if(!$_domain_info)
	{
		// domain is invalid
		xmlrpc_error(1001,'not a valid domain');
	}

	if ( isset($headers['Authorization']) 
		&& ereg('Basic', $headers['Authorization']) )
	{
		$tmp = $headers['Authorization'];
		$tmp = str_replace(' ','',$tmp);
		$tmp = str_replace('Basic','',$tmp);
		$auth = base64_decode(trim($tmp));
		list($login,$password) = split(':',$auth);

		if($GLOBALS['phpgw']->session->create($login, $password))
		{
			$GLOBALS['phpgw_info']['flags']['authed'] = true;

			// Find out what method they are calling
			// This function is odd, you *NEED* to assign the results
			// to a value, or $method is never returned.  (jengo)
			$null = xmlrpc_decode_request($request_xml, $method);

			$GLOBALS['phpgw']->session->xmlrpc_method_called = $method;
			$GLOBALS['phpgw']->session->update_dla();

			// Check permissions and load the class, register all methods
			// for that class, and execute it
			list($app,$class,$func) = explode('.',$method);

			if ($method == 'system.logout' || $GLOBALS['phpgw_info']['user']['apps'][$app] || $app == 'phpgwapi' || $app == 'xmlrpc')
			{
				$GLOBALS['obj'] = CreateObject($app . '.' . $class);

				xmlrpc_server_register_method($GLOBALS['xmlrpc_server'],sprintf('%s.%s.%s',$app,$class,'list_methods'),'xmlrpc_list_methods');
				xmlrpc_server_register_method($GLOBALS['xmlrpc_server'],sprintf('%s.%s.%s',$app,$class,'describeMethods'),'xmlrpc_describe_methods');
				xmlrpc_server_register_method($GLOBALS['xmlrpc_server'],'system.logout','xmlrpc_logout');

				if(isset($GLOBALS['obj']->xmlrpc_methods) && is_array($GLOBALS['obj']->xmlrpc_methods))
				{
					foreach ($GLOBALS['obj']->xmlrpc_methods as $new_method)
					{
						$full_method_name = sprintf('%s.%s.%s',$app,$class,$new_method['name']);

						xmlrpc_server_register_method($GLOBALS['xmlrpc_server'],$full_method_name,'xmlrpc_call_wrapper');
						// The following function is listed as being in the API, but doesn't actually exisit.
						// This is more of a mental note to track down its exisitence
						//xmlrpc_server_set_method_description($GLOBALS['xmlrpc_server'],$full_method_name,$new_method);
					}
				}
			}
			else if ($method != 'system.listMethods' && $method != 'system.describeMethods')
			{
				xmlrpc_error(1001,'Access not permitted');
			}

			//$output_options = array( "output_type" => "xml", "verbosity" => "pretty", "escaping" => array("markup", "non-ascii", "non-print"), "version" => "xmlrpc", "encoding" => "utf-8" );
			//$output_options = array('output_type' => 'php', 'version' => 'xmlrpc', 'encoding' => 'UTF-8');
			$output_options = array('encoding' => 'UTF-8');

			echo xmlrpc_server_call_method($GLOBALS['xmlrpc_server'],$request_xml, null,$output_options);
			xmlrpc_server_destroy($GLOBALS['xmlrpc_server']);
		}
		else
		{
			xmlrpc_error(1001, 'not authenticated');
		}
	}
	else
	{
		// Find out what method they are calling
		// This function is odd, you *NEED* to assign the results
		// to a value, or $method is never returned.  (jengo)
		$null = xmlrpc_decode_request($request_xml, $method);

		if ($method == 'system.login')
		{
			xmlrpc_server_register_method($GLOBALS['xmlrpc_server'],'system.login','xmlrpc_login');
			echo xmlrpc_server_call_method($GLOBALS['xmlrpc_server'],$request_xml,'');
			xmlrpc_server_destroy($GLOBALS['xmlrpc_server']);

			exit;
		}
		else
		{
			// They didn't request system.login and they didn't pass sessionid or
			// kp3, this is an invailed session (The session could have also been killed or expired)
			xmlrpc_error(1001,'Session expired');
		}
	}


	/**
	* XMLRPC custom error
	*
	* When PHP returns an error, return that error with a fault instead of
	* HTML with will make most parsers fall apart
	* @param integer $error_number
	* @param string $error_string
	* @param string $filename
	* @param integer $line
	* @param array $vars
	*/
	function xmlrpc_custom_error($error_number, $error_string, $filename, $line, $vars)
	{
		if (error_reporting() & $error_number)
		{
			$error_string .= sprintf("\nFilename: %s\nLine: %s",$filename,$line);

			xmlrpc_error(1005,$error_string);
		}
	}

	/**
	* Create an XML-RPC error
	*
	* @param integer $error_number
	* @param string $error_string
	* @todo FIXME! This needs to be expanded to handle PHP errors themselfs it will make debugging easier
	*/
	function xmlrpc_error($error_number, $error_string)
	{
		$values = array
		(
			'faultString' => $error_string,
			'faultCode'   => $error_number
		);

		echo xmlrpc_encode_request(NULL,$values,array("encoding" => "utf-8"));

		xmlrpc_server_destroy($GLOBALS['xmlrpc_server']);
		exit;
	}

	/**
	* Dynamicly create the avaiable methods for each class
	*
	* @param string $method
	* @return array Method names
	*/
	function xmlrpc_list_methods($method)
	{
		list($app,$class,$func) = explode('.',$method);
		$methods[] = 'system.login';
		$methods[] = 'system.logout';
		$methods[] = $method;
		$methods[] = $app . '.' . $class . '.describeMethods';
		for ($i=0; $i<count($GLOBALS['obj']->xmlrpc_methods); $i++)
		{
			$methods[] = $GLOBALS['obj']->xmlrpc_methods[$i]['name'];
		}

		return $methods;
	}

	/**
	* Get XMLRPC methods
	*
	* @param string $method
	* @return array 
	*/
	function xmlrpc_describe_methods($method)
	{
		list($app,$class,$func) = explode('.',$method);
		// FIXME! Add the missing pre-defined methods, example: system.login
		for ($i=0; $i<count($GLOBALS['obj']->xmlrpc_methods); $i++)
		{
			$methods[] = $GLOBALS['obj']->xmlrpc_methods[$i];
		}

		return $methods;
	}

	// I know everyone hates wrappers, but this is the best way this can be done
	// The XML-RPC functions pass method_name as the first parameter, which is
	// unacceptable.
	// Another reason for this, is it might be possiable to pass the sessionid
	// and kp3 instead of using HTTP_AUTH features.
	// Would be a nice workaround for librarys that don't support it, as its
	// not in the XML-RPC spec.
	
	/**
	* XMLRPC call wrapper
	*
	* @param string $method_name
	* @param array $parameters
	* @return unknown
	*/ 
	function xmlrpc_call_wrapper($method_name, $parameters)
	{
		$a = explode('.',$method_name);

		if (count($parameters) == 0)
		{
			$return = $GLOBALS['obj']->$a[2]();
		}
		else if (count($parameters) == 1)
		{
			$return = $GLOBALS['obj']->$a[2]($parameters[0]);
		}
		else
		{
			for ($i=0; $i<count($parameters); $i++)
			{
				$p[] = '$parameters[' . $i . ']';
			}
			eval('$return = $GLOBALS[\'obj\']->$a[2](' . implode(',',$p) . ');');
		}

		// This needs to be expanded and more fully tested
		if (gettype($return) == 'NULL')
		{
			return xmlrpc_error(1002,'No return value detected');
		}
		else
		{
			return $return;
		}
	}

	// The following are common functions used ONLY by XML-RPC
	
	
	/**
	* XMLRPC login
	*
	* @param string $method_name
	* @param array $parameters
	*/
	function xmlrpc_login($method_name, $parameters)
	{
		$p = $parameters[0];

		if ( isset($p['domain']) && $p['domain'] )
		{
			$username = $p['username'] . '@' . $p['domain'];
		}
		else
		{
			$username = $p['username'];
		}

		$sessionid = $GLOBALS['phpgw']->session->create($username, $p['password']);
		$kp3       = $GLOBALS['phpgw']->session->kp3;
		$domain    = $GLOBALS['phpgw']->session->account_domain;

		if ($sessionid && $kp3)
		{
			return array
			(
				'sessionid' => $sessionid,
				'kp3'       => $kp3,
				'domain'    => $domain
			);
		}
		else
		{
			xmlrpc_error(1001,'Login failed');
		}
	}

	/**
	* XMLRPC logout
	*
	* @param string $method
	* @param array $parameters
	* @return boolean Always true
	*/
	function xmlrpc_logout($method, $parameters)
	{
		// We have already verified the session upon before this method is even created
		// As long as nothing happens upon, its safe to destroy the session without
		// fear of it being a hijacked session
		$GLOBALS['phpgw']->session->destroy($GLOBALS['phpgw']->session->sessionid,$GLOBALS['phpgw']->session->kp3);

		return True;
	}
