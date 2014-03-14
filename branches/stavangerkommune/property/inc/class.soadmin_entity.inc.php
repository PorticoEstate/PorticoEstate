<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2010 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soadmin_entity
	{
		var $grants;
		var $type = 'entity';
		var $type_app;
		var $bocommon;
		private $move_child = array();
		public $category_tree = array();

		function __construct($entity_id='', $cat_id='', $bocommon = '')
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			if(!$bocommon || !is_object($bocommon))
			{
				$this->bocommon			= CreateObject('property.bocommon');
			}
			else
			{
				$this->bocommon = $bocommon;
			}

			$this->db           = & $GLOBALS['phpgw']->db;
			$this->db2			= clone($this->db);
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;

			if($entity_id && $cat_id)
			{
				$this->category_name	= $this->read_category_name($entity_id,$cat_id);
			}
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query']) ? $data['query'] : '';
				$sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
				$order		= isset($data['order']) ? $data['order'] : '';
				$type		= isset($data['type']) && $data['type'] ? $data['type'] : $this->type;
				$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by id asc';
			}

			$table = "fm_{$type}";

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " where name $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$entity = array();

			while ($this->db->next_record())
			{
				$entity[] = array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $this->db->f('name'),
						'descr'	=> $this->db->f('descr'),
						'documentation'	=> $this->db->f('documentation')
					);
			}
			return $entity;
		}

		function read_category($data)
		{
			$start		= isset($data['start'])&& $data['start'] ? $data['start'] : 0;
			$query		= isset($data['query'])?$data['query']:'';
			$sort		= isset($data['sort'])?$data['sort']:'DESC';
			$order		= isset($data['order'])?$data['order']:'';
			$allrows	= isset($data['allrows'])?$data['allrows']:'';
			$entity_id	= isset($data['entity_id'])? (int)$data['entity_id']:0;
			$type		= isset($data['type']) && $data['type'] ? $data['type'] : $this->type;
			$required	= isset($data['required'])?$data['required']:'';

			if ($order)
			{
				$ordermethod = " ORDER BY {$order} {$sort}";
			}
			else
			{
				$ordermethod = ' ORDER BY id ASC';
			}

			$table = "fm_{$type}_category";

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " AND name {$this->like} '%{$query}%' OR descr {$this->like} '%{$query}%'";
			}

			$sql = "SELECT * FROM {$table} WHERE entity_id={$entity_id} {$querymethod}";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db2->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db2->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$values = array();
			while ($this->db2->next_record())
			{
				$id	= $this->db2->f('id');
				$category = array
				(
					'entity_id'		=> $entity_id,
					'id'			=> $id,
					'name'			=> $this->db2->f('name'),
					'prefix'		=> $this->db2->f('prefix'),
					'descr'			=> $this->db2->f('descr'),
					'level'			=> $this->db2->f('level'),
					'parent_id'		=> $this->db2->f('parent_id'),
					'is_eav'		=> $this->db2->f('is_eav'),
					'enable_bulk'	=> $this->db2->f('enable_bulk'),
				);

				if($required)
				{
					if($GLOBALS['phpgw']->acl->check(".{$type}.{$entity_id}.{$id}", $required, $this->type_app[$type]))
					{
						$values[] = $category;
					}
				}
				else
				{
					$values[] = $category;
				}
			}

			foreach ($values as &$entry)
			{
				$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id($this->type_app[$type], ".{$type}.{$entity_id}.{$entry['id']}");
			}

			return $values;
		}


		function get_children2($entity_id, $parent, $level, $reset = false)
		{
			if($reset)
			{
				$this->category_tree = array();
			}
			$db = clone($this->db);
			$table = "fm_{$this->type}_category";
			$sql = "SELECT * FROM {$table} WHERE entity_id = {$entity_id} AND parent_id = {$parent} ORDER BY name ASC";
			$db->query($sql,__LINE__,__FILE__);

			while ($db->next_record())
			{
				$id	= $db->f('id');
				$this->category_tree[] = array
				(
					'id'			=> $id,
					'name'			=> str_repeat('..',$level).$db->f('name'),
					'parent_id'		=> $db->f('parent_id'),
					'location_id'	=> $db->f('location_id')
				);
				$this->get_children2($entity_id, $id, $level+1);
			}
			return $this->category_tree;
		} 

		public function read_category_tree2($entity_id)
		{
			$table = "fm_{$this->type}_category";

			$sql = "SELECT * FROM $table WHERE entity_id=$entity_id AND (parent_id = 0 OR parent_id IS NULL) ORDER BY name ASC";

			$this->db->query($sql,__LINE__,__FILE__);

			$this->category_tree = array();
			while ($this->db->next_record())
			{
				$id	= $this->db->f('id');
				$categories[$id] = array
				(
					'id'			=> $id,
					'name'			=> $this->db->f('name',true),
					'parent_id'		=> 0,
					'location_id'	=> $this->db->f('location_id')
				);
			}

			foreach($categories as $category)
			{
				$this->category_tree[$category['id']] = array
				(
					'id'			=> $category['id'],
					'name'			=> $category['name'],
					'location_id'	=> $category['location_id']
				);
				$this->get_children2($entity_id, $category['id'], 1);
			}
			return $this->category_tree;
		}


		/**
		 * used for retrive a child-node from a hierarchy
		 *
		 * @param integer $entity_id Entity id
		 * @param integer $parent is the parent of the children we want to see
		 * @param integer $level is increased when we go deeper into the tree,
		 * @return array $child Children
		 */

		protected function get_children($entity_id, $parent, $level, $menuaction)
		{	
			$table = "fm_{$this->type}_category";
			$sql = "SELECT * FROM {$table} WHERE entity_id = {$entity_id} AND parent_id = {$parent} ORDER BY name ASC";
			$this->db2->query($sql,__LINE__,__FILE__);

			$children = array();
			while ($this->db2->next_record())
			{
				$id	= $this->db2->f('id');

				$children[$id] = array
					(
						'id'			=> $id,
						'name'			=> $this->db2->f('name'),
						'prefix'		=> $this->db2->f('prefix'),
						'descr'			=> $this->db2->f('descr'),
						'level'			=> (int)$this->db2->f('level'),
						'parent_id'		=> (int)$this->db2->f('parent_id'),
						'owner'			=> (int)$this->db2->f('owner'),
						'location_id'	=> (int)$this->db2->f('location_id')
					);
			}

			foreach($children as &$child)
			{
				$child['url']	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $menuaction, 'entity_id'=> $entity_id , 'cat_id'=> $child['id'], 'type' => $this->type));
				$child['text']	= $child['name'];
				$_children = $this->get_children($entity_id, $child['id'], $level+1, $menuaction);
				if($_children)
				{
					$child['children'] = $_children;
				}
			}
			return $children;
		} 


		public function read_category_tree($entity_id, $menuaction, $required = '')
		{
			$table = "fm_{$this->type}_category";

			$sql = "SELECT * FROM $table WHERE entity_id=$entity_id AND (parent_id = 0 OR parent_id IS NULL) ORDER BY name ASC";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			$categories = array();
			while ($this->db2->next_record())
			{
				$id	= $this->db2->f('id');
				$location = ".entity.{$entity_id}.{$id}";

				if ( !$required || ($required && $GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, $this->type_app[$this->type])) )
				{
					$categories[$id] = array
						(
							'id'			=> $id,
							'name'			=> $this->db2->f('name',true),
							'prefix'		=> $this->db2->f('prefix'),
							'descr'			=> $this->db2->f('descr',true),
							'level'			=> 0,
							'parent_id'		=> 0,
							'location_id'	=> $this->db2->f('location_id')
						);
				}
			}

			foreach($categories as &$category)
			{
				$category['url']	= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $menuaction, 'entity_id'=> $entity_id , 'cat_id'=> $category['id'], 'type' => $this->type));
				$category['text']	= $category['name'];
				$children = $this->get_children($entity_id, $category['id'], 0, $menuaction);
				if ($children)
				{
					$category['children'] = $children;
				}
			}
			return $categories;
		}

		/**
		 * used for retrive the path for a particular node from a hierarchy
		 *
		 * @param integer $entity_id Entity id
		 * @param integer $node is the id of the node we want the path of
		 * @return array $path Path
		 */

		public function get_path($entity_id, $node)
		{
			$table = "fm_{$this->type}_category";
			$sql = "SELECT * FROM {$table} WHERE entity_id = {$entity_id} AND id = {$node}";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$parent_id = $this->db->f('parent_id');
			$name = $this->db->f('name', true);
			$path = array($name);
			if ($parent_id)
			{
				$path = array_merge($this->get_path($entity_id, $parent_id), $path);
			}
			return $path;
		}

		function read_single($id)
		{

			$id = (int)$id;
			$sql = "SELECT * FROM fm_{$this->type} WHERE id={$id}";

			$this->db->query($sql,__LINE__,__FILE__);

			$entity = array();
			if ($this->db->next_record())
			{
				$entity['id']				= $this->db->f('id');
				$entity['name']				= $this->db->f('name',true);
				$entity['descr']			= $this->db->f('descr',true);
				$entity['location_form']	= $this->db->f('location_form');
				$entity['lookup_entity']	= unserialize($this->db->f('lookup_entity'));
				$entity['documentation']	= $this->db->f('documentation');
			}

			$sql = "SELECT location FROM fm_{$this->type}_lookup WHERE entity_id={$id} AND type='lookup'";

			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$entity['include_entity_for'][] = $this->db->f('location');
			}

			$sql = "SELECT location FROM fm_{$this->type}_lookup WHERE entity_id={$id} AND type='start'";

			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$entity['start_entity_from'][] = $this->db->f('location');
			}

			return $entity;
		}

		function read_single_category($entity_id, $cat_id)
		{
			$sql = "SELECT * FROM fm_{$this->type}_category WHERE entity_id =" . (int)$entity_id . ' AND id = ' . (int)$cat_id;

			$this->db->query($sql,__LINE__,__FILE__);

			$category = array();
			if ($this->db->next_record())
			{
				$category = array
				(
					'id'						=> $this->db->f('id'),
					'name'						=> $this->db->f('name',true),
					'descr'						=> $this->db->f('descr',true),
					'prefix'					=> $this->db->f('prefix',true),
					'lookup_tenant'				=> $this->db->f('lookup_tenant'),
					'tracking'					=> $this->db->f('tracking'),
					'location_level'			=> $this->db->f('location_level'),
					'location_link_level'		=> $this->db->f('location_link_level'),
					'fileupload'				=> $this->db->f('fileupload'),
					'loc_link'					=> $this->db->f('loc_link'),
					'start_project'				=> $this->db->f('start_project'),
					'start_ticket'				=> $this->db->f('start_ticket'),
					'is_eav'					=> $this->db->f('is_eav'),
					'enable_bulk'				=> $this->db->f('enable_bulk'),
					'jasperupload'				=> $this->db->f('jasperupload'),
					'parent_id'					=> $this->db->f('parent_id'),
					'level'						=> $this->db->f('level'),
					'location_id'				=> $this->db->f('location_id')
					);
			}


			return $category;
		}

		/**
		* Get entity category based on location_id
		* @param int $location_id the system location id
		* @return array info about the entity category
		*/
		function get_single_category($location_id)
		{
			$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);
			
			$type_arr = explode('.',  $loc_arr['location']);
			
			if(!count($type_arr) == 3)
			{
				return array();
			}

			$type = $type_arr[1];

			$sql = "SELECT * FROM fm_{$type}_category WHERE location_id =" . (int)$location_id;

			$this->db->query($sql,__LINE__,__FILE__);

			$category = array();
			if ($this->db->next_record())
			{
				$category = array
				(
					'id'						=> $this->db->f('id'),
					'entity_id'					=> $this->db->f('entity_id'),
					'name'						=> $this->db->f('name',true),
					'descr'						=> $this->db->f('descr',true),
					'prefix'					=> $this->db->f('prefix',true),
					'lookup_tenant'				=> $this->db->f('lookup_tenant'),
					'tracking'					=> $this->db->f('tracking'),
					'location_level'			=> $this->db->f('location_level'),
					'location_link_level'		=> $this->db->f('location_link_level'),
					'fileupload'				=> $this->db->f('fileupload'),
					'loc_link'					=> $this->db->f('loc_link'),
					'start_project'				=> $this->db->f('start_project'),
					'start_ticket'				=> $this->db->f('start_ticket'),
					'is_eav'					=> $this->db->f('is_eav'),
					'enable_bulk'				=> $this->db->f('enable_bulk'),
					'jasperupload'				=> $this->db->f('jasperupload'),
					'parent_id'					=> $this->db->f('parent_id'),
					'level'						=> $this->db->f('level'),
					'location_id'				=> $location_id
					);

			}

			return $category;
		}


		function read_category_name($entity_id,$cat_id)
		{
			$sql = "SELECT * FROM fm_{$this->type}_category WHERE entity_id =" . (int)$entity_id . ' AND id = ' . (int)$cat_id;
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('name', true);
		}

		function add_entity($entity)
		{

			$entity['name'] = $this->db->db_addslashes($entity['name']);
			$entity['descr'] = $this->db->db_addslashes($entity['descr']);

			$entity['id'] = $this->bocommon->next_id("fm_{$this->type}");
			$location_id = $GLOBALS['phpgw']->locations->add(".{$this->type}." . $entity['id'], $entity['name'], $this->type_app[$this->type], true);

			$values= array(
				$location_id,
				$entity['id'],
				$entity['name'],
				$entity['descr'],
				$entity['location_form'],
				$entity['documentation']
			);

			$values	= $this->db->validate_insert($values);

			$this->db->query("INSERT INTO fm_{$this->type} (location_id,id,name, descr,location_form,documentation) "
				. "VALUES ($values)",__LINE__,__FILE__);


			$receipt['id']= $entity['id'];

			$receipt['message'][] = array('msg'=> lang('entity has been added'));
			return $receipt;
		}

		function get_default_column_def()
		{
			$fd = array();
			$fd['id'] = array('type' => 'int', 'precision' => 4, 'nullable' => false);
			$fd['num'] = array('type' => 'varchar', 'precision' => 16, 'nullable' => false);

			$fd['p_num'] = array('type' => 'varchar', 'precision' => 16, 'nullable' => true);
			$fd['p_entity_id'] = array('type' => 'int', 'precision' => 4, 'nullable' => true);
			$fd['p_cat_id'] = array('type' => 'int', 'precision' => 4, 'nullable' => true);
			$fd['location_code'] = array('type' => 'varchar', 'precision' => 30, 'nullable' => true);

			$location_type = $this->bocommon->next_id('fm_location_type');

			for ($i=1; $i<$location_type; $i++)
			{
				$fd['loc' . $i] = array('type' => 'varchar', 'precision' => 4, 'nullable' => true);
			}
			//Correct the first one
			$fd['loc1'] = array('type' => 'varchar', 'precision' => 6, 'nullable' => true);

			$fd['address'] = array('type' => 'varchar', 'precision' => 150, 'nullable' => true);
			$fd['tenant_id'] = array('type' => 'int', 'precision' => 4, 'nullable' => true);
			$fd['contact_phone'] = array('type' => 'varchar', 'precision' => 30, 'nullable' => true);
	//		$fd['status'] = array('type' => 'int', 'precision' => 4, 'nullable' => true);

			$fd['entry_date'] = array('type' => 'int', 'precision' => 4, 'nullable' => true);
			$fd['user_id'] = array('type' => 'int', 'precision' => 4, 'nullable' => true);

			return $fd;
		}

		function add_category($values)
		{
			$this->db->transaction_begin();

			$table = "fm_{$this->type}_category";
			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$values['id'] = $this->bocommon->next_id($table, array('entity_id'=>$values['entity_id']));
			
			$custom_tbl = !$values['is_eav'] ? "fm_{$this->type}_{$values['entity_id']}_{$values['id']}" : null;
			
			$location_id = $GLOBALS['phpgw']->locations->add(".{$this->type}.{$values['entity_id']}.{$values['id']}", $values['name'],  $this->type_app[$this->type], true, $custom_tbl, $c_function = true);

			if($values['parent_id'])
			{
				$this->db->query("SELECT level FROM $table  WHERE entity_id = {$values['entity_id']} AND id=" . (int)$values['parent_id'],__LINE__,__FILE__);
				$this->db->next_record();
				$level	= (int)$this->db->f('level') +1;
			}
			else
			{
				$level	= 0;
			}


			$values_insert= array
				(
					$location_id,
					$values['entity_id'],
					$values['id'],
					$values['name'],
					$values['descr'],
					$values['prefix'],
					$values['lookup_tenant'],
					$values['tracking'],
					$values['location_level'],
					$values['location_link_level'],
					$values['fileupload'],
					$values['loc_link'],
					$values['start_project'],
					$values['start_ticket'],
					$values['is_eav'],
					$values['enable_bulk'],
					$values['jasperupload'],
					$values['parent_id'],
					$level
				);

			$values_insert	= $this->db->validate_insert($values_insert);

			$this->db->query("INSERT INTO {$table} (location_id,entity_id,id,name, descr,prefix,lookup_tenant,tracking,location_level,location_link_level,fileupload,loc_link,start_project,start_ticket,is_eav,enable_bulk,jasperupload,parent_id,level ) "
				. "VALUES ($values_insert)",__LINE__,__FILE__);


			$receipt['id']= $values['id'];

			if($values['is_eav']) // if modelles as eav - we are good
			{
				$values_insert = array
				(
					'location_id'	=> $location_id,
					'name'			=> ".{$this->type}.{$values['entity_id']}.{$values['id']}::{$values['name']}",
					'description'	=> $values['descr'],
					'is_ifc'		=> 0
				);

				$this->db->query('INSERT INTO fm_bim_type (' . implode(',',array_keys($values_insert)) . ') VALUES ('
				 . $this->db->validate_insert(array_values($values_insert)) . ')',__LINE__,__FILE__);

				$this->db->transaction_commit();			

				$receipt['message'][] = array('msg'	=> lang('%1 has been saved as an eav-model',$values['name']));
				return $receipt;
			}
			
			
			// if not eav - we need a table to hold the attributes
			$this->init_process();

			$fd = $this->get_default_column_def();

			$pk[]= 'id';

			$ix = array();

			if( $this->type == 'entity' )
			{
				$ix =  array('location_code');
			}

			$table			= "fm_{$this->type}_{$values['entity_id']}_{$values['id']}";

			if(($this->oProc->CreateTable($table,array('fd' => $fd,'pk' => $pk,'fk' => $fk,'ix' => $ix,'uc' => array()))))
			{

	/*			$values_insert= array(
					$location_id,
					1,
					'status',
					'Status',
					'Status',
					'LB',
					1,
					'true'
					);

				$values_insert	= $this->db->validate_insert($values_insert);

				$this->db->query("INSERT INTO phpgw_cust_attribute (location_id,id,column_name,input_text,statustext,datatype,attrib_sort,nullable) "
					. "VALUES ($values_insert)",__LINE__,__FILE__);
	 */

				$receipt['message'][] = array('msg'	=> lang('table %1 has been saved',$table));
				$this->db->transaction_commit();
			}
			else
			{
				$receipt['error'][] = array('msg'	=> lang('table could not be added')	);
				if( $this->db->get_transaction() )
				{
					$this->db->transaction_abort();
				}
				else
				{
					$this->db->query("DELETE FROM {$table} WHERE id=" . $values['id'] . " AND entity_id=" . $values['entity_id'],__LINE__,__FILE__);
					unset($receipt['id']);
				}
			}

			return $receipt;
		}

		function edit_entity($entity)
		{
			if (!$entity['name'])
			{
				$receipt['error'][] = array('msg'=>lang('Name not entered!'));
			}

			if (!$receipt['error'])
			{
				$table = "fm_{$this->type}";

				$entity['name'] = $this->db->db_addslashes($entity['name']);
				$entity['descr'] = $this->db->db_addslashes($entity['descr']);

				if(!$entity['location_form'])
				{
					unset($entity['lookup_entity']);
				}

				$value_set=array(
					'descr'			=> $entity['descr'],
					'name'			=> $entity['name'],
					'location_form'	=> $entity['location_form'],
					'lookup_entity'	=> serialize($entity['lookup_entity']),
					'documentation'	=> $entity['documentation']
				);

				$value_set	= $this->db->validate_update($value_set);

				$this->db->transaction_begin();

				$this->db->query("UPDATE $table set $value_set WHERE id=" . $entity['id'],__LINE__,__FILE__);

				$GLOBALS['phpgw']->locations->update_description(".{$this->type}.{$entity['id']}", $entity['name'],  $this->type_app[$this->type]);

				$this->db->query("DELETE FROM fm_{$this->type}_lookup WHERE type='lookup' AND entity_id=" . $entity['id'],__LINE__,__FILE__);
				if (isset($entity['include_entity_for']) AND is_array($entity['include_entity_for']))
				{
					foreach($entity['include_entity_for'] as $location)
					{
						$this->db->query("INSERT INTO fm_{$this->type}_lookup (entity_id,location,type)"
							. "VALUES (" .$entity['id'] . ",'$location','lookup' )",__LINE__,__FILE__);
					}
				}

				$this->db->query("DELETE FROM fm_{$this->type}_lookup WHERE type='start' AND entity_id=" . (int)$entity['id'],__LINE__,__FILE__);

				if (isset($entity['start_entity_from']) AND is_array($entity['start_entity_from']))
				{
					foreach($entity['start_entity_from'] as $location)
					{
						$this->db->query("INSERT INTO fm_{$this->type}_lookup (entity_id,location,type)"
							. "VALUES (" .$entity['id'] . ",'$location','start' )",__LINE__,__FILE__);
					}
				}

				$this->db->transaction_commit();

				$receipt['message'][] = array('msg'=> lang('entity has been edited'));
			}
			else
			{
				$receipt['error'][] = array('msg'	=> lang('entity has NOT been edited'));
			}

			return $receipt;
		}

		function edit_category($entity)
		{
			$receipt = array();
			if (!$entity['name'])
			{
				$receipt['error'][] = array('msg'=>lang('Name not entered!'));
			}

			if (!isset($receipt['error']))
			{
				$table = "fm_{$this->type}_category";

				$this->db->query("SELECT level FROM $table WHERE entity_id=" . $entity['entity_id']. " AND id=" . $entity['id'],__LINE__,__FILE__);
				$this->db->next_record();
				$old_level	= (int)$this->db->f('level');

				if(isset($entity['parent_id']) && $entity['parent_id'])
				{
					$this->db->query("SELECT level FROM $table  WHERE entity_id=" . $entity['entity_id']. " AND id=" . (int)$entity['parent_id'],__LINE__,__FILE__);
					$this->db->next_record();
					$level	= (int)$this->db->f('level') +1;
				}
				else
				{
					$level	= 0;
				}

				if($old_level !=$level)
				{
					$this->level = $level;
					$this->parent_gap = 1;
					$this->category_parent = $entity['id'];
					while ($this->category_parent)
					{
						$this->check_move_child($entity['entity_id']);
					}

					if ( count($this->move_child) )
					{
						foreach ($this->move_child as $child)
						{
							$this->db->query("UPDATE $table set level= {$child['new_level']} WHERE entity_id={$entity['entity_id']} AND id=" . (int)$child['id'],__LINE__,__FILE__);
						}
					}
				}

				$entity['name'] = $this->db->db_addslashes($entity['name']);
				$entity['descr'] = $this->db->db_addslashes($entity['descr']);

				$value_set=array
					(
						'descr'						=> $entity['descr'],
						'name'						=> $entity['name'],
						'prefix'					=> $entity['prefix'],
						'lookup_tenant'				=> $entity['lookup_tenant'],
						'tracking'					=> $entity['tracking'],
						'location_level'			=> $entity['location_level'],
						'location_link_level'		=> $entity['location_link_level'],
						'fileupload'				=> $entity['fileupload'],
						'loc_link'					=> $entity['loc_link'],
						'start_project'				=> $entity['start_project'],
						'start_ticket'				=> $entity['start_ticket'],
						'is_eav'					=> $entity['is_eav'],
						'enable_bulk'				=> $entity['enable_bulk'],
						'jasperupload'				=> $entity['jasperupload'],
						'parent_id'					=> $entity['parent_id'],
						'level'						=> $level
					);

				$value_set	= $this->db->validate_update($value_set);

				$this->db->query("UPDATE $table set $value_set WHERE entity_id=" . (int)$entity['entity_id']. " AND id=" . (int)$entity['id'],__LINE__,__FILE__);

				$GLOBALS['phpgw']->locations->update_description(".{$this->type}.{$entity['entity_id']}.{$entity['id']}", $entity['name'],  $this->type_app[$this->type]);

				$receipt['message'][] = array('msg'=> lang('entity has been edited'));
			}
			else
			{
				$receipt['error'][] = array('msg'	=> lang('entity has NOT been edited'));
			}

			return $receipt;
		}

		/**
		 * ???
		 *
		 * @param bool $recursive is the function being called recursively
		 * @return a list of children to be moved
		 */
		private function check_move_child($entity_id, $recursive = false)
		{
			$entity_id = (int)$entity_id;
			// New run so lets reset the data
			if ( !$recursive )
			{
				$this->move_child = array();
			}

			$table = "fm_{$this->type}_category";

			$continue = false;
			$move_child = array();
			$this->db->query("SELECT id FROM $table WHERE entity_id= {$entity_id} AND parent_id=" . (int)$this->category_parent,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$this->move_child[] = array
					(
						'id' 			=>(int)$this->db->f('id'),
						'category_parent' 	=>(int)$this->category_parent,
						'new_level' 	=> ($this->level + $this->parent_gap)
					);

				$move_child[] = (int)$this->db->f('id');
				$continue = true;
			}
			if($continue)
			{
				$this->parent_gap++;
				foreach ($move_child as $parent_id)
				{
					$this->category_parent = $parent_id;
					$this->check_move_child($entity_id,true);
				}

			}
			else
			{
				$this->category_parent = false;
			}
		}

		protected function check_move_child_delete($entity_id,$id)
		{
			$continue = false;
			$move_child = array();
			$table = "fm_{$this->type}_category";

			$this->db->query("SELECT id FROM {$table} WHERE entity_id = {$entity_id} AND parent_id=" . (int) $this->category_id,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$this->move_child[] = array
					(
						'id' 			=> (int)$this->db->f('id'),
						'parent'	 	=> $this->category_parent,
						'new_level' 	=> $this->level
					);

				$move_child[] = (int)$this->db->f('id');
				$continue = true;
			}
			unset ($this->category_parent);
			if($continue)
			{
				$this->level++;
				foreach ($move_child as $id)
				{
					$this->category_id = $id;
					$this->check_move_child_delete($entity_id, $id);
				}

			}
			else
			{
				$this->check_parent = false;
			}
		}


		function delete_entity($id)
		{
			$this->db->transaction_begin();
			$id = (int) $id;
			$category_list=$this->read_category(array('allrows'=>true, 'entity_id'=>$id));
			$locations = array();
			$locations[] = $GLOBALS['phpgw']->locations->get_id( $this->type_app[$this->type], ".{$this->type}.{$id}");
			$subs = $GLOBALS['phpgw']->locations->get_subs( $this->type_app[$this->type], ".{$this->type}.{$id}");
			if (is_array($subs) && count($subs))
			{
				$locations = array_merge($locations, array_keys($subs));
			}

			$this->db->query("DELETE FROM fm_{$this->type} WHERE id={$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_{$this->type}_category WHERE entity_id={$id}",__LINE__,__FILE__);
			$this->db->query('DELETE FROM phpgw_cust_attribute WHERE location_id IN (' . implode(',', $locations) . ')',__LINE__,__FILE__);
			$this->db->query('DELETE FROM phpgw_locations WHERE location_id IN (' . implode(',', $locations) . ')',__LINE__,__FILE__);
			$this->db->query('DELETE FROM phpgw_acl WHERE location_id IN (' . implode(',', $locations) . ')',__LINE__,__FILE__);
			if (isset($category_list) && is_array($category_list))
			{
				$this->init_process();

				foreach($category_list as $entry)
				{
					$this->oProc->DropTable("fm_{$this->type}_{$id}_{$entry['id']}");
				}
			}
			$this->db->transaction_commit();
		}

		function delete_category($entity_id, $id)
		{	
			$this->init_process();

			$this->db->transaction_begin();

			$table = "fm_{$this->type}_category";
			$this->db->query("SELECT parent_id,level FROM $table WHERE entity_id = {$entity_id} AND id={$id}",__LINE__,__FILE__);
			$this->db->next_record();
			$this->level		= (int)$this->db->f('level');
			$this->category_parent	= (int)$this->db->f('parent_id');

			$this->check_parent = true;
			$this->category_id = $id;
			while ($this->check_parent)
			{
				$this->check_move_child_delete($entity_id, $id);
			}

			if (is_array($this->move_child))
			{
				foreach ($this->move_child as $child)
				{
					$new_level = $child['new_level'];

					if($child['parent'] || $child['parent']===0)
					{
						$sql = "UPDATE $table SET level= $new_level, parent_id = {$child['parent']} WHERE entity_id = {$entity_id} AND id= {$child['id']}";
					}
					else
					{
						$sql = "UPDATE $table SET level = $new_level WHERE entity_id = {$entity_id} AND id= {$child['id']}";
					}
					$this->db->query($sql,__LINE__,__FILE__);
				}
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id( $this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$id}");

			$category = $this->read_single_category($entity_id, $id);
			if($category['is_eav'])
			{
				$this->db->query("SELECT id as type FROM fm_bim_type WHERE location_id= {$location_id}",__LINE__,__FILE__);
				$this->db->next_record();
				$type = (int)$this->db->f('type');
				$this->db->query("DELETE FROM fm_bim_item WHERE type = {$type}",__LINE__,__FILE__);
				$this->db->query("DELETE FROM fm_bim_type WHERE location_id= {$location_id}",__LINE__,__FILE__);
			}
			else
			{
				$this->oProc->DropTable("fm_{$this->type}_{$entity_id}_{$id}");
			}

			$this->db->query("DELETE FROM fm_{$this->type}_category WHERE entity_id= {$entity_id} AND id= {$id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_cust_attribute WHERE location_id = {$location_id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_locations WHERE location_id  = {$location_id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_acl WHERE  location_id  = {$location_id}",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_{$this->type}_history WHERE history_appname = '{$this->type}_{$entity_id}_{$id}'",__LINE__,__FILE__);

			$this->db->transaction_commit();
		}

		function get_table_def($entity_id,$cat_id)
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id( $this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");
			$table = "fm_{$this->type}_{$entity_id}_{$cat_id}";
			$metadata = $this->db->metadata($table);

			if(isset($this->db->adodb))
			{
				$i = 0;
				foreach($metadata as $key => $val)
				{
					$metadata_temp[$i]['name'] = $key;
					$i++;
				}
				$metadata = $metadata_temp;
				unset ($metadata_temp);
			}

			$fd = $this->get_default_column_def();

			for ($i=0; $i<count($metadata); $i++)
			{
				$sql = "SELECT * FROM phpgw_cust_attribute WHERE location_id = {$location_id} AND column_name = '{$metadata[$i]['name']}'";

				$this->db->query($sql,__LINE__,__FILE__);
				while($this->db->next_record())
				{
					if(!$precision = $this->db->f('precision_'))
					{
						$precision = $this->bocommon->translate_datatype_precision($this->db->f('datatype'));
					}

					$fd[$metadata[$i]['name']] = array(
						'type' => $this->bocommon->translate_datatype_insert(stripslashes($this->db->f('datatype'))),
						'precision' => $precision,
						'nullable' => stripslashes($this->db->f('nullable')),
						'default' => stripslashes($this->db->f('default_value')),
						'scale' => $this->db->f('scale')
					);
					unset($precision);
				}
			}

			$table_def = array(
				$table =>	array(
					'fd' => $fd
				)
			);

			$table_def[$table]['pk'] = array('id');
			$table_def[$table]['fk'] = array();
			$table_def[$table]['ix'] = array();
			$table_def[$table]['uc'] = array();

			return $table_def;
		}

		function delete_history($entity_id, $cat_id, $attrib_id)
		{
			$this->db->query("DELETE FROM fm_{$this->type}_history WHERE history_appname = '{$this->type}_{$entity_id}_{$cat_id}' AND history_attrib_id = {$attrib_id}",__LINE__,__FILE__);
		}

		function init_process()
		{
			$this->oProc 				= CreateObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$this->oProc->m_odb			= & $this->db;
			$this->oProc->m_odb->Halt_On_Error	= 'yes';
		}

 
		/**
		 * Reduserer fra 684 til 265 tabeller for Nordlandssykehuset
		 *
		 * @return bool true on success
		 */
		function convert_to_eav()
		{
//			die('vent litt med denne');

			phpgw::import_class('phpgwapi.xmlhelper');			
			$this->type = 'entity';
			$entity_list 	= $this->read(array('allrows' => true));

			$this->db->transaction_begin();

			foreach($entity_list as $entry)
			{
				$cat_list = $this->read_category(array('allrows'=>true,'entity_id'=>$entry['id']));

				foreach($cat_list as $category)
				{
					if(!$category['is_eav'])
					{

						$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".{$this->type}.{$category['entity_id']}.{$category['id']}");
						$values_insert = array
						(
							'location_id'	=> $location_id,
							'name'			=> ".{$this->type}.{$category['entity_id']}.{$category['id']}::{$category['name']}",
							'description'	=> $category['descr'],
							'is_ifc'		=> 0
						);

						$this->db->query('INSERT INTO fm_bim_type (' . implode(',',array_keys($values_insert)) . ') VALUES ('
				 		. $this->db->validate_insert(array_values($values_insert)) . ')',__LINE__,__FILE__);
				 		
						$sql = "UPDATE fm_{$this->type}_category SET is_eav = 1 WHERE entity_id =" . (int)$category['entity_id'] . ' AND id = ' . (int)$category['id'];
						$this->db->query($sql,__LINE__,__FILE__);
						
						$sql = "UPDATE phpgw_locations SET c_attrib_table = NULL WHERE location_id = {$location_id}";
						$this->db->query($sql,__LINE__,__FILE__);

				 		$type = $this->db->get_last_insert_id('fm_bim_type', 'id');
				 		
				 		$sql = "SELECT * FROM fm_{$this->type}_{$category['entity_id']}_{$category['id']}";
				 		$this->db->query($sql,__LINE__,__FILE__);
						while ($this->db->next_record())
						{
							$data = $this->db->Record;

							$xmldata = phpgwapi_xmlhelper::toXML($data, "_{$this->type}_{$category['entity_id']}_{$category['id']}");
							$doc = new DOMDocument('1.0', 'utf-8');
							$doc->loadXML($xmldata);
							$domElement = $doc->getElementsByTagName("_{$this->type}_{$category['entity_id']}_{$category['id']}")->item(0);
							$domAttribute = $doc->createAttribute('appname');
							$domAttribute->value = 'property';

							// Don't forget to append it to the element
							$domElement->appendChild($domAttribute);
	
							// Append it to the document itself
							$doc->appendChild($domElement);

							$doc->preserveWhiteSpace = true;
							$doc->formatOutput = true;
							$xml = $doc->saveXML();

							$p_location_id = '';
							if($data['p_cat_id'])
							{
								$p_location_id = $GLOBALS['phpgw']->locations->get_id('property', ".{$this->type}.{$data['p_entity_id']}.{$data['p_cat_id']}");
							}

							$p_id ='';
							if($data['p_num'])
							{
								$p_id		= (int) ltrim($data['p_num'], $category['prefix']);
							}
							if (function_exists('com_create_guid') === true)
							{
								$guid = trim(com_create_guid(), '{}');
							}
							else
							{
								$guid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
							}

							$values_insert = array
							(
				  				'id'					=> $data['id'],
				  				'location_id'			=> $location_id,
				  				'type'					=> $type,
				  				'guid'					=> $guid,
								'xml_representation'	=> $this->db->db_addslashes($xml),
								'model'					=> 0,
								'p_location_id'			=> $p_location_id,
								'p_id'					=> isset($data['p_num']) && $data['p_num'] ? (int)$data['p_num'] : '',
								'location_code'			=> $data['location_code'],
								'loc1'					=> $data['loc1'],
								'address'				=> $data['address'],
								'entry_date'			=> $data['entry_date'],
								'user_id'				=> $data['user_id']
							);
							
							$this->db->query("INSERT INTO fm_bim_item (" . implode(',',array_keys($values_insert)) . ') VALUES ('
			 					. $this->db->validate_insert(array_values($values_insert)) . ')',__LINE__,__FILE__);
						}

				 		$sql = "DROP TABLE fm_{$this->type}_{$category['entity_id']}_{$category['id']}";
				 		_debug_array($sql);
				 		$this->db->query($sql,__LINE__,__FILE__);
					}
				}
			}
			return $this->db->transaction_commit();
		}
	}
