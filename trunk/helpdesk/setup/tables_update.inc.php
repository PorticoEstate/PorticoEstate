<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package helpdesk
	* @subpackage setup
	 * @version $Id: tables_update.inc.php 15163 2016-05-13 14:24:03Z sigurdne $
	*/
	/**
	* Update helpdesk version from 0.9.18.000 to 0.9.18.001
	*/
	$test[] = '0.9.18.000';

	function helpdesk_upgrade0_9_18_000()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_helpdesk_tickets', 'modified_date', array(
			'type' => 'int',
			'precision' => 8,
			'nullable' => True)
			);

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['helpdesk']['currentver'] = '0.9.18.001';
			return $GLOBALS['setup_info']['helpdesk']['currentver'];
		}
	}
