<?php 
	/**
	* Translation class loader
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id: class.translation.inc.php,v 1.8 2004/12/30 06:47:31 skwashd Exp $
	*/

	if (empty($GLOBALS['phpgw_info']['server']['translation_system']))
	{
		$GLOBALS['phpgw_info']['server']['translation_system'] = 'sql';
	}
	/**
	* Include translation class
	*/
	include(PHPGW_API_INC.'/class.translation_sql.inc.php'); 
?>
