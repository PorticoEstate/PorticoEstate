<?php
	/**
	* Setup
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage setup
	* @version $Id: default_records.inc.php 4175 2009-11-22 14:00:45Z sigurd $
	*/

	$GLOBALS['phpgw']->locations->add('org_person', "Allow custom fields on relation org_person", 'addressbook', false, 'phpgw_contact_org_person');
	$GLOBALS['phpgw']->locations->add('person', "Allow custom fields on table person", 'addressbook', false, 'phpgw_contact_person');
	$GLOBALS['phpgw']->locations->add('organisation', "Allow custom fields on table org", 'addressbook', false, 'phpgw_contact_org');

