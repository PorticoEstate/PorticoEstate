<?php
	/**
	* phpGroupWare - helpdesk: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package helpdesk
	* @subpackage setup
 	* @version $Id: default_records.inc.php 6689 2010-12-21 14:23:40Z sigurdne $
	*/


	/**
	 * Description
	 * @package helpdesk
	 */

//$app_id = $GLOBALS['phpgw']->applications->name2id('helpdesk');

$GLOBALS['phpgw_setup']->oProc->query("SELECT app_id FROM phpgw_applications WHERE app_name = 'helpdesk'");
$GLOBALS['phpgw_setup']->oProc->next_record();
$app_id = $GLOBALS['phpgw_setup']->oProc->f('app_id');

#
#  phpgw_locations
#

$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant) VALUES ({$app_id}, '.', 'Top', 1)");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.admin', 'Admin')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr, allow_grant, allow_c_function, allow_c_attrib, c_attrib_table) VALUES ({$app_id}, '.ticket', 'Helpdesk', 1, 1, 1, 'phpgw_helpdesk_tickets')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.ticket.order', 'Helpdesk ad hock order')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.custom', 'Custom reports')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.ticket.response_template', 'Ticket response template')");
$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_locations (app_id, name, descr) VALUES ({$app_id}, '.email_out', 'email out')");

$GLOBALS['phpgw_setup']->oProc->query("DELETE from phpgw_config WHERE config_app='helpdesk'");
