<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	phpgw::import_class('phpgwapi.datetime');

	class property_sopending_action
	{
		public $total_records;

		/**
		 * @var array valid responsible types
		 */
		protected $valid_responsible_types = array(
			'user',
			'vendor',
			'tenant'
		);

		//To avoid conflicting transactions
		protected $global_transaction  = false;

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join 	= & $this->db->left_join;
			$this->like			= & $this->db->like;
		}

		/**
		 * Set pending action for items across the system.
		 *
		 * @param array   $data array containing string  'appname'			- the name of the module being looked up
		 *										string  'location'			- the location within the module to look up
		 * 										integer 'id'				- id of the referenced item - could possibly be a bigint
		 * 										integer 'responsible'		- the user_id asked for approval
		 * 										string  'responsible_type'  - what type of responsible is asked for action (user,vendor or tenant)
		 * 										string  'action'			- what type of action is pending
		 * 										string  'remark'			- a general remark - if any
		 * 										integer 'deadline'			- unix timestamp if any deadline is given.
		 *
		 * @return integer $reminder  number of request for this action
		 */

		public function set_pending_action($data = array())
		{
			$appname		= $data['appname'];
			$location		= $data['location'];
			$item_id		= $data['id']; //possible bigint
			$responsible	= (int) $data['responsible'];
			$action			= $this->db->db_addslashes($data['action']);
			$remark			= $this->db->db_addslashes($data['remark']);
			$deadline		= (int) $data['deadline'];

			if( !$item_id)
			{
				throw new Exception("No item_id given");
			}

			$responsible_type = isset($data['responsible_type']) && $data['responsible_type'] ? $data['responsible_type'] : 'user';

			if( !in_array($responsible_type, $this->valid_responsible_types))
			{
				throw new Exception("'{$responsible_type}' is not a valid responsible_type");
			}

			$sql = "SELECT id FROM fm_action_pending_category WHERE num = '{$action}'";
			$this->db->query($sql, __LINE__,__FILE__);
			$this->db->next_record();
			$action_category = $this->db->f('id');
			if ( !$action_category )
			{
				throw new Exception("'{$action}' is not a valid action_type");
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);

			if ( !$location_id )
			{
				throw new Exception("phpgwapi_locations::get_id ({$appname}, {$location}) returned 0");
			}

			$reminder = 1;

			if( $this->db->get_transaction() )
			{
				$this->global_transaction = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$condition = " WHERE location_id = {$location_id}"
				. " AND item_id = {$item_id}"
				. " AND responsible = {$responsible}"
				. " AND action_category = {$action_category}"
				. " AND action_performed IS NULL"
				. " AND expired_on IS NULL";

			$sql = "SELECT id, reminder FROM fm_action_pending {$condition}";

			$this->db->query($sql, __LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('reminder'))
			{
				$reminder	= $this->db->f('reminder') + 1;
				$id			= $this->db->f('id');

				$value_set = array
					(
						'expired_on' 		=> phpgwapi_datetime::user_localtime(),
						'expired_by' 		=> $this->account,
					);

				if ( $deadline > 0 )
				{
					$value_set['deadline'] = $deadline;
				}

				if( isset($data['close']) && $data['close'] )
				{
					$value_set['action_performed'] = phpgwapi_datetime::user_localtime();
				}

				$value_set	= $this->db->validate_update($value_set);
				$sql = "UPDATE fm_action_pending SET {$value_set} WHERE id = $id";
				$ok = !!$this->db->query($sql, __LINE__,__FILE__);

				if( isset($data['close']) && $data['close'] )
				{
					if( !$this->global_transaction )
					{
						$this->db->transaction_commit();
					}
					return $ok;
				}
			}

			//if nothing found - and you want to close
			if( isset($data['close']) && $data['close'] )
			{
				return 0;
			}


			$values= array
				(
					$item_id,								//item_id
					$location_id,
					$responsible,							// responsible
					$responsible_type,						// responsible_type
					$action_category, 						//action_category
					phpgwapi_datetime::user_localtime(),	// action_requested
					$reminder,
					$deadline,								//action_deadline
					phpgwapi_datetime::user_localtime(),	//created_on
					$this->account,							//created_by
					$remark									//remark
				);

			$values	= $this->db->validate_insert($values);
			$sql = "INSERT INTO fm_action_pending ("
				. "item_id, location_id, responsible, responsible_type,"
				. "action_category, action_requested, reminder, action_deadline,"
				. "created_on, created_by, remark) VALUES ( $values $vals)";
			$this->db->query($sql, __LINE__,__FILE__);

			if( !$this->global_transaction )
			{
				$this->db->transaction_commit();
			}

			return $reminder;
		}

		/**
		 * Get pending action for items across the system.
		 *
		 * @param array   $data array containing string  'appname'			- the name of the module being looked up
		 *										string  'location'			- the location within the module to look up
		 * 										integer 'id'				- id of the referenced item - could possibly be a bigint
		 * 										integer 'responsible'		- the user_id asked for approval
		 * 										string  'responsible_type'  - what type of responsible is asked for action (user,vendor or tenant)
		 * 										string  'action'			- what type of action is pending
		 * 										integer 'created_by'		- The user that owns the record
		 * 										integer 'deadline'			- unix timestamp if any deadline is given.
		 *
		 * @return array $ret  dataset also containing an url to the item in question
		 */

		public function get_pending_action($data = array())
		{
			$start				= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$appname			= isset($data['appname']) && $data['appname'] ? $data['appname'] : '';
			$location			= isset($data['location']) && $data['location'] ? $data['location'] : '';
			$item_id			= isset($data['id']) && $data['id'] ? $data['id'] : '';$data['id']; //possible bigint
			$responsible		= (int) $data['responsible'];
			$responsible_type	= isset($data['responsible_type']) && $data['responsible_type'] ? $data['responsible_type'] : 'user';
			$action				= isset($data['action']) && $data['action'] ? $this->db->db_addslashes($data['action']) : '';
			$deadline			= isset($data['deadline']) && $data['deadline'] ? (int) $data['deadline'] : 0;
			$created_by			= isset($data['created_by']) && $data['created_by'] ? (int) $data['created_by'] : 0;
			$sort				= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order				= isset($data['order']) ? $data['order'] : '';
			$allrows			= isset($data['allrows']) ? $data['allrows'] : '';

			if( !in_array($responsible_type, $this->valid_responsible_types))
			{
				throw new Exception("'{$responsible_type}' is not a valid responsible_type");
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);

			if ( !$location_id )
			{
				throw new Exception("phpgwapi_locations::get_id ({$appname}, {$location}) returned 0");
			}

			$ret = array();
			$condition = " WHERE action_performed IS NULL AND expired_on IS NULL AND num = '{$action}' AND location_id = {$location_id}";

			if( $responsible )
			{
				$condition .= " AND responsible = {$responsible}";
			}

			if( $item_id )
			{
				$condition .= " AND item_id = {$item_id}";
			}

			if( $deadline )
			{
				$condition .= " AND deadline < {$deadline}";
			}

			if( $created_by )
			{
				$condition .= " AND created_by = {$created_by}";
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY created_on DESC';
			}

			$sql = "SELECT fm_action_pending.* FROM fm_action_pending {$this->join} fm_action_pending_category"
				. " ON fm_action_pending.action_category = fm_action_pending_category.id {$condition}";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$ret = $this->db->resultSet;

			$interlink = CreateObject('property.interlink');

			foreach ($ret as &$entry)
			{
				if( !$location )
				{
					$location = $GLOBALS['phpgw']->locations->get_name($entry['location_id']);
				}
				$entry['url'] = $interlink->get_relation_link($location, $entry['item_id'], 'edit');
			}
			return $ret;
		}

		/**
		 * Close pending action for items across the system.
		 *
		 * @param array   $data array containing string  'appname'			- the name of the module being looked up
		 *										string  'location'			- the location within the module to look up
		 * 										integer 'id'				- id of the referenced item - could possibly be a bigint
		 * 										integer 'responsible'		- the user_id asked for approval
		 * 										string  'responsible_type'  - what type of responsible is asked for action (user,vendor or tenant)
		 * 										string  'action'			- what type of action is pending
		 * 										string  'remark'			- a general remark - if any
		 * 										integer 'deadline'			- unix timestamp if any deadline is given.
		 *
		 * @return integer $reminder  number of request for this action
		 */
		public function close_pending_action($data = array())
		{
			$data['close'] = true;
			return !!$this->set_pending_action($data);
		}
	}
