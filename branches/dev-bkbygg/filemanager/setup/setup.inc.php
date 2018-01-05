<?php
	/**
	* Filemanager setup
	*
	* @copyright Copyright (C) 2002-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @subpackage setup
	* @version $Id$
	*/

	$setup_info['filemanager'] = array
	(
		'name'				=> 'filemanager',
		'version'			=> '0.9.17.500',
		'app_order'			=> 10,
		'enable'			=> 1,
		'app_group'			=> 'office',
		'author'			=> 'Jason Wies (Zone), Mark A Peters (skeeter), Jonathon Sim (sim), Bettina Gille [ceb]',
		'note'				=> 'The phpGroupWare Filemanager is based on the phpWebhosting application.',
		'license'			=> 'GPL',
		'description'		=> 'phpGroupWare Filemanager. Provides upload | download | create | edit files | create directories. Supports user- and group specific access permissions.',
		'maintainer'		=> 'Bettina Gille [ceb]',
		'maintainer_email'	=> 'ceb@phpgroupware.org',
		'hooks'				=> array
		(
			'add_def_pref',
//			'admin',
			'deleteaccount',
//			'preferences',
//			'sidebox_menu'
			'menu'	=> 'filemanager.menu.get_menu'
		));

	/* Dependencies for this app to work */
	$setup_info['filemanager']['depends'][] = array
	(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);
?>
