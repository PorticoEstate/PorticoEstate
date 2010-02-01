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
	* @subpackage helpdesk
 	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */

	class property_sotts
	{
		var $uicols_related = array();
		var $acl_location = '.ticket';

		var $soap_enabled = array
		(
			'read'	=> true
		);

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->historylog	= CreateObject('property.historylog','tts');
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->db->fetchmode= 'ASSOC';
			$this->like 		= & $this->db->like;
			$this->join 		= & $this->db->join;
			$this->dateformat 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		}

		function get_category_name($cat_id)
		{
			$this->db->query("SELECT descr FROM fm_tts_category  WHERE id='$cat_id' ");
			$this->db->next_record();
			return stripslashes($this->db->f('descr'));
		}

		function read($data)
		{
			$start			= isset($data['start']) && $data['start'] ? $data['start']:0;
			$status_id		= isset($data['status_id']) && $data['status_id'] ? $data['status_id']:'O'; //O='Open'
			$user_id		= isset($data['user_id']) && $data['user_id'] ? (int)$data['user_id']: 0;
			$owner_id		= isset($data['owner_id'])?$data['owner_id']:'';
			$query			= isset($data['query'])?$data['query']:'';
			$sort			= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
			$order			= isset($data['order'])?$data['order']:'';
			$cat_id			= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id']:0;
			$district_id	= isset($data['district_id']) && $data['district_id'] ? $data['district_id']:0;
			$allrows		= isset($data['allrows'])?$data['allrows']:'';
			$start_date		= isset($data['start_date'])?$data['start_date']:'';
			$end_date		= isset($data['end_date'])?$data['end_date']:'';
			$external		= isset($data['external'])?$data['external']:'';
			$dry_run		= isset($data['dry_run']) ? $data['dry_run'] : '';

			$this->grants 	= $GLOBALS['phpgw']->session->appsession('grants_ticket','property');

			if(!$this->grants)
			{
//				$this->grants	= $GLOBALS['phpgw']->acl->get_grants('property','.ticket');
				$GLOBALS['phpgw']->session->appsession('grants_ticket','property',$this->grants);
			}

			if ($order)
			{
				if( $order == 'assignedto' )
				{
					$order_join = "$this->join phpgw_accounts ON fm_tts_tickets.assignedto=phpgw_accounts.account_id";
					$order = 'account_lastname';
				}
				else if( $order == 'user' )
				{
					$order_join = "$this->join phpgw_accounts ON fm_tts_tickets.user_id=phpgw_accounts.account_id";
					$order = 'account_lastname';
				}
				else
				{
					$order_join = '';
				}
				
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY fm_tts_tickets.id DESC';
			}

			$where= 'WHERE';

			$filtermethod = '';
			$GLOBALS['phpgw']->config->read();
			if(isset($GLOBALS['phpgw']->config->config_data['acl_at_location']) && $GLOBALS['phpgw']->config->config_data['acl_at_location'])
			{
				$access_location = execMethod('property.socommon.get_location_list', PHPGW_ACL_READ);
				if($access_location)
				{
					$filtermethod = " WHERE fm_tts_tickets.loc1 in ('" . implode("','", $access_location) . "')";
					$where= 'AND';
				}
			}

			if (is_array($this->grants))
			{
				$grants = & $this->grants;
				foreach($grants as $user => $right)
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod .= " $where ( fm_tts_tickets.user_id IN(" . implode(',',$public_user_list) . "))";
				$where= 'AND';
			}

			if($tenant_id = $GLOBALS['phpgw']->session->appsession('tenant_id','property'))
			{
				$filtermethod .= $where . ' fm_tts_tickets.tenant_id=' . $tenant_id;
				$where = 'AND';
			}

			if ($status_id == 'X')
			{
				$filtermethod .= " $where fm_tts_tickets.status='X'";
				$where = 'AND';
			}
			else if($status_id == 'O')
			{
				$open = '';
				$this->db->query('SELECT * from fm_tts_status',__LINE__,__FILE__);

				while ($this->db->next_record())
				{
					if( ! $this->db->f('closed'))
					{
						$open .= " OR fm_tts_tickets.status = 'C" . $this->db->f('id') . "'";
					}
				}

				$filtermethod .= " $where (fm_tts_tickets.status='O'{$open})";
				$where = 'AND';
			}
			else if($status_id == 'all')
			{
				//nothing
			}
			else if(is_array($status_id) && count($status_id))
			{
				$or = '';
				$filtermethod .= "{$where} (";
				
				foreach ($status_id as $value)
				{
					$value = trim($value,'C');
					$filtermethod .= "{$or} fm_tts_tickets.status = '{$value}'";					
					$or = ' OR';
				}

				$filtermethod .= ')';

				$where = 'AND';
			}
			else
			{
				$filtermethod .= " $where fm_tts_tickets.status='{$status_id}'";
				$where = 'AND';
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " $where cat_id=" . (int)$cat_id;
				$where = 'AND';
			}

			if ($user_id > 0)
			{
				$filtermethod .= " {$where} (assignedto={$user_id}";
				$where = 'AND';

				$membership = $GLOBALS['phpgw']->accounts->membership($user_id);
				$filtermethod .= ' OR (assignedto IS NULL AND group_id IN (' . implode(',',array_keys($membership)) . ')))'; 
			}

			if ($owner_id > 0)
			{
				$filtermethod .= " $where fm_tts_tickets.user_id=" . (int)$owner_id;
				$where = 'AND';
			}

			if ($district_id > 0)
			{
				$filtermethod .= " $where  district_id=" .(int)$district_id;
				$where = 'AND';
			}

			if ($start_date)
			{
				$filtermethod .= " $where fm_tts_tickets.entry_date >= $start_date AND fm_tts_tickets.entry_date <= $end_date ";
				$where= 'AND';
			}

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$query = str_replace(",",'.',$query);
				if(stristr($query, '.'))
				{
					$query=explode(".",$query);
					$querymethod = " $where (fm_tts_tickets.loc1='" . $query[0] . "' AND fm_tts_tickets.loc4='" . $query[1] . "')";
				}
				else
				{
					$querymethod = " $where (subject $this->like '%$query%' OR address $this->like '%$query%' OR fm_tts_tickets.location_code $this->like '%$query%' OR fm_tts_tickets.order_id =" . (int)$query . ')';
				}
			}

			$sql = "SELECT fm_tts_tickets.* ,fm_location1.loc1_name FROM fm_tts_tickets"
			. " $this->join fm_location1 ON fm_tts_tickets.loc1=fm_location1.loc1"
			. " $this->join fm_part_of_town ON fm_location1.part_of_town_id=fm_part_of_town.part_of_town_id"
			. " $order_join"
			. " $filtermethod $querymethod";

			if(!$dry_run)
			{
				$this->db->query('SELECT count(*) as hits ' . substr($sql,strripos($sql,'from')),__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f('hits');
				$this->db->fetchmode = 'ASSOC';
				if(!$allrows)
				{
					$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
				}
				else
				{
					$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
				}

			}

			$tickets = array();

			while ($this->db->next_record())
			{
				$tickets[]= array
				(
					'id'				=> (int) $this->db->f('id'),
					'subject'			=> $this->db->f('subject',true),
					'loc1_name'			=> $this->db->f('loc1_name',true),
					'location_code'		=> $this->db->f('location_code'),
					'user_id'			=> $this->db->f('user_id'),
					'address'			=> $this->db->f('address',true),
					'assignedto'		=> $this->db->f('assignedto'),
					'status'			=> $this->db->f('status'),
					'priority'			=> $this->db->f('priority'),
					'cat_id'			=> $this->db->f('cat_id'),
					'group_id'			=> $this->db->f('group_id'),
					'entry_date'		=> $this->db->f('entry_date'),
					'finnish_date'		=> $this->db->f('finnish_date'),
					'finnish_date2'		=> $this->db->f('finnish_date2'),
					'order_id'			=> $this->db->f('order_id'),
					'vendor_id'			=> $this->db->f('vendor_id'),
					'new_ticket'		=> ''
				);
			}
			
			foreach ($tickets as &$ticket)
			{
				$this->db->query("SELECT count(*) as hits FROM fm_tts_views where id={$ticket['id']}"
					. " AND account_id='{$this->account}'",__LINE__,__FILE__);
				$this->db->next_record();

				if(! $this->db->f('hits'))
				{
					$ticket['new_ticket'] = true;
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
				$uicols[]	= $this->db->f('name');
				$i++;
			}

			$entity[$i]['type']='.project';
			$uicols[]	= 'project';

			$this->uicols_related	= $uicols;
			return $entity;
		}

		function read_single($id)
		{
			$sql = "SELECT * FROM fm_tts_tickets WHERE id=$id";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$ticket['assignedto']		= $this->db->f('assignedto');
				$ticket['user_id']			= $this->db->f('user_id');
				$ticket['group_id']			= $this->db->f('group_id');
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

				$user_id=(int)$this->db->f('user_id');
				$this->db->query("SELECT account_firstname,account_lastname FROM phpgw_accounts WHERE account_id='$user_id' ");
				$this->db->next_record();

				$ticket['user_name']	= $this->db->f('account_firstname') . " " .$this->db->f('account_lastname') ;
				if ($ticket['assignedto']>0)
				{
					$this->db->query("SELECT account_firstname,account_lastname FROM phpgw_accounts WHERE account_id='" . $ticket['assignedto'] . "'");
					$this->db->next_record();
					$ticket['assignedto_name']	= $this->db->f('account_firstname') . " " .$this->db->f('account_lastname') ;
				}

			}

			return $ticket;
		}

		function update_view($id='')
		{
			// Have they viewed this ticket before ?
			$id = (int) $id;
			$this->db->query("SELECT count(*) as cnt FROM fm_tts_views where id={$id}"
					. " AND account_id='" . $GLOBALS['phpgw_info']['user']['account_id'] . "'",__LINE__,__FILE__);
			$this->db->next_record();

			if (! $this->db->f('cnt'))
			{
				$this->db->query("INSERT INTO fm_tts_views (id,account_id,time) values ({$id},'"
					. $GLOBALS['phpgw_info']['user']['account_id'] . "','" . phpgwapi_datetime::user_localtime() . "')",__LINE__,__FILE__);
			}
		}

		function add($ticket)
		{
			if(isset($ticket['location']) && is_array($ticket['location']))
			{
				foreach ($ticket['location'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}

			if(isset($ticket['extra']) && is_array($ticket['extra']))
			{
				foreach ($ticket['extra'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= ",'" . implode("','", $vals) . "'";
			}

			$address = '';
			if(isset($ticket['street_name']) && $ticket['street_name'])
			{
				$address[]= $ticket['street_name'];
				$address[]= $ticket['street_number'];
				$address	= $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($ticket['location_name']);
			}

			$values= array
			(
				isset($ticket['priority'])?$ticket['priority']:0,
				$GLOBALS['phpgw_info']['user']['account_id'],
				$ticket['assignedto'],
				$ticket['group_id'],
				$this->db->db_addslashes($ticket['subject']),
				$ticket['cat_id'],
				$ticket['status'],
				$this->db->db_addslashes($ticket['details']),
				$ticket['location_code'],
				$address,
				phpgwapi_datetime::user_localtime(),
				$ticket['finnish_date'],
				$ticket['contact_id']
			);

			$values	= $this->db->validate_insert($values);
			$this->db->transaction_begin();

			$this->db->query("insert into fm_tts_tickets (priority,user_id,"
				. "assignedto,group_id,subject,cat_id,status,details,location_code,"
				. "address,entry_date,finnish_date,contact_id $cols)"
				. "VALUES ($values $vals )",__LINE__,__FILE__);

			$id = $this->db->get_last_insert_id('fm_tts_tickets','id');
			if(isset($ticket['extra']['contact_phone']) && $ticket['extra']['contact_phone'] && isset($ticket['extra']['tenant_id']) && $ticket['extra']['tenant_id'])
			{
				$this->db->query("update fm_tenant set contact_phone='". $ticket['extra']['contact_phone']. "' where id='". $ticket['extra']['tenant_id']. "'",__LINE__,__FILE__);
			}

			if(isset($ticket['origin']) && is_array($ticket['origin']))
			{
				if($ticket['origin'][0]['data'][0]['id'])
				{
					$interlink_data = array
					(
						'location1_id'		=> $GLOBALS['phpgw']->locations->get_id('property', $ticket['origin'][0]['location']),
						'location1_item_id' => $ticket['origin'][0]['data'][0]['id'],
						'location2_id'		=> $GLOBALS['phpgw']->locations->get_id('property', '.ticket'),			
						'location2_item_id' => $id,
						'account_id'		=> $this->account
					);

					$interlink 	= CreateObject('property.interlink');
					$interlink->add($interlink_data,$this->db);
				}
			}

			if($this->db->transaction_commit())
			{
				$this->historylog->add('O',$id, phpgwapi_datetime::user_localtime(),'');
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
			$sql = "SELECT * FROM fm_tts_status ORDER BY sorting ASC";
			$this->db->query($sql,__LINE__,__FILE__);

			$status= array();
			while ($this->db->next_record())
			{
				$status[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name', true),
					'color'	=> $this->db->f('color')
				);
			}
			return $status;
		}
		function update_status($ticket,$id = 0)
		{
			$id = (int) $id;
			$receipt = array();
			// DB Content is fresher than http posted value.
			$this->db->query("select * from fm_tts_tickets where id='$id'",__LINE__,__FILE__);
			$this->db->next_record();
			$old_status  		= $this->db->f('status');

			$this->db->transaction_begin();

			/*
			** phpgw_fm_tts_append.append_type - Defs
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
				$this->db->query("SELECT * from fm_tts_status WHERE id = {$check_old_custom}",__LINE__,__FILE__);
				$this->db->next_record();
				$this->fields_updated = true;
				if($old_status=='X' || $this->db->f('closed'))
				{
					$new_status = $ticket['status'];
					$this->historylog->add('R',$id,$ticket['status'],$old_status);

					$this->db->query("UPDATE fm_tts_tickets SET status='{$new_status}' WHERE id= {$id}",__LINE__,__FILE__);
				}
				else
				{
					$this->historylog->add($ticket['status'],$id,$ticket['status'],$old_status);
					$this->db->query("UPDATE fm_tts_tickets SET status='{$ticket['status']}' WHERE id={$id}",__LINE__,__FILE__);
				}
				$this->check_pending_action($ticket, $id);
			}

			$this->db->transaction_commit();

			if ($this->fields_updated)
			{
				$receipt['message'][]= array('msg' => lang('Ticket %1 has been updated',$id));
			}

			return $receipt;

		}

		function update_ticket($ticket,$id = 0)
		{
			$id = (int) $id;
			$receipt = array();
			// DB Content is fresher than http posted value.
			$this->db->query("select * from fm_tts_tickets where id='$id'",__LINE__,__FILE__);
			$this->db->next_record();


			$location_code 	= $this->db->f('location_code');
			$oldlocation_code 	= $this->db->f('location_code');
			$oldfinnish_date 	= $this->db->f('finnish_date');
			$oldfinnish_date2 	= $this->db->f('finnish_date2');
			$oldassigned 		= $this->db->f('assignedto');
			$oldgroup_id 		= $this->db->f('group_id');
			$oldpriority 		= $this->db->f('priority');
			$oldcat_id 			= $this->db->f('cat_id');
			$old_status  		= $this->db->f('status');
			$old_budget  		= $this->db->f('budget');
		//	$old_billable_hours	= $this->db->f('billable_hours');
		//	$old_billable_rate	= $this->db->f('billable_rate');
			$old_subject		= $this->db->f('subject');
			$old_contact_id		= $this->db->f('contact_id');
			$old_actual_cost	= $this->db->f('actual_cost');
			$old_order_cat_id	= $this->db->f('order_cat_id');

			if($oldcat_id ==0){$oldcat_id ='';}
			if($old_order_cat_id ==0){$old_order_cat_id ='';}
			if($oldassigned ==0){$oldassigned ='';}
			if($oldgroup_id ==0){$oldgroup_id ='';}

			// Figure out and last note

			$history_values = $this->historylog->return_array(array(),array('C'),'history_timestamp','DESC',$id);
			$old_note = $history_values[0]['new_value'];

			if(!$old_note)
			{
				$old_note = $this->db->f('details');
			}


			$this->db->transaction_begin();

			/*
			** phpgw_fm_tts_append.append_type - Defs
			** R - Reopen ticket
			** X - Ticket closed
			** O - Ticket opened
			** C - Comment appended
			** A - Ticket assignment
			** G - Ticket group assignment
			** P - Priority change
			** T - Category change
			** S - Subject change
			** B - Budget change
			** H - Billing hours
			** F - finnish date
			** C% - Status change
			** L - Location changed
			** M - Mail sent to vendor
			*/

			$finnish_date	= (isset($ticket['finnish_date']) ? phpgwapi_datetime::date_to_timestamp($ticket['finnish_date']):'');

			if ($oldfinnish_date && isset($ticket['finnish_date']) && $ticket['finnish_date']):
			{
				$this->db->query("update fm_tts_tickets set finnish_date2='" . $finnish_date
					. "' where id='$id'",__LINE__,__FILE__);
			}
			elseif(!$oldfinnish_date && isset($ticket['finnish_date']) && $ticket['finnish_date']):
			{
				$this->db->query("update fm_tts_tickets set finnish_date='" . $finnish_date
					. "' where id='$id'",__LINE__,__FILE__);
			}
			endif;

			if($oldfinnish_date2>0)
			{
				$oldfinnish_date = $oldfinnish_date2;
			}
			if(isset($ticket['finnish_date']) && $ticket['finnish_date'])
			{
				if ($oldfinnish_date != $finnish_date)
				{
					$this->fields_updated = true;
					$this->historylog->add('F',$id,$finnish_date,$oldfinnish_date);
				}
			}

			if (isset($ticket['status']) && ($old_status != $ticket['status']))
			{
				$check_old_custom = (int) trim($old_status,'C');
				$this->db->query("SELECT * from fm_tts_status WHERE id = {$check_old_custom}",__LINE__,__FILE__);
				$this->db->next_record();
				$this->fields_updated = true;
				if($old_status=='X' || $this->db->f('closed'))
				{
					$new_status = $ticket['status'];
					$this->historylog->add('R',$id,$ticket['status'],$old_status);

					$this->db->query("UPDATE fm_tts_tickets SET status='{$new_status}' WHERE id= {$id}",__LINE__,__FILE__);
				}
				else
				{
					$this->historylog->add($ticket['status'],$id,$ticket['status'],$old_status);
					$this->db->query("UPDATE fm_tts_tickets SET status='{$ticket['status']}' WHERE id={$id}",__LINE__,__FILE__);
				}
				$this->check_pending_action($ticket, $id);
			}

			if (($oldassigned != $ticket['assignedto']) && $ticket['assignedto'] != 'ignore')
			{
				$this->fields_updated = true;

				$value_set=array('assignedto'	=> $ticket['assignedto']);
				$value_set	= $this->db->validate_update($value_set);

				$this->db->query("update fm_tts_tickets set $value_set where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('A',$id,$ticket['assignedto'],$oldassigned);
			}

			if (($oldgroup_id != $ticket['group_id']) && $ticket['group_id'] != 'ignore')
			{
				$this->fields_updated = true;

				$value_set=array('group_id'	=> $ticket['group_id']);
				$value_set	= $this->db->validate_update($value_set);

				$this->db->query("update fm_tts_tickets set $value_set where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('G',$id,$ticket['group_id'],$oldgroup_id);
			}

			if ($oldpriority != $ticket['priority'])
			{
				$this->fields_updated = true;
				$this->db->query("update fm_tts_tickets set priority='" . $ticket['priority']
					. "' where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('P',$id,$ticket['priority'],$oldpriority);
			}

			if ($old_contact_id != $ticket['contact_id'])
			{
				$contact_id  = (int) $ticket['contact_id'];
				$this->fields_updated = true;
				$this->db->query("update fm_tts_tickets set contact_id={$contact_id} WHERE id=$id",__LINE__,__FILE__);
			}

			if (($oldcat_id != $ticket['cat_id']) && $ticket['cat_id'] != 'ignore')
			{
				$this->fields_updated = true;
				$this->db->query("update fm_tts_tickets set cat_id='" . $ticket['cat_id']
					. "' where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('T',$id,$ticket['cat_id'],$oldcat_id);
			}

			if ($old_budget != $ticket['budget'])
			{
				$this->fields_updated = true;
				$this->db->query("UPDATE fm_tts_tickets set budget='" . (int)$ticket['budget']
					. "' where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('B',$id,$ticket['budget'],$old_budget);
			}
	/*
			if ($old_billable_rate != $ticket['billable_rate'])
			{
				$this->fields_updated = true;
				$this->db->query("update fm_tts_tickets set billable_rate='" . $ticket['billable_rate']
					. "' where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('B',$id,$ticket['billable_rate'],$old_billable_rate);
			}
	*/
			if ($old_subject != $ticket['subject'])
			{
				$this->db->query("UPDATE fm_tts_tickets SET subject='" . $ticket['subject']
					. "' where id='$id'",__LINE__,__FILE__);
				$this->historylog->add('S',$id,$ticket['subject'],$old_subject);
				$receipt['message'][]= array('msg' => lang('Subject has been updated'));
			}

			if ((int)$old_actual_cost != (int)$ticket['actual_cost'])
			{
				$this->db->query("UPDATE fm_tts_tickets SET actual_cost='" . (float)$ticket['actual_cost']
					. "' WHERE id='$id'",__LINE__,__FILE__);
				$this->historylog->add('AC',$id,(float)$ticket['actual_cost'] , $old_actual_cost);
				$receipt['message'][]= array('msg' => lang('actual_cost has been updated'));
			}

			if ((int)$old_order_cat_id != (int)$ticket['order_cat_id'])
			{
				$this->db->query("UPDATE fm_tts_tickets SET order_cat_id='" . (int)$ticket['order_cat_id']
					. "' WHERE id='$id'",__LINE__,__FILE__);
				$receipt['message'][]= array('msg' => lang('order category has been updated'));
				$this->fields_updated = true;
			}


			if (($old_note != $ticket['note']) && $ticket['note'])
			{
				$this->fields_updated = true;
				$this->historylog->add('C',$id,$ticket['note'],$old_note);
			}

			if(isset($ticket['location']) && $ticket['location'])
			{
				$ticket['location_code'] = implode('-', $ticket['location']);
			}

			if (isset($ticket['location_code']) && $ticket['location_code'] && ($oldlocation_code != $ticket['location_code']))
			{
				$interlink 	= CreateObject('property.interlink');
				if( $interlink->get_relation('property', '.ticket', $id, 'origin') || $interlink->get_relation('property', '.ticket', $id, 'target'))
				{
					$receipt['message'][]= array('msg' => lang('location could not be changed'));
				}
				else
				{
					$value_set	= array();

					if(isset($ticket['street_name']) && $ticket['street_name'])
					{
						$address[]= $ticket['street_name'];
						$address[]= $ticket['street_number'];
						$value_set['address'] = $this->db->db_addslashes(implode(" ", $address));
					}

					if(!isset($address) || !$address)
					{
						$address = isset($ticket['location_name']) ? $this->db->db_addslashes($ticket['location_name']) : '';
						if($address)
						{
							$value_set['address'] = $address;
						}
					}

					if (isset($ticket['location_code']) && $ticket['location_code'])
					{
						$value_set['location_code'] = $ticket['location_code'];
					}

					$admin_location	= CreateObject('property.soadmin_location');
					$admin_location->read(false);

				// Delete old values for location - in case of moving up in the hierarchy
					$metadata = $this->db->metadata('fm_tts_tickets');
					for ($i = 1;$i < $admin_location->total_records + 1; $i++)
					{
						if(isset($metadata["loc{$i}"]))
						{
							$value_set["loc{$i}"]	= false;
						}
					}

					if(isset($ticket['location']) && is_array($ticket['location']))
					{
						foreach ($ticket['location'] as $column => $value)
						{
							$value_set[$column]	= $value;
						}
					}

					if(isset($ticket['extra']) && is_array($ticket['extra']))
					{
						foreach ($ticket['extra'] as $column => $value)
						{
							$value_set[$column]	= $value;
						}
					}

					$value_set	= $this->db->validate_update($value_set);

					$this->db->query("UPDATE fm_tts_tickets SET $value_set WHERE id={$id}",__LINE__,__FILE__);

					$this->historylog->add('L',$id,$ticket['location_code'],$oldlocation_code);
					$receipt['message'][]= array('msg' => lang('Location has been updated'));
				}
				unset($interlink);
			}


			if(isset($ticket['make_order']) && $ticket['make_order'])
			{
				$order_id = execMethod('property.socommon.increment_id', 'order');
				if($order_id)
				{
					$this->db->query("UPDATE fm_tts_tickets SET order_id = {$order_id} WHERE id={$id}",__LINE__,__FILE__);
				}
			}

			$value_set					= array();
			$value_set['vendor_id']		= $ticket['vendor_id'];
			$value_set['b_account_id']	= $ticket['b_account_id'];
			$value_set['order_descr']	= $this->db->db_addslashes($ticket['order_descr']);
			$value_set['ecodimb']		= $ticket['ecodimb'];
			$value_set['budget']		= $ticket['budget'];
			$value_set					= $this->db->validate_update($value_set);
			$this->db->query("UPDATE fm_tts_tickets SET $value_set WHERE id={$id}",__LINE__,__FILE__);

			$this->db->transaction_commit();

			if (isset($this->fields_updated))
			{
				$receipt['message'][]= array('msg' => lang('Ticket has been updated'));

				$criteria = array
				(
					'appname'	=> 'property',
					'location'	=> $this->acl_location,
					'allrows'	=> true
				);

				$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

				foreach ( $custom_functions as $entry )
				{
					// prevent path traversal
					if ( preg_match('/\.\./', $entry['file_name']) )
					{
						continue;
					}

					$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
					if ( $entry['active'] && is_file($file) )
					{
						require_once $file;
					}
				}
			}
			return $receipt;
		}

		function check_pending_action($ticket,$id)
		{
			$status = (int)trim($ticket['status'], 'C');
			$this->db->query("SELECT * FROM fm_tts_status WHERE id = '{$status}'");

			$this->db->next_record();

			if ($this->db->f('approved') )
			{
				$action_params = array
				(
					'appname'			=> 'property',
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
					'appname'			=> 'property',
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

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');

			if ( !$location_id )
			{
				throw new Exception("phpgwapi_locations::get_id ('property', '.ticket') returned 0");
			}

			$this->db->transaction_begin();	

			$this->db->query("DELETE FROM fm_action_pending WHERE location_id = {$location_id} AND item_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_interlink WHERE location1_id = {$location_id} AND location1_item_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_interlink WHERE location2_id = {$location_id} AND location2_item_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_tts_history WHERE history_record_id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_tts_views WHERE id = {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_tts_tickets WHERE id = {$id}",__LINE__,__FILE__);

			if($this->db->transaction_commit())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
