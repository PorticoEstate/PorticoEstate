<?php
	/**
	* phpGroupWare - frontend: a simplified tool for end users.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package frontend
	* @subpackage setup
 	* @version $Id: tables_update.inc.php 11377 2013-10-18 08:25:54Z sigurdne $
	*/

	/**
	* Update frontend version from 0.1 to 0.2
	* Add locations as placeholders for functions and menues
	* 
	*/

	$test[] = '0.1';
	function frontend_upgrade0_1()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw']->locations->add('.', 'top', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.ticket', 'helpdesk', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.rental.contract', 'contract_internal', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.document.drawings', 'drawings', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.document.pictures', 'pictures', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.property.maintenance', 'maintenance', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.property.refurbishment', 'refurbishment', 'frontend', false);
		$GLOBALS['phpgw']->locations->add('.property.services', 'services', 'frontend', false);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['frontend']['currentver'] = '0.2';
			return $GLOBALS['setup_info']['frontend']['currentver'];
		}
	}
	
	/**
	* Update frontend version from 0.2 to 0.3
	* Add new location as placeholders for functions and menues
	* 
	*/
	$test[] = '0.2';
	function frontend_upgrade0_2()
	{
		$GLOBALS['phpgw']->locations->add('.rental.contract_in','contract_in','frontend', false);
		$GLOBALS['setup_info']['frontend']['currentver'] = '0.3';
		return $GLOBALS['setup_info']['frontend']['currentver'];
	}
	
	/**
	* Update frontend version from 0.3 to 0.4
	* Add new location as placeholders for functions and menues
	* 
	*/
	$test[] = '0.3';
	function frontend_upgrade0_3()
	{
		$GLOBALS['phpgw']->locations->add('.rental.contract_ex','contract_ex','frontend', false);
		$GLOBALS['setup_info']['frontend']['currentver'] = '0.4';
		return $GLOBALS['setup_info']['frontend']['currentver'];
	}
	
	/**
	* Update frontend version from 04 to 0.5
	* Add new location as placeholders for functions and menues
	* 
	*/
	$test[] = '0.4';
	function frontend_upgrade0_4()
	{
		$GLOBALS['phpgw']->locations->add('.document.contracts','contract_documents','frontend', false);
		$GLOBALS['setup_info']['frontend']['currentver'] = '0.5';
		return $GLOBALS['setup_info']['frontend']['currentver'];
	}
