<?php
	/**
	* Filemanager - Sidebox-Menu for iDots Template
	*
	* This hookfile is for generating an app-specific side menu used in the idots template set.
	* $menu_title speaks for itself
	* $file is the array with link to app functions
	* display_sidebox can be called as much as you like
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @author Pim Snel <pim@lingewoud.nl>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @version $Id$
	* @internal $Source$
	*/


	{
		$appname = 'filemanager';
		$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');

		$file[] = array('text'	=> 'Preferences',
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'filemanager.uifilemanager.preferences')));

		display_sidebox($appname,$menu_title,$file);
	}
?>
