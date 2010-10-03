<?php
	/**
	* Trouble Ticket System - Setup
	*
	* @copyright Copyright (C) 2001-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage setup
	* @version $Id$
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
/*
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_tts_tickets',array(),'ticket_platform');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_tts_tickets',array(),'ticket_attachment');
*/

		$GLOBALS['phpgw_setup']->oProc->query("SELECT * FROM phpgw_tts_tickets");
		$tickets = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$tickets[] = array
			(
				'id'			=> (int)$GLOBALS['phpgw_setup']->oProc->f('ticket_id'),
				'group'			=> (int)$GLOBALS['phpgw_setup']->oProc->f('ticket_group'),
				'owner'			=> (int)$GLOBALS['phpgw_setup']->oProc->f('ticket_owner'),
				'assignedto'	=> (int)$GLOBALS['phpgw_setup']->oProc->f('ticket_assignedto'),
				'category'		=> (int)$GLOBALS['phpgw_setup']->oProc->f('ticket_category'),
				'deadline'		=> (int)$GLOBALS['phpgw_setup']->oProc->f('ticket_deadline'),
			);
		}


		$GLOBALS['phpgw_setup']->oProc->m_odb->transaction_begin();
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_tts_tickets',array(),'ticket_group');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_group',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));


		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_tts_tickets',array(),'ticket_owner');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_owner',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_tts_tickets',array(),'ticket_assignedto');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_assignedto',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_tts_tickets',array(),'ticket_category');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_category',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => True
		));

		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_tts_tickets',array(),'ticket_deadline');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_tts_tickets','ticket_deadline',array(
			'type' => 'int',
			'precision' => '4',
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

		foreach($tickets as $ticket)
		{
			$sql = "UPDATE phpgw_tts_tickets SET ticket_group = {$ticket['group']},"
			. "ticket_owner = {$ticket['owner']},"
			. "ticket_assignedto = {$ticket['assignedto']},"
			. "ticket_category = {$ticket['category']},"
			. "ticket_deadline = {$ticket['deadline']} "
			. "WHERE ticket_id = {$ticket['id']}";

			$GLOBALS['phpgw_setup']->oProc->query($sql,__LINE__,__FILE__);		
		}

		if($GLOBALS['phpgw_setup']->oProc->m_odb->transaction_commit())
		{
			$GLOBALS['setup_info']['tts']['currentver'] = '0.9.17.500';
			return $GLOBALS['setup_info']['tts']['currentver'];
		}
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
