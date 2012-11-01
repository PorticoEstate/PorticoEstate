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
	* @subpackage project
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_socondition_survey
	{
		/**
		* @var int $_total_records total number of records found
		*/
		protected $_total_records = 0;


		/**
		* @var int $_receipt feedback on actions
		*/
		protected $_receipt = array();


		/**
		 * @var object $_db reference to the global database object
		 */
		protected $_db;

		/**
		 * @var string $_join SQL JOIN statement
		 */
		protected $_join;

		/**
		 * @var string $_like SQL LIKE statement
		 */
		protected $_like;


		public function __construct()
		{
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->_db		= & $GLOBALS['phpgw']->db;
			$this->_join	= & $this->_db->join;
			$this->_like	= & $this->_db->like;
		}

		/**
		 * Magic get method
		 *
		 * @param string $varname the variable to fetch
		 *
		 * @return mixed the value of the variable sought - null if not found
		 */
		public function __get($varname)
		{
			switch ($varname)
			{
				case 'total_records':
					return $this->_total_records;
					break;
				case 'receipt':
					return $this->_receipt;
					break;
				default:
					return null;
			}
		}

		function read($data = array())
		{
			$start		= isset($data['start'])  ? (int) $data['start'] : 0;
			$filter		= isset($data['filter']) ? $data['filter'] : 'none';
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '' ;
			$cat_id		= isset($data['cat_id']) ? (int)$data['cat_id'] : 0;
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';

			$table = 'fm_condition_survey';
			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by id DESC';
			}

			$where = 'WHERE';
			if ($cat_id)
			{
				$filtermethod .= " {$where} category = {$cat_id}";
				$where = 'AND';
			}

			if($query)
			{
				$query			= $this->_db->db_addslashes($query);
				$querymethod	= " {$where} name {$this->_like} '%{$query}%'";
			}

			$sql = "SELECT * FROM {$table} $filtermethod $querymethod";

			$this->_db->query($sql,__LINE__,__FILE__);
			$this->_total_records = $this->_db->num_rows();

			if(!$allrows)
			{
				$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->_db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->_db->next_record())
			{
				$customs[] = array
					(
						'custom_id'		=> $this->_db->f('id'),
						'name'			=> stripslashes($this->_db->f('name')),
						'entry_date'	=> $this->_db->f('entry_date'),
						'user'			=> $GLOBALS['phpgw']->accounts->id2name($this->_db->f('user_id'))
					);
			}
			return $customs;
		}

		function read_single($id)
		{
			$table = 'fm_condition_survey';

			$id = (int) $id;
			$this->_db->query("SELECT * FROM {$table} WHERE id={$id}",__LINE__,__FILE__);

			$values = array();
			if ($this->_db->next_record())
			{
				$values = array
				(
					'id'			=> (int)$this->_db->f('id'),
					'name'			=> $this->_db->f('name', true),
					'sql_text'		=> $this->_db->f('sql_text', true),
					'entry_date'	=> $this->_db->f('entry_date'),

				);
			}

			return $values;
		}


		function add($data)
		{
			$table = 'fm_condition_survey';
			$custom['sql_text'] = $this->_db->db_addslashes(htmlspecialchars_decode($custom['sql_text']));

			$this->_db->transaction_begin();

			$id = $this->_db->next_id($table);

			$value_set = array
			(
				'id'				=> $id,
				'title'				=> $this->_db->db_addslashes($data['title']),
				'month'				=> $entry['month'],
				'budget'			=> $entry['budget'],
				'user_id'			=> $entry['user_id'],
				'entry_date'		=> $entry['entry_date'],
				'modified_date'		=> $entry['modified_date']
			);
			$cols = implode(',', array_keys($value_set));
			$values	= $this->_db->validate_insert(array_values($value_set));
			$this->_db->query("INSERT INTO {$table} ({$cols}) VALUES ({$values})",__LINE__,__FILE__);

			$receipt['id']= $id;

			if($this->_db->transaction_commit())
			{
				$this->_receipt['message'][] = array('msg'=>lang('survey %1 has been saved',$id));
				return $id;
			}
			return 0;
		}

		function edit($data)
		{
			$table = 'fm_condition_survey';
			$id = (int)$data['id'];

			$value_set	= $this->db->validate_update($value_set);

			$this->db->transaction_begin();

			$this->db->query("UPDATE {$table} SET $value_set WHERE id= {$id}",__LINE__,__FILE__);

			if($this->_db->transaction_commit())
			{
				$this->_receipt['message'][] = array('msg'=>lang('survey %1 has been saved',$id));
			}
			return $id;
		}

		function delete($id)
		{
			$id = (int) $id;
			$table = 'fm_condition_survey';
			$this->_db->query("DELETE FROM $table WHERE id={$id}",__LINE__,__FILE__);
		}
	}
