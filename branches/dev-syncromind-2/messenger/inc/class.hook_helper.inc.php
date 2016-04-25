<?php
	/**
	 * messenger - Hook helper
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package messenger
	 * @version $Id: class.hook_helper.inc.php 8281 2011-12-13 09:24:03Z sigurdne $
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
	 * @package messenger
	 */
	class messenger_hook_helper
	{

		/**
		 * Add welkome message to new users
		 *
		 * @return void
		 */
		public function add_welkome_message( $data )
		{
			$message['to'] = $data['account_lid'];
			$message['subject'] = lang('Welcome');
			$message['content'] = $data['message'];

			$so = createobject('messenger.somessenger');
			$so->send_message($message, True);
		}
	}