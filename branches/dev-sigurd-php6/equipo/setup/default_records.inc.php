<?php
	/**
	* phpGroupWare - DEMO: A demo application.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage setup
 	* @version $Id: default_records.inc.php 690 2008-02-02 10:11:33Z dave $
	*/


	/**
	 * Description
	 * @package demo
	 */
	/*
	$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl_location WHERE appname = 'demo'");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr) VALUES ('demo', '.', 'Top')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl_location (appname,id, descr, allow_grant,allow_c_attrib,c_attrib_table) VALUES ('demo', '.demo_location', 'Demo location',1,1,'phpgw_demo_table')");
	$GLOBALS['phpgw_setup']->oProc->query("SELECT max(account_id) as account_id from phpgw_accounts WHERE account_type = 'u'");
	$GLOBALS['phpgw_setup']->oProc->next_record();
	$account_id = $GLOBALS['phpgw_setup']->oProc->f('account_id');

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_demo_table (name,address,zip,town, remark,entry_date,user_id) VALUES ('demo name', 'demo address', '12345','Demo Town', 'Remark', " . time() . ", '$account_id')");
	*/
	$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_acl WHERE acl_appname = 'newdesign'");

	$GLOBALS['phpgw_setup']->oProc->query("SELECT account_id from phpgw_accounts WHERE account_lid = 'Default' AND account_type = 'g'");
	$GLOBALS['phpgw_setup']->oProc->next_record();
	$account_id = $GLOBALS['phpgw_setup']->oProc->f('account_id');
	die($account_id);
	/*
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl (acl_appname,acl_location, acl_account,acl_rights, acl_grantor) VALUES ('demo','run', '$account_id', '1', NULL)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl (acl_appname,acl_location, acl_account,acl_rights, acl_grantor) VALUES ('demo','.demo_location', '$account_id', '15', NULL)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_acl (acl_appname,acl_location, acl_account,acl_rights, acl_grantor) VALUES ('demo','.demo_location', '$account_id', '15', '$account_id')");
	unset($account_id);
	*/
