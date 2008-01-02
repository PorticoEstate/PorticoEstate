<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage place
 	* @version $Id: class.sosms.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class sms_sosms
	{
		var $grants;
		var $db;
		var $db2;
		var $account;

		function sms_sosms()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon	= CreateObject('sms.bocommon');
			$this->db 		= clone($GLOBALS['phpgw']->db);
			$this->db2 		= clone($this->db);

			$this->left_join	= $this->bocommon->left_join;
			$this->join		= $this->bocommon->join;
			$this->like		= $this->db->like;
		}

		function read_inbox($data)
		{
			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$query		= (isset($data['query'])?$data['query']:'');
				$sort		= (isset($data['sort'])?$data['sort']:'DESC');
				$order		= (isset($data['order'])?$data['order']:'');
				$allrows	= (isset($data['allrows'])?$data['allrows']:'');
				$acl_location	= (isset($data['acl_location'])?$data['acl_location']:'');
			}

			if($acl_location)
			{
				$grants		= $GLOBALS['phpgw']->acl->get_grants('sms',$acl_location);
			}

//_debug_array($grants);
			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";

			}
			else
			{
				$ordermethod = ' ORDER BY in_id DESC';
			}

			$table = 'phpgw_sms_tbluserinbox';

			$where= 'WHERE';
			
/*			if (is_array($grants))
			{
				while (list($user) = each($grants))
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod .= " $where ( $table.in_uid IN(" . implode(',',$public_user_list) . "))";

				$where= 'AND';
			}
*/
			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " $where in_sender $this->like '%$query%' OR in_msg $this->like '%$query%'";
				
				$where= 'AND';
			}

			$sql = "SELECT * FROM $table $filtermethod $querymethod $where in_hidden='0'";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$inbox[] = array
				(
					'id'		=> $this->db->f('in_id'),
					'sender'	=> stripslashes($this->db->f('in_sender')),
					'entry_time'	=> $this->db->f('in_datetime'),
					'message'	=> stripslashes($this->db->f('in_msg')),
					'user'		=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('in_uid')),
					'grants'	=> (int)isset($grants[$this->db->f('in_uid')])?$grants[$this->db->f('in_uid')]:0
				);

			}

			return $inbox;
		}

		function read_outbox($data)
		{
			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$query		= (isset($data['query'])?$data['query']:'');
				$sort		= (isset($data['sort'])?$data['sort']:'DESC');
				$order		= (isset($data['order'])?$data['order']:'');
				$allrows	= (isset($data['allrows'])?$data['allrows']:'');
				$acl_location	= (isset($data['acl_location'])?$data['acl_location']:'');
			}

			if($acl_location)
			{
				$grants		= $GLOBALS['phpgw']->acl->get_grants('sms',$acl_location);
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";

			}
			else
			{
				$ordermethod = ' ORDER BY smslog_id DESC';
			}

			$table = 'phpgw_sms_tblsmsoutgoing';


			$where= 'WHERE';
			
			if (is_array($grants))
			{
				while (list($user) = each($grants))
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod = " $where ( $table.uid IN(" . implode(',',$public_user_list) . "))";

				$where= 'AND';
			}

			$querymethod = '';
			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " AND p_dst $this->like '%$query%' OR p_msg $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $filtermethod $querymethod AND flag_deleted='0'";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			
			$status_array = array(
					0 => lang('pending'),
	     				1 => lang('sent'),
					2 => lang('failed'),
					3 => lang('delivered')
					);

			while ($this->db->next_record())
			{
				$outbox[] = array
				(
					'id'		=> $this->db->f('smslog_id'),
					'p_dst'		=> stripslashes($this->db->f('p_dst')),
					'user'		=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('uid')),
					'dst_group'	=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('p_gpid')),
					'entry_time'	=> $this->db->f('p_datetime'),
					'message'	=> stripslashes($this->db->f('p_msg')),
					'status'	=> $status_array[$this->db->f('p_status')],
					'grants'	=> (int)$grants[$this->db->f('uid')]
				);

			}
			return $outbox;
		}


		function read_single($id)
		{
			$sql = 'SELECT * FROM phpgw_hrm_training_place where id=' . intval($id);

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$values['id']		= $id;
				$values['name']	= stripslashes($this->db->f('name'));
				$values['address']	= stripslashes($this->db->f('address'));
				$values['remark']	= stripslashes($this->db->f('remark'));
				$values['town']	= stripslashes($this->db->f('town'));
				$values['zip']	= $this->db->f('zip');
				$values['entry_date']	= $this->db->f('entry_date');
				$values['owner']	= $this->db->f('owner');
			}
			return $values;
		}


		function add($values)
		{
			$this->db->transaction_begin();

			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['address'] = $this->db->db_addslashes($values['address']);
			$values['town'] = $this->db->db_addslashes($values['town']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);
			$values['place_id'] = $this->bocommon->next_id('phpgw_hrm_training_place');

			$insert_values=array(
				$values['place_id'],
				$values['name'],
				$values['address'],
				$values['zip'],
				$values['town'],
				$values['remark'],
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);
			$this->db->query("INSERT INTO phpgw_hrm_training_place (id,name,address,zip,town, remark) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('training item has been saved'));
			$receipt['place_id']= $values['place_id'];

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit($values)
		{
			$this->db->transaction_begin();

			$value_set['name']			= $this->db->db_addslashes($values['name']);
			$value_set['address']			= $this->db->db_addslashes($values['address']);
			$value_set['zip']	= $values['zip'];
			$value_set['remark']		= $this->db->db_addslashes($values['remark']);
			$value_set['town']			= $this->db->db_addslashes($values['town']);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE phpgw_hrm_training_place set $value_set WHERE id=" . $values['place_id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('Place item has been edited'));

			$receipt['place_id']= $values['place_id'];
			return $receipt;
		}

		function delete_out($id)
		{
			$this->db->query("UPDATE phpgw_sms_tblsmsoutgoing SET flag_deleted='1' WHERE smslog_id="  . intval($id),__LINE__,__FILE__);
		}

		function delete_in($id)
		{
			$this->db->query("UPDATE phpgw_sms_tbluserinbox SET in_hidden='1' WHERE in_id="  . intval($id),__LINE__,__FILE__);
		}


		function select_place_list()
		{
			$this->db->query("SELECT * FROM phpgw_hrm_training_place  ORDER BY name ");

			$i = 0;
			while ($this->db->next_record())
			{
				$place[$i]['id']		= $this->db->f('id');
				$place[$i]['name']		= stripslashes($this->db->f('name'));
				$i++;
			}
			return $place;
		}
	}
