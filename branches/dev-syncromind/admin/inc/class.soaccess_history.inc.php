<?php
	/**************************************************************************\
	* phpGroupWare - Administration                                            *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class soaccess_history
	{
		var $db;

		function soaccess_history()
		{
			$this->db =& $GLOBALS['phpgw']->db;
		}

		function test_account_id($account_id)
		{
			if ($account_id)
			{
				return ' WHERE account_id=' . (int) $account_id;
			}
			return '';
		}

		function list_history($account_id,$start,$order,$sort)
		{
			$where = $this->test_account_id($account_id);

			$this->db->limit_query("SELECT loginid, ip, li, lo, account_id, sessionid FROM phpgw_access_log $where ORDER BY li desc", $start,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$records[] = array(
					'loginid'    => $this->db->f('loginid'),
					'ip'         => $this->db->f('ip'),
					'li'         => $this->db->f('li'),
					'lo'         => $this->db->f('lo'),
					'account_id' => $this->db->f('account_id'),
					'sessionid'  => $this->db->f('sessionid')
				);
			}
			return $records;
		}

		function total($account_id)
		{
			$where = $this->test_account_id($account_id);

			$this->db->query("SELECT COUNT(*) as cnt from phpgw_access_log $where", __LINE__, __FILE__);
			$this->db->next_record();

			return $this->db->f('cnt');
		}

		function return_logged_out($account_id)
		{
			if ($account_id)
			{
				$where  = 'WHERE account_id=' . (int) $account_id . ' AND lo !=0';
			}
			else
			{
				$where = 'WHERE lo !=0';
			}
			$this->db->query("SELECT COUNT(*) as cnt FROM phpgw_access_log $where", __LINE__, __FILE__);
			$this->db->next_record();

			return $this->db->f('cnt');
		}
	}
