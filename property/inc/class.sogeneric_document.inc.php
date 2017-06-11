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
		protected
			$perform_update_relation_path = false;

		function __construct()
		{
			$this->vfs = CreateObject('phpgwapi.vfs');
			$this->vfs->fakebase = '/property';

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
			$query = isset($data['query']) ? $data['query'] : '';
			$allrows = isset($data['allrows']) ? $data['allrows'] : '';
			$dry_run = isset($data['dry_run']) ? $data['dry_run'] : '';
			$user_id = isset($data['user_id']) && $data['user_id'] ? (int)$data['user_id'] : 0;
			$cat_id = isset($data['cat_id']) && $data['cat_id'] ? (int)$data['cat_id'] : 0;
			$location_id = isset($data['location_id']) && $data['location_id'] ? (int)$data['location_id'] : 0;
			$location_item_id = isset($data['location_item_id']) && $data['location_item_id'] ? (int)$data['location_item_id'] : 0;
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

				$ordermethod = " ORDER BY a.{$_order} {$sort}";
			}
			else
			{
				$ordermethod = ' ORDER BY a.file_id ASC';
			}

			$filtermethod = "WHERE a.mime_type != 'Directory' AND a.mime_type != 'journal' AND a.mime_type != 'journal-deleted'";
			$joinmethod .= " {$this->left_join} phpgw_vfs_filedata b ON ( a.file_id = b.file_id )";

			if ($cat_id)
			{
				$filtermethod .= " AND b.metadata->>'cat_id' = '{$cat_id}'";
			}

			if ($location_id)
			{
				$filtermethod .= " AND c.location_id = {$location_id}";
			}

			$joinmethod .= " {$this->left_join} phpgw_vfs_file_relation c ON ( a.file_id = c.file_id )";

			if($location_item_id)
			{
				$filtermethod .= " AND c.location_item_id = {$location_item_id}";
			}

			if ($user_id)
			{
				$filtermethod .= " AND a.createdby_id = {$user_id}";
			}

			if ($mime_type)
			{
				$filtermethod .= " AND a.mime_type = '{$mime_type}'";
			}

			if ($start_date)
			{
				$date_format = $this->db->date_format();
				$start_date = date($date_format, $start_date);
				$end_date = date($date_format, $end_date);
				$filtermethod .= " AND a.created >= '$start_date' AND a.created <= '$end_date'";
			}

			if ($location)
			{
				$filtermethod .= " AND a.directory {$this->like} '%{$location}%'";
			}

			$querymethod = '';
			if ($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " AND (a.name $this->like '%{$query}%'";
				$querymethod .= " OR metadata->>'path' ilike '%{$query}%')";
			}

			$sql = "SELECT DISTINCT a.file_id, a.*, metadata->>'path' as path FROM phpgw_vfs a " ." {$joinmethod} "." {$filtermethod} "." {$querymethod} ";

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
				$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				$ids = array();
				while ($this->db->next_record())
				{
					$id = $this->db->f('file_id');
					$ids[] = $id;
					$values[] = array
						(
						'id' => $id,
						'owner_id' => $this->db->f('owner_id'),
						'createdby_id' => $this->db->f('createdby_id'),
						'modifiedby_id' => $this->db->f('modifiedby_id'),
						'created' => $GLOBALS['phpgw']->common->show_date(strtotime($this->db->f('created')), $dateformat),
						'modified' => $GLOBALS['phpgw']->common->show_date(strtotime($this->db->f('modified')), $dateformat),
						'size' => $this->db->f('size'),
						'mime_type' => $this->db->f('mime_type', true),
						'app' => $this->db->f('app'),
						'directory' => $this->db->f('directory', true),
						'name' => $this->db->f('name'),
						'link_directory' => $this->db->f('link_directory', true),
						'link_name' => $this->db->f('link_name', true),
						'version' => $this->db->f('version'),
						'path'	=>  $this->db->f('path'),
					);
				}

				if($this->perform_update_relation_path && $ids)
				{
					foreach ($ids as $file_id)
					{
						$this->update_relation_path($file_id);
					}
				}
			}

			return $values;
		}

		public function get_file_relations($file_id, $location_id = null)
		{
			if ($location_id)
			{
				$filtermethod = "WHERE location_id = {$location_id} AND file_id = {$file_id}";
			} else {
				$filtermethod = "WHERE file_id = {$file_id}";
			}

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

		public function get_file_relations_componentes($data)
		{
			$start = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$results = isset($data['results']) && $data['results'] ? $data['results'] : 0;
			$file_id = isset($data['file_id']) && $data['file_id'] ? (int)$data['file_id'] : 0;
			$location_id = isset($data['location_id']) && $data['location_id'] ? (int)$data['location_id'] : 0;
			$allrows = isset($data['allrows']) ? $data['allrows'] : '';
			$entity_group_id = isset($data['entity_group_id']) ? $data['entity_group_id'] : '';

			if ($location_id)
			{
				$filtermethod = "WHERE a.location_id = {$location_id} AND a.file_id = {$file_id}";
			}
			else
			{
				$filtermethod = "WHERE a.file_id = {$file_id}";
			}

			if ($entity_group_id)
			{
				$filtermethod .= " AND c.entity_group_id = {$entity_group_id}";
			}

			$sql = "SELECT DISTINCT a.file_id, b.type, b.id, b.location_id, b.location_code, b.json_representation, c.name AS category_name, c.entity_id, c.entity_group_id "
					. "FROM phpgw_vfs_file_relation a INNER JOIN fm_bim_item b ON a.location_id = b.location_id AND a.location_item_id = b.id "
					. "INNER JOIN fm_entity_category c ON b.location_id = c.location_id" ." {$filtermethod} ";

			if (!$allrows)
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}

			$values = array();
			$i = 0;
			while ($this->db->next_record())
			{
				$values[$i] = array
					(
					'id' => $this->db->f('id'),
					'entity_id' => $this->db->f('entity_id'),
					'entity_group_id' => $this->db->f('entity_group_id'),
					'location_id' => $this->db->f('location_id'),
					'category_name' => $this->db->f('category_name')
				);

				$jsondata = json_decode($this->db->f('json_representation', true), true);
				foreach ($jsondata as $k => $v)
				{
					$values[$i][$k] = $v;
				}
				$i++;
			}

			$sql2 = "SELECT count(*) as cnt "
					. "FROM phpgw_vfs_file_relation a INNER JOIN fm_bim_item b ON a.location_id = b.location_id AND a.location_item_id = b.id "
					. "INNER JOIN fm_entity_category c ON b.location_id = c.location_id" ." {$filtermethod} ";
			$this->db->query($sql2, __LINE__, __FILE__);

			$this->db->next_record();
			$this->total_records_componentes = $this->db->f('cnt');

			return $values;
		}

		function save_file_relations( $add, $delete, $location_id, $file_id )
		{
			$this->db->transaction_begin();

			if (count($delete))
			{
				foreach($delete as $item)
				{
					$this->db->query("DELETE FROM phpgw_vfs_file_relation WHERE location_item_id = {$item} AND file_id = {$file_id} AND location_id = {$location_id}", __LINE__, __FILE__);
					$this->update_relation_path($file_id, true, $location_id);
				}
			}

			$date_format = phpgwapi_datetime::date_array(date('Y-m-d'));
			$date = mktime(2, 0, 0, $date_format['month'], $date_format['day'], $date_format['year']);

			if (count($add))
			{
				foreach($add as $item)
				{
					$values_insert = array
					(
						'file_id' => (int)$file_id,
						'location_id' => (int)$location_id,
						'location_item_id' => (int)$item,
						'is_private' => 0,
						'account_id' => $GLOBALS['phpgw_info']['user']['account_id'],
						'entry_date' => $date,
						'start_date' => $date,
						'end_date' => $date
					);

					$this->db->query("INSERT INTO phpgw_vfs_file_relation (" . implode(',', array_keys($values_insert)) . ') VALUES ('
						. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);

					$this->update_relation_path($file_id);
				}
			}

			if ($this->db->transaction_commit())
			{
				return true;
			}
			else
			{
				return false;
			}
		}


		public function add( $data = array(), $file_id )
		{
			$receipt = array();
			$values_insert = array
				(
				'file_id' => $file_id,
				'metadata' => "'" . json_encode($data) . "'"
			);

			$result = $this->db->query("INSERT INTO phpgw_vfs_filedata (" . implode(',', array_keys($values_insert)) . ') VALUES ('
				. implode(",", array_values($values_insert)) . ')', __LINE__, __FILE__);

			if ($result)
			{
				$receipt['message'] = lang('filedata has been saved');
			}
			else {
				$receipt['error'] = lang('filedata has not been saved');
			}
			return $receipt;
		}

		public function update( $data = array(), $file_id)
		{
			$receipt = array();

			$this->db->transaction_begin();

			if($data)
			{
				foreach ($data as $key => $value)
				{
					$value = $this->db->db_addslashes($value);
					$sql = "UPDATE phpgw_vfs_filedata SET metadata=jsonb_set(metadata, '{{$key}}', '\"{$value}\"', true)"
						. " WHERE file_id = {$file_id}";

					$this->db->query($sql, __LINE__, __FILE__);

				}
			}

			$result = $this->db->transaction_commit();

			if ($result)
			{
				$receipt['message'] = lang('filedata has been edited');
			}
			else
			{
				$receipt['error'] = lang('filedata has not been edited');
			}
			return $receipt;
		}

		/**
		 *
		 * @param type $file_id
		 * @param type $delete
		 * @return bool
		 */
		public function update_relation_path( $file_id, $delete = false , $location_id = 0)
		{
			$ok = true; // ok if it don't choke

			$sql = "SELECT metadata FROM phpgw_vfs_filedata WHERE file_id =" . (int)$file_id;
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$_metadata = $this->db->f('metadata', true);
			$metadata = (array)json_decode($_metadata, true);

			if($delete && $location_id)
			{
				$sql = "SELECT fm_entity_category.name as location_name"
					. " FROM fm_entity_category WHERE location_id =" . (int)$location_id;
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$location_name = $this->db->f('location_name');

				if(in_array($location_name, $metadata['path']))
				{
					$key = (int)array_search($location_name, $metadata['path']);
					unset($metadata['path'][$key]);
				}
				unset($location_name);
			}

			$location_names = array();

			$sql = "SELECT DISTINCT fm_entity_category.name as location_name"
				. " FROM fm_entity_category {$this->join} phpgw_vfs_file_relation ON fm_entity_category.location_id = phpgw_vfs_file_relation.location_id"
				. " WHERE file_id =" . (int)$file_id
				. " ORDER BY fm_entity_category.name DESC";
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$location_names[] = $this->db->f('location_name', true);
			}

			if($location_names)
			{
				foreach ($location_names as $location_name)
				{
					if(!empty($metadata['path']) && is_array($metadata['path']) && !in_array($location_name, $metadata['path']))
					{
						array_unshift($metadata['path'], $location_name);
					}
				}

				sort($metadata['path']);
			}

			if($metadata)
			{
				if(!isset($metadata['path']))
				{
					$metadata['path'] = array();
				}

				$value_set = array
				(
					'metadata' => json_encode($metadata),
				);
				$value_set = $this->db->validate_update($value_set);

				//Still ok?
				$ok = $this->db->query("UPDATE phpgw_vfs_filedata SET {$value_set}  WHERE file_id='{$file_id}'", __LINE__, __FILE__);
			}
			return $ok;
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

		public function delete( $file_id )
		{
			$file_info = $this->vfs->get_info($file_id);
			$file = "{$file_info['directory']}/{$file_info['name']}";

			if ($file)
			{
				$this->db->transaction_begin();

				$this->db->query("DELETE FROM phpgw_vfs_file_relation WHERE file_id = {$file_id}", __LINE__, __FILE__);
				$this->db->query("DELETE FROM phpgw_vfs_filedata WHERE file_id = {$file_id}", __LINE__, __FILE__);

				$result = $this->delete_file($file);

				if ($result)
				{
					$this->db->transaction_commit();
					$receipt = lang('file deleted') . ' : ' . $file_info['name'];
				} else {
					$receipt = lang('failed to delete file') . ' : ' . $file_info['name'];
				}
			}

			return $receipt;
		}

		function delete_file( $file )
		{
			$result = false;
			if ($this->vfs->file_exists(array(
					'string' => $file,
					'relatives' => array(RELATIVE_NONE)
				)))
			{
				$this->vfs->override_acl = 1;

				if ($this->vfs->rm(array(
						'string' => $file,
						'relatives' => array(
							RELATIVE_NONE
						)
					)))
				{
					$result = true;
				}

				$this->vfs->override_acl = 0;
			}

			return $result;
		}

	}