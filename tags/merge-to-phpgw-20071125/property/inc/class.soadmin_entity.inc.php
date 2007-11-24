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
	* @subpackage admin
 	* @version $Id: class.soadmin_entity.inc.php,v 1.33 2007/10/13 10:02:53 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soadmin_entity
	{
		var $grants;

		function property_soadmin_entity($entity_id='',$cat_id='')
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();
			$this->join			= $this->bocommon->join;
			$this->like			= $this->bocommon->like;

			if($entity_id && $cat_id)
			{
				$this->category_name	= $this->read_category_name($entity_id,$cat_id);
			}
		}

		function read($data)
		{
			if(is_array($data))
			{
				if (isset($data['start']))
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by id asc';
			}

			$table = 'fm_entity';

			$querymethod = '';
			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " where name $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

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
			if(is_array($data))
			{
				$start = (isset($data['start'])&& $data['start'] ? $data['start'] : 0);
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$entity_id = (isset($data['entity_id'])?$data['entity_id']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by id asc';
			}

			$table = 'fm_entity_category';

			$querymethod = '';
			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " AND name $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table WHERE entity_id=$entity_id $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$standard[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name'),
					'prefix'=> $this->db->f('prefix'),
					'descr'	=> $this->db->f('descr')
				);
			}
			return $standard;
		}

		function read_status($data)
		{
			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$entity_id = (isset($data['entity_id'])?$data['entity_id']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by id asc';
			}

			$table	= 'fm_entity_'. $entity_id .'_'.$cat_id . '_status';

			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " AND name $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$status[] = array
				(
					'id'	=> $this->db->f('id'),
					'descr'	=> $this->db->f('descr')
				);
			}
			return $status;
		}


		function read_single($id)
		{

			$sql = "SELECT * FROM fm_entity  where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$entity['id']				= $this->db->f('id');
				$entity['name']				= $this->db->f('name');
				$entity['descr']			= $this->db->f('descr');
				$entity['location_form']	= $this->db->f('location_form');
				$entity['lookup_entity']	= unserialize($this->db->f('lookup_entity'));
				$entity['documentation']	= $this->db->f('documentation');
			}

			$sql = "SELECT location FROM fm_entity_lookup where entity_id=$id AND type='lookup'";

			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$entity['include_entity_for'][] = $this->db->f('location');
			}

			$sql = "SELECT location FROM fm_entity_lookup where entity_id=$id AND type='start'";

			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$entity['start_entity_from'][] = $this->db->f('location');
			}

			return $entity;
		}

		function read_single_category($entity_id,$cat_id)
		{
			$sql = "SELECT * FROM fm_entity_category where entity_id=$entity_id AND id=$cat_id";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$category['id']			= $this->db->f('id');
				$category['name']		= $this->db->f('name');
				$category['descr']		= $this->db->f('descr');
				$category['prefix']		= $this->db->f('prefix');
				$category['lookup_tenant']	= $this->db->f('lookup_tenant');
				$category['tracking']	= $this->db->f('tracking');
				$category['location_level']	= $this->db->f('location_level');
				$category['fileupload']	= $this->db->f('fileupload');
				$category['loc_link']	= $this->db->f('loc_link');
				$category['start_project']	= $this->db->f('start_project');
				$category['start_ticket']	= $this->db->f('start_ticket');
				return $category;
			}
		}

		function read_category_name($entity_id,$cat_id)
		{
			$sql = "SELECT * FROM fm_entity_category where entity_id=$entity_id AND id=$cat_id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('name');
		}

		function read_single_status($entity_id,$cat_id,$id)
		{

			$table	= 'fm_entity_'. $entity_id .'_'.$cat_id . '_status';
			$sql = "SELECT * FROM $table  where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$status['id']				= $this->db->f('id');
				$status['descr']			= $this->db->f('descr');

				return $status;
			}
		}

		function add_entity($entity)
		{
			$entity['name'] = $this->db->db_addslashes($entity['name']);
			$entity['descr'] = $this->db->db_addslashes($entity['descr']);

			$entity['id'] = $this->bocommon->next_id('fm_entity');

			$values= array(
				$entity['id'],
				$entity['name'],
				$entity['descr'],
				$entity['location_form'],
				$entity['documentation']
				);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->query("INSERT INTO fm_entity (id,name, descr,location_form,documentation) "
				. "VALUES ($values)",__LINE__,__FILE__);


			$values_acl_location= array(
				$this->currentapp,
				'.entity.' . $entity['id'],
				$entity['name'],
				1
				);

			$values_acl_location	= $this->bocommon->validate_db_insert($values_acl_location);

			$this->db->query("INSERT INTO phpgw_acl_location (appname,id,descr,allow_grant) "
				. "VALUES ($values_acl_location)",__LINE__,__FILE__);

			$receipt['id']= $entity['id'];

			$receipt['message'][] = array('msg'=> lang('entity has been added'));
			return $receipt;
		}


		function add_status($values,$entity_id,$cat_id)
		{
			$values['id'] = $this->db->db_addslashes($values['id']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$values_insert= array(
				$values['id'],
				$values['descr'],
				);

			$values_insert	= $this->bocommon->validate_db_insert($values_insert);

			$table	= 'fm_entity_'. $entity_id .'_'.$cat_id . '_status';

			$this->db->query("INSERT INTO $table (id,descr) VALUES ($values_insert)",__LINE__,__FILE__);

			$receipt['id']= $values['id'];

			$receipt['message'][] = array('msg'=> lang('status has been added'));
			return $receipt;
		}


		function get_default_column_def()
		{
		
			$fd=array();
			$fd['id'] = array('type' => 'int', 'precision' => 4, 'nullable' => False);
			$fd['num'] = array('type' => 'varchar', 'precision' => 16, 'nullable' => False);
			$fd['p_num'] = array('type' => 'varchar', 'precision' => 16, 'nullable' => True);
			$fd['p_entity_id'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);
			$fd['p_cat_id'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);
			$fd['location_code'] = array('type' => 'varchar', 'precision' => 25, 'nullable' => True);

			$location_type = $this->bocommon->next_id('fm_location_type');
			
			for ($i=1; $i<$location_type; $i++)
			{
				$fd['loc' . $i] = array('type' => 'varchar', 'precision' => 4, 'nullable' => True);
			}

			$fd['address'] = array('type' => 'varchar', 'precision' => 150, 'nullable' => True);
			$fd['tenant_id'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);
			$fd['contact_phone'] = array('type' => 'varchar', 'precision' => 30, 'nullable' => True);
			$fd['status'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);
			$fd['entry_date'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);
			$fd['user_id'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);
			
			return $fd;
		}

		function add_category($values)
		{
			$this->db->transaction_begin();
			
			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$values['id'] = $this->bocommon->next_id('fm_entity_category',array('entity_id'=>$values['entity_id']));

			$values_insert= array(
				$values['entity_id'],
				$values['id'],
				$values['name'],
				$values['descr'],
				$values['prefix'],
				$values['lookup_tenant'],
				$values['tracking'],
				$values['location_level'],
				$values['fileupload'],
				$values['loc_link'],
				$values['start_project'],
				$values['start_ticket']
				);

			$values_insert	= $this->bocommon->validate_db_insert($values_insert);

			$this->db->query("INSERT INTO fm_entity_category (entity_id,id,name, descr,prefix,lookup_tenant,tracking,location_level,fileupload,loc_link,start_project,start_ticket) "
				. "VALUES ($values_insert)",__LINE__,__FILE__);

			$values_acl_location= array(
				$this->currentapp,
				'.entity.' . $values['entity_id'] . '.' . $values['id'],
				$values['name'],
				1
				);

			$values_acl_location	= $this->bocommon->validate_db_insert($values_acl_location);

			$this->db->query("INSERT INTO phpgw_acl_location (appname,id,descr,allow_grant) "
				. "VALUES ($values_acl_location)",__LINE__,__FILE__);

			$receipt['id']= $values['id'];

			$this->init_process();

			$fd = $this->get_default_column_def();

			$pk[]= 'id';
			$table			= 'fm_entity_'. $values['entity_id'] .'_'.$values['id'];
/*
			$fd_status['id'] = array('type' => 'varchar', 'precision' => 20, 'nullable' => False);
			$fd_status['descr'] = array('type' => 'varchar', 'precision' => 255, 'nullable' => False);
			$pk_status[]= 'id';

			$statustable	= $table . '_' .'status';
*/
			if(($this->oProc->CreateTable($table,array('fd' => $fd,'pk' => $pk,'fk' => $fk,'ix' => array('location_code'),'uc' => array()))))
//				&& ($this->oProc->CreateTable($statustable,array('fd' => $fd_status,'pk' => $pk_status,'fk' => $fk_status,'ix' => False,'uc' => array()))))
			{

				$values_insert= array(
					$values['entity_id'],
					$values['id'],
					1,
					'status',
					'Status',
					'Status',
					'LB',
					1,
					'True'
					);

				$values_insert	= $this->bocommon->validate_db_insert($values_insert);

				$this->db->query("INSERT INTO fm_entity_attribute (entity_id,cat_id,id,column_name,input_text,statustext,datatype,attrib_sort,nullable) "
					. "VALUES ($values_insert)",__LINE__,__FILE__);

				$receipt['message'][] = array('msg'	=> lang('table %1 has been saved',$table));
				$this->db->transaction_commit();
			}
			else
			{
				$receipt['error'][] = array('msg'	=> lang('table could not be added')	);
				if($this->db->Transaction)
				{
					$this->db->transaction_abort();
				}
				else
				{
					$this->db->query("DELETE FROM fm_entity_category WHERE id=" . $values['id'] . " AND entity_id=" . $values['entity_id'],__LINE__,__FILE__);
					unset($receipt['id']);
				}
			}

			return $receipt;
		}

		function edit_status($values,$entity_id,$cat_id)
		{
			$table	= 'fm_entity_'. $entity_id .'_'.$cat_id . '_status';

			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$value_set=array(
				'descr'			=> $values['descr'],
				);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE $table set $value_set WHERE id='" . $values['id'] . "'",__LINE__,__FILE__);

			$receipt['message'][] = array('msg'=> lang('Status has been edited'));

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
				$table = 'fm_entity';

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

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->transaction_begin();

				$this->db->query("UPDATE $table set $value_set WHERE id=" . $entity['id'],__LINE__,__FILE__);

				$value_set_acl=array(
					'descr'			=> $entity['name']
					);

				$value_set_acl	= $this->bocommon->validate_db_update($value_set_acl);

				$this->db->query("UPDATE phpgw_acl_location set $value_set_acl WHERE appname = '" . $this->currentapp . "' AND id='.entity." . $entity['id']. "'",__LINE__,__FILE__);

				$this->db->query("DELETE FROM fm_entity_lookup WHERE type='lookup' AND entity_id=" . $entity['id'],__LINE__,__FILE__);
				if (isset($entity['include_entity_for']) AND is_array($entity['include_entity_for']))
				{
					foreach($entity['include_entity_for'] as $location)
					{
						$this->db->query("INSERT INTO fm_entity_lookup (entity_id,location,type)"
						. "VALUES (" .$entity['id'] . ",'$location','lookup' )",__LINE__,__FILE__);
					}
				}

				$this->db->query("DELETE FROM fm_entity_lookup WHERE type='start' AND entity_id=" . $entity['id'],__LINE__,__FILE__);

				if (isset($entity['start_entity_from']) AND is_array($entity['start_entity_from']))
				{
					foreach($entity['start_entity_from'] as $location)
					{
						$this->db->query("INSERT INTO fm_entity_lookup (entity_id,location,type)"
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

			if (!$entity['name'])
			{
				$receipt['error'][] = array('msg'=>lang('Name not entered!'));
			}

			if (!$receipt['error'])
			{
				$table = 'fm_entity_category';

				$entity['name'] = $this->db->db_addslashes($entity['name']);
				$entity['descr'] = $this->db->db_addslashes($entity['descr']);

				$value_set=array(
					'descr'			=> $entity['descr'],
					'name'			=> $entity['name'],
					'prefix'		=> $entity['prefix'],
					'lookup_tenant'	=> $entity['lookup_tenant'],
					'tracking'		=> $entity['tracking'],
					'location_level'=> $entity['location_level'],
					'fileupload'	=> $entity['fileupload'],
					'loc_link'		=> $entity['loc_link'],
					'start_project'	=> $entity['start_project'],
					'start_ticket'	=> $entity['start_ticket']
					);

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("UPDATE $table set $value_set WHERE entity_id=" . $entity['entity_id']. " AND id=" . $entity['id'],__LINE__,__FILE__);

				$value_set_acl=array(
					'descr'			=> $entity['name']
					);

				$value_set_acl	= $this->bocommon->validate_db_update($value_set_acl);

				$this->db->query("UPDATE phpgw_acl_location set $value_set_acl WHERE appname = '" . $this->currentapp . "' AND id='.entity." . $entity['entity_id']. "." . $entity['id']. "'",__LINE__,__FILE__);


				$receipt['message'][] = array('msg'=> lang('entity has been edited'));
			}
			else
			{
				$receipt['error'][] = array('msg'	=> lang('entity has NOT been edited'));
			}

			return $receipt;
		}

		function delete_entity($id)
		{
			$category_list=$this->read_category(array('entity_id'=>$id));
			$this->db->query("DELETE FROM fm_entity WHERE id=$id",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_entity_category WHERE entity_id=$id",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_entity_attribute WHERE entity_id=$id",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_acl_location WHERE appname = '" . $this->currentapp . "' AND id " . $this->like ."'.entity." . $id ."%'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_acl WHERE acl_appname = '" . $this->currentapp . "' AND  acl_location $this->like '.entity." . $id ."%'",__LINE__,__FILE__);
			if (isset($category_list) AND is_array($category_list))
			{
				$this->init_process();

				foreach($category_list as $entry)
				{
					$this->oProc->DropTable('fm_entity_' . $id . '_' . $entry['id']);
				}
			}

		}

		function delete_category($id,$entity_id)
		{
			$this->init_process();
			$this->oProc->DropTable('fm_entity_' . $entity_id . '_' . $id);
//			$this->oProc->DropTable('fm_entity_' . $entity_id . '_' . $id . '_' . 'status');
			$this->db->query("DELETE FROM fm_entity_category WHERE entity_id= $entity_id AND id= $id",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_entity_attribute WHERE entity_id= $entity_id AND cat_id= $id",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_acl_location WHERE appname = '" . $this->currentapp . "' AND id='.entity." . $entity_id . "." . $id ."'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_acl WHERE acl_appname = '" . $this->currentapp . "' AND  acl_location='.entity." . $entity_id . "." . $id ."'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_entity_history WHERE history_appname = 'entity_" . $entity_id  . '_' . $id . "'",__LINE__,__FILE__);
		}


		function get_table_def($entity_id,$cat_id)
		{
			$table = 'fm_entity_' . $entity_id . '_' . $cat_id;
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
				$sql = "SELECT * FROM fm_entity_attribute WHERE entity_id = $entity_id AND cat_id=$cat_id AND column_name = '" . $metadata[$i]['name'] . "'";

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


		function delete_attrib($cat_id,$entity_id,$attrib_id)
		{
			$this->init_process();
			$this->oProc->m_odb->transaction_begin();
			$this->db->transaction_begin();

			$sql = "SELECT * FROM fm_entity_attribute WHERE entity_id=$entity_id AND cat_id=$cat_id AND id=$attrib_id";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$ColumnName		= $this->db->f('column_name');
			$table	= 'fm_entity_'. $entity_id .'_'.$cat_id;
			$table_def = $this->get_table_def($entity_id,$cat_id);	
			$this->oProc->m_aTables = $table_def;

			$this->oProc->DropColumn($table,$table_def[$table], $ColumnName);

			$sql = "SELECT attrib_sort FROM fm_entity_attribute where entity_id=$entity_id AND cat_id=$cat_id AND id=$attrib_id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('attrib_sort');
			$sql2 = "SELECT max(attrib_sort) as max_sort FROM fm_entity_attribute where entity_id=$entity_id AND cat_id=$cat_id";
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');
			if($max_sort>$attrib_sort)
			{
				$sql = "UPDATE fm_entity_attribute set attrib_sort=attrib_sort-1 WHERE entity_id=$entity_id AND cat_id=$cat_id AND attrib_sort > $attrib_sort";
				$this->db->query($sql,__LINE__,__FILE__);
			}

			$this->db->query("DELETE FROM fm_entity_attribute WHERE entity_id=$entity_id AND cat_id=$cat_id AND id=$attrib_id",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_entity_history WHERE history_appname = 'entity_" . $entity_id  . '_' . $cat_id . "' AND history_entity_attrib_id = $attrib_id",__LINE__,__FILE__);
			$this->db->transaction_commit();
			$this->oProc->m_odb->transaction_commit();

		}

		function delete_status($cat_id,$entity_id,$status_id)
		{
			$table	= 'fm_entity_'. $entity_id .'_'.$cat_id . '_status';

			$this->db->query("DELETE FROM $table WHERE id='$status_id'",__LINE__,__FILE__);
		}

		function read_attrib($data)
		{
			if(is_array($data))
			{
				$start = isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$entity_id = (isset($data['entity_id'])?$data['entity_id']:0);
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$filter_list = (isset($data['filter_list'])?$data['filter_list']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by attrib_sort asc';
			}

			if($filter_list)
			{
				$filter_list = "AND list is NULL";
			} 
			$querymethod = '';
			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " AND (fm_entity_attribute.name $this->like '%$query%' or fm_entity_attribute.descr $this->like '%$query%')";
			}

			$sql = "SELECT * FROM fm_entity_attribute WHERE entity_id=$entity_id AND cat_id = $cat_id $filter_list $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();
			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$attrib[] = array
				(
					'id'		=> $this->db->f('id'),
					'entity_type'	=> $this->db->f('type_id'),
					'attrib_sort'	=> $this->db->f('attrib_sort'),
					'list'		=> $this->db->f('list'),
					'lookup_form'	=> $this->db->f('lookup_form'),
					'entity_form'	=> $this->db->f('entity_form'),
					'column_name'	=> $this->db->f('column_name'),
					'name'		=> $this->db->f('input_text'),
					'size'		=> $this->db->f('size'),
					'statustext'	=> $this->db->f('statustext'),
					'input_text'	=> $this->db->f('input_text'),
					'type_name'	=> $this->db->f('type'),
					'datatype'	=> $this->db->f('datatype'),
					'search'	=> $this->db->f('search')
				);
			}
			return $attrib;
		}

		function read_single_attrib($entity_id,$cat_id,$id)
		{
			$sql = "SELECT * FROM fm_entity_attribute where entity_id=$entity_id AND cat_id=$cat_id AND id=$id";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$attrib['id']						= $this->db->f('id');
				$attrib['column_name']				= $this->db->f('column_name');
				$attrib['input_text']				= $this->db->f('input_text');
				$attrib['statustext']				= $this->db->f('statustext');
				$attrib['column_info']['precision']	= $this->db->f('precision_');
				$attrib['column_info']['scale']		= $this->db->f('scale');
				$attrib['column_info']['default']	= $this->db->f('default_value');
				$attrib['column_info']['nullable']	= $this->db->f('nullable');
				$attrib['column_info']['type']		= $this->db->f('datatype');
				$attrib['type_id']					= $this->db->f('type_id');
				$attrib['type_name']				= $this->db->f('type_name');
				$attrib['lookup_form']				= $this->db->f('lookup_form');
				$attrib['list']						= $this->db->f('list');
				$attrib['search']					= $this->db->f('search');
				$attrib['history']					= $this->db->f('history');
				$attrib['disabled']					= $this->db->f('disabled');
				$attrib['helpmsg']					= $this->db->f('helpmsg');
				if($this->db->f('datatype')=='R' || $this->db->f('datatype')=='CH' || $this->db->f('datatype')=='LB')
				{
					$attrib['choice'] = $this->read_attrib_choice($entity_id,$cat_id,$id);
				}

				return $attrib;
			}
		}

		function read_attrib_choice($entity_id,$cat_id,$attrib_id)
		{
			$choice_table = 'fm_entity_choice';
			$sql = "SELECT * FROM $choice_table WHERE entity_id=$entity_id AND cat_id=$cat_id AND attrib_id=$attrib_id ORDER BY id";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$choice[] = array
				(
					'id'	=> $this->db->f('id'),
					'value'	=> $this->db->f('value')
				);
			}
			return $choice;
		}

		function add_attrib($attrib)
		{
			$attrib['column_name'] = strtolower($this->db->db_addslashes($attrib['column_name']));
			$attrib['input_text'] = $this->db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->db->db_addslashes($attrib['statustext']);
			$attrib['helpmsg'] = $this->db->db_addslashes($attrib['helpmsg']);
			$attrib['default'] = $this->db->db_addslashes($attrib['default']);
			$attrib['id'] = $this->bocommon->next_id('fm_entity_attribute',array('entity_id'=>$attrib['entity_id'],'cat_id'=>$attrib['cat_id']));

			$sql = "SELECT * FROM fm_entity_attribute WHERE entity_id= '{$attrib['entity_id']}' AND cat_id='{$attrib['cat_id']}' AND column_name = '{$attrib['column_name']}'";
			$this->db->query($sql,__LINE__,__FILE__);
			if ( $this->db->next_record() )
			{
				$receipt['id'] = '';
				$receipt['error'] = array();
				$receipt['error'][] = array('msg' => lang('field already exists, please choose another name'));
				$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				return $receipt; //no point continuing
			}


			$sql = "SELECT max(attrib_sort) as max_sort FROM fm_entity_attribute where entity_id=" . $attrib['entity_id'] . " AND cat_id=" . $attrib['cat_id'];
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('max_sort')+1;
		
			if($attrib['column_info']['type']=='R' || $attrib['column_info']['type']== 'CH' || $attrib['column_info']['type'] =='LB' || $attrib['column_info']['type'] =='AB' || $attrib['column_info']['type'] =='VENDOR')
			{
				if ($attrib['history'])
				{
					$receipt['error'][] = array('msg'	=> lang('History not allowed for this datatype'));
				}

				$attrib['history'] = False;
			}

			$values= array(
				$attrib['entity_id'],
				$attrib['cat_id'],
				$attrib['id'],
				$attrib['column_name'],
				$attrib['input_text'],
				$attrib['statustext'],
				$attrib['search'],
				$attrib['list'],
				$attrib['history'],
				$attrib['disabled'],
				$attrib['helpmsg'],
				$attrib_sort,
				$attrib['column_info']['type'],
				$attrib['column_info']['precision'],
				$attrib['column_info']['scale'],
				$attrib['column_info']['default'],
				$attrib['column_info']['nullable']
				);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_entity_attribute (entity_id,cat_id,id,column_name, input_text, statustext,search,list,history,disabled,helpmsg,attrib_sort, datatype,precision_,scale,default_value,nullable) "
				. "VALUES ($values)",__LINE__,__FILE__);

			$receipt['id']= $attrib['id'];

			if($attrib['column_info']['type']=='email' && !$attrib['column_info']['precision'])
			{
				$attrib['column_info']['precision']=64;
			}

			if(!$attrib['column_info']['precision'])
			{
				$attrib['column_info']['precision']=$this->bocommon->translate_datatype_precision($attrib['column_info']['type']);
			}

			$attrib['column_info']['type']  = $this->bocommon->translate_datatype_insert($attrib['column_info']['type']);

			if(!$attrib['column_info']['default'])
			{
				unset($attrib['column_info']['default']);
			}

			$this->init_process();

			if($this->oProc->AddColumn('fm_entity_'.$attrib['entity_id'] . '_' . $attrib['cat_id'],$attrib['column_name'], $attrib['column_info']))
			{
				$receipt['message'][] = array('msg'	=> lang('Attribute has been saved')	);
				$this->db->transaction_commit();

			}
			else
			{
				$receipt['error'][] = array('msg'	=> lang('column could not be added')	);
				if($this->db->Transaction)
				{
					$this->db->transaction_abort();
				}
				else
				{
					$this->db->query("DELETE FROM fm_entity_attribute WHERE entity_id=" . $attrib['entity_id']. " AND cat_id=" . $attrib['id']. " AND id='" . $receipt['id'] . "'",__LINE__,__FILE__);
					unset($receipt['id']);

				}
			}

			return $receipt;
		}

		function init_process()
		{
			$this->oProc 				= CreateObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$this->oProc->m_odb			= $this->db;
			$this->oProc->m_odb->Halt_On_Error	= 'report';
		}

		function edit_attrib($attrib)
		{
			$choice_table = 'fm_entity_choice';

			$attrib['column_name'] = strtolower($this->db->db_addslashes($attrib['column_name']));
			$attrib['input_text'] = $this->db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->db->db_addslashes($attrib['statustext']);
			$attrib['helpmsg'] = $this->db->db_addslashes($attrib['helpmsg']);
			$attrib['column_info']['default'] = $this->db->db_addslashes($attrib['column_info']['default']);

			if($attrib['column_info']['type']=='R' || $attrib['column_info']['type']== 'CH' || $attrib['column_info']['type'] =='LB' || $attrib['column_info']['type'] =='AB' || $attrib['column_info']['type'] =='VENDOR')
			{
				if ($attrib['history'])
				{
					$receipt['error'][] = array('msg'	=> lang('History not allowed for this datatype'));
				}
				
				$attrib['history'] = False;
			}

			$this->db->query("SELECT column_name, datatype,precision_ FROM fm_entity_attribute WHERE entity_id=" . $attrib['entity_id']. " AND cat_id=" . $attrib['cat_id']. " AND id='" . $attrib['id']. "'",__LINE__,__FILE__);
			$this->db->next_record();
			$OldColumnName		= $this->db->f('column_name');
			$OldDataType		= $this->db->f('datatype');
			$OldPrecision		= $this->db->f('precision_');			
			
			$table_def = $this->get_table_def($attrib['entity_id'],$attrib['cat_id']);	

			$this->db->transaction_begin();

			$value_set=array(
				'input_text'	=> $attrib['input_text'],
				'statustext'	=> $attrib['statustext'],
				'search'		=> (isset($attrib['search'])?$attrib['search']:''),
				'list'			=> (isset($attrib['list'])?$attrib['list']:''),
				'history'		=> (isset($attrib['history'])?$attrib['history']:''),
				'nullable'		=> $attrib['column_info']['nullable'],
				'disabled'		=> (isset($attrib['disabled'])?$attrib['disabled']:''),
				'helpmsg'		=> $attrib['helpmsg'],
				);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE fm_entity_attribute set $value_set WHERE entity_id=" . $attrib['entity_id']. " AND cat_id=" . $attrib['cat_id']. " AND id=" . $attrib['id'],__LINE__,__FILE__);

			$this->init_process();
			
			$this->oProc->m_odb->transaction_begin();

			$this->oProc->m_aTables = $table_def;

			if($OldColumnName !=$attrib['column_name'])
			{
				$value_set=array('column_name'	=> $attrib['column_name']);

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("UPDATE fm_entity_attribute set $value_set WHERE entity_id=" . $attrib['entity_id']. " AND cat_id=" . $attrib['cat_id']. " AND id=" . $attrib['id'],__LINE__,__FILE__);

				$this->oProc->RenameColumn('fm_entity_'.$attrib['entity_id'] . '_' . $attrib['cat_id'], $OldColumnName, $attrib['column_name']);
			}

			if (($OldDataType != $attrib['column_info']['type']) || ($OldPrecision != $attrib['column_info']['precision']) )
			{
				if($attrib['column_info']['type']!='R' && $attrib['column_info']['type']!='CH' && $attrib['column_info']['type']!='LB')
				{
					$this->db->query("DELETE FROM $choice_table WHERE entity_id=" . $attrib['entity_id']. " AND cat_id=" . $attrib['cat_id']. " AND attrib_id=" . $attrib['id'],__LINE__,__FILE__);
				}

				if(!$attrib['column_info']['precision'])
				{
					if($precision = $this->bocommon->translate_datatype_precision($attrib['column_info']['type']))
					{
						$attrib['column_info']['precision']=$precision;
					}
				}

				if(!isset($attrib['column_info']['default']))
				{
					unset($attrib['column_info']['default']);
				}

				$value_set=array(
					'column_name'	=> $attrib['column_name'],
					'datatype'		=> $attrib['column_info']['type'],
					'precision_'	=> $attrib['column_info']['precision'],
					'scale'			=> $attrib['column_info']['scale'],
					'default_value'	=> $attrib['column_info']['default'],
					'nullable'		=> $attrib['column_info']['nullable']
					);

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("UPDATE fm_entity_attribute set $value_set WHERE entity_id=" . $attrib['entity_id']. " AND cat_id=" . $attrib['cat_id']. " AND id=" . $attrib['id'],__LINE__,__FILE__);

				$attrib['column_info']['type']  = $this->bocommon->translate_datatype_insert($attrib['column_info']['type']);
				$this->oProc->AlterColumn('fm_entity_'.$attrib['entity_id'] . '_' . $attrib['cat_id'],$attrib['column_name'],$attrib['column_info']);			
			}
			
			if(isset($attrib['new_choice']) && $attrib['new_choice'])
			{
				$choice_id = $this->bocommon->next_id($choice_table ,array('entity_id'=>$attrib['entity_id'],'cat_id'=>$attrib['cat_id'],'attrib_id'=>$attrib['id']));

				$values= array(
					$attrib['entity_id'],
					$attrib['cat_id'],
					$attrib['id'],
					$choice_id,
					$attrib['new_choice']
					);

				$values	= $this->bocommon->validate_db_insert($values);

				$this->db->query("INSERT INTO $choice_table (entity_id,cat_id,attrib_id,id,value) "
				. "VALUES ($values)",__LINE__,__FILE__);
			}

			if(isset($attrib['delete_choice']) && is_array($attrib['delete_choice']))
			{
				for ($i=0;$i<count($attrib['delete_choice']);$i++)
				{
					$this->db->query("DELETE FROM $choice_table WHERE entity_id=" . $attrib['entity_id']. " AND cat_id=" . $attrib['cat_id']. " AND attrib_id=" . $attrib['id']  ." AND id=" . $attrib['delete_choice'][$i],__LINE__,__FILE__);
				}
			}

			$this->db->transaction_commit();
			$this->oProc->m_odb->transaction_commit();
			$receipt['message'][] = array('msg'	=> lang('Attribute has been edited'));

			return $receipt;
		}

		function resort_attrib($data)
		{
			if(is_array($data))
			{
				$resort = (isset($data['resort'])?$data['resort']:'up');
				$entity_id = (isset($data['entity_id'])?$data['entity_id']:0);
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$id = (isset($data['id'])?$data['id']:'');
			}

			$sql = "SELECT attrib_sort FROM fm_entity_attribute where entity_id=$entity_id AND cat_id=$cat_id AND id=$id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('attrib_sort');
			$sql2 = "SELECT max(attrib_sort) as max_sort FROM fm_entity_attribute where entity_id=$entity_id AND cat_id=$cat_id";
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');

			switch($resort)
			{
				case 'up':
					if($attrib_sort>1)
					{
						$sql = "UPDATE fm_entity_attribute set attrib_sort=$attrib_sort WHERE entity_id=$entity_id AND cat_id=$cat_id AND attrib_sort =" . ($attrib_sort-1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE fm_entity_attribute set attrib_sort=" . ($attrib_sort-1) ." WHERE entity_id=$entity_id AND cat_id=$cat_id AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				case 'down':
					if($max_sort > $attrib_sort)
					{
						$sql = "UPDATE fm_entity_attribute set attrib_sort=$attrib_sort WHERE entity_id=$entity_id AND cat_id=$cat_id AND attrib_sort =" . ($attrib_sort+1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE fm_entity_attribute set attrib_sort=" . ($attrib_sort+1) ." WHERE entity_id=$entity_id AND cat_id=$cat_id AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				default:
					return;
					break;
			}
		}

		function read_custom_function($data)
		{
			if(isset($data) && is_array($data))
			{
				$start = isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$acl_location = (isset($data['acl_location'])?$data['acl_location']:'');
			}

			if(!$acl_location)
			{
				return;
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by custom_sort asc';
			}

			$table = 'fm_custom_function';

			$querymethod = '';
			if(isset($query) && $query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " AND name $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table WHERE acl_location='$acl_location' $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$custom_function = array();
			while ($this->db->next_record())
			{
				$custom_function[] = array
				(
					'id'	=> $this->db->f('id'),
					'file_name'	=> $this->db->f('file_name'),
					'sorting'	=> $this->db->f('custom_sort'),
					'descr'		=> $this->db->f('descr'),
					'active'	=> $this->db->f('active')
				);
			}
			return $custom_function;
		}


		function read_single_custom_function($acl_location,$id)
		{

			$sql = "SELECT * FROM fm_custom_function where acl_location='$acl_location' AND id=$id";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$custom_function['id']			= $this->db->f('id');
				$custom_function['descr']		= $this->db->f('descr');
				$custom_function['custom_function_file']= $this->db->f('file_name');
				$custom_function['active']		= $this->db->f('active');

				return $custom_function;
			}

		}

		function add_custom_function($custom_function)
		{
			if(!$custom_function['acl_location'] && $custom_function['entity_id'] && $custom_function['cat_id'])
			{
				$acl_location = '.entity.' . $custom_function['entity_id'] . '.' . $custom_function['cat_id'];
			}
			else
			{
				$acl_location = $custom_function['acl_location'];
			}

			if(!$acl_location)
			{
				return 	$receipt['error'][] = array('msg' => lang('acl_locastion is missing'));
			}

			$custom_function['descr'] = $this->db->db_addslashes($custom_function['descr']);


			$this->db->query("SELECT max(id) as maximum FROM fm_custom_function WHERE acl_location='$acl_location'",__LINE__,__FILE__);
			$this->db->next_record();
			$custom_function['id'] = $this->db->f('maximum')+1;

			$sql = "SELECT max(custom_sort) as max_sort FROM fm_custom_function where acl_location='$acl_location'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$custom_sort	= $this->db->f('max_sort')+1;

			$values= array(
				$acl_location,
				$custom_function['id'],
				$custom_function['custom_function_file'],
				$custom_function['descr'],
				$custom_function['active'],
				$custom_sort
				);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_custom_function (acl_location, id, file_name, descr, active, custom_sort) "
				. "VALUES ($values)",__LINE__,__FILE__);

			$receipt['id']= $custom_function['id'];

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit_custom_function($custom_function)
		{
			if(!$custom_function['acl_location'] && $custom_function['entity_id'] && $custom_function['cat_id'])
			{
				$acl_location = '.entity.' . $custom_function['entity_id'] . '.' . $custom_function['cat_id'];
			}
			else
			{
				$acl_location = $custom_function['acl_location'];
			}

			if(!$acl_location)
			{
				return 	$receipt['error'][] = array('msg' => lang('acl_locastion is missing'));
			}

			$custom_function['descr'] = $this->db->db_addslashes($custom_function['descr']);

			$this->db->transaction_begin();

				$value_set=array(
					'descr'		=> $custom_function['descr'],
					'file_name'	=> $custom_function['custom_function_file'],
					'active'	=> $custom_function['active']
					);

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("UPDATE fm_custom_function set $value_set WHERE acl_location='" . $acl_location . "' AND id=" . $custom_function['id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'	=> lang('Custom function has been edited'));

			return $receipt;
		}

		function resort_custom_function($data)
		{
			if(is_array($data))
			{
				$resort = (isset($data['resort'])?$data['resort']:'up');
				$entity_id = (isset($data['entity_id'])?$data['entity_id']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:'');
				$acl_location = (isset($data['acl_location'])?$data['acl_location']:'');
				$id = (isset($data['id'])?$data['id']:'');
			}

			if(!$acl_location && $entity_id && $cat_id)
			{
				$acl_location = '.entity.' . $entity_id . '.' . $cat_id;
			}
			else
			{
				$acl_location = $acl_location;
			}

			if(!$acl_location)
			{
				return 	$receipt['error'][] = array('msg' => lang('acl_locastion is missing'));
			}

			$sql = "SELECT custom_sort FROM fm_custom_function where acl_location='$acl_location' AND id=$id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$custom_sort	= $this->db->f('custom_sort');
			$sql2 = "SELECT max(custom_sort) as max_sort FROM fm_custom_function where acl_location='$acl_location'";
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');

			switch($resort)
			{
				case 'up':
					if($custom_sort>1)
					{
						$sql = "UPDATE fm_custom_function set custom_sort=$custom_sort WHERE acl_location='$acl_location' AND custom_sort =" . ($custom_sort-1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE fm_custom_function set custom_sort=" . ($custom_sort-1) ." WHERE acl_location='$acl_location' AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				case 'down':
					if($max_sort > $custom_sort)
					{
						$sql = "UPDATE fm_custom_function set custom_sort=$custom_sort WHERE acl_location='$acl_location' AND custom_sort =" . ($custom_sort+1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE fm_custom_function set custom_sort=" . ($custom_sort+1) ." WHERE acl_location='$acl_location' AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				default:
					return;
					break;
			}
		}

		function delete_custom_function($acl_location,$custom_function_id)
		{
			$sql = "SELECT custom_sort FROM fm_custom_function where acl_location='$acl_location' AND id=$custom_function_id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$custom_sort	= $this->db->f('custom_sort');
			$sql2 = "SELECT max(custom_sort) as max_sort FROM fm_custom_function where acl_location='$acl_location'";
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');
			if($max_sort>$custom_sort)
			{
				$sql = "UPDATE fm_custom_function set custom_sort=custom_sort-1 WHERE acl_location='$acl_location' AND custom_sort > $custom_sort";
				$this->db->query($sql,__LINE__,__FILE__);
			}
			$this->db->query("DELETE FROM fm_custom_function WHERE acl_location='$acl_location' AND id=$custom_function_id",__LINE__,__FILE__);
		}
	}
?>
