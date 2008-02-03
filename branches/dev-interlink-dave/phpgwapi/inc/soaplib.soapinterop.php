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

	$GLOBALS['server']->add_to_map(
		'hello',
		array('string'),
		array('string')
	);


	/**
	* Create soapval object
	*
	* @param string $serverid unused
	* @return object soapval
	*/
	function hello($serverid)
	{
		return CreateObject('soap.soapval','return','string',$GLOBALS['phpgw_info']['server']['site_title']);
	}

	$GLOBALS['server']->add_to_map(
		'echoString',
		array('string'),
		array('string')
	);
	function echoString($inputString)
	{
		return CreateObject('soap.soapval','return','string',$inputString);
	}

	$GLOBALS['server']->add_to_map(
		'echoStringArray',
		array('array'),
		array('array')
	);
	function echoStringArray($inputStringArray)
	{
		return $inputStringArray;
	}

	$GLOBALS['server']->add_to_map(
		'echoInteger',
		array('int'),
		array('int')
	);
	function echoInteger($inputInteger)
	{
		return $inputInteger;
	}

	$GLOBALS['server']->add_to_map(
		'echoIntegerArray',
		array('array'),
		array('array')
	);
	function echoIntegerArray($inputIntegerArray)
	{
		return $inputIntegerArray;
	}

	$GLOBALS['server']->add_to_map(
		'echoFloat',
		array('float'),
		array('float')
	);
	function echoFloat($inputFloat)
	{
		return $inputFloat;
	}

	$GLOBALS['server']->add_to_map(
		'echoFloatArray',
		array('array'),
		array('array')
	);
	function echoFloatArray($inputFloatArray)
	{
		return $inputFloatArray;
	}

	$GLOBALS['server']->add_to_map(
		'echoStruct',
		array('SOAPStruct'),
		array('SOAPStruct')
	);
	function echoStruct($inputStruct)
	{
		return $inputStruct;
	}

	$GLOBALS['server']->add_to_map(
		'echoStructArray',
		array('array'),
		array('array')
	);
	function echoStructArray($inputStructArray)
	{
		return $inputStructArray;
	}

	$GLOBALS['server']->add_to_map(
		'echoVoid',
		array(),
		array()
	);
	function echoVoid()
	{
	}

	$GLOBALS['server']->add_to_map(
		'echoBase64',
		array('base64'),
		array('base64')
	);
	function echoBase64($b_encoded)
	{
		return base64_encode(base64_decode($b_encoded));
	}

	$GLOBALS['server']->add_to_map(
		'echoDate',
		array('timeInstant'),
		array('timeInstant')
	);
	function echoDate($timeInstant)
	{
		return $timeInstant;
	}

	$GLOBALS['server']->add_to_map(
		'system_auth',
		array('string','string','string'),
		array('array')
	);

	$GLOBALS['server']->add_to_map(
		'system_auth_verify',
		array('string','string','string'),
		array('array')
	);
?>
