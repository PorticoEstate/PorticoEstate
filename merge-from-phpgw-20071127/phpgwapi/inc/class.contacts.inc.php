<?php
	/**
	* Contacts loader
	* @copyright Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage contacts
	*/

	//print_debug('Contact Repository:',"'".$GLOBALS['phpgw_info']['server']['contact_repository']."'",'api'); 
	/**
	* Include SQL handler
	*/
	include(PHPGW_API_INC . '/class.contacts_sql.inc.php');
	/**
	* Include shared methods
	*/
	include(PHPGW_API_INC . '/class.contacts_shared.inc.php');
?>
