<?php
	/**
	 * phpGroupWare - eventplanner
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package eventplanner
	 * @subpackage setup
	 * @version $Id: default_records.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
	 */
	/**
	 * Description
	 * @package eventplanner
	 */
	$GLOBALS['phpgw']->locations->add('.', 'Tom', 'eventplanner');
	$GLOBALS['phpgw']->locations->add('.admin', 'admin', 'eventplanner');
	$GLOBALS['phpgw']->locations->add('.application', 'application', 'eventplanner', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.events', 'events', 'eventplanner', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.customer', 'customer', 'eventplanner', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.vendor', 'vendor', 'eventplanner', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.calendar', 'calendar', 'eventplanner', $allow_grant = true);
	$GLOBALS['phpgw']->locations->add('.booking', 'booking', 'eventplanner', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.vendor_report', 'vendor_report', 'eventplanner', $allow_grant = true, $custom_tbl = '', $c_function = true, $c_attrib = true);
	$GLOBALS['phpgw']->locations->add('.customer_report', 'customer_report', 'eventplanner', $allow_grant = true, $custom_tbl = '', $c_function = true, $c_attrib = true);

