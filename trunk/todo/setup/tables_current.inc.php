<?php
	/**
	* Todo - setup
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @subpackage setup
	* @version $Id$
	*/

	$phpgw_baseline = array(
		'phpgw_todo' => array(
			'fd' => array(
				'todo_id' => array('type' => 'auto', 'nullable' => false),
				'todo_id_parent' => array('type' => 'int', 'precision' => 4, 'default' => 0, 'nullable' => false),
				'todo_id_main' => array('type' => 'int', 'precision' => 4, 'default' => 0, 'nullable' => false),
				'todo_level' => array('type' => 'int', 'precision' => 2, 'default' => 0, 'nullable' => false),
				'todo_owner' => array('type' => 'int', 'precision' => 4, 'default' => 0, 'nullable' => false),
				'todo_access' => array('type' => 'varchar', 'precision' => 7),
				'todo_cat' => array('type' => 'int', 'precision' => 4, 'default' => 0, 'nullable' => false),
				'todo_title' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
				'todo_des' => array('type' => 'text'),
				'todo_pri' => array('type' => 'int', 'precision' => 4),
				'todo_status' => array('type' => 'int', 'precision' => 4),
				'todo_datecreated' => array('type' => 'int', 'precision' => 4),
				'todo_startdate' => array('type' => 'int', 'precision' => 4),
				'todo_enddate' => array('type' => 'int', 'precision' => 4, 'default' => 0, 'nullable' => false),
				'todo_assigned' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
				'assigned_group' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
				'entry_date' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			),
			'pk' => array('todo_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
