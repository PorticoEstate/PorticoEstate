<?php

	/**
	 * phpGroupWare - registration
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package registration
	 * @version $Id: class.bodimb_role_user.inc.php 16604 2017-04-20 14:53:00Z sigurdne $
	 */
	class property_bosubstitute
	{

		var $public_functions = array
			(
		);

		function __construct()
		{
			$this->account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->so = CreateObject('property.sosubstitute');
			$this->allrows = $this->bo->allrows;
		}

		public function read( $data )
		{
			static $users = array();
			$values = $this->so->read($data);

			foreach ($values as &$entry)
			{
				if ($entry['user_id'])
				{
					if (!$entry['user'] = $users[$entry['user_id']])
					{
						$entry['user'] = $GLOBALS['phpgw']->accounts->get($entry['user_id'])->__toString();
						$users[$entry['user_id']] = $entry['user'];
					}
				}
				if ($entry['substitute_user_id'])
				{
					if (!$entry['substitute'] = $users[$entry['substitute_user_id']])
					{
						$entry['substitute'] = $GLOBALS['phpgw']->accounts->get($entry['substitute_user_id'])->__toString();
						$users[$entry['substitute_user_id']] = $entry['substitute'];
					}
				}

			}

			return $values;
		}

		public function delete( $data )
		{
			return $this->so->delete($data);
		}

		public function edit( $data )
		{
			$values = $this->so->edit($data);
			return $values;
		}

		public function update_substitute( $user_id, $substitute_user_id )
		{
			return $this->so->update_substitute($user_id, $substitute_user_id);
		}

		public function get_substitute( $user_id)
		{
			return $this->so->get_substitute($user_id);
		}
	}