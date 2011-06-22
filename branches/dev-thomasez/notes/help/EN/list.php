<?php
	/**
	* Notes - User manual
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package notes
	* @subpackage manual
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

	$GLOBALS['phpgw_info']['flags'] = Array
	(
		'headonly'		=> True,
		'currentapp'	=> 'notes'
	);

	/**
	 * Include phpgroupware header
	 */
	include('../../../header.inc.php');

	$GLOBALS['phpgw']->help = CreateObject('phpgwapi.help_helper');
	$GLOBALS['phpgw']->help->set_params(array('app_name'	=> 'notes',
												'title'		=> lang('notes') . ' - ' . lang('list'),
												'controls'	=> array()));
	$values['list']	= array
	(
		'list_img'	=> $GLOBALS['phpgw']->common->image('notes','help_list'),
		'item_1'	=> 'The single arrow moves backward one page, while the double arrows move to the first page.',
		'item_2'	=> 'The single arrow moves forward one page, while the double arrows move to the last page.',
		'item_3'	=> 'Select which catagory of notes you want to display.',
		'item_4'	=> 'Type in a keyword to find a note with that specific word in it.',
		'item_5'	=> 'The note: it shows the date entered and the begining few words of the note.',
		'item_6'	=> 'View: Lets you view the complete note.',
		'item_7'	=> 'Edit: you can edit the note.',
		'item_8'	=> 'Delete: Delete the note.',
		'item_9'	=> 'Add Note: Brings up a screen to add a new note.'
	);

	$GLOBALS['phpgw']->help->xdraw($values);
	$GLOBALS['phpgw']->xslttpl->set_var('phpgw',$GLOBALS['phpgw']->help->output);
?>
