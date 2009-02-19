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

	/**
	 * Description
	 * @package property
	 */

	class property_sotts
	{
		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->historylog	= CreateObject('property.historylog','tts');
			$this->socommon		= CreateObject('property.socommon');
			$this->db 			= & $GLOBALS['phpgw']->db;
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
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start']:0;
				$status_id		= isset($data['status_id']) && $data['status_id'] ? $data['status_id']:'O'; //O='Open'
				$user_id		= isset($data['user_id'])?$data['user_id']:'';
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
			}

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
				$access_location = $this->socommon->get_location_list(PHPGW_ACL_READ);
				$filtermethod = " WHERE fm_tts_tickets.loc1 in ('" . implode("','", $access_location) . "')";
				$where= 'AND';
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
			}

			if ($status_id == 'X')
			{
				$filtermethod .= " $where fm_tts_tickets.status='X'";
				$where = 'AND';
			}
			else if($status_id == 'O')
			{
				$filtermethod .= " $where (fm_tts_tickets.status='O' OR fm_tts_tickets.status $this->like 'C%')";
				$where = 'AND';
			}
			else if($status_id == 'all')
			{
				//nothing
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
				$filtermethod .= " $where assignedto=" . (int)$user_id;
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
					$querymethod = " $where (subject $this->like '%$query%' or address $this->like '%$query%' or fm_tts_tickets.location_code $this->like '%$query%')";
				}
			}

			$sql = "SELECT fm_tts_tickets.* FROM fm_tts_tickets"
			. " $this->join fm_location1 ON fm_tts_tickets.loc1=fm_location1.loc1"
			. " $this->join fm_part_of_town ON fm_location1.part_of_town_id=fm_part_of_town.part_of_town_id"
			. " $order_join"
			. " $filtermethod $querymethod";

//echo $sql;

			if(!$dry_run)
			{
				$this->db->query('SELECT count(*) ' . substr($sql,strripos($sql,'from')),__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f(0);
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
					'new_ticket'		=> ''
				);
			}
			
			foreach ($tickets as &$ticket)
			{
				$this->db->query("SELECT count(*) FROM fm_tts_views where id={$ticket['id']}"
					. " AND account_id='" . $GLOBALS['phpgw_info']['user']['account_id'] . "'",__LINE__,__FILE__);
				$this->db->next_record();

				if(! $this->db->f(0))
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
			$uicols[]	= lang('project');

			$this->uicols	= $uicols;
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
				$ticket['address']			= $this->db->f('address', true);
				$ticket['tenant_id']		= $this->db->f('tenant_id');
				$ticket['p_num']			= $this->db->f('p_num');
				$ticket['p_entity_id']		= $this->db->f('p_entity_id');
				$ticket['p_cat_id']			= $this->db->f('p_cat_id');
				$ticket['finnish_date']		= $this->db->f('finnish_date');
				$ticket['finnish_date2']	= $this->db->f('finnish_date2');

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
			$this->db->query("SELECT count(*) FROM fm_tts_views where id={$id}"
					. " AND account_id='" . $GLOBALS['phpgw_info']['user']['account_id'] . "'",__LINE__,__FILE__);
			$this->db->next_record();

			if (! $this->db->f(0))
			{
				$this->db->query("INSERT INTO fm_tts_views (id,account_id,time) values ({$id},'"
					. $GLOBALS['phpgw_info']['user']['account_id'] . "','" . time() . "')",__LINE__,__FILE__);
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

			$values= array(
				$ticket['priority'],
				$GLOBALS['phpgw_info']['user']['account_id'],
				$ticket['assignedto'],
				$ticket['group_id'],
				$this->db->db_addslashes($ticket['subject']),
				$ticket['cat_id'],
				'O',
				$this->db->db_addslashes($ticket['details']),
				$ticket['location_code'],
				$address,
				time(),
				$ticket['finnish_date']);

			$values	= $this->db->validate_insert($values);
			$this->db->transaction_begin();

			$this->db->query("insert into fm_tts_tickets (priority,user_id,"
				. "assignedto,group_id,subject,cat_id,status,details,location_code,"
				. "address,entry_date,finnish_date $cols)"
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
						'location2_item_id' => $ticket['project_id'],
						'account_id'		=> $this->account
					);
					
					$interlink 	= CreateObject('property.interlink');
					$interlink->add($interlink_data,$this->db);
				}
			}

			if($this->db->transaction_commit())
			{
				$this->historylog->add('O',$id,mktime(),'');
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
			$sql = "SELECT * FROM fm_tts_status";
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
	}
