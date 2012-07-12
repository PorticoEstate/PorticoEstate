<?php
	/**
	* phpGroupWare - demo: a demo application.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage setup
 	* @version $Id$
	*/

	/**
	* Update demo version from 0.9.17.000 to 0.9.17.001
	*/

	$test[] = '0.9.17.000';
	function demo_upgrade0_9_17_000()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_demo_table','category', array('type' => 'int','precision' => '4','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_demo_table','access', array('type' => 'varchar', 'precision' => '7','nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_acl_location set allow_c_attrib = 1, c_attrib_table = 'phpgw_demo_table' WHERE appname = 'demo' AND id = '.demo_location'");

		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit();
		$GLOBALS['setup_info']['demo']['currentver'] = '0.9.17.001';
		return $GLOBALS['setup_info']['demo']['currentver'];
	}
