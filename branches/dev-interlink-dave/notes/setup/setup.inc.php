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

	$setup_info['notes']['name']      = 'notes';
	$setup_info['notes']['version']   = '0.9.15.002';
	$setup_info['notes']['app_order'] = 8;
	$setup_info['notes']['tables']    = array('phpgw_notes');
	$setup_info['notes']['enable']    = 1;
	$setup_info['notes']['app_group']	= 'office';
	$setup_info['notes']['description'] = 'Notes and short texts can go in here';

	 $setup_info['notes']['author'][] = array
	(
		'name'  => 'Andy Holman (LoCdOg)'
	);

	$setup_info['notes']['author'][] = array
	(
		'name'	=> 'Bettina Gille',
		'email'	=> 'ceb@phpgroupware.org'
	);

	$setup_info['notes']['maintainer'] = array
	(
		'name'	=> 'Bettina Gille',
		'email'	=> 'ceb@phpgroupware.org'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['notes']['hooks'] = array
	(
		'add_def_pref',
		'deleteaccount',
		'help',
		'menu'	=> 'notes.menu.get_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['notes']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['notes']['depends'][] = array(
		'appname'  => 'admin',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['notes']['depends'][] = array(
		'appname'  => 'preferences',
		'versions' => Array('0.9.17', '0.9.18')
	);

