<?php
	/**
	* Custom PHP Session management - using database storage
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage sessions
	* @version $Id$
	* @link http://php.net/session_set_save_handler
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU Lesser General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Custom PHP Session management - using database storage
	* 
	* @package phpgwapi
	* @subpackage sessions
	*/
	class phpgwapi_session_handler_db
	{
		private function __construct()
		{
			//prevent instiation
		}

		/**
		 * Close connection to session handler backend
		 *
		 * @internal does nothing for us
		 */
		public static function close()
		{
			return true;
		}

		/**
		 * Destroy a user's session
		 *
		 * @param string $id the session id to destroy
		 * @return bool was the session destroyed?
		 */
		public static function destroy($id)
		{
			$id = $GLOBALS['phpgw']->db->db_addslashes($id);

			return !!$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_sessions WHERE session_id = '{$id}'", __LINE__, __FILE__);
		}

		/**
		 * Garbage Collection - remove stale sessions out of the database
		 *
		 * @param int $max the maximum lifetime for a session
		 */
		public static function gc($max)
		{
			$timestamp = time() - (int) $max;

			return !!$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_sessions WHERE lastmodts <= {$timestamp}", __LINE__, __FILE__);
		}

		/**
		 * Get a list of currently logged in sessions
		 *
		 * @return array list of sessions
		 */
		public static function get_list()
		{
			// clean out the dead sessions
			self::gc(ini_get('session.gc_maxlifetime'));
			
			$values = array();

			$sql = 'SELECT session_id, ip, data FROM phpgw_sessions';

			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$rawdata = $GLOBALS['phpgw']->crypto->decrypt($GLOBALS['phpgw']->db->f('data', true));

				//taken from http://no.php.net/manual/en/function.session-decode.php#79244
				$vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
				$rawdata, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				$data = array();

				if(isset($vars[3]))
				{
					$data[$vars[0]]=unserialize($vars[1]);
					$data[$vars[2]]=unserialize($vars[3]);
				}

				// skip invalid or anonymous sessions
				if ( !isset($data['phpgw_session'])
					|| !isset($data['phpgw_session']['session_flags'])
					|| $data['phpgw_session']['session_flags'] == 'A' )
				{
					continue;
				}

				$values[$data['phpgw_session']['session_id']] = array
				(
					'id'		=> $data['phpgw_session']['session_id'],
					'lid'		=> $data['phpgw_session']['session_lid'],
					'ip'		=> $data['phpgw_session']['session_ip'],
					'action'	=> $data['phpgw_session']['session_action'],
					'dla'		=> $data['phpgw_session']['session_dla'],
					'logints'	=> $data['phpgw_session']['session_logintime']
				);
			}
			return $values;
		}


		/**
		 * Open connection to session handler backend
		 *
		 * @internal does nothing for us
		 */
		public static function open()
		{
			return true;
		}
		
		/**
		 * Read a session
		 *
		 * @param string $id the id of the session to retreive
		 * @return string user's session data as a serialized string - empty string if not found
		 */
		public static function read($id)
		{
			$id = $GLOBALS['phpgw']->db->db_addslashes($id);
			$sql = "SELECT data FROM phpgw_sessions WHERE session_id = '{$id}'";

			if ( isset($GLOBALS['phpgw_info']['server']['sessions_checkip'])
				&& $GLOBALS['phpgw_info']['server']['sessions_checkip'] )
			{
				$ip = phpgw::get_var('REMOTE_ADDR', 'ip', 'SERVER');
				$sql .= " AND ip = '{$ip}'";
			}

			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
			if ( $GLOBALS['phpgw']->db->next_record() )
			{
			//	return $GLOBALS['phpgw']->crypto->decrypt($GLOBALS['phpgw']->db->f('data', true));
				return unserialize($GLOBALS['phpgw']->db->f('data', true));
			}
			return '';
		}

		/**
		 * Write session data to the database
		 *
		 * @param string $id the id of the session to retreive
		 * @param string $data the session data to store (serialised data)
		 * @return bool was the session written to the database?
		 */
		public static function write($id, $data)
		{
			$db 	= & $GLOBALS['phpgw']->db;
		//	$crypto = & $GLOBALS['phpgw']->crypto;

			$id   = $db->db_addslashes($id);
		//	$data = $db->db_addslashes($crypto->encrypt($data));
			$data = $db->db_addslashes(serialize($data));
			$ts   = time();

			$db->query("SELECT session_id FROM phpgw_sessions WHERE session_id = '{$id}'", __LINE__, __FILE__);
			if ( $db->next_record() )
			{
				$sql = 'UPDATE phpgw_sessions'
					. " SET data = '{$data}', lastmodts = {$ts}"
					. " WHERE session_id = '{$id}'";
			}
			else
			{
				$ip = phpgw::get_var('REMOTE_ADDR', 'ip', 'SERVER');
				$sql = "INSERT INTO phpgw_sessions VALUES('{$id}', '{$ip}', '{$data}', {$ts})";
			}

			$ret = $db->query($sql, __LINE__, __FILE__);
			return $ret;
		}
	}

	// Now to make it all work
	ini_set('session.save_handler', 'user');

	// register out methods and we should be right to go now
	session_set_save_handler
	(
		array('phpgwapi_session_handler_db', 'open'),
		array('phpgwapi_session_handler_db', 'close'),
		array('phpgwapi_session_handler_db', 'read'),
		array('phpgwapi_session_handler_db', 'write'),
		array('phpgwapi_session_handler_db', 'destroy'),
		array('phpgwapi_session_handler_db', 'gc')
	);
