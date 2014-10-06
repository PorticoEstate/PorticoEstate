<?php
	/**
	* This simply loads up additional utility libraries
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage utilities
	* @version $Id$
	*/

	$d1 = strtolower(substr(PHPGW_API_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp')
	{
		echo 'Failed attempt to break in via an old Security Hole!<br>' . "\n";
		exit;
	}
	unset($d1);


	/**
	* This simply loads up additional utility libraries
	* 
	* @package phpgwapi
	* @subpackage utilities
	*/
	class utilities
	{
		var $rssparser;
		var $clientsniffer;
		var $http;
		var $matrixview;
		var $menutree;
		var $sbox;

		function utilities_()
		{
			//      $GLOBALS['phpgw']->rssparser = createObject("phpgwapi.rssparser");
			//      $GLOBALS['phpgw']->clientsniffer = createObject("phpgwapi.clientsniffer");
			//      $GLOBALS['phpgw']->http = createObject("phpgwapi.http");
			//     $GLOBALS['phpgw']->matrixview = createObject("phpgwapi.matrixview");
			//     $GLOBALS['phpgw']->menutree = createObject("phpgwapi.menutree");
			$GLOBALS['phpgw']->sbox = createObject('phpgwapi.portalbox');
		}
	}
?>
