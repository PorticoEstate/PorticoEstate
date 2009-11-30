<?php
	/**
	* Notes Preferences
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package notes
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

	{
// Only Modify the $file and $title variables.....
		$file = Array
		(
			'Grant Access'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uiadmin_acl.aclprefs','acl_app'=>$appname)),
			'Edit categories' => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uicategories.index','cats_app'=>'notes','cats_level'=>true,'global_cats'=>true))
		);
// Do not modify below this line
		display_section($appname,$file);
	}
?>
