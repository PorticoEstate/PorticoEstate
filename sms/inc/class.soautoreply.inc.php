<?php
	/**
	 * phpGroupWare - SMS: A SMS Gateway.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package sms
	 * @subpackage autoreply
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package sms
	 */
	class sms_soautoreply
	{
		var $db;
		var $account;
		var $autoreply_data;

		function __construct()
		{
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db = clone($GLOBALS['phpgw']->db);

			$GLOBALS['phpgw']->acl->set_account_id($this->account);
			$this->join = $this->db->join;
			$this->like = $this->db->like;
		}

		function read( $data )
		{
			$start = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query = isset($data['query']) ? $data['query'] : '';
			$sort = isset($data['sort']) ? $data['sort'] : 'DESC';
			$order = isset($data['order']) ? $data['order'] : '';
			$allrows = isset($data['allrows']) ? $data['allrows'] : '';

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by autoreply_code asc';
			}

			$table = 'phpgw_sms_featautoreply';

			$where = 'WHERE';

			$querymethod = '';
			if ($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where autoreply_code $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $filtermethod $querymethod";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			if (!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
			}

			$autoreply_info = array();
			while ($this->db->next_record())
			{
				$autoreply_info[] = array
					(
					'id' => $this->db->f('autoreply_id'),
					'uid' => $this->db->f('uid'),
					'code' => stripslashes($this->db->f('autoreply_code')),
				);
			}

			return $autoreply_info;
		}
	}