<?php
	/**
	* phpGroupWare - helpdesk: a Facilities Management System.
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
	* @package helpdesk
	* @subpackage helpdesk
 	* @version $Id: class.sotts.inc.php 6701 2010-12-25 10:51:59Z sigurdne $
	*/

	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package helpdesk
	 */

	class helpdesk_sotts
	{
		var $uicols_related = array();
		var $acl_location = '.ticket';

		public $soap_functions = array
			(
				'read' => array(
					'in'  => array('array'),
					'out' => array('array')
				)
			);


		public $xmlrpc_methods = array
			(
				array
				(
					'name'       => 'read',
					'decription' => 'Get list of tickets'
				)
			);


		function __construct()
		{
			$this->account		= (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->historylog	= CreateObject('phpgwapi.historylog','helpdesk');
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->db2 			= clone($this->db);
			$this->like 		= & $this->db->like;
			$this->join 		= & $this->db->join;
			$this->left_join 	= & $this->db->left_join;
			$this->dateformat 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$this->custom		= createObject('property.custom_fields');
		}


		function list_methods($_type='xmlrpc')
		{
			/*
			  This handles introspection or discovery by the logged in client,
			  in which case the input might be an array.  The server always calls
			  this function to fill the server dispatch map using a string.
			 */
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
			case 'xmlrpc':
				$xml_functions = array(
					'read' => array(
						'function'  => 'read',
						'signature' => array(array(xmlrpcArray,xmlrpcArray)),
						'docstring' => 'Get list of tickets'
					),
				);
				return $xml_functions;
				break;
			case 'soap':
				return $this->soap_functions;
				break;
			default:
				return array();
				break;
			}
		}

		function read($data)
		{
			$start			= isset($data['start']) && $data['start'] ? $data['start']:0;
			$status_id		= isset($data['status_id']) && $data['status_id'] ? $data['status_id']:'O'; //O='Open'
			$user_id		= isset($data['user_id']) && $data['user_id'] ? $data['user_id']: 0;
			$reported_by	= isset($data['reported_by']) && $data['reported_by'] ? (int)$data['reported_by'] : 0;
			$owner_id		= isset($data['owner_id'])?$data['owner_id']:'';
			$query			= isset($data['query'])?$data['query']:'';
			$sort			= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
			$order			= isset($data['order'])?$data['order']:'';
			$cat_id			= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id']:0;
			$parent_cat_id	= isset($data['parent_cat_id']) && $data['parent_cat_id'] ? $data['parent_cat_id']:0;
			$start_date		= isset($data['start_date']) && $data['start_date'] ? (int)$data['start_date'] : 0;
			$end_date		= isset($data['end_date']) && $data['end_date'] ? (int)$data['end_date'] : 0;
			$results		= isset($data['results']) && $data['results'] ? (int)$data['results'] : 0;
			$allrows		= $results == -1 ? true : false;
			$dry_run		= isset($data['dry_run']) ? $data['dry_run'] : '';
			$new			= isset($data['new']) ? $data['new'] : '';
			$location_code	= isset($data['location_code']) ? $data['location_code'] : '';
			$p_num			= isset($data['p_num']) ? $data['p_num'] : '';

			$GLOBALS['phpgw']->acl->set_account_id($this->account);
			$this->grants	= $GLOBALS['phpgw']->acl->get_grants2('helpdesk','.ticket');

			$order_join = "{$this->join} phpgw_accounts ON phpgw_helpdesk_tickets.user_id=phpgw_accounts.account_id";

			$result_order_field = '';
			if ($order)
			{
				if( $order == 'assignedto' )
				{
					$result_order_field = ',account_lastname';
					$order = 'account_lastname';
				}
				else if( $order == 'user' )
				{
					$result_order_field = ',account_lastname';
					$order = 'account_lastname';
				}

				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY phpgw_helpdesk_tickets.id DESC';
			}

			$order_join .= " {$this->join} phpgw_group_map ON (phpgw_accounts.account_id = phpgw_group_map.account_id)";

			$filtermethod = '';

			$categories = $GLOBALS['phpgw']->locations->get_subs('helpdesk', '.ticket.category');

			$grant_category = array();
			foreach ($categories as $location)
			{
				if ($GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, 'helpdesk'))
				{
					$category = explode('.',$location);
					$grant_category[] = $category[3];
				}
			}

			$grant_category[] = -1;//If no one found - not breaking the query

			$where= 'WHERE';

			if($parent_cat_id)
			{
				$_cats	= CreateObject('phpgwapi.categories', -1, 'helpdesk', '.ticket')->return_sorted_array(0, false, '', '', '', false, $parent_cat_id);
				$_filter_cat = array($parent_cat_id);
				foreach ($_cats as $_cat)
				{
					$_filter_cat[] = $_cat['id'];

				}

				$filtermethod = ' WHERE cat_id IN (' . implode(',', $_filter_cat) . ')';
				$where= 'AND';
			}

			$config = CreateObject('phpgwapi.config', 'helpdesk')->read();

			if(!empty($config['acl_at_tts_category']))
			{
				$filtermethod .= " {$where} phpgw_helpdesk_tickets.cat_id IN (" . implode(",", $grant_category) . ")";
				$where= 'AND';
			}


			if (is_array($this->grants))
			{
				$public_user_list = array();
				if (is_array($this->grants['accounts']) && $this->grants['accounts'])
				{
					foreach($this->grants['accounts'] as $user => $_right)
					{
						$public_user_list[] = $user;
					}
					unset($user);
					reset($public_user_list);
					$filtermethod .= " $where (phpgw_helpdesk_tickets.user_id IN(" . implode(',', $public_user_list) . ")";
					$where = 'AND';
				}

				$public_group_list = array();
				if (is_array($this->grants['groups']) && $this->grants['groups'])
				{
					foreach($this->grants['groups'] as $user => $_right)
					{
						$public_group_list[] = $user;
					}
					unset($user);
					reset($public_group_list);
					$where = $public_user_list ? 'OR' : $where;
					$filtermethod .= " $where phpgw_group_map.group_id IN(" . implode(',', $public_group_list) . "))";
					$where = 'AND';
				}
				if($public_user_list && !$public_group_list)
				{
					$filtermethod .=')';
				}
			}

			if ($status_id == 'X')
			{
				$closed = '';
				$this->db->query('SELECT * from phpgw_helpdesk_status',__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					if( $this->db->f('closed'))
					{
						$closed .= " OR phpgw_helpdesk_tickets.status = 'C" . $this->db->f('id') . "'";
					}
				}

				$filtermethod .= " $where ( (phpgw_helpdesk_tickets.status='X'{$closed})";
				$where = 'AND';

//				$filtermethod .= " $where ( phpgw_helpdesk_tickets.status='X'";
//				$where = 'AND';
			}
			else if ($status_id == 'O2') // explicite 'open'
			{
				$filtermethod .= " $where ( phpgw_helpdesk_tickets.status='O'";
				$where = 'AND';
			}
			else if($status_id == 'O')
			{
				$open = '';
				$this->db->query('SELECT * from phpgw_helpdesk_status',__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					if( ! $this->db->f('closed'))
					{
						$open .= " OR phpgw_helpdesk_tickets.status = 'C" . $this->db->f('id') . "'";
					}
				}

				$filtermethod .= " $where ( (phpgw_helpdesk_tickets.status='O'{$open})";
				$where = 'AND';
			}
			else if($status_id == 'all')
			{
				$filtermethod .= "{$where} (1=1";//nothing
				$where = 'AND';
			}
			else if(is_array($status_id) && count($status_id))
			{
				$or = '';
				$filtermethod .= "{$where} ((";

				foreach ($status_id as $value)
				{
					if($value)
					{
						$filtermethod .= "{$or} phpgw_helpdesk_tickets.status = '{$value}'";
						$or = ' OR';
					}
				}

				$filtermethod .= ')';

				$where = 'AND';
			}
			else
			{
				$filtermethod .= " $where (phpgw_helpdesk_tickets.status='{$status_id}'";
				$where = 'AND';
			}

			if($new)
			{
				$filtermethod .= " OR phpgw_helpdesk_views.id IS NULL )";
			}
			else
			{
				$filtermethod .= ')';
			}

			if ($cat_id > 0)
			{
				$_cats	= CreateObject('phpgwapi.categories', -1, 'helpdesk', '.ticket')->return_sorted_array(0, false, '', '', '', false, $cat_id);
				$_filter_cat = array($cat_id);
				foreach ($_cats as $_cat)
				{
					$_filter_cat[] = $_cat['id'];

				}

				$filtermethod .= " $where cat_id IN (" . implode(',', $_filter_cat) . ')';
				$where= 'AND';
			}

			if ($user_id)
			{
				$_membership = array();
				$membership = array(-1);
				if (is_array($user_id))
				{
					$user_ids = array(-1);
					foreach ($user_id as &$_user_id)
					{
						if($_user_id < 0)
						{
							$_membership = array_merge($_membership ,$GLOBALS['phpgw']->accounts->membership(abs($_user_id)));
						}
						$user_ids[] = abs($_user_id);
					}
				}
				else if ($user_id > 0)
				{
					$user_ids = array((int)abs($user_id));
				}
				else if ($user_id < 0)
				{
					$user_ids = array((int)abs($user_id));
					$_membership = $GLOBALS['phpgw']->accounts->membership(abs($user_id));
				}

				foreach ($_membership as $_key => $group_member)
				{
					$membership[] = $group_member->id;
				}

				$filtermethod .= " {$where} (assignedto IN (" . implode(', ' ,$user_ids) . ')';
				$where = 'AND';
				$filtermethod .= ' OR (assignedto IS NULL AND phpgw_helpdesk_tickets.group_id IN (' . implode(',',$membership) . ')))';

			}

			if ($reported_by > 0)
			{
				$filtermethod .= " $where phpgw_helpdesk_tickets.user_id=" . (int)$reported_by;
				$where = 'AND';
			}

			if ($owner_id > 0)
			{
				$filtermethod .= " $where phpgw_helpdesk_tickets.user_id=" . (int)$owner_id;
				$where = 'AND';
			}

			if ($start_date)
			{
				$end_date	= $end_date + 3600 * 16 + phpgwapi_datetime::user_timezone();
				$start_date	= $start_date - 3600 * 8 + phpgwapi_datetime::user_timezone();
				$filtermethod .= " $where phpgw_helpdesk_tickets.entry_date >= $start_date AND phpgw_helpdesk_tickets.entry_date <= $end_date ";
				$where= 'AND';
			}


			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where ( phpgw_helpdesk_tickets.id = " . (int) $query;

				$query = $this->db->db_addslashes($query);
				$querymethod .= " OR subject $this->like '%$query%')";
			}

			$sql = "SELECT DISTINCT phpgw_helpdesk_tickets.* , phpgw_helpdesk_views.id as view {$result_order_field} FROM phpgw_helpdesk_tickets"
				. " $order_join"
				. " LEFT OUTER JOIN phpgw_helpdesk_views ON (phpgw_helpdesk_tickets.id = phpgw_helpdesk_views.id AND phpgw_helpdesk_views.account_id='{$this->account}')"
				. " $filtermethod $querymethod";

			$sql2 = "SELECT count(*) as cnt FROM ({$sql}) as t";
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$this->total_records = $this->db->f('cnt');
			unset($sql2);

			$tickets = array();
			if(!$dry_run)
			{
				$location_id = $GLOBALS['phpgw']->locations->get_id('helpdesk', '.ticket');
				$custom_cols = $this->custom->find('helpdesk', '.ticket', 0, '', 'ASC', 'attrib_sort', true, true);
				$_return_field_array = array();

				if(!$allrows)
				{
					$this->db2->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__, $results);
				}
				else
				{
					if($this->total_records > 200)
					{
						$_fetch_single = true;
					}
					else
					{
						$_fetch_single = false;
					}
					$this->db2->query($sql . $ordermethod,__LINE__,__FILE__, false, $_fetch_single );
					unset($_fetch_single);
				}

				$i = 0;
				while ($this->db2->next_record())
				{
					$tickets[]= array
					(
						'id'				=> (int) $this->db2->f('id'),
						'subject'			=> $this->db2->f('subject',true),
						'user_id'			=> $this->db2->f('user_id'),
						'assignedto'		=> $this->db2->f('assignedto'),
						'status'			=> $this->db2->f('status'),
						'priority'			=> $this->db2->f('priority'),
						'cat_id'			=> $this->db2->f('cat_id'),
						'group_id'			=> $this->db2->f('group_id'),
						'entry_date'		=> $this->db2->f('entry_date'),
						'modified_date'		=> $this->db2->f('modified_date'),
						'finnish_date'		=> $this->db2->f('finnish_date'),
						'finnish_date2'		=> $this->db2->f('finnish_date2'),
						'order_id'			=> $this->db2->f('order_id'),
						'vendor_id'			=> $this->db2->f('vendor_id'),
						'actual_cost'		=> $this->db2->f('actual_cost'),
						'estimate'			=> $this->db2->f('budget'),
						'new_ticket'		=> $this->db2->f('view') ? false : true,
						'billable_hours'	=> $this->db2->f('billable_hours'),
						'details' =>		$this->db2->f('details', true),

					);
					foreach ($custom_cols as $custom_col)
					{
						if ($custom_value = $this->db2->f($custom_col['column_name'], true))
						{
							$custom_value = $this->custom->get_translated_value(array(
								'value' => $custom_value,
								'attrib_id' => $custom_col['id'],
								'datatype' => $custom_col['datatype'],
								'get_single_function' => $custom_col['get_single_function'],
								'get_single_function_input' => $custom_col['get_single_function_input']
								),
								$location_id);
						}
						$tickets[$i][$custom_col['column_name']] = $custom_value;
					}
					$i ++;
				}
			}

			return $tickets;
		}

		function get_origin_entity_type()
		{
			$sql = "SELECT entity_id, id as cat_id,name"
				. " FROM fm_entity_category "
				. " WHERE tracking=1 ORDER by entity_id,cat_id";

			$this->db->query($sql,__LINE__,__FILE__);

			$i=0;
			while ($this->db->next_record())
			{
				$entity[$i]['entity_id']=$this->db->f('entity_id');
				$entity[$i]['cat_id']=$this->db->f('cat_id');
				$entity[$i]['type']=".entity.{$this->db->f('entity_id')}.{$this->db->f('cat_id')}";
				$uicols[]	=  str_replace(' ', '_', $this->db->f('name',true));
				$i++;
			}

			$entity[$i]['type']='.project';
			$uicols[]	= 'project';

			$this->uicols_related	= $uicols;
			return $entity;
		}

		function read_single($id, $values = array() )
		{
			$id = (int) $id;

			$location_id = $GLOBALS['phpgw']->locations->get_id('helpdesk', '.ticket');

			$notified = createObject('property.notify')->read(array('location_id' => $location_id, 'location_item_id' => $id));

			$additional_users = array();
			foreach ($notified as $entry)
			{
				if($entry['account_id'])
				{
					$additional_users[] = $entry['account_id'];
				}
			}

			$GLOBALS['phpgw']->acl->set_account_id($this->account);
			$this->grants	= $GLOBALS['phpgw']->acl->get_grants2('helpdesk','.ticket');

			$acl_join = "{$this->join} phpgw_accounts ON phpgw_helpdesk_tickets.user_id=phpgw_accounts.account_id";
			$acl_join .= " {$this->join} phpgw_group_map ON (phpgw_accounts.account_id = phpgw_group_map.account_id)";

			if($additional_users)
			{
				$acl_join .= "{$this->left_join} phpgw_notification ON (phpgw_notification.location_item_id = phpgw_helpdesk_tickets.id AND phpgw_notification.location_id = {$location_id})";
				$acl_join .= "{$this->left_join} phpgw_accounts AS additional_users ON (phpgw_notification.contact_id = additional_users.person_id)";
			}

			$categories = $GLOBALS['phpgw']->locations->get_subs('helpdesk', '.ticket.category');

			$grant_category = array();
			foreach ($categories as $location)
			{
				if ($GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, 'helpdesk'))
				{
					$category = explode('.',$location);
					$grant_category[] = $category[3];
				}
			}
			$grant_category[] = -1;//If no one found - not breaking the query

			$_additional_user_filter = array();

			if($additional_users)
			{
				$_additional_user_filter[] = 'additional_users.account_id = ' . (int) $this->account;
			}

			$config = CreateObject('phpgwapi.config', 'helpdesk')->read();

			$filtermethod = 'AND (phpgw_helpdesk_tickets.user_id = ' . (int) $this->account;

			if(!empty($config['acl_at_tts_category']))
			{
				$filtermethod .= " AND (phpgw_helpdesk_tickets.cat_id IN (" . implode(",", $grant_category) . ")";
			}

			if (is_array($this->grants))
			{
				$public_user_list = array();
				if (is_array($this->grants['accounts']) && $this->grants['accounts'])
				{
					foreach($this->grants['accounts'] as $user => $_right)
					{
						$public_user_list[] = $user;
					}
					unset($user);
					reset($public_user_list);
					$_additional_user_filter[] = "phpgw_helpdesk_tickets.user_id IN(" . implode(',', $public_user_list) . ")";
				}

				$public_group_list = array();
				if (is_array($this->grants['groups']) && $this->grants['groups'])
				{
					foreach($this->grants['groups'] as $user => $_right)
					{
						$public_group_list[] = $user;
					}
					unset($user);
					reset($public_group_list);
					$_additional_user_filter[] = "phpgw_group_map.group_id IN(" . implode(',', $public_group_list) . ")";
				}
			}

			if($_additional_user_filter)
			{
				$filtermethod .= ' OR(' . implode(' OR ',  $_additional_user_filter) . ')';
			}

			$filtermethod .=')';


			if(!empty($config['acl_at_tts_category']))
			{
				$filtermethod .=')';
			}

			$sql = "SELECT DISTINCT phpgw_helpdesk_tickets.* FROM phpgw_helpdesk_tickets {$acl_join} WHERE phpgw_helpdesk_tickets.id = {$id} {$filtermethod}";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$ticket['assignedto']		= (int)$this->db->f('assignedto');
				$ticket['user_id']			= (int)$this->db->f('user_id');
				$ticket['group_id']			= (int)$this->db->f('group_id');
				$ticket['reverse_id']		= (int)$this->db->f('reverse_id');
				$ticket['status']			= $this->db->f('status');
				$ticket['cat_id']			= $this->db->f('cat_id');
				$ticket['subject']			= $this->db->f('subject', true);
				$ticket['priority']			= $this->db->f('priority');
				$ticket['details']			= $this->db->f('details', true);
				$ticket['location_code']	= $this->db->f('location_code');
				$ticket['contact_phone']	= $this->db->f('contact_phone');
				$ticket['contact_email']	= $this->db->f('contact_email',true);
				$ticket['address']			= $this->db->f('address', true);
				$ticket['tenant_id']		= $this->db->f('tenant_id');
				$ticket['p_num']			= $this->db->f('p_num');
				$ticket['p_entity_id']		= $this->db->f('p_entity_id');
				$ticket['p_cat_id']			= $this->db->f('p_cat_id');
				$ticket['finnish_date']		= $this->db->f('finnish_date');
				$ticket['finnish_date2']	= $this->db->f('finnish_date2');
				$ticket['contact_id']		= $this->db->f('contact_id');
				$ticket['order_id']			= $this->db->f('order_id');
				$ticket['vendor_id']		= $this->db->f('vendor_id');
				$ticket['b_account_id']		= $this->db->f('b_account_id');
				$ticket['order_descr']		= $this->db->f('order_descr', true);
				$ticket['ecodimb']			= $this->db->f('ecodimb');
				$ticket['budget']			= $this->db->f('budget');
				$ticket['actual_cost']		= $this->db->f('actual_cost');
				$ticket['order_cat_id']		= $this->db->f('order_cat_id');
				$ticket['building_part']	= $this->db->f('building_part',true);
				$ticket['order_dim1']		= $this->db->f('order_dim1');
				$ticket['publish_note']		= $this->db->f('publish_note');
				$ticket['billable_hours']	= $this->db->f('billable_hours');
				$ticket['modified_date'] = $this->db->f('modified_date');

				if($ticket['reverse_id'])
				{
					$ticket['reverse_name']	= $GLOBALS['phpgw']->accounts->get($ticket['reverse_id'])->__toString();
				}

				$ticket['user_name']	= $GLOBALS['phpgw']->accounts->get($ticket['user_id'])->__toString();
				if ($ticket['assignedto'] > 0)
				{
					$ticket['assignedto_name']	= $GLOBALS['phpgw']->accounts->get($ticket['assignedto'])->__toString();
				}

				if (isset($values['attributes']) && is_array($values['attributes']))
				{
					$ticket['attributes'] = $values['attributes'];
					foreach ($ticket['attributes'] as &$attr)
					{
						$attr['value'] = $this->db->f($attr['column_name'], true);
					}
				}
			}

			return $ticket;
		}

		function update_view($id='')
		{
			// Have they viewed this ticket before ?
			$id = (int) $id;
			$this->db->query("SELECT count(*) as cnt FROM phpgw_helpdesk_views where id={$id}"
				. " AND account_id='" . $GLOBALS['phpgw_info']['user']['account_id'] . "'",__LINE__,__FILE__);
			$this->db->next_record();

			if (! $this->db->f('cnt'))
			{
				$this->db->query("INSERT INTO phpgw_helpdesk_views (id,account_id,time) values ({$id},'"
					. $GLOBALS['phpgw_info']['user']['account_id'] . "','" . time() . "')",__LINE__,__FILE__);
			}
		}

		function add( &$ticket, $values_attribute = array() )
		{
			$table = 'phpgw_helpdesk_tickets';

			$value_set = array();
			$data_attribute = $this->custom->prepare_for_db($table, $values_attribute);
			if (isset($data_attribute['value_set']))
			{
				foreach ($data_attribute['value_set'] as $input_name => $value)
				{
					if (isset($value) && $value)
					{
						$value_set[$input_name] = $value;
					}
				}
			}

			if(!empty($ticket['set_user_id']))
			{
				$ticket['assignedto'] = $GLOBALS['phpgw_info']['user']['account_id'];
			}
			$ticket['user_id']	= !empty($ticket['set_user_id']) ? $ticket['set_user_id'] : $GLOBALS['phpgw_info']['user']['account_id'];

			$value_set['priority'] = isset($ticket['priority']) ? $ticket['priority'] : 0;
			$value_set['user_id'] =  $ticket['user_id'];
			$value_set['reverse_id'] = $GLOBALS['phpgw_info']['user']['account_id']; //The originator for the reversed ticket
			$value_set['assignedto'] = $ticket['assignedto'];
			$value_set['group_id'] = $ticket['group_id'];
			$value_set['subject'] = $this->db->db_addslashes($ticket['subject']);
			$value_set['cat_id'] = $ticket['cat_id'];
			$value_set['status'] = $ticket['status'];
			$value_set['details'] = $this->db->db_addslashes($ticket['details']);
			$value_set['location_code'] = $ticket['location_code'];
			$value_set['entry_date'] = time();
			$value_set['modified_date'] = time();
			$value_set['finnish_date'] = $ticket['finnish_date'];
			$value_set['contact_id'] = $ticket['contact_id'];
			$value_set['publish_note'] = 1;


			$cols = implode(',', array_keys($value_set));
			$values = $this->db->validate_insert(array_values($value_set));
			$this->db->transaction_begin();

			$this->db->query("INSERT INTO {$table} ({$cols}) VALUES ({$values})", __LINE__, __FILE__);

			$id = $this->db->get_last_insert_id($table, 'id');


			if($this->db->transaction_commit())
			{
				$this->historylog->add('O',$id, time(),'');
				if($ticket['finnish_date'])
				{
					$this->historylog->add('IF',$id,$ticket['finnish_date'],'');
				}
			}

			$receipt['message'][]=array('msg'=>lang('Ticket %1 has been saved',$id));
			$receipt['id']	= $id;
			return $receipt;
		}

		/**
		 * Get a list of user(admin)-configured status
		 *
		 * @return array with list of custom status
		 */

		public function get_custom_status()
		{
			$sql = "SELECT * FROM phpgw_helpdesk_status ORDER BY sorting ASC";
			$this->db->query($sql,__LINE__,__FILE__);

			$status= array();
			while ($this->db->next_record())
			{
				$status[] = array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $this->db->f('name', true),
						'color'	=> $this->db->f('color'),
						'closed'=> $this->db->f('color'),
					);
			}
			return $status;
		}
		function update_status($ticket,$id = 0)
		{
			$id = (int) $id;
			$receipt = array();
			// DB Content is fresher than http posted value.
			$this->db->query("select * from phpgw_helpdesk_tickets where id='$id'",__LINE__,__FILE__);
			$this->db->next_record();
			$old_status  		= $this->db->f('status');

			$this->db->transaction_begin();

			/*
			 ** phpgw_phpgw_helpdesk_append.append_type - Defs
			 ** R - Reopen ticket
			 ** X - Ticket closed
			 ** O - Ticket opened
			 ** C - Comment appended
			 ** A - Ticket assignment
			 ** G - Ticket group assignment
			 ** P - Priority change
			 ** T - Category change
			 ** S - Subject change
			 ** B - Budget
			 ** H - Billing hours
			 ** F - finnish date
			 ** C% - Status changed
			 ** L - Location changed
			 ** M - Mail sent to vendor
			 */

			if ($old_status != $ticket['status'])
			{
				$check_old_custom = (int) trim($old_status,'C');
				$this->db->query("SELECT * from phpgw_helpdesk_status WHERE id = {$check_old_custom}",__LINE__,__FILE__);
				$this->db->next_record();
				$this->fields_updated = true;
				if($old_status=='X' || $this->db->f('closed'))
				{
					$new_status = $ticket['status'];
					$this->historylog->add('R',$id,$ticket['status'],$old_status);

					$this->db->query("UPDATE phpgw_helpdesk_tickets SET status='{$new_status}' WHERE id= {$id}",__LINE__,__FILE__);
				}
				else
				{
					$this->historylog->add($ticket['status'],$id,$ticket['status'],$old_status);
					$this->db->query("UPDATE phpgw_helpdesk_tickets SET status='{$ticket['status']}' WHERE id={$id}",__LINE__,__FILE__);
				}
				$this->check_pending_action($ticket, $id);
			}

			$this->db->transaction_commit();

			if ($this->fields_updated)
			{
				$this->db->query('UPDATE phpgw_helpdesk_tickets SET modified_date= ' . time() . " WHERE id={$id}", __LINE__, __FILE__);
				$receipt['message'][]= array('msg' => lang('Ticket %1 has been updated',$id));
			}

			return $receipt;

		}

		function update_priority( $ticket, $id = 0 )
		{
			$id = (int)$id;
			$receipt = array();
			$this->fields_updated = false;

			$this->db->query("SELECT priority FROM phpgw_helpdesk_tickets WHERE id={$id}", __LINE__, __FILE__);
			$this->db->next_record();
			$oldpriority = $this->db->f('priority');

			$this->db->transaction_begin();

			if ($oldpriority != $ticket['priority'])
			{
				$this->fields_updated = true;
				$this->db->query("UPDATE phpgw_helpdesk_tickets set priority='" . $ticket['priority']
					. "' WHERE id={$id}", __LINE__, __FILE__);
				$this->historylog->add('P', $id, $ticket['priority'], $oldpriority);
			}

			if ($this->fields_updated)
			{
				$this->db->query('UPDATE phpgw_helpdesk_tickets SET modified_date= ' . time() . " WHERE id={$id}", __LINE__, __FILE__);
				$receipt['message'][] = array('msg' => lang('Ticket %1 has been updated', $id));
			}

			$this->db->transaction_commit();

			return $receipt;
		}

		function update_ticket( &$ticket, $id = 0, $receipt = array(), $values_attribute = array(), $simple = false )
		{
			$this->fields_updated = array();
			$id = (int)$id;
			$ticket['id'] = $id;
			// DB Content is fresher than http posted value.
			$this->db->query("SELECT * FROM phpgw_helpdesk_tickets WHERE id='$id'", __LINE__, __FILE__);
			$this->db->next_record();

			$location_code = $this->db->f('location_code');
			$oldlocation_code = $this->db->f('location_code');
			$oldfinnish_date = $this->db->f('finnish_date');
			$oldfinnish_date2 = $this->db->f('finnish_date2');
			$oldassigned = $this->db->f('assignedto');
			$oldgroup_id = $this->db->f('group_id');
			$oldpriority = $this->db->f('priority');
			$oldcat_id = $this->db->f('cat_id');
			$old_status = $this->db->f('status');
			$ticket['old_status'] = $old_status; // used for custom functions
			//	$old_budget  			= $this->db->f('budget');
			$old_billable_hours = (float)$this->db->f('billable_hours');
			//	$old_billable_rate	= $this->db->f('billable_rate');
			$old_subject = $this->db->f('subject');
			$old_contact_id = $this->db->f('contact_id');
			$old_order_cat_id = $this->db->f('order_cat_id');
			$old_building_part = $this->db->f('building_part', true);
			$old_order_dim1 = (int)$this->db->f('order_dim1');
			$old_user_id = $this->db->f('user_id');

			if ($oldcat_id == 0)
			{
				$oldcat_id = '';
			}
			if ($old_order_cat_id == 0)
			{
				$old_order_cat_id = '';
			}
			if ($oldassigned == 0)
			{
				$oldassigned = '';
			}
			if ($oldgroup_id == 0)
			{
				$oldgroup_id = '';
			}

			// Figure out and last note

			$history_values = $this->historylog->return_array(array(), array('C'), 'history_timestamp', 'DESC', $id);
			$old_note = $history_values[0]['new_value'];

			if (!$old_note)
			{
				$old_note = $this->db->f('details');
			}

			$this->db->transaction_begin();

			/*
			 * * phpgw_fm_tts_append.append_type - Defs
			 * * R - Reopen ticket
			 * * X - Ticket closed
			 * * O - Ticket opened
			 * * C - Comment appended
			 * * A - Ticket assignment
			 * * G - Ticket group assignment
			 * * P - Priority change
			 * * T - Category change
			 * * S - Subject change
			 * * B - Budget change
			 * *	AC - actual cost changed
			 * * H - Billing hours
			 * * F - finnish date
			 * * C% - Status change
			 * * L - Location changed
			 * * M - Mail sent to vendor
			 * * FW - Forwarded
			 */

			if (!$simple)
			{
				$this->db->query("UPDATE phpgw_helpdesk_tickets SET publish_note = NULL WHERE id = {$id}", __LINE__, __FILE__);
				$this->db->query("UPDATE phpgw_history_log SET publish = NULL WHERE history_record_id = {$id}", __LINE__, __FILE__);
				if (isset($ticket['publish_note']))
				{
					foreach ($ticket['publish_note'] as $publish_info)
					{
						$note = explode('_', $publish_info);
						if (!$note[1])
						{
							$this->db->query("UPDATE phpgw_helpdesk_tickets SET publish_note = 1 WHERE id = {$note[0]}", __LINE__, __FILE__);
						}
						else
						{
							$this->db->query("UPDATE phpgw_history_log SET publish = 1 WHERE history_id = {$note[1]}", __LINE__, __FILE__);
						}
					}
				}
				if ($ticket['assignedto'] && ( ($oldassigned != $ticket['assignedto']) && $ticket['assignedto'] != 'ignore'))
				{
					$this->fields_updated[] = 'assignedto';

					$value_set = array('assignedto' => $ticket['assignedto']);
					$value_set = $this->db->validate_update($value_set);

					$this->db->query("update phpgw_helpdesk_tickets set $value_set where id='$id'", __LINE__, __FILE__);
					$this->historylog->add('A', $id, $ticket['assignedto'], $oldassigned);
				}

				if($oldgroup_id || $ticket['group_id'])
				{
					if($oldassigned && ! $ticket['assignedto'])
					{
						$this->fields_updated[] = 'assignedto';
						$this->db->query("UPDATE phpgw_helpdesk_tickets SET assignedto = NULL WHERE id={$id}", __LINE__, __FILE__);
						$this->historylog->add('A', $id, '0', $oldassigned);
					}
				}

				if (($oldgroup_id != $ticket['group_id']) && $ticket['group_id'] != 'ignore')
				{
					$this->fields_updated[] = 'group_id';

					$value_set = array('group_id' => $ticket['group_id']);
					$value_set = $this->db->validate_update($value_set);

					$this->db->query("update phpgw_helpdesk_tickets set $value_set where id='$id'", __LINE__, __FILE__);
					$this->historylog->add('G', $id, (int)$ticket['group_id'], $oldgroup_id);
				}

				if ($ticket['priority'] && $oldpriority != $ticket['priority'])
				{
					$this->fields_updated[] = 'priority';
					$this->db->query("update phpgw_helpdesk_tickets set priority='" . $ticket['priority']
						. "' where id='$id'", __LINE__, __FILE__);
					$this->historylog->add('P', $id, $ticket['priority'], $oldpriority);
				}

				if ($old_contact_id != $ticket['contact_id'])
				{
					$contact_id = (int)$ticket['contact_id'];
					$this->fields_updated[] = 'contact_id';
					$this->db->query("update phpgw_helpdesk_tickets set contact_id={$contact_id} WHERE id=$id", __LINE__, __FILE__);
				}

				if ($ticket['cat_id'] && ( ($oldcat_id != $ticket['cat_id']) && $ticket['cat_id'] != 'ignore'))
				{
					$this->fields_updated[] = 'cat_id';
					$this->db->query("update phpgw_helpdesk_tickets set cat_id='" . $ticket['cat_id']
						. "' where id='$id'", __LINE__, __FILE__);
					$this->historylog->add('T', $id, $ticket['cat_id'], $oldcat_id);
				}
			}

			if (($old_note != $ticket['note']) && $ticket['note'])
			{
				$this->fields_updated[] = 'note';
				$this->historylog->add('C', $id, $ticket['note'], $old_note);
				$_history_id = $this->db->get_last_insert_id('phpgw_history_log', 'history_id');
				$this->db->query("UPDATE phpgw_history_log SET publish = 1 WHERE history_id = $_history_id", __LINE__, __FILE__);
				unset($_history_id);

				$check_old_custom = (int)trim($old_status, 'C');
				$this->db->query("SELECT * from phpgw_helpdesk_status WHERE id = {$check_old_custom}", __LINE__, __FILE__);
				$this->db->next_record();
				$old_closed = $this->db->f('closed');
				if ($old_status == 'X' || $old_closed)
				{
					$config = CreateObject('phpgwapi.config', 'helpdesk')->read();
					$new_status = !empty($config['reopen_status']) ? $config['reopen_status'] : '0';
					$this->fields_updated[] = 'status';
					$this->historylog->add('R', $id, $new_status, $old_status);
					$this->db->query("UPDATE phpgw_helpdesk_tickets SET status='{$new_status}' WHERE id= {$id}", __LINE__, __FILE__);
					$this->db->query("UPDATE phpgw_helpdesk_tickets SET priority = 1 WHERE id = {$id}", __LINE__, __FILE__);
				}
			}

			$value_set = array();

			$data_attribute = $this->custom->prepare_for_db('phpgw_helpdesk_tickets', $values_attribute);

			if (!empty($data_attribute['value_set']))
			{
				$this->db->query("SELECT * FROM phpgw_helpdesk_tickets WHERE id='$id'", __LINE__, __FILE__);
				$this->db->next_record();
				foreach ($data_attribute['value_set'] as $input_name => $value)
				{
					$old_values = $this->db->f($input_name);
					if($value == $old_values)
					{
						continue;
					}
					$this->fields_updated[] = $input_name;
					$value_set[$input_name] = $value;
				}
			}

			$value_set['modified_date'] = time();

			$value_set = $this->db->validate_update($value_set);
			$this->db->query("UPDATE phpgw_helpdesk_tickets SET $value_set WHERE id={$id}", __LINE__, __FILE__);

			if (isset($this->fields_updated) && $this->fields_updated && $simple)
			{
				$this->db->query("DELETE FROM phpgw_helpdesk_views WHERE id={$id} AND account_id !=" . (int) $this->account, __LINE__, __FILE__);
				$receipt['message'][] = array('msg' => lang('Ticket has been updated'));
				$this->db->transaction_commit();
				return $receipt;
			}

			$finnish_date = (isset($ticket['finnish_date']) ? phpgwapi_datetime::date_to_timestamp($ticket['finnish_date']) : '');

			if ($oldfinnish_date && isset($ticket['finnish_date']) && $ticket['finnish_date'])
			{
				$this->db->query("update phpgw_helpdesk_tickets set finnish_date2='" . $finnish_date
					. "' where id='$id'", __LINE__, __FILE__);
			}
			else if (!$oldfinnish_date && isset($ticket['finnish_date']) && $ticket['finnish_date'])
			{
				$this->db->query("update phpgw_helpdesk_tickets set finnish_date='" . $finnish_date
					. "' where id='$id'", __LINE__, __FILE__);
			}

			if ($oldfinnish_date2 > 0)
			{
				$oldfinnish_date = $oldfinnish_date2;
			}
			if (isset($ticket['finnish_date']) && $ticket['finnish_date'])
			{
				if ($oldfinnish_date != $finnish_date)
				{
					$this->fields_updated[] = 'finnish_date';
					$this->historylog->add('F', $id, $finnish_date, $oldfinnish_date);
				}
			}

			if (isset($ticket['status']) && ($old_status != $ticket['status']))
			{
				$check_old_custom = (int)trim($old_status, 'C');
				$this->db->query("SELECT * from phpgw_helpdesk_status WHERE id = {$check_old_custom}", __LINE__, __FILE__);
				$this->db->next_record();
				$old_closed = $this->db->f('closed');
				$this->fields_updated[] = 'status';
				if ($old_status == 'X' || $old_closed)
				{
					$new_status = $ticket['status'];
					$this->historylog->add('R', $id, $ticket['status'], $old_status);

					$this->db->query("UPDATE phpgw_helpdesk_tickets SET status='{$new_status}' WHERE id= {$id}", __LINE__, __FILE__);
				}
				else
				{
					$this->historylog->add($ticket['status'], $id, $ticket['status'], $old_status);
					$this->db->query("UPDATE phpgw_helpdesk_tickets SET status='{$ticket['status']}' WHERE id={$id}", __LINE__, __FILE__);
				}
				$this->check_pending_action($ticket, $id);

				//Close cases at related
				$check_new_custom = (int)trim($ticket['status'], 'C');
				$this->db->query("SELECT closed from phpgw_helpdesk_status WHERE id = {$check_new_custom}", __LINE__, __FILE__);
				$this->db->next_record();

				if (($this->db->f('closed') || $ticket['status'] == 'X') && ($old_status != 'X' && !$old_closed))
				{
					$this->db->query("UPDATE phpgw_helpdesk_tickets SET priority = 0 WHERE id = {$id}", __LINE__, __FILE__);
					$location_id = $GLOBALS['phpgw']->locations->get_id('helpdesk', '.ticket');
					// at controller
					if (isset($GLOBALS['phpgw_info']['user']['apps']['controller']))
					{
						$controller = CreateObject('controller.uicase');
						$controller->updateStatusForCases($location_id, $id, 1);
					}
					// at request
					execMethod('property.sorequest.update_status_from_related', array(
						'location_id' => $location_id,
						'id' => $id,
						'status' => 'closed')
					);
				}
			}


			/*
			  if ($old_billable_rate != $ticket['billable_rate'])
			  {
			  $this->fields_updated[] = 'billable_rate';
			  $this->db->query("update phpgw_helpdesk_tickets set billable_rate='" . $ticket['billable_rate']
			  . "' where id='$id'",__LINE__,__FILE__);
			  $this->historylog->add('B',$id,$ticket['billable_rate'],$old_billable_rate);
			  }
			 */
			if ($old_subject != $ticket['subject'])
			{
				$this->db->query("UPDATE phpgw_helpdesk_tickets SET subject='" . $ticket['subject']
					. "' where id='$id'", __LINE__, __FILE__);
				$this->historylog->add('S', $id, $ticket['subject'], $old_subject);
				$receipt['message'][] = array('msg' => lang('Subject has been updated'));
			}

			if ($ticket['billable_hours'])
			{
				$ticket['billable_hours'] = (float)str_replace(',', '.', $ticket['billable_hours']);
				$ticket['billable_hours'] += (float)$old_billable_hours;
				$this->db->query("UPDATE phpgw_helpdesk_tickets SET billable_hours='{$ticket['billable_hours']}'"
					. " WHERE id='{$id}'", __LINE__, __FILE__);
				$this->historylog->add('H', $id, $ticket['billable_hours'], $old_billable_hours);
				$receipt['message'][] = array('msg' => lang('billable hours has been updated'));
			}

			if (!empty($ticket['set_user_id']) &&  $old_user_id != $ticket['set_user_id'] && $oldassigned ==  $this->account)
			{
				$set_user_id = (int) $ticket['set_user_id'];
				$this->db->query("UPDATE phpgw_helpdesk_tickets SET user_id = {$set_user_id}"
					. " WHERE id='{$id}'", __LINE__, __FILE__);
				$this->historylog->add('FW', $id, $set_user_id, $old_user_id);
				$receipt['message'][] = array('msg' => lang('ticket is forwarded'));
			}

			$value_set = array();

			if (isset($ticket['extra']) && is_array($ticket['extra']) && $ticket['extra'])
			{
				foreach ($ticket['extra'] as $column => $value)
				{
					$value_set[$column] = $value;
				}

				$value_set = $this->db->validate_update($value_set);

				$this->db->query("UPDATE phpgw_helpdesk_tickets SET $value_set WHERE id={$id}", __LINE__, __FILE__);
			}
//			_debug_array($this->fields_updated);die();
			if (isset($this->fields_updated) && $this->fields_updated)
			{
				$this->db->query("DELETE FROM phpgw_helpdesk_views WHERE id={$id} AND account_id !=" . (int) $this->account, __LINE__, __FILE__);
				$receipt['message'][] = array('msg' => lang('Ticket has been updated'));
			}

			$this->db->transaction_commit();

			return $receipt;
		}

		function check_pending_action($ticket,$id)
		{
			$status = (int)trim($ticket['status'], 'C');
			$this->db->query("SELECT * FROM phpgw_helpdesk_status WHERE id = '{$status}'");

			$this->db->next_record();

			if ($this->db->f('approved') )
			{
				$action_params = array
					(
						'appname'			=> 'helpdesk',
						'location'			=> '.ticket',
						'id'				=> $id,
						'responsible'		=> $this->account,
						'responsible_type'  => 'user',
						'action'			=> 'approval',
						'remark'			=> '',
						'deadline'			=> ''
					);

				execMethod('property.sopending_action.close_pending_action', $action_params);
				unset($action_params);
			}
			if ($this->db->f('in_progress') )
			{
				$action_params = array
					(
						'appname'			=> 'helpdesk',
						'location'			=> '.ticket',
						'id'				=> $id,
						'responsible'		=> $ticket['vendor_id'],
						'responsible_type'  => 'vendor',
						'action'			=> 'remind',
						'remark'			=> '',
						'deadline'			=> ''
					);

				execMethod('property.sopending_action.close_pending_action', $action_params);
				unset($action_params);
			}

			if ($this->db->f('delivered') )
			{
				//close
			}
		}

		function delete($id)
		{
			$id = (int)$id;

			$location_id = $GLOBALS['phpgw']->locations->get_id('helpdesk', '.ticket');

			if ( !$location_id )
			{
				throw new Exception("phpgwapi_locations::get_id ('helpdesk', '.ticket') returned 0");
			}

			$this->db->transaction_begin();

			$this->db->query("DELETE FROM fm_action_pending WHERE location_id = {$location_id} AND item_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_interlink WHERE location1_id = {$location_id} AND location1_item_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_interlink WHERE location2_id = {$location_id} AND location2_item_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_history_log WHERE history_record_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_helpdesk_views WHERE id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_helpdesk_tickets WHERE id = {$id}",__LINE__,__FILE__);

			if($this->db->transaction_commit())
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		public function get_reported_by($parent_cat_id = 0)
		{
			$filtermethod = '';

			if($parent_cat_id)
			{
				$categories	= CreateObject('phpgwapi.categories', -1, 'helpdesk', '.ticket');
				$categories->supress_info	= true;
				$_cats = $categories->return_sorted_array(0, false, '', '', '', false, $parent_cat_id);
				$_filter_cat = array($parent_cat_id);
				foreach ($_cats as $_cat)
				{
					$_filter_cat[] = $_cat['id'];

				}

				$filtermethod = ' WHERE cat_id IN (' . implode(',', $_filter_cat) . ')';
			}

			$values = array();
			$sql = "SELECT DISTINCT user_id as id , account_lastname, account_firstname FROM phpgw_helpdesk_tickets"
				. " {$this->join} phpgw_accounts ON phpgw_helpdesk_tickets.user_id = phpgw_accounts.account_id"
				. " {$filtermethod}"
				. " ORDER BY account_lastname ASC";

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('account_lastname', true) . ', ' . $this->db->f('account_firstname', true)
				);
			}

			return $values;
		}
	}
