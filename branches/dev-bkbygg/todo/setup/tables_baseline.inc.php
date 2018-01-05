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
		'todo' => array(
			'fd' => array(
				'todo_id' => array('type' => 'auto', 'nullable' => false),
				'todo_owner' => array('type' => 'varchar', 'precision' => 25),
				'todo_access' => array('type' => 'varchar', 'precision' => 10),
				'todo_des' => array('type' => 'text'),
				'todo_pri' => array('type' => 'int', 'precision' => 4),
				'todo_status' => array('type' => 'int', 'precision' => 4),
				'todo_datecreated' => array('type' => 'int', 'precision' => 4),
				'todo_datedue' => array('type' => 'int', 'precision' => 4)
			),
			'pk' => array('todo_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>
