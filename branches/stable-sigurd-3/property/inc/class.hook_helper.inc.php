<?php
	/**
	 * property - Hook helper
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package property
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


	/**
	 * Hook helper
	 *
	 * @package property
	 */
	class property_hook_helper
	{
		/**
		 * Clear ACL-based userlists
		 *
		 * @return void
		 */
		public function clear_userlist()
		{
			$cleared = ExecMethod('property.bocommon.reset_fm_cache_userlist');
			$receipt['message'][] = array('msg' => lang('%1 userlists cleared from cache',$cleared));
			phpgwapi_cache::session_set('phpgwapi', 'phpgw_messages', $receipt);
		}
	}
