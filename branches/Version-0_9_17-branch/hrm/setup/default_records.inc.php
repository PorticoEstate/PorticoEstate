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
 	* @version $Id: default_records.inc.php 16747 2006-05-22 07:34:33Z sigurdne $
	*/


	/**
	 * Description
	 * @package hrm
	 */

	$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl_location where appname = 'hrm'");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr) VALUES ('hrm', '.', 'Top')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr, allow_grant) VALUES ('hrm', '.user', 'User',1)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr) VALUES ('hrm', '.job', 'Job description')");


