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
	 * @version $Id: class.sogeneric_document.inc.php 14913 2016-04-27 12:27:37Z sigurdne $
	 */

	class property_sogeneric_document 
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
			$start = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$sort = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'ASC';
			$order = isset($data['order']) ? $data['order'] : '';
			$allrows = isset($data['allrows']) ? $data['allrows'] : '';
			$dry_run = isset($data['dry_run']) ? $data['dry_run'] : '';
			$user_id = isset($data['user_id']) && $data['user_id'] ? (int)$data['user_id'] : 0;
			$mime_type = isset($data['mime_type']) ? $data['mime_type'] : '';
			$start_date = isset($data['start_date']) ? $data['start_date'] : 0;
			$end_date = isset($data['end_date']) ? $data['end_date'] : 0;
			$location = !empty($data['location']) ? $data['location'] : '';
			$results = (isset($data['results']) ? $data['results'] : 0);

			if ($order)
			{
				switch ($order)
				{
					case 'id':
						$_order = 'file_id';
						break;
					case 'date':
						$_order = 'created';
						break;
					default:
						$_order = $order;
				}

				$ordermethod = " ORDER BY $_order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY file_id ASC';
			}

			$filtermethod = "WHERE mime_type != 'Directory' AND mime_type != 'journal' AND mime_type != 'journal-deleted'";

			if ($user_id)
			{
				$filtermethod .= " AND createdby_id = {$user_id}";
			}

			if ($mime_type)
			{
				$filtermethod .= " AND mime_type = '{$mime_type}'";
			}

			if ($start_date)
			{
				$date_format = $this->db->date_format();
				$start_date = date($date_format, $start_date);
				$end_date = date($date_format, $end_date);
				$filtermethod .= " AND phpgw_vfs.created >= '$start_date' AND phpgw_vfs.created <= '$end_date'";
			}

			if ($location)
			{
				$filtermethod .= " AND phpgw_vfs.directory {$this->like} '%{$location}%'";
			}

			$sql = "SELECT * FROM  phpgw_vfs" ." {$filtermethod} ";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			$values = array();
			if (!$dry_run)
			{
				if (!$allrows)
				{
					$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);
				}
				else
				{
					$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
				}

				while ($this->db->next_record())
				{
					$values[] = array
						(
						'id' => $this->db->f('file_id'),
						'owner_id' => $this->db->f('owner_id'),
						'createdby_id' => $this->db->f('createdby_id'),
						'modifiedby_id' => $this->db->f('modifiedby_id'),
						'created' => $this->db->f('created'),
						'modified' => $this->db->f('modified'),
						'size' => $this->db->f('size'),
						'mime_type' => $this->db->f('mime_type', true),
						'app' => $this->db->f('app'),
						'directory' => $this->db->f('directory', true),
						'name' => $this->db->f('name'),
						'link_directory' => $this->db->f('link_directory', true),
						'link_name' => $this->db->f('link_name', true),
						'version' => $this->db->f('version')
					);
				}
			}

			return $values;
		}
		
		public function get_file_relations($location_id, $file_id) 
		{
			$filtermethod = "WHERE location_id = {$location_id} AND file_id = {$file_id}";
			
			$sql = "SELECT * FROM phpgw_vfs_file_relation " ." {$filtermethod} ";
			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();

			while ($this->db->next_record())
			{
				$values[] = array
					(
					'id' => $this->db->f('relation_id'),
					'location_item_id' => $this->db->f('location_item_id')
				);
			}
			
			return $values;
		}
		
		function set_file_relation( $items = array(), $location_id, $file_id )
		{
			$this->db->query("DELETE FROM phpgw_vfs_file_relation WHERE file_id = {$file_id} AND location_id = {$location_id}", __LINE__, __FILE__);
			
			$date_format = phpgwapi_datetime::date_array(date('Y-m-d'));
			$date = mktime(2, 0, 0, $date_format['month'], $date_format['day'], $date_format['year']);
				
			if (count($items))
			{
				foreach($items as $item)
				{
					$values_insert = array
					(
						'relation_id' => $item,
						'file_id' => (int)$file_id,
						'location_id' => (int)$location_id,
						'location_item_id' => $item,
						'is_private' => 0,
						'account_id' => $GLOBALS['phpgw_info']['user']['account_id'],
						'entry_date' => $date,
						'start_date' => $date,
						'end_date' => $date
					);				

					$this->db->query("INSERT INTO phpgw_vfs_file_relation (" . implode(',', array_keys($values_insert)) . ') VALUES ('
						. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);				
				}
			}
			
			return true;
		}
		
		
		public function add( $data = array(), $file_id )
		{
			$values_insert = array
				(
				'file_id' => $file_id,
				'metadata' => json_encode($data)
			);

			$resutl = $this->db->query("INSERT INTO phpgw_vfs_filedata (" . implode(',', array_keys($values_insert)) . ') VALUES ('
				. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);

			return $resutl;
		}
		
		public function update( $data = array(), $file_id)
		{
			$value_set = array
			(
				'metadata' => json_encode($data)
			);

			$value_set = $this->db->validate_update($value_set);
			
			return $this->db->query("UPDATE phpgw_vfs_filedata SET $value_set WHERE file_id = {$file_id}", __LINE__, __FILE__);
		}
		
		public function read_single( $id )
		{
			$id = (int)$id;

			$this->db->query("SELECT * FROM phpgw_vfs_filedata WHERE file_id = {$id}");

			$values = array();
			if ($this->db->next_record())
			{
				$values['id'] = $id;
				$values['metadata'] = $this->db->f('metadata');
			}

			return $values['metadata'];
			
		}
		
	}