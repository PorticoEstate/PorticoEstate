<?php
/*
 * $Horde: chora/checkout.php,v 1.7 2001/03/18 03:11:26 avsm Exp $
 *
 * Copyright 2000, 2001 Anil Madhavapeddy <anil@recoil.org>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chora',
		'noheader'                => True,
		'nonavbar'                => True,
		'enable_nextmatchs_class' => True,
		'enable_config_class'     => True
	);
	include('../header.inc.php');
	include('./config/conf.php');

	if(!isset($r))
	{
		$r = 0;
	}

	/* Check to see if the file exists */

	if (!@is_file("$fullname,v"))
	{
		fatal('404 Not Found','File Not Found: '.str_replace($cvsroot, '', $fullname));
	}

	/* Is this a valid revision being requested? */

	$_rev = CreateObject('chora.cvslib_rev');
	if(!$_rev->valid($r))
	{
		fatal('404 Not Found', "Revision Not Found: $r is not a valid RCS revision number");
	}

	/* Retrieve the actual checkout */

	$co = CreateObject('chora.cvslib_checkout');
	$checkOut = $co->get($CVS, $fullname, $r);

	/* Check error status, and either show error page, or the checkout contents */

	if (is_object($checkOut) && $checkOut->id() == CVSLIB_ERROR)
	{
		checkError($checkOut);
	} 
	else
	{
		Header('Content-Type: '.$CVS->getMimeType($fullname));
		fpassthru($checkOut);
	}
