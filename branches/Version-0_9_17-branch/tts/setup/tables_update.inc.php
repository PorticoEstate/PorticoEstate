<?php
	/**
	* Trouble Ticket System - Setup
	*
	* @copyright Copyright (C) 2001-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage setup
	* @version $Id: tables_update.inc.php 17766 2006-12-26 12:54:44Z skwashd $
	*/

	/* This is since the last release */
	$test[] = '0.9.16';
	
	/**
	 * Upgrade from 0.9.16 to 0.9.16.001
	 * 
	 * @return string New version
	 */
	function tts_upgrade0_9_16()
	{
		global $setup_info,$phpgw_setup;
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_type',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true
			));
			
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_deadline',array(
			'type' => 'varchar',
			'precision' => 10,
			'nullable' => true
			));
		
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_effort',array(
			'type' => 'varchar',
			'precision' => 4,
			'nullable' => true
			));
			
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_platform',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => true
			));

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_attachment',array(
			'type' => 'int',
			'precision' => 2,
			'nullable' => false
			));
		
		$setup_info['tts']['currentver'] = '0.9.16.001';
		return $setup_info['tts']['currentver'];
	}

	$test[] = '0.9.16.001';
	function tts_upgrade0_9_16_001() {
		global $setup_info,$phpgw_setup;

		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_lastmod',array(
			'type' => 'int',
			'precision' => 4,
			'nullable' => false
			));

		$setup_info['tts']['currentver'] = '0.9.16.002';
		return $setup_info['tts']['currentver'];
	}



	$test[] = '0.9.16.002';
	function tts_upgrade0_9_16_002()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_tts_tickets',array(
			'fd' => array(
				'ticket_id' => array('type' => 'auto','nullable' => False),
				'ticket_group' => array('type' => 'varchar','precision' => '40','nullable' => True),
				'ticket_priority' => array('type' => 'int','precision' => '2','nullable' => False),
				'ticket_owner' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'ticket_assignedto' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'ticket_subject' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'ticket_category' => array('type' => 'varchar','precision' => '25','nullable' => True),
				'ticket_billable_hours' => array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => False),
				'ticket_billable_rate' => array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => False),
				'ticket_status' => array('type' => 'char','precision' => '1','nullable' => False),
				'ticket_details' => array('type' => 'text','nullable' => False),
				'ticket_type' => array('type' => 'int','precision' => '2','nullable' => False),
				'ticket_deadline' => array('type' => 'varchar','precision' => '10','nullable' => False),
				'ticket_effort' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'ticket_attachment' => array('type' => 'int','precision' => '2','nullable' => False),
				'ticket_lastmod' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('ticket_id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),'ticket_platform');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_tts_tickets',array(
			'fd' => array(
				'ticket_id' => array('type' => 'auto','nullable' => False),
				'ticket_group' => array('type' => 'varchar','precision' => '40','nullable' => True),
				'ticket_priority' => array('type' => 'int','precision' => '2','nullable' => False),
				'ticket_owner' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'ticket_assignedto' => array('type' => 'varchar','precision' => '10','nullable' => True),
				'ticket_subject' => array('type' => 'varchar','precision' => '255','nullable' => True),
				'ticket_category' => array('type' => 'varchar','precision' => '25','nullable' => True),
				'ticket_billable_hours' => array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => False),
				'ticket_billable_rate' => array('type' => 'decimal','precision' => '8','scale' => '2','nullable' => False),
				'ticket_status' => array('type' => 'char','precision' => '1','nullable' => False),
				'ticket_details' => array('type' => 'text','nullable' => False),
				'ticket_type' => array('type' => 'int','precision' => '2','nullable' => False),
				'ticket_deadline' => array('type' => 'varchar','precision' => '10','nullable' => False),
				'ticket_effort' => array('type' => 'varchar','precision' => '4','nullable' => False),
				'ticket_lastmod' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('ticket_id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),'ticket_attachment');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_tts_tickets','ticket_group',array(
			'type' => 'int',
			'precision' => '8',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_tts_tickets','ticket_owner',array(
			'type' => 'int',
			'precision' => '8',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_tts_tickets','ticket_assignedto',array(
			'type' => 'int',
			'precision' => '8',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_tts_tickets','ticket_category',array(
			'type' => 'int',
			'precision' => '8',
			'nullable' => True
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_tts_tickets','ticket_deadline',array(
			'type' => 'int',
			'precision' => '8',
			'nullable' => False
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_lastmod_user',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => False
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_request_note',array(
			'type' => 'varchar',
			'precision' => '255',
			'nullable' => True
		));
		
		$GLOBALS['setup_info']['tts']['currentver'] = '0.9.17.500';
		return $GLOBALS['setup_info']['tts']['currentver'];
	}


	$test[] = '0.9.17.500';
	function tts_upgrade0_9_17_500()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_tts_email_map',array(
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
		));


		$GLOBALS['setup_info']['tts']['currentver'] = '0.9.17.501';
		return $GLOBALS['setup_info']['tts']['currentver'];
	}
?>
