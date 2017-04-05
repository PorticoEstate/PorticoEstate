<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage logistic
	 * @version $Id: class.soreport.inc.php 14913 2016-04-27 12:27:37Z sigurdne $
	 */

	class property_soreport 
	{

		function __construct()
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->join = & $this->db->join;
			$this->left_join = & $this->db->left_join;
			$this->like = & $this->db->like;		
			$this->total_records = 0;
		}

		public function read($data)
		{
			return array();
		}
		
		public function get_views()
		{
			$sql = "SELECT table_name as name
					FROM information_schema.tables
					WHERE table_schema = current_schema()
					AND table_type = 'VIEW'";
	
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'name' => $this->db->f('name')
				);
			}
			
			return $values;
		}
		
		public function get_columns($table)
		{
			$sql = "SELECT column_name, data_type
				FROM   information_schema.columns
				WHERE  table_name = '".$table."'
				ORDER  BY ordinal_position";
	
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			
			while ($this->db->next_record())
			{
				$values[] = array
					(
					'name' => $this->db->f('column_name'),
					'type' => $this->db->f('data_type')
				);
			}
			
			return $values;
		}
		
		function read_single_dataset ( $id, $values = array() )
		{
			$id = (int)$id;
			$sql = "SELECT * FROM fm_view_dataset WHERE id = {$id}";

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			if ($this->db->next_record())
			{
				$values = array
					(
					'id' => $this->db->f('id'),
					'view_name' => $this->db->f('view_name'),
					'dataset_name' => $this->db->f('dataset_name')
				);
			}

			return $values;
		}
		
		function read_dataset ( $data )
		{
			$start = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query = isset($data['query']) ? $data['query'] : '';
			$sort = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order = isset($data['order']) ? $data['order'] : '';
			$allrows = isset($data['allrows']) ? $data['allrows'] : '';
			$results = isset($data['results']) && $data['results'] ? (int)$data['results'] : 0;
			
			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = " ORDER BY id DESC";
			}

			$where = 'WHERE';

			/*if ($dimb_id > 0)
			{
				$filtermethod .= " $where fm_budget.ecodimb={$dimb_id}";
				$where = 'AND';
			}*/

			if ($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where ( dataset_name {$this->like} '%$query%')";
			}

			$sql = "SELECT fm_view_dataset.id, fm_view_dataset.view_name, fm_view_dataset.dataset_name"
				. " FROM fm_view_dataset"
				. " {$filtermethod} {$querymethod}";

			$sql_count = 'SELECT count(fm_view_dataset.id) AS cnt FROM fm_view_dataset';
			$this->db->query($sql_count, __LINE__, __FILE__);
			$this->db->next_record();
			$this->total_records_dataset = $this->db->f('cnt');

			if (!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
			}

			$values = array();
			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('id'),
					'view_name' => $this->db->f('view_name'),
					'dataset_name' => $this->db->f('dataset_name')
				);
			}

			return $values;
		}
		
		function add_dataset ( $data )
		{
			$receipt = array();
			$values_insert = array
				(
				'view_name' => $data['view_name'],
				'dataset_name' => $this->db->db_addslashes($data['dataset_name']),
				'owner_id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'entry_date' => time()
			);
			
			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_view_dataset (" . implode(',', array_keys($values_insert)) . ') VALUES ('
					. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);
			
			if ($this->db->transaction_commit())
			{
				$receipt['message'][] = array('msg' => lang('event has been saved'));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('event has not been saved'));
			}
			
			return $receipt;
		}

		function update_dataset ( $data )
		{
			$receipt = array();

			$value_set = array
				(
				'view_name' => $data['view_name'],
				'dataset_name' => $this->db->db_addslashes($data['dataset_name']),
				'owner_id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'entry_date' => time()
			);

			$value_set = $this->db->validate_update($value_set);

			$this->db->transaction_begin();
			
			$this->db->query("UPDATE fm_view_dataset SET {$value_set} WHERE id='" . $data['id'] . "'", __LINE__, __FILE__);

			$receipt['id'] = $data['id'];
			if ($this->db->transaction_commit())
			{
				$receipt['message'][] = array('msg' => lang('event has been updated'));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('event has not been updated'));
			}
			
			return $receipt;
		}
		
		function delete_dataset ( $id )
		{
			$id = (int)$id;

			$this->db->transaction_begin();
			
			$this->db->query("DELETE FROM fm_view_dataset WHERE id ='{$id}'", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_view_dataset_report WHERE dataset_id ='{$id}'", __LINE__, __FILE__);

			if ($this->db->transaction_commit())
			{
				return true;
			}
			
			return false;
		}		
		
	}