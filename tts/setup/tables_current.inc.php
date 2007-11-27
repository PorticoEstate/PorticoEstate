<?php
	/**
	* Trouble Ticket System - Setup
	*
	* @copyright Copyright (C) 2001-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage setup
	* @version $Id: tables_current.inc.php 17766 2006-12-26 12:54:44Z skwashd $
	*/

	$phpgw_baseline = array(
		'phpgw_tts_tickets' => array(
			'fd' => array(
				'ticket_id' => array('type' => 'auto','nullable' => False),
				'ticket_group' => array('type' => 'int','precision' => '8','nullable' => True),
				'ticket_priority' => array('type' => 'int','precision' => '2','nullable' => False),
				'ticket_owner' => array('type' => 'int','precision' => '8','nullable' => True),
				'ticket_assignedto' => array('type' => 'int','precision' => '4','nullable' => True),
				'ticket_subject' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'ticket_category' => array('type' => 'int','precision' => '8','nullable' => True),
				'ticket_billable_hours' => array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => False),
				'ticket_billable_rate' => array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => False),
				'ticket_status' => array('type' => 'char','precision' => '1','nullable' => False),
				'ticket_details' => array('type' => 'text','nullable' => False),
				'ticket_type' => array('type' => 'int','precision' => '2','nullable' => False),
				'ticket_deadline' => array('type' => 'int','precision' => '8','nullable' => False),
				'ticket_effort' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'ticket_lastmod' => array('type' => 'int','precision' => '8','nullable' => False),
				'ticket_lastmod_user' => array('type' => 'int','precision' => '8','nullable' => False),
				'ticket_request_note' => array('type' => 'varchar','precision' => '255','nullable' => True)
			),
			'pk' => array('ticket_id'),
			'fk' => array(),
			'ix' => array('ticket_group','ticket_owner','ticket_assignedto','ticket_subject','ticket_category','ticket_status','ticket_deadline'),
			'uc' => array()
		),
		'phpgw_tts_views' => array(
			'fd' => array(
				'view_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'view_account_id' => array('type' => 'varchar','precision' => '40','nullable' => True),
				'view_time' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array(),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'phpgw_tts_email_map' => array(
			'fd' => array(
				'tts_email_map_id' => array('type' => 'auto','nullable' => False),
				'api_handler_id' => array('type' => 'int','precision' => '8','nullable' => False),
				'tts_cat_id' => array('type' => 'varchar','precision' => '8','nullable' => False),
				'is_active' => array('type' => 'varchar','precision' => '4','nullable' => False)
			),
			'pk' => array('tts_email_map_id'),
			'fk' => array(),
			'ix' => array('api_handler_id','tts_cat_id','is_active'),
			'uc' => array()
		)
	);
