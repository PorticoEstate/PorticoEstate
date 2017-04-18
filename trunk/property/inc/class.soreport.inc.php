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

		function read_single ( $id, $values = array() )
		{
			$id = (int)$id;
			$sql = "SELECT * FROM fm_view_dataset_report WHERE id = {$id}";

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			if ($this->db->next_record())
			{
				$values = array
					(
					'id' => $this->db->f('id'),
					'dataset_id' => $this->db->f('dataset_id'),
					'report_name' => $this->db->f('report_name'),
					'report_definition' => $this->db->f('report_definition')
				);
			}

			return $values;
		}
		
		public function read($data)
		{
			$start = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query = isset($data['query']) ? $data['query'] : '';
			$sort = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order = isset($data['order']) ? $data['order'] : '';
			$allrows = isset($data['allrows']) ? $data['allrows'] : '';
			$dataset_id = isset($data['dataset_id']) ? $data['dataset_id'] : '';
			$results = isset($data['results']) && $data['results'] ? (int)$data['results'] : 0;
			
			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = " ORDER BY a.id DESC";
			}

			$where = 'WHERE';

			if ($dataset_id > 0)
			{
				$filtermethod .= " $where a.dataset_id={$dataset_id}";
				$where = 'AND';
			}

			if ($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where ( b.dataset_name {$this->like} '%$query%' OR a.report_name {$this->like} '%$query%')";
			}

			$sql = "SELECT a.id, a.report_name, b.dataset_name"
				. " FROM fm_view_dataset_report a {$this->join} fm_view_dataset b ON a.dataset_id = b.id"
				. " {$filtermethod} {$querymethod}";

			$sql_count = 'SELECT count(fm_view_dataset_report.id) AS cnt FROM fm_view_dataset_report';
			$this->db->query($sql_count, __LINE__, __FILE__);
			$this->db->next_record();
			$this->total_records_reports = $this->db->f('cnt');

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
					'report_name' => $this->db->f('report_name'),
					'dataset_name' => $this->db->f('dataset_name')
				);
			}

			return $values;
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
		
		public function get_datasets()
		{
			$sql = "SELECT * FROM fm_view_dataset";
	
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('dataset_name')
				);
			}
			
			return $values;
		}
		
		public function get_columns($id)
		{
			$dataset = $this->read_single_dataset($id);
			
			$sql = "SELECT column_name, data_type
				FROM   information_schema.columns
				WHERE  table_name = '".$dataset['view_name']."'
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
		
		function read_to_export ( $id )
		{
			$id = (int)$id;
			
			$definition = $this->read_single($id);
			$dataset = $this->read_single_dataset($definition['dataset_id']);
			
			$jsonB = json_decode($definition['report_definition'], true);
			
			$columns = implode(',', $jsonB['group']);
			$agregates = array();
			foreach ($jsonB['aggregate'] as $c => $v)
			{
				$agregates[] = $jsonB['cbo_aggregate'][$v]."(".$v.") AS ".$jsonB['txt_aggregate'][$v];
			}
			$func_agregates = implode(',', $agregates);
			$order = '';
			if (count($jsonB['order']))
			{
				$order = ' ORDER BY '.implode(',', $jsonB['order']);
			}
			
			$sql = "SELECT ".$columns.",".$func_agregates." FROM ".$dataset['view_name']." GROUP BY ".$columns.$order;
			
			$this->db->query($sql, __LINE__, __FILE__);

			$resultado = array_merge(array_values($jsonB['group']), array_values($jsonB['txt_aggregate']));
			
			$values = array();
			while ($this->db->next_record())
			{
				$value = array();
				foreach ($resultado as $column)
				{
					$value[$column] = $this->db->f($column);
				}
				$values[] = $value;
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

			$where = 'HAVING';

			/*if ($dimb_id > 0)
			{
				$filtermethod .= " $where fm_budget.ecodimb={$dimb_id}";
				$where = 'AND';
			}*/

			if ($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where ( a.dataset_name {$this->like} '%$query%')";
			}

			$sql = "SELECT a.id, a.view_name, a.dataset_name, count(b.id) AS n_reports
						FROM fm_view_dataset a LEFT JOIN fm_view_dataset_report b ON a.id = b.dataset_id
						GROUP BY a.id, a.view_name, a.dataset_name"
				. " {$querymethod}";

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
					'dataset_name' => $this->db->f('dataset_name'),
					'n_reports' => $this->db->f('n_reports')
				);
			}

			return $values;
		}
		
		function add ( $data )
		{
			$receipt = array();
			$values_insert = array
				(
				'dataset_id' => $data['dataset_id'],
				'report_name' => $data['report_name'],
				'report_definition' => json_encode($data['report_definition']),
				'owner_id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'entry_date' => time()
			);
			
			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_view_dataset_report (" . implode(',', array_keys($values_insert)) . ') VALUES ('
					. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);
			
			if ($this->db->transaction_commit())
			{
				$receipt['message'][] = array('msg' => lang('dataset has been saved'));
				$receipt['id'] = $this->db->get_last_insert_id('fm_view_dataset_report', 'id');
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('dataset has not been saved'));
			}
			
			return $receipt;
		}

		function update ( $data )
		{
			$receipt = array();

			$value_set = array
				(
				'dataset_id' => $data['dataset_id'],
				'report_name' => $data['report_name'],
				'report_definition' => json_encode($data['report_definition']),
				'owner_id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'entry_date' => time()
			);

			$value_set = $this->db->validate_update($value_set);

			$this->db->transaction_begin();
			
			$this->db->query("UPDATE fm_view_dataset_report SET {$value_set} WHERE id='" . $data['id'] . "'", __LINE__, __FILE__);

			$receipt['id'] = $data['id'];
			if ($this->db->transaction_commit())
			{
				$receipt['message'][] = array('msg' => lang('dataset has been updated'));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('dataset has not been updated'));
			}
			
			return $receipt;
		}
		
		function delete ( $id )
		{
			$id = (int)$id;
			$receipt = array();

			$this->db->transaction_begin();
			
			$this->db->query("DELETE FROM fm_view_dataset WHERE id ='{$id}'", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_view_dataset_report WHERE dataset_id ='{$id}'", __LINE__, __FILE__);

			if ($this->db->transaction_commit())
			{
				$receipt['message'][] = array('msg' => lang('dataset has been deleted'));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('dataset has not been deleted'));
			}
			
			return $receipt;
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
				$receipt['message'][] = array('msg' => lang('dataset has been saved'));
				$receipt['id'] = $this->db->get_last_insert_id('fm_view_dataset', 'id');
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('dataset has not been saved'));
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
				$receipt['message'][] = array('msg' => lang('dataset has been updated'));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('dataset has not been updated'));
			}
			
			return $receipt;
		}
		
		function delete_dataset ( $id )
		{
			$id = (int)$id;
			$receipt = array();

			$this->db->transaction_begin();
			
			$this->db->query("DELETE FROM fm_view_dataset_report WHERE dataset_id ='{$id}'", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_view_dataset WHERE id ='{$id}'", __LINE__, __FILE__);

			if ($this->db->transaction_commit())
			{
				$receipt['message'][] = array('msg' => lang('dataset has been deleted'));
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('dataset has not been deleted'));
			}
			
			return $receipt;
		}		
		
	}