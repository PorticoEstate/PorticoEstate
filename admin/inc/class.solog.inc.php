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

	class solog
	{
		var $db;

		function __construct()
		{
			$this->db =& $GLOBALS['phpgw']->db;
		}

		function test_account_id($account_id, $date = null)
		{
			$account_id = (int) $account_id;

			$where = 'WHERE';

			$filtermethod = '';

			if ($account_id > 0)
			{
				$filtermethod.= " $where log_account_id = $account_id";
				$where = 'AND';
			}

			if ($date > 0)
			{
				$limit_date = date($this->db->date_format(), $date);
				$filtermethod.= " $where log_date <= '$limit_date 23:59:59'";
				$where = 'AND';
			}
			return $filtermethod;
		}

		function list_log($account_id,$start,$order,$sort, $date = null)
		{
			$where = $this->test_account_id($account_id, $date);
			$this->db->limit_query("SELECT log_date,log_account_lid,log_app,log_severity,"
				. "log_file,log_line,log_msg FROM phpgw_log"
				. " $where ORDER BY log_id desc",$start, __LINE__, __FILE__);
			$records = array();
			while ($this->db->next_record())
			{
				$records[] = array(
					'log_date'        	=> $this->db->f('log_date'),
					'log_account_lid' 	=> $this->db->f('log_account_lid', true),
					'log_app'         	=> $this->db->f('log_app', true),
					'log_severity'    	=> $this->db->f('log_severity'),
					'log_file' 			=> $this->db->f('log_file', true),
					'log_line'  		=> $this->db->f('log_line'),
					'log_msg'  			=> $this->db->f('log_msg',true)
				);
			}
			return $records;
		}

		function total($account_id, $date = null)
		{
			$where = $this->test_account_id($account_id, $date);

			$this->db->query("SELECT COUNT(*) as cnt FROM phpgw_log $where", __LINE__, __FILE__);
			$this->db->next_record();

			return $this->db->f('cnt');
		}
		
		function purge_log($account_id)
		{
			$where = $this->test_account_id($account_id);

			$this->db->query("delete from phpgw_log $where", __LINE__, __FILE__);
			if ( isset($this->db->Errno) && $this->db->Errno )
			{
				log_error(array('text' => 'Failed to delete log records from database using where clause of %1. DB errno %2: message %3',
								'p1' => $where,
       							'p2' => $this->db->Errno,
       							'p3' => $this->db->Error,
								'file' => __FILE__,
						 		'line' => __LINE__));
				return false;
			}
			return true;
		}

	}
