<?php
	/**
	* frontend
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package frontend
	* @subpackage setup
	* @version $Id: default_records.inc.php 11377 2013-10-18 08:25:54Z sigurdne $
	*/

	$GLOBALS['phpgw']->locations->add('.', 'top', 'frontend', false);
	$GLOBALS['phpgw']->locations->add('.ticket', 'helpdesk', 'frontend', false);
	$GLOBALS['phpgw']->locations->add('.rental.contract', 'contract_internal', 'frontend', false);
	$GLOBALS['phpgw']->locations->add('.rental.contract_in','contract_in','frontend', false);
	$GLOBALS['phpgw']->locations->add('.rental.contract_ex','contract_ex','frontend', false);
	$GLOBALS['phpgw']->locations->add('.document.drawings', 'drawings', 'frontend', false);
	$GLOBALS['phpgw']->locations->add('.document.pictures', 'pictures', 'frontend', false);
	$GLOBALS['phpgw']->locations->add('.document.contracts','contract_documents','frontend', false);
	$GLOBALS['phpgw']->locations->add('.property.maintenance', 'maintenance', 'frontend', false);
	$GLOBALS['phpgw']->locations->add('.property.refurbishment', 'refurbishment', 'frontend', false);
	$GLOBALS['phpgw']->locations->add('.property.services', 'services', 'frontend', false);
	$GLOBALS['phpgw']->locations->add('.delegates', 'delegates', 'frontend', false);

