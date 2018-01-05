<?php
	/**
	* Notes
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package notes
	* @version $Id$
	* @internal $Source$
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
		$file = array
		(
			'Global Categories' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'notes', 'global_cats' => 'true') )
		);
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
