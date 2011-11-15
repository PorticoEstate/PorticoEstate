<?php
	/**
	* phpGroupWare - bim: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package bim
	* @subpackage setup
 	* @version $Id: tables_update.inc.php 6982 2011-02-14 20:01:17Z sigurdne $
	*/

	/**
	* Update bim version from 0.9.17.500 to 0.9.17.501
	*/
	$test[] = '0.9.17.500';
	function bim_upgrade0_9_17_500()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_type','location_id',array('type' => 'int','precision' => 4,'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_type','is_ifc',array('type' => 'int','precision' => 2,'default' => 1,'nullable' => True));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','p_location_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','p_id', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','location_code', array('type' => 'varchar','precision' => '20','nullable' => true));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','address', array('type' => 'varchar','precision' => '150','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','entry_date', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('fm_bim_item','user_id', array('type' => 'int','precision' => '4','nullable' => True));
		
		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['bim']['currentver'] = '0.9.17.501';
			return $GLOBALS['setup_info']['bim']['currentver'];
		}
	}

