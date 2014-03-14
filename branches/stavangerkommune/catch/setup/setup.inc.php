<?php
	/**
	* phpGroupWare - CATCH: An application for importing data from handhelds into property.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package catch
	* @subpackage catch
 	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	$setup_info['catch']['name']			= 'catch';
	$setup_info['catch']['version']			= '0.9.17.515';
	$setup_info['catch']['app_order']		= 20;
	$setup_info['catch']['enable']			= 1;
	$setup_info['catch']['globals_checked']	= True;
	$setup_info['catch']['app_group']		= 'office';

	$setup_info['catch']['author'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['catch']['maintainer'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);

	$setup_info['catch']['license']  = 'GPL';
	$setup_info['catch']['description'] =
	'<div align="left">
		<b>catch</b> application:
		Data import from mobile devices
	</div>';

	$setup_info['catch']['note'] =
		'Notes for the catch goes here';

	$setup_info['catch']['tables'] = array(
		'fm_catch',
		'fm_catch_category',
		'fm_catch_lookup',
		'fm_catch_history',
		'fm_catch_config_type',
		'fm_catch_config_attrib',
		'fm_catch_config_choice',
		'fm_catch_1_1',
	//	'fm_catch_2_1'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['catch']['hooks'] = array
	(
		'manual',
		'preferences',
		'admin',
		'help',
		'menu'	=> 'catch.menu.get_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['catch']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);
	$setup_info['catch']['depends'][] = array
	(
		'appname'  => 'property',
		'versions' => Array('0.9.17', '0.9.18')
	);

