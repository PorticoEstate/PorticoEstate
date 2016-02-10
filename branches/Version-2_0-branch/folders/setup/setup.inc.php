<?php
	/**
	* Skeleton - folders
	*
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Copyright (C) 2003,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package folders
	* @subpackage setup	
	* @version $Id$
	*/

	$setup_info['folders']['name']      = 'folders';
	$setup_info['folders']['title']     = 'Folders';
	$setup_info['folders']['version']   = '0.9.17.000';
	$setup_info['folders']['app_order'] = 26;
	$setup_info['folders']['enable']    = 2;
	$setup_info['folders']['app_group']	= 'accessories';

	$setup_info['folders']['author']           = 'probusiness AG';
	$setup_info['folders']['note']             = 'The Folder Preferences manages user level control';
	$setup_info['folders']['license']          = 'GPL';
	$setup_info['folders']['description']      = 'The Folder List view has a tree structure for navigation between application categories';
	$setup_info['folders']['maintainer']       = 'Philipp Kamps (fips)';
	$setup_info['folders']['maintainer_email'] = 'fips@phpgroupware.org';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['folders']['hooks']['getFolderContent'] = 'folders.folders_service.getFolderContent';

	/* Dependacies for this app to work */
/*
	$setup_info['folders']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.13','0.9.14','0.9.15','0.9.16')
	);

	$setup_info['folders']['depends'][] = array(
		 'appname' => 'admin',
		 'versions' => Array('0.9.13','0.9.14','0.9.15','0.9.16')
	);

	$setup_info['folders']['depends'][] = array(
		 'appname' => 'preferences',
		 'versions' => Array('0.9.13','0.9.14','0.9.15','0.9.16')
	);
*/
?>
