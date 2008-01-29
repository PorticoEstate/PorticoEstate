<?php
/**************************************************************************\
* phpGroupWare - addressbook                                               *
* http://www.phpgroupware.org                                              *
* Written by Joseph Engo <jengo@phpgroupware.org>                          *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id: test_client.php 18147 2007-05-23 13:14:20Z Caeies $ */
/*
	$phpgw_info['flags'] = array(
		'currentapp' => 'soap',
		'noheader' => True
	);

	include('../header.inc.php');
*/
	$login  = 'anonymous';
	$passwd = 'anonymous1';

	$phpgw_info['flags'] = array(
		'disable_Template_class' => True,
		'login' => True,
		'currentapp' => 'soap',
		'noheader'  => True);

	include('../header.inc.php');
	include('./vars.php');

	$sessionid = $phpgw->session->create($login,$passwd);

	if (!$symbol)
	{
		$symbol = 'LNUX';
	}
	$soapclient = CreateObject('phpgwapi.phpgw_soapclient',"http://services.xmethods.net:80/soap");
	echo $soapclient->call("getQuote",array("symbol"=>$symbol),"urn:xmethods-delayed-quotes","urn:xmethods-delayed-quotes#getQuote");

//	$soapclient = CreateObject('phpgwapi.phpgw_soapclient','http://' . $HTTP_HOST . '/phpgroupware/soap.php');
//	echo 'response: ' . $soapclient->call("hello",array($phpgw_info['server']['site_title']),"http://soapinterop.org/ilab","http://soapinterop.org/ilab#hello");
//	echo $soapclient->call('echoString','hello',False,'echoString');
