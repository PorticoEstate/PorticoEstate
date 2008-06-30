<?php
	/**
	* Trouble Ticket System - Setup
	*
	* @copyright Copyright (C) 2001,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package tts
	* @subpackage setup
	* @version $Id$
	*/

	$phpgw_baseline = array(
		'ticket' => array(
			'fd' => array(
				't_id'                => array('type' => 'auto', 'nullable' => False),
				't_category'          => array('type' => 'varchar', 'precision' => 40, 'nullable' => True),
				't_detail'            => array('type' => 'text', 'nullable' => True),
				't_priority'          => array('type' => 'int', 'precision' => 2, 'nullable' => False),
				't_user'              => array('type' => 'varchar', 'precision' => 10, 'nullable' => True),
				't_assignedto'        => array('type' => 'varchar', 'precision' => 10, 'nullable' => True),
				't_timestamp_opened'  => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				't_timestamp_closed'  => array('type' => 'int', 'precision' => 4, 'nullable' => False),
				't_subject'           => array('type' => 'varchar', 'precision' => 255, 'nullable' => True),
				't_department'        => array('type' => 'varchar', 'precision' => 25, 'nullable' => True),
				't_watchers'          => array('type' => 'text', 'nullable' => True)
			),
			'pk' => array('t_id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'category' => array(
			'fd' => array(
				'c_id'         => array('type' => 'auto', 'nullable' => False),
				'c_department' => array('type' => 'varchar', 'precision' => 25, 'nullable' => True),
				'c_name'       => array('type' => 'varchar', 'precision' => 40, 'nullable' => True)
			),
			'pk' => array('c_id'),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		),
		'department' => array(
			'fd' => array(
				'd_name' => array('type' => 'varchar', 'precision' => 25, 'nullable' => True)
			),
			'pk' => array(),
			'ix' => array(),
			'fk' => array(),
			'uc' => array()
		)
	);
