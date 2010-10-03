<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage command
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_socommand
	{
		var $grants;
		var $db;
		var $db2;
		var $account;
		var $command_data;

		function sms_socommand()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('sms.bocommon');
			$this->db 		= clone($GLOBALS['phpgw']->db);
			$this->db2 		= clone($this->db);

			$this->grants		= $GLOBALS['phpgw']->acl->get_grants('sms','.config');
			$this->join		= $this->db->join;
			$this->like		= $this->db->like;
		}



		function read($data)
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
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by command_code asc';
			}

			$table = 'phpgw_sms_featcommand';

			$where= 'WHERE';
			$grants = $this->grants;

/*			if (is_array($grants))
			{
				while (list($user) = each($grants))
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod .= " $where ( $table.uid IN(" . implode(',',$public_user_list) . "))";

				$where= 'AND';
			}
*/

			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " $where command_code $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $filtermethod $querymethod";

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
				$command_info[] = array
				(
					'id'	=> $this->db->f('command_id'),
					'uid'	=> $this->db->f('uid'),
					'code'	=> stripslashes($this->db->f('command_code')),
					'exec'	=> stripslashes($this->db->f('command_exec')),
					'grants'	=> (int)$grants[$this->db->f('uid')]
				);
			}

			return $command_info;
		}


		function read_log($data)
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
				$cat_id	= (isset($data['cat_id'])?$data['cat_id']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by command_log_id desc';
			}

			$table = 'phpgw_sms_featcommand_log';

			$where= 'WHERE';

			if($cat_id)
			{
				$filtermethod = " $where command_log_code = '$cat_id'";
				$where= 'AND';
			}

			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " $where command_log_code $this->like '%$query%' OR command_log_param $this->like '%$query%' OR sms_sender $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $filtermethod $querymethod";

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
				$command_info[] = array
				(
					'id'	=> $this->db->f('command_log_id'),
					'sender'=> $this->db->f('sms_sender'),
					'success'=> $this->db->f('command_log_success'),
					'datetime'=> $this->db->f('command_log_datetime'),
					'code'	=> stripslashes($this->db->f('command_log_code')),
					'param'	=> stripslashes($this->db->f('command_log_param'))
				);
			}

			return $command_info;
		}

		function get_category_list()
		{
			$sql = "SELECT command_code FROM phpgw_sms_featcommand GROUP BY command_code";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$values[] = array(
					'id'=> $this->db->f('command_code'),
					'name'	=> $this->db->f('command_code')
					);
			}
			return $values;
		}

		function read_single_command($id)
		{
			$sql = 'SELECT * FROM phpgw_sms_featcommand WHERE command_id=' . intval($id);
			$this->db->query($sql,__LINE__,__FILE__);
			$bin_path = PHPGW_SERVER_ROOT . 'sms/bin';
			if ($this->db->next_record())
			{
				$values['id']		= $id;
				$values['code']		= stripslashes($this->db->f('command_code'));
				$values['exec']		= stripslashes(str_replace($bin_path,'',$this->db->f('command_exec')));
				$values['type']		= $this->db->f('command_type');
				$values['descr']	= stripslashes($this->db->f('command_descr'));
			}
			return $values;
		}


		function add_command($values)
		{
			$this->db->transaction_begin();

			$values['exec'] = PHPGW_SERVER_ROOT . '/sms/bin/' . $values['exec'];
			$values['exec'] = str_replace("//","/",$values['exec']);
			$values['exec'] = str_replace("..",".",$values['exec']);
			$values['exec'] = $this->db->db_addslashes($values['exec']);
			$values['code'] = $this->db->db_addslashes($values['code']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$insert_values=array(
				$this->account,
				$values['code'],
				$values['exec'],
				$values['type'],
				$values['descr'],
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);

			$this->db->query("INSERT INTO phpgw_sms_featcommand (uid,command_code,command_exec,command_type,command_descr) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('SMS command code %1 has been added',$values['code']));
			$receipt['command_id']= $this->db->get_last_insert_id(phpgw_sms_featcommand,'command_id');

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit_command($values)
		{
			$this->db->transaction_begin();

			$values['exec'] = PHPGW_SERVER_ROOT . '/sms/bin/' . $values['exec'];
			$values['exec'] = str_replace("//","/",$values['exec']);
			$values['exec'] = str_replace("..",".",$values['exec']);
			$value_set['command_type'] 	= $this->db->db_addslashes($values['type']);
			$value_set['command_exec'] 	= $this->db->db_addslashes($values['exec']);
			$value_set['command_code'] 	= $this->db->db_addslashes($values['code']);
			$value_set['command_descr']	= $this->db->db_addslashes($values['descr']);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE phpgw_sms_featcommand set $value_set WHERE command_id=" . $values['command_id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('SMS command code %1 has been saved',$values['code']));

			$receipt['command_id']= $values['command_id'];
			return $receipt;
		}

		function delete_command($id)
		{
			$this->db->transaction_begin();
			$this->db->query('DELETE FROM phpgw_sms_command_value WHERE command_id =' . intval($command_id),__LINE__,__FILE__);
			$this->db->query('DELETE FROM phpgw_sms_command_choice WHERE command_id =' . intval($command_id),__LINE__,__FILE__);
			$this->db->query('DELETE FROM phpgw_sms_command_attrib WHERE command_id =' . intval($command_id),__LINE__,__FILE__);
			$this->db->query('DELETE FROM phpgw_sms_command_command WHERE id='  . intval($id),__LINE__,__FILE__);
			$this->db->transaction_commit();
		}
	}
