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
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class sms_sosms
	{
		var $grants;
		var $db;
		var $account;

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db 			= clone $GLOBALS['phpgw']->db;

			$this->left_join	= $this->db->left_join;
			$this->join			= $this->db->join;
			$this->like			= $this->db->like;
		}

		function read_inbox($data)
		{
			$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query			= isset($data['query']) ? $data['query'] : '';
			$sort			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order			= isset($data['order']) ? $data['order'] : '';
			$allrows		= isset($data['allrows']) ? $data['allrows'] : '';
			$acl_location	= isset($data['acl_location']) ? $data['acl_location'] : '';

			if($acl_location)
			{
				$GLOBALS['phpgw']->acl->set_account_id($this->account);
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
				$query = $this->db->db_addslashes($query);

				$querymethod = " $where in_sender $this->like '%$query%' OR in_msg $this->like '%$query%'";

				$where= 'AND';
			}

			$sql = "SELECT * FROM $table $filtermethod $querymethod $where in_hidden='0'";

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

			$inbox = array();
			while ($this->db->next_record())
			{
				$inbox[] = array
				(
					'id'			=> $this->db->f('in_id'),
					'sender'		=> $this->db->f('in_sender',true),
					'entry_time'	=> $this->db->f('in_datetime'),
					'message'		=> $this->db->f('in_msg',true),
					'user'			=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('in_uid')),
					'grants'		=> (int)isset($grants[$this->db->f('in_uid')])?$grants[$this->db->f('in_uid')]:0
				);
			}

			return $inbox;
		}

		function read_outbox($data)
		{
			$start			= isset($data['start']) && $data['start'] ? (int)$data['start']:0;
			$query			= isset($data['query'])?$data['query']:'';
			$sort			= isset($data['sort'])?$data['sort']:'DESC';
			$order			= isset($data['order'])?$data['order']:'';
			$allrows		= isset($data['allrows'])?$data['allrows']:'';
			$acl_location	= isset($data['acl_location'])?$data['acl_location']:'';

			if($acl_location)
			{
				$GLOBALS['phpgw']->acl->set_account_id($this->account);
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
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND p_dst $this->like '%$query%' OR p_msg $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $filtermethod $querymethod AND flag_deleted='0'";

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


			$status_array = array
			(
				0 => lang('pending'),
	     		1 => lang('sent'),
				2 => lang('failed'),
				3 => lang('delivered')
			);

			$outbox = array();
			while ($this->db->next_record())
			{
				$outbox[] = array
				(
					'id'			=> $this->db->f('smslog_id'),
					'p_dst'			=> $this->db->f('p_dst',true),
					'user'			=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('uid')),
					'dst_group'		=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('p_gpid')),
					'entry_time'	=> $this->db->f('p_datetime'),
					'message'		=> $this->db->f('p_msg',true),
					'status'		=> $status_array[$this->db->f('p_status')],
					'grants'		=> (int)$grants[$this->db->f('uid')]
				);

			}
			return $outbox;
		}

		function delete_out($id)
		{
			$this->db->query("UPDATE phpgw_sms_tblsmsoutgoing SET flag_deleted='1' WHERE smslog_id="  . intval($id),__LINE__,__FILE__);
		}

		function delete_in($id)
		{
			$this->db->query("UPDATE phpgw_sms_tbluserinbox SET in_hidden='1' WHERE in_id="  . intval($id),__LINE__,__FILE__);
		}
	}
