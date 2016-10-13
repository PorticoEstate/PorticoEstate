<?php
	/**
	 * phpGroupWare - eventplanner.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package eventplanner
	 * @subpackage setup
	 * @version $Id: tables_update.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
	 */
	/**
	 * Update eventplanner version from 0.9.17.000 to 0.9.17.001
	 */
	$test[] = '0.9.18.001';

	function eventplanner_upgrade0_9_18_001()
	{
		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['eventplanner']['currentver'] = '0.9.18.002';
		}
		return $GLOBALS['setup_info']['eventplanner']['currentver'];
	}
