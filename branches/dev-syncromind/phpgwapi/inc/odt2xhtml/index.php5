<?php
/***
 * class PHP odt2xhtml : file index ... necessary to include !
 * Copyright (C) 2006  Stephane HUC
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * Contact information:
 *   Stephane HUC
 *   <devs@stephane-huc.net>
 *
 ***/
$hosted = array (
	'dev.stephane-huc.net',
	'odt2xhtml.eu.org',
);

$included = array (
	'config.php5',
//	'pclzip/pclzip.lib.php',
	'php5/mssg.php5',
	'php5/odt2xhtml.php5',
);

if(in_array($_SERVER['HTTP_HOST'],$hosted)) error_reporting(E_ALL);
else error_reporting(0);

if(class_exists('XSLTProcessor')) {
	switch(PHP_OS) {
		case 'WINNT' : define('ODT2XHTML_ROOT',str_replace('\index.php5', '', __FILE__)); break; // path to this script ! 
		default: define('ODT2XHTML_ROOT',str_replace('/index.php5', '', __FILE__)); break; // path to this script !
	}
	
	define('ODT2XHTML_INDEX_OWNER', fileowner(__FILE__));
	define('ODT2XHTML_XSL_ROOT',ODT2XHTML_ROOT.'/xsl');
	define('ODT2XHTML_PHPVERSION', phpversion());
	define('ODT2XHTML_MEM', ini_get('memory_limit') );
	define('ODT2XHTML_MAX_TIME', ini_get('max_execution_time') );
	if(fileowner(ODT2XHTML_ROOT.'/VERSION')==ODT2XHTML_INDEX_OWNER) {
		define('ODT2XHTML_VERSION',trim(file_get_contents(ODT2XHTML_ROOT.'/VERSION')));	// version d'ODT2XHTML
	}

	foreach($included as $v) {
		$file = ODT2XHTML_ROOT.'/'.$v;
		if(file_exists($file) && is_file($file) && (fileowner($file)==ODT2XHTML_INDEX_OWNER)) {
			require_once($file);
			if(ODT2XHTML_DEBUG==true) echo '<p>file: '.$file.' included!</p>';
		}
		unset($file); 
	}	
}
else die('<p style="color:red;">Your WebHosting not support correctly the XSL Transformation in PHP5! Needed PHP-XSL Library ...</p>');