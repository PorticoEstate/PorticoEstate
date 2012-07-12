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
	phpgw::import_class('phpgwapi.contacts_sql');

	/**
	* Include shared methods
	*/
	phpgw::import_class('phpgwapi.contacts_shared');
