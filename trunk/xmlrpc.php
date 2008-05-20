<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/
	
	$GLOBALS['phpgw_info'] = array();
	
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'			=> 'login',
		'noheader'				=> True,
		'disable_Template_class'=> True
	);
	
	/**
	* Include phpgroupware header
	*/
	include_once('header.inc.php');

	/**
	* Include the XMLRPC specific functions
	*/
	include_once(PHPGW_API_INC . '/xml_functions.inc.php');

	// If XML-RPC isn't enabled in PHP, return an XML-RPC response stating so
	if (! function_exists('xmlrpc_server_create'))
	{
		echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
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
	$GLOBALS['xmlrpc_server'] = xmlrpc_server_create();
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
			
	if ( isset($headers['Authorization']) 
		&& ereg('Basic', $headers['Authorization']) )
	{
		if ( $GLOBALS['phpgw']->session->verify($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) )
		{
			// Find out what method they are calling
			// This function is odd, you *NEED* to assign the results
			// to a value, or $method is never returned.  (jengo)
			$null = xmlrpc_decode_request($request_xml, $method);
			
			$GLOBALS['phpgw']->session->xmlrpc_method_called = $method;
			$GLOBALS['phpgw']->session->update_dla();

			// Check permissions and load the class, register all methods
			// for that class, and execute it
			list($app,$class,$func) = explode('.',$method);

			if ($method == 'system.logout' || $GLOBALS['phpgw_info']['user']['apps'][$app] || $app == 'phpgwapi')
			{
				$GLOBALS['obj'] = CreateObject($app . '.' . $class);

				xmlrpc_server_register_method($xmlrpc_server,sprintf('%s.%s.%s',$app,$class,'listMethods'),'xmlrpc_list_methods');
				xmlrpc_server_register_method($xmlrpc_server,sprintf('%s.%s.%s',$app,$class,'describeMethods'),xmlrpc_describe_methods);
				xmlrpc_server_register_method($xmlrpc_server,'system.logout','xmlrpc_logout');

				while (list(,$new_method) = @each($obj->xmlrpc_methods))
				{
					$full_method_name = sprintf('%s.%s.%s',$app,$class,$new_method['name']);

					xmlrpc_server_register_method($xmlrpc_server,$full_method_name,'xmlrpc_call_wrapper');
					// The following function is listed as being in the API, but doesn't actually exisit.
					// This is more of a mental note to track down its exisitence
					//xmlrpc_server_set_method_description($xmlrpc_server,$full_method_name,$new_method);
				}
			}
			else if ($method != 'system.listMethods' && $method != 'system.describeMethods')
			{
				xmlrpc_error(1001,'Access not permitted');
			}

			echo xmlrpc_server_call_method($xmlrpc_server,$request_xml,'');
			xmlrpc_server_destroy($xmlrpc_server);
		}
		else
		{
			// Session is invalid
			xmlrpc_error(1001,'Session expired');
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
			xmlrpc_server_register_method($xmlrpc_server,'system.login','xmlrpc_login');
			echo xmlrpc_server_call_method($xmlrpc_server,$request_xml,'');
			xmlrpc_server_destroy($xmlrpc_server);

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

		echo xmlrpc_encode_request(NULL,$values);

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

		$sessionid = $GLOBALS['phpgw']->session->create($username,$p['password'],'text');
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
