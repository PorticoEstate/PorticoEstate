<?php
	/**************************************************************************\
	 * phpGroupWare - administration                                            *
	 * http://www.phpgroupware.org                                              *
	 * --------------------------------------------                             *
	 *  This program is free software; you can redistribute it and/or modify it *
	 *  under the terms of the GNU General Public License as published by the   *
	 *  Free Software Foundation; either version 2 of the License, or (at your  *
	 *  option) any later version.                                              *
		\**************************************************************************/
	/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'		=> 'admin',
		'menu_selection'	=> 'admin::admin::phpinfo'
	);

	if (isset($_GET['noheader']) && $_GET['noheader']) {
		$GLOBALS['phpgw_info']['flags'] = array(
			'nofooter'			=> true,
			'noframework'		=> true,
			'noheader'			=> true,
			'nonavbar'			=> true,
			'currentapp'		=> 'admin',
			'menu_selection'	=> 'admin::admin::phpinfo'
		);
	}

	include_once('../header.inc.php');

	if (phpgw::get_var('noheader', 'bool', 'GET'))
	{
		$close = lang('close window');

		echo <<<HTML
				<div style="text-align: center;">
					<a href="javascript:window.close();">{$close}</a>
				</div>
HTML;

	}

	if (function_exists('phpinfo'))
	{
		/*
		* place output in iframe to avoid conficts with css
		* from https://www.php.net/manual/en/function.phpinfo.php#100809
		*/
		ob_start();
		phpinfo();
		$info = trim(ob_get_clean());           // output
		ob_end_clean();
		// Replace white space in ID and NAME attributes... if exists
		$info = preg_replace('/(id|name)(=["\'][^ "\']+) ([^ "\']*["\'])/i', '$1$2_$3', $info);

		$imp = new DOMImplementation();
		$dtd = $imp->createDocumentType(
			'html',
			'-//W3C//DTD XHTML 1.0 Transitional//EN',
			'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'
		);
		$doc = $imp->createDocument(
			'http://www.w3.org/1999/xhtml',
			'html',
			$dtd
		);

		$doc->encoding = 'utf-8';

		$info_doc = new DOMDocument('1.0', 'utf-8');
		/* Parse phpinfo's output
		* operator @ used to avoid messages about undefined entities
		* or use loadHTML instead
		*/
		$info_doc->loadXML($info);

		$doc->documentElement->appendChild( // Adding HEAD element to HTML
			$doc->importNode(
				$info_doc->getElementsByTagName('head')->item(0),
				true                         // With all the subtree
			)
		);
		$doc->documentElement->appendChild( // Adding BODY element to HTML
			$doc->importNode(
				$info_doc->getElementsByTagName('body')->item(0),
				true                         // With all the subtree
			)
		);

		// Now you get a clean output and you are able to validate...

		//echo ($doc->saveXML ());
		//      OR
		//echo ($doc->saveHTML ());

		$data = $doc->saveHTML();

		echo <<<HTML
				<iframe id="phpinfo" src="about:blank"  width="100%" height="1600" srcdoc='{$data}'>
				</iframe >

HTML;
	}
	else
	{
		$error = lang('phpinfo is not available on this system!');
		echo <<<HTML
				<div class="error"><h1>$error</h1><div>
	
HTML;
}
