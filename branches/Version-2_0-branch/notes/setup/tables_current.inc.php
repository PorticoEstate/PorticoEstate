<?php
	/**
	* Notes - Setup
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package notes
	* @subpackage setup
	* @version $Id$
	*/

	/*
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/


	$phpgw_baseline = array(
		'phpgw_notes' => array(
			'fd' => array(
				'note_id' => array('type' => 'auto','nullable' => False),
				'note_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'note_access' => array('type' => 'varchar','precision' => '7','nullable' => False),
				'note_date' => array('type' => 'int','precision' => '4','nullable' => False),
				'note_category' => array('type' => 'int','precision' => '4','nullable' => False),
				'note_content' => array('type' => 'text','nullable' => False),
				'note_lastmod' => array('type' => 'int','precision' => '8','nullable' => False)
			),
			'pk' => array('note_id'),
			'fk' => array(),
			'ix' => array('note_owner','note_access','note_date','note_category','note_lastmod'),
			'uc' => array()
		)
	);
?>
