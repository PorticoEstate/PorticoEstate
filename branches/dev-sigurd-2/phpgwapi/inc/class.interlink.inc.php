<?php
	/**
	* Inter module data linking manager for phpGroupWare
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright (c) 2007 Dave Hall http://davehall.com.au
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License Version 2 or later
	* @version $Id$
	* @package phpgwapi
	* @subpackage utility
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
	* Inter module data linking manager for phpGroupWare
	*
	* @package phpgwapi
	* @subpackage linking
	*/
	class phpgw_interlink
	{
		/**
		* @var object $db reference to global db object
		*/
		private $db;

		/**
		* Constructor
		*/
		public function __construct()
		{
			$this->db =& $GLOBALS['phpgw']->db;
		}

		/**
		* Add a link
		*
		* @param string $app1 the first application (usually the source)
		* @param string $loc1 the first class
		* @param int $id1 the first record id
		* @param string $app2 the second application (usually the target)
		* @param string $loc2 the second class
		* @param int $id2 the second record id
		* @param bool $private is the link private
		* @param int $active_from unix time stamp from when the link becomes active - should be future dated
		* @return int the new link id, or 0 on failure
		*/
		public function add_link($app1, $loc1,  $id1, $app2, $loc2, $id2, $private = true, $active_from = null, $active_to = null)
		{
			$app1 = $this->db->db_addslashes($app1);
			$loc1 = (int) $loc1;
			$id1 = (int) $id1;
			$app2 = $this->db->db_addslashes($app2);
			$loc2 = (int) $loc2;
			$id2 = (int) $id2;
			$owner = (int) $GLOBALS['phpgw_info']['user']['account_id'];
			$now = time();
			$active_from = $active_from ? (int) $active_from : 'NULL';
			$active_to = $active_to ? (int) $active_to : 'NULL';


			$owner = (int) $GLOBALS['phpgw_info']['user']['account_id'];

			$sql = 'INSERT INTO phpgw_interlink(app1_name, app1_loc, app1_id, app2_name, app2_loc, app2_id, is_private, account_id, entry_date, start_date, end_date)'
				. " VALUES('$app1', $loc1, $id1, '$app2', $loc2, $id2, $owner, $now, $active_from, $active_to)";

			$id = 0;

			// Use a transaction to ensure reliable last insert id generation
			$this->db->transaction_begin();
			if ( $this->db->query($sql) )
			{
				$id = $this->db->get_insert_id('phpgw_interlink', 'interlink_id');
				if ( $this->db->transaction_commit() )
				{
					return $id;
				}
				return 0;
			}
			$this->db->transaction_abort();
			return $id;
		}

		/**
		* Count the number of records linked to a record
		*
		* @internal this should be called before deleting a record to ensure orphaned links aren't created
		* @param string $app the module to link to
		* @param string $loc the location to link to
		* @param int $id the id to link to
		* @return int the number of links found
		*/
		public function count_links($app, $loc, $id)
		{
			$app = $this->db->db_addslashes($app);
			$loc = (int) $loc;
			$id = (int) $id;
			$owner = (int) $GLOBALS['phpgw_info']['user']['account_id'];

			$sql = 'SELECT COUNT(interlink_id) as cnt'
				. ' FROM phpgw_interlink'
				. " WHERE (app1_name = '{$app}' AND app1_loc = '{$loc}' AND app1_id = '{$id}')"
				. " OR (app2_name = '{$app}' AND app2_loc = '{$loc}' AND app2_id = '{$id}')"
				. " AND ( is_private = 0 OR (is_private = 1 AND account_id = {$owner}) )"
				. ' AND is_active = 0 AND active_from >= ' . time();
			
			if ( $this->db->next_record() )
			{
				return $this->db->f('cnt');
			}
			return 0;
		}

		/**
		* Delete a link
		*
		* @param int $link_id the link to delete
		* @param bool $make_inactive make entry inactive instead of deleting it
		* @return bool was the link deleted?
		*/
		public function delete_link($link_id, $make_inactive = false)
		{
			$link_id = (int) $link_id;
			if ( $make_inactive )
			{
				$this->db->query("UPDATE phpgw_interlink SET is_active = 0 WHERE interlink_id = {$link_id}");
			}
			else
			{
				$this->db->query("DELETE FROM phpgw_interlink WHERE interlink_id = {$link_id}");
			}
			return $this->db->affected_rows() == 1;
		}

		/**
		* List available links
		*
		* @param string $app the module to link to
		* @param string $loc the location to link to
		* @param int $id the id to link to
		* @return array list of links in the following format - ['link_id'] = array('app' => 'string', 'summary' => 'string', 'account_id' => int, 'view' => 'string', 'edit' => 'string')
		*/
		public function list_links($app, $loc, $id)
		{
			$app = $this->db->db_addslashes($app);
			$loc = (int) $loc;
			$id = (int) $id;
			$owner = (int) $GLOBALS['phpgw_info']['user']['account_id'];

			$sql = 'SELECT interlink_id, app1_name, app1_loc, app1_id, app2_name, app2_loc, app2_id, is_private, account_id' 
				. ' FROM phpgw_interlink'
				. " WHERE (app1_name = '{$app}' AND app1_loc = '{$loc}' AND app1_id = '{$id}')"
				. " OR (app2_name = '{$app}' AND app2_loc = '{$loc}' AND app2_id = '{$id}')"
				. " AND ( is_private = 0 OR (is_private = 1 AND account_id = {$owner}) )"
				. ' AND is_active = 1 AND active_from >= ' . time();

			$recs = array();
			while ( $this->db->next_record() )
			{
				if ( $this->db->f('app1_name') == $app )
				{
					$recs[] = array
					(
						'interlink_id'	=> $this->db->f('interlink_id'),
						'app2_name'		=> $this->db->f('app2_name'),
						'app2_loc'		=> $this->db->f('app2_loc'),
						'app2_id'		=> $this->db->f('app2_id'),
						'is_private'	=> !!$this->db->f('is_private'),
						'account_id'	=> $this->db->f('account_id')
					);
				}
				else
				{
					$recs[] = array
					(
						'interlink_id'	=> $this->db->f('interlink_id'),
						'app1_name'		=> $this->db->f('app1_name'),
						'app1_loc'		=> $this->db->f('app1_loc'),
						'app1_id'		=> $this->db->f('app1_id'),
						'is_private'	=> !!$this->db->f('is_private'),
						'account_id'	=> $this->db->f('account_id')
					);
				}
			}

			foreach ( $recs as &$rec )
			{
				$rec['summary']	= $this->get_summary($rec);
				$rec['owner']	= $GLOBALS['phpgw']->accounts->id2name($rec['account_id']);
			}
			return $recs;
		}
	}
