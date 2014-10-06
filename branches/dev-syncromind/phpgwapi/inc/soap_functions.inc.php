<?php
	/**
	* Shared functions and vars for use with soap client/server
	* @author Sigurd Nes <sigurdne@online.no>
	* @author Dietrich <dietrich@ganx4.com>
	* @copyright Copyright (C) ? Dietrich
	* @copyright Portions Copyright (C) 2004-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

/*
	$GLOBALS['soapTypes'] = array(
		'i4'           => 1,
		'int'          => 1,
		'boolean'      => 1,
		'string'       => 1,
		'double'       => 1,
		'float'        => 1,
		'dateTime'     => 1,
		'timeInstant'  => 1,
		'dateTime'     => 1,
		'base64Binary' => 1,
		'base64'       => 1,
		'array'        => 2,
		'Array'        => 2,
		'SOAPStruct'   => 3,
		'ur-type'      => 2
	);

	while(list($key,$val) = each($GLOBALS['soapTypes']))
	{
		$GLOBALS['soapKeys'][] = $val;
	}

	$GLOBALS['typemap'] = array(
		'http://soapinterop.org/xsd'                => array('SOAPStruct'),
		'http://schemas.xmlsoap.org/soap/encoding/' => array('base64'),
		'http://www.w3.org/1999/XMLSchema'          => $GLOBALS['soapKeys']
	);

	$GLOBALS['namespaces'] = array(
		'http://schemas.xmlsoap.org/soap/envelope/' => 'SOAP-ENV',
		'http://www.w3.org/1999/XMLSchema-instance' => 'xsi',
		'http://www.w3.org/1999/XMLSchema'          => 'xsd',
		'http://schemas.xmlsoap.org/soap/encoding/' => 'SOAP-ENC',
		'http://soapinterop.org/xsd'                => 'si'
	);
*/
	/*
	NOTE: already defined in xml_functions
	$xmlEntities = array(
		'quot' => '"',
		'amp'  => '&',
		'lt'   => '<',
		'gt'   => '>',
		'apos' => "'"
	);
	*/

//	$GLOBALS['soap_defencoding'] = 'UTF-8';

	/**
	* SOAP Login
	*
	* @param string $m1 Server name
	* @param string $m2 username
	* @param string $m3 password
	* @return array Array with soapval object(s)
	*/
	function system_login($data)
	{
		$domain		= $data['domain'];
		$username	= $data['username'];
		$password	= $data['password'];

		$sessionid = $GLOBALS['phpgw']->session->create_server("{$username}@{$domain}", $password);

		if(!$sessionid)
		{
			if($domain)
			{
				$user = $username.'@'.$domain;
			}
			else
			{
				$user = $username;
			}
			$sessionid = $GLOBALS['phpgw']->session->create($user,$password);
		}
		if($sessionid)
		{
			$rtrn = array('sessionid' => $sessionid);

		}
		else
		{
			$rtrn = array('GOAWAY' => $username);
		}
		return $rtrn;
	}

	function system_logout($data)
	{
		$sessionid   = $data['sessionid'];
		
		$later = $GLOBALS['phpgw']->session->destroy($sessionid);

		if($later)
		{
			$rtrn = array('GOODBYE' => 'XOXO');
		}
		else
		{
			$rtrn = array('OOPS'=> 'WHAT?');
		}
		return $rtrn;
	}

	
	function system_list_apps()
	{
		$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_applications WHERE app_enabled<3",__LINE__,__FILE__);
		$apps = array();
		if($GLOBALS['phpgw']->db->num_rows())
		{
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$name   = $GLOBALS['phpgw']->db->f('app_name');
				$title  = $GLOBALS['phpgw']->db->f('app_title');
				$status = $GLOBALS['phpgw']->db->f('app_enabled');
				$version= $GLOBALS['phpgw']->db->f('app_version');

				$apps[$name] = array(
					$name,
					$status,
					$version
				);
			}
		}
		return $apps;
	}
