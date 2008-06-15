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
 	* @version $Id: class.soadmin_entity.inc.php 18358 2007-11-27 04:43:37Z skwashd $
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
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
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
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

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
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

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
				'property',
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
				'property',
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

			if(($this->oProc->CreateTable($table,array('fd' => $fd,'pk' => $pk,'fk' => $fk,'ix' => array('location_code'),'uc' => array()))))
			{

				$values_insert= array(
					'property',
					".entity.{$values['entity_id']}.{$values['id']}",
					1,
					'status',
					'Status',
					'Status',
					'LB',
					1,
					'True'
					);

				$values_insert	= $this->bocommon->validate_db_insert($values_insert);

				$this->db->query("INSERT INTO phpgw_cust_attribute (appname,location,id,column_name,input_text,statustext,datatype,attrib_sort,nullable) "
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

				$this->db->query("UPDATE phpgw_acl_location set $value_set_acl WHERE appname = '" . 'property' . "' AND id='.entity." . $entity['id']. "'",__LINE__,__FILE__);

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

				$this->db->query("UPDATE phpgw_acl_location set $value_set_acl WHERE appname = '" . 'property' . "' AND id='.entity." . $entity['entity_id']. "." . $entity['id']. "'",__LINE__,__FILE__);


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
			$this->db->query("DELETE FROM phpgw_cust_attribute WHERE appname='property' AND location {$this->like} '.entity.{$id}%'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_acl_location WHERE appname = 'property' AND id {$this->like} '.entity.{$id}%'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_acl WHERE acl_appname = 'property' AND  acl_location {$this->like} '.entity.{$id}%'",__LINE__,__FILE__);
			if (isset($category_list) AND is_array($category_list))
			{
				$this->init_process();

				foreach($category_list as $entry)
				{
					$this->oProc->DropTable('fm_entity_' . $id . '_' . $entry['id']);
				}
			}

		}

		function delete_category($entity_id, $id)
		{
			$this->init_process();
			$this->oProc->DropTable('fm_entity_' . $entity_id . '_' . $id);
			$this->db->query("DELETE FROM fm_entity_category WHERE entity_id= $entity_id AND id= $id",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_cust_attribute WHERE appname='property' AND location='.entity." . $entity_id . "." . $id ."'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_acl_location WHERE appname = '" . 'property' . "' AND id='.entity." . $entity_id . "." . $id ."'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM phpgw_acl WHERE acl_appname = '" . 'property' . "' AND  acl_location='.entity." . $entity_id . "." . $id ."'",__LINE__,__FILE__);
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
			
			$location =	".entity.{$values['entity_id']}.{$values['id']}";
			for ($i=0; $i<count($metadata); $i++)
			{
				$sql = "SELECT * FROM phpgw_cust_attribute WHERE appname = 'property' AND location = '{$location}' AND column_name = '" . $metadata[$i]['name'] . "'";

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
			$this->db->query("DELETE FROM fm_entity_history WHERE history_appname = 'entity_" . $entity_id  . '_' . $cat_id . "' AND history_entity_attrib_id = $attrib_id",__LINE__,__FILE__);
		}

		function init_process()
		{
			$this->oProc 				= CreateObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$this->oProc->m_odb			= & $this->db;
			$this->oProc->m_odb->Halt_On_Error	= 'yes';
		}
	}
?>
