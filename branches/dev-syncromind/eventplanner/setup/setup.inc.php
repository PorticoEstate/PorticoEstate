<?php
	/**
	 * phpGroupWare - eventplanner: a eventplanner application.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package eventplanner
	 * @subpackage setup
	 * @version $Id: setup.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
	 */
	$setup_info['eventplanner']['name'] = 'eventplanner';
	$setup_info['eventplanner']['version'] = '0.9.18.011';
	$setup_info['eventplanner']['app_order'] = 20;
	$setup_info['eventplanner']['enable'] = 1;
	$setup_info['eventplanner']['app_group'] = 'office';

	$setup_info['eventplanner']['author'] = array
		(
		'name' => 'Sigurd Nes',
		'email' => 'sigurdne@online.no'
	);

	$setup_info['eventplanner']['maintainer'] = array
		(
		'name' => 'Sigurd Nes',
		'email' => 'sigurdne@online.no'
	);

	$setup_info['eventplanner']['license'] = 'GPL';
	$setup_info['eventplanner']['description'] = '<div align="left">
		<b>Eventplanner</b> for cultural events:
		<ol>
			<li>Application</li>
				<ol>
					<li>Artist / vendor</li>
				</ol>
				<ol>
					<li>Institution / customer</li>
				</ol>
			<li>Tour planning</li>
		</ol>
	</div>';

	$setup_info['eventplanner']['note'] = 'Notes for the eventplanner goes here';

	$setup_info['eventplanner']['tables'] = array(
		'eventplanner_customer_category',
		'eventplanner_customer',
		'eventplanner_customer_comment',
		'eventplanner_vendor_category',
		'eventplanner_vendor',
		'eventplanner_vendor_comment',
		'eventplanner_application_type',
		'eventplanner_application',
		'eventplanner_application_comment',
		'eventplanner_calendar',
		'eventplanner_calendar_comment',
		'eventplanner_booking',
		'eventplanner_booking_comment',
		'eventplanner_booking_cost',
		'eventplanner_order',
		'eventplanner_booking_vendor_report',
		'eventplanner_booking_customer_report'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['eventplanner']['hooks'] = array(
		'config',
		'manual',
		'settings',
		'help',
		'menu' => 'eventplanner.menu.get_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['eventplanner']['depends'][] = array
		(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

