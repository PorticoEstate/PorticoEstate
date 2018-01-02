<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage setup
 	* @version $Id$
	*/


	/**
	 * Description
	 * @package hrm
	 */

//	$app_id = $GLOBALS['phpgw']->applications->name2id('hrm');
	$GLOBALS['phpgw_setup']->oProc->query("SELECT app_id FROM phpgw_applications WHERE app_name = 'hrm'");
	$GLOBALS['phpgw_setup']->oProc->next_record();
	$app_id = $GLOBALS['phpgw_setup']->oProc->f('app_id');

	$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_locations where app_id = {$app_id} AND name != 'run'");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.', 'Top')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.user', 'User',1)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.job', 'Job description')");


