<?php
	/**
	* Shared functions and vars for use with soap client/server
	* @author Dietrich <dietrich@ganx4.com>
	* @copyright Copyright (C) ? Dietrich
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

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

	$GLOBALS['soap_defencoding'] = 'UTF-8';

	/**
	* SOAP Login
	*
	* @param string $m1 Server name
	* @param string $m2 username
	* @param string $m3 password
	* @return array Array with soapval object(s)
	*/
	function system_login($m1,$m2,$m3)
	{
		$server_name = trim($m1);
		$username    = trim($m2);
		$password    = trim($m3);

		list($sessionid,$kp3) = $GLOBALS['phpgw']->session->create_server($username.'@'.$server_name,$password,'text');

		if(!$sessionid && !$kp3)
		{
			if($server_name)
			{
				$user = $username.'@'.$server_name;
			}
			else
			{
				$user = $username;
			}
			$sessionid = $GLOBALS['phpgw']->session->create($user,$password);
			$kp3 = $GLOBALS['phpgw']->session->kp3;
			$domain = $GLOBALS['phpgw']->session->account_domain;
		}
		if($sessionid && $kp3)
		{
			$rtrn = array(
				CreateObject('phpgwapi.soapval','domain','string',$domain),
				CreateObject('phpgwapi.soapval','sessionid','string',$sessionid),
				CreateObject('phpgwapi.soapval','kp3','string',$kp3)
			);
		}
		else
		{
			$rtrn = array(CreateObject('phpgwapi.soapval','GOAWAY','string',$username));
		}
		return $rtrn;
	}

	function system_logout($m1,$m2)
	{
		$sessionid   = $m1;
		$kp3         = $m2;
		
		$later = $GLOBALS['phpgw']->session->destroy($sessionid,$kp3);

		if($later)
		{
			$rtrn = array(
				CreateObject('phpgwapi.soapval','GOODBYE','string','XOXO')
			);
		}
		else
		{
			$rtrn = array(
				CreateObject('phpgwapi.soapval','OOPS','string','WHAT?')
			);
		}
		return $rtrn;
	}

	/*
	function system_listApps()
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
					CreateObject('phpgwapi.soapval','title','string',$title),
					CreateObject('phpgwapi.soapval','name','string',$name),
					CreateObject('phpgwapi.soapval','status','string',$status),
					CreateObject('phpgwapi.soapval','version','string',$version)
				);
			}
		}
		return $apps;
	}
	*/
?>
