<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package bim
	* @subpackage setup
 	* @version $Id: setup.inc.php 6982 2011-02-14 20:01:17Z sigurdne $
	*/

	$setup_info['bim']['name']			= 'bim';
	$setup_info['bim']['version']		= '0.9.17.506';
	$setup_info['bim']['app_order']		= 8;
	$setup_info['bim']['enable']		= 1;
	$setup_info['bim']['app_group']		= 'office';

	$setup_info['bim']['author'] = array
	(
		'name'	=> 'Petur Bjørn Thorsteinsson',
		'email'	=> 'petur-bjorn.Thorsteinsson@capgemini.com'
	);

	$setup_info['bim']['maintainer'] = array
	(
		'name'	=> 'Sigurd Nes',
		'email'	=> 'sigurdne@online.no'
	);


	$setup_info['bim']['tables'] = array
	(
		'fm_bim_type',
		'fm_bim_model',
		'fm_bim_item',
		'fm_bim_item_inventory'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['bim']['hooks'] = array
	(
		'manual',
		'settings',
		'preferences',
		'help',
		'menu'	=> 'bim.menu.get_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['bim']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['bim']['depends'][] = array
	(
		'appname'  => 'admin',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['bim']['depends'][] = array
	(
		'appname'  => 'preferences',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['bim']['depends'][] = array
	(
		'appname'  => 'property',
		'versions' => Array('0.9.17', '0.9.18')
	);
