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
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soadmin_location
	{
		function property_soadmin_location()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon','property');
			$this->db           	= $this->bocommon->new_db();

			$this->join		= $this->bocommon->join;
			$this->like		= $this->bocommon->like;
		}

		function reset_fm_cache()
		{
			$this->db->query("DELETE FROM fm_cache ",__LINE__,__FILE__);
		}

		function read($data)
		{
			$start	= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query	= isset($data['query'])?$data['query']:'';
			$sort	= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order	= isset($data['order'])?$data['order']:'';

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by id asc';
			}

			$table = 'fm_location_type';

			$querymethod = '';
			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " where name $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();
			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$standard[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name'),
					'descr'	=> $this->db->f('descr')
				);
			}
			return $standard;
		}

		function read_config($data=0)
		{
			if (isset($data['start']))
			{
				$start=$data['start'];
			}
			else
			{
				$start=0;
			}

		//	if(is_array($data))
			{
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by column_name asc';
			}

			$querymethod = '';
			if(isset($query))
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " where name $this->like '%$query%' or column_name $this->like '%$query%'";
			}

			$sql = "SELECT fm_location_config.* ,fm_location_type.name as name FROM fm_location_config  $this->join fm_location_type on fm_location_config.location_type=fm_location_type.id $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$config[] = array
				(
					'column_name'		=> $this->db->f('column_name'),
					'input_text'		=> $this->db->f('input_text'),
					'f_key'				=> $this->db->f('f_key'),
					'lookup_form'		=> $this->db->f('lookup_form'),
					'ref_to_category'	=> $this->db->f('ref_to_category'),
					'query_value'		=> $this->db->f('query_value'),
					'reference_table'	=> $this->db->f('reference_table'),
					'reference_id'		=> $this->db->f('reference_id'),
					'location_name'		=> $this->db->f('name'),
					'location_type'		=> $this->db->f('location_type')
				);
			}
			return $config;
		}

		function read_config_single($column_name='')
		{
			$this->db->query("SELECT location_type FROM fm_location_config where column_name='$column_name'",__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('location_type');
		}


		function read_single($id)
		{

			$table = 'fm_location_type';

			$sql = "SELECT * FROM $table  where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$standard['id']			= $this->db->f('id');
				$standard['name']		= $this->db->f('name');
				$standard['descr']		= $this->db->f('descr');
				$standard['list_info']	= unserialize($this->db->f('list_info'));
				$standard['list_address']	= $this->db->f('list_address');

				return $standard;
			}
		}

		function add($standard)
		{

			$standard['name'] = $this->db->db_addslashes($standard['name']);
			$standard['descr'] = $this->db->db_addslashes($standard['descr']);

			$standard['id'] = $this->bocommon->next_id('fm_location_type');

			$receipt['id']= $standard['id'];

			$this->init_process();

			$j=1;
			$default_attrib['id'][]= $j;
			$default_attrib['column_name'][]= 'location_code';
			$default_attrib['type'][]='V';
			$default_attrib['precision'][] =4*$standard['id'];
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['attrib_sort'][] ='';
			$default_attrib['custom'][] ='';

			$j++;
			$default_attrib['id'][]= $j;
			$default_attrib['column_name'][]= 'loc' . $standard['id'] . '_name';
			$default_attrib['type'][]='V';
			$default_attrib['precision'][] =50;
			$default_attrib['nullable'][] ='True';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['attrib_sort'][] ='';
			$default_attrib['custom'][] ='';

			$j++;
			$default_attrib['id'][]= $j;
			$default_attrib['column_name'][]= 'entry_date';
			$default_attrib['type'][]='I';
			$default_attrib['precision'][] =4;
			$default_attrib['nullable'][] ='True';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['attrib_sort'][] ='';
			$default_attrib['custom'][] ='';

			$j++;
			$default_attrib['id'][]= $j;
			$default_attrib['column_name'][]= 'category';
			$default_attrib['type'][]='I';
			$default_attrib['precision'][] =4;
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['attrib_sort'][] ='';
			$default_attrib['custom'][] ='';

			$j++;
			$default_attrib['id'][]= $j;
			$default_attrib['column_name'][]= 'user_id';
			$default_attrib['type'][]='I';
			$default_attrib['precision'][] =4;
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['attrib_sort'][] ='';
			$default_attrib['custom'][] ='';

			$j++;
			$status_id = $j;
			$default_attrib['id'][]= $j;
			$default_attrib['column_name'][]= 'status';
			$default_attrib['type'][]='LB';
			$default_attrib['precision'][] = False;
			$default_attrib['nullable'][] ='True';
			$default_attrib['input_text'][] ='Status';
			$default_attrib['statustext'][] ='Status';
			$default_attrib['attrib_sort'][] =1;
			$default_attrib['custom'][] =1;

			$j++;
			$default_attrib['id'][]= $j;
			$default_attrib['column_name'][]= 'remark';
			$default_attrib['type'][]='T';
			$default_attrib['precision'][] = False;
			$default_attrib['nullable'][] ='False';
			$default_attrib['input_text'][] ='Remark';
			$default_attrib['statustext'][] ='Remark';
			$default_attrib['attrib_sort'][] =2;
			$default_attrib['custom'][] =1;

			$j++;
			$default_attrib['id'][]= $j;
			$default_attrib['column_name'][]= 'change_type';
			$default_attrib['type'][]='I';
			$default_attrib['precision'][] =4;
			$default_attrib['nullable'][] ='True';
			$default_attrib['input_text'][] ='dummy';
			$default_attrib['statustext'][] ='dummy';
			$default_attrib['attrib_sort'][] ='';
			$default_attrib['custom'][] ='';

			$fd=array();
			$fd['location_code'] = array('type' => 'varchar', 'precision' => 25, 'nullable' => False);

			for ($i=1; $i<$standard['id']+1; $i++)
			{
				if($i==1)
				{
					$fd['loc' . $i] = array('type' => 'varchar', 'precision' => 6, 'nullable' => False);
				}
				else
				{
					$fd['loc' . $i] = array('type' => 'varchar', 'precision' => 4, 'nullable' => False);
				}
				
				$pk[$i-1]= 'loc' . $i;

				$default_attrib['id'][]= $i+$j;
				$default_attrib['column_name'][]= 'loc' . $i;
				$default_attrib['type'][]='V';
				$default_attrib['precision'][] =4;
				$default_attrib['nullable'][] ='False';
				$default_attrib['input_text'][] ='dummy';
				$default_attrib['statustext'][] ='dummy';
				$default_attrib['attrib_sort'][] ='';
				$default_attrib['custom'][] ='';
			}

			$fk_table='fm_location'. ($standard['id']-1);

			for ($i=1; $i<$standard['id']; $i++)
			{
				$fk[$fk_table]['loc' . $i]	= 'loc' . $i;
			}

			if($standard['id']==1)
			{
				$fd['part_of_town_id'] = array('type' => 'int', 'precision' => 2, 'nullable' => True);
			}

			$fd['loc' .$standard['id'] . '_name'] = array('type' => 'varchar', 'precision' => 25, 'nullable' => True);
			$fd['entry_date'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);
			$fd['category'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);
			$fd['user_id'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);
			$fd['remark'] = array('type' => 'text', 'nullable' => True);
			$fd['status'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);
			$fd['change_type'] = array('type' => 'int', 'precision' => 4, 'nullable' => True);

			$ix = array('location_code');
			$uc = array();

			$fd_history = $fd;
			$fd_history['exp_date'] = array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp');

			$add_columns_in_tables=array('fm_project','fm_tts_tickets','fm_request','fm_document','fm_investment');

			$this->oProc->m_odb->transaction_begin();
			$this->db->transaction_begin();

			if($this->oProc->CreateTable('fm_location'. $standard['id'],array('fd' => $fd,'pk' => $pk,'fk' => $fk,'ix' => $ix,'uc' => $uc))
				&& $this->oProc->CreateTable('fm_location'. $standard['id'] . '_history',array('fd' => $fd_history)))
			{

				$this->oProc->CreateTable('fm_location'. $standard['id'] . '_category', array(
				'fd' => array(
					'id' => array('type' => 'int','precision' => '4','nullable' => False),
					'descr' => array('type' => 'varchar','precision' => '50','nullable' => True)
				),
				'pk' => array('id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()));

				for ($i=0;$i<count($add_columns_in_tables);$i++)
				{
					$this->oProc->AddColumn($add_columns_in_tables[$i],'loc'. $standard['id'], array('type' => 'varchar', 'precision' => 4, 'nullable' => True));
				}

				$values_insert= array(
					$standard['id'],
					$standard['name'],
					$standard['descr'],
				    $this->db->db_addslashes(implode(',',$pk)),
				    $this->db->db_addslashes(implode(',',$ix)),
				    $this->db->db_addslashes(implode(',',$uc)),
					);

				$values_insert	= $this->bocommon->validate_db_insert($values_insert);

				$this->db->query("INSERT INTO fm_location_type (id,name, descr,pk,ix,uc) "
					. "VALUES ($values_insert)",__LINE__,__FILE__);

				for ($i=0;$i<count($default_attrib['id']);$i++)
				{
					$values_insert= array(
						'property',
						'.location.'.$standard['id'],
						$default_attrib['id'][$i],
						$default_attrib['column_name'][$i],
						$default_attrib['type'][$i],
						$default_attrib['precision'][$i],
						$default_attrib['input_text'][$i],
						$default_attrib['statustext'][$i],
						$default_attrib['attrib_sort'][$i],
						$default_attrib['custom'][$i],
						$default_attrib['nullable'][$i]
						);

					$values_insert	= $this->bocommon->validate_db_insert($values_insert);

					$this->db->query("INSERT INTO phpgw_cust_attribute (appname,location,id,column_name,datatype,precision_,input_text,statustext,attrib_sort,custom,nullable) "
						. "VALUES ($values_insert)",__LINE__,__FILE__);
				}

				$type_id=$standard['id'];

				$this->db->query("INSERT INTO phpgw_cust_choice (appname,location,attrib_id,id,value) "
					. "VALUES ('property','.location.$type_id',$status_id,1,'ok')",__LINE__,__FILE__);
				$this->db->query("INSERT INTO phpgw_cust_choice (appname,location,attrib_id,id,value) "
					. "VALUES ('property','.location.$type_id',$status_id,2,'Not Ok')",__LINE__,__FILE__);
				$this->db->query("INSERT INTO fm_location{$type_id}_category (id,descr) "
					. "VALUES (1,'Category 1')",__LINE__,__FILE__);
				$this->db->query("INSERT INTO fm_location{$type_id}_category (id,descr) "
					. "VALUES (99,'Not active')",__LINE__,__FILE__);

				$this->db->query("INSERT INTO phpgw_acl_location (appname, id, descr)"
		 			. " VALUES ('" . 'property' ."','" . '.location.' . $standard['id'] ."', '" . $standard['name'] . "')");
		 			
	//			$GLOBALS['phpgw']->acl->add_location('.location.' . $standard['id'], $standard['name'], 'property', $allow_grant = false, $custom_tbl = '');

				$receipt['message'][] = array('msg' => lang('table %1 has been saved','fm_location'. $receipt['id']));
				$this->db->transaction_commit();
				$this->oProc->m_odb->transaction_commit();
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('table could not be added'));
				if($this->db->Transaction)
				{
					$this->db->transaction_abort();
				}
				else
				{
					$this->db->query("DELETE FROM fm_location_type WHERE id='" . $standard['id'] . "'",__LINE__,__FILE__);
					unset($receipt['id']);

				}
			}

			return $receipt;
		}

		function edit($values)
		{

			$table = 'fm_location_type';

			$value_set=array(
				'name'			=> $this->db->db_addslashes($values['name']),
				'descr'			=> $this->db->db_addslashes($values['descr']),
				'list_info'		=> (isset($values['list_info'])?serialize($values['list_info']):''),
				'list_address'	=> (isset($values['list_address'])?$values['list_address']:''),
				);
			
			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE $table SET $value_set WHERE id='" . $values['id']. "'",__LINE__,__FILE__);

			$receipt['id'] = $values['id'];
			$receipt['message'][] = array('msg'=> lang('Standard has been edited'));

			return $receipt;
		}

		function delete($id)
		{
			$this->init_process();
			$this->oProc->m_odb->transaction_begin();
			$this->db->transaction_begin();

			$table 		= 'fm_location_type';
			$this->db->query("SELECT max(id) as id FROM $table",__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('id') > $id)
			{
				$this->db->transaction_abort();
				$this->oProc->m_odb->transaction_abort();
				$receipt['error'][] = array('msg' => lang('please delete from the bottom'));
				$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
				
				return;
			}
				
			$this->oProc->DropTable('fm_location' . $id);
			$this->oProc->DropTable('fm_location' . $id . '_category');
			$this->oProc->DropTable('fm_location' . $id . '_history');

			$attrib_table 	= 'phpgw_cust_attribute';
			$choice_table 	= 'phpgw_cust_choice';
			
			$this->db->query("DELETE FROM $attrib_table WHERE appname = 'property' AND location = '.location. " . $id . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM $choice_table WHERE appname = 'property' AND location = '.location. " . $id . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM $table WHERE id=" . (int)$id,__LINE__,__FILE__);

			$this->db->transaction_commit();
			$this->oProc->m_odb->transaction_commit();
		}



		function init_process()
		{
			$this->oProc 				= CreateObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$this->oProc->m_odb			= $this->db;
			$this->oProc->m_odb->Halt_On_Error	= 'yes';
		}

		function save_config($values='',$column_name='')
		{
			$this->db->query("SELECT * FROM fm_location_config  WHERE column_name='$column_name' ",__LINE__,__FILE__);
			$this->db->next_record();

			$column_info['type']		= $this->db->f('datatype');
			$column_info['precision']	= $this->db->f('precision_');
			$column_info['scale']		= $this->db->f('scale');
			$column_info['default']		= $this->db->f('default_value');
			$column_info['nullable']	= $this->db->f('nullable');
			$location_type				= $this->db->f('location_type');

			$custom 	= createObject('phpgwapi.custom_fields');
			$table_def = $custom->get_table_def('fm_location'.$location_type);
			$history_table_def = $custom->get_table_def('fm_location' . $location_type . '_history');
//_debug_array($table_def);
//_debug_array($history_table_def);
			if(!($location_type==$values[$column_name]))
			{
				$id = $this->bocommon->next_id('phpgw_cust_attribute',array('appname' => 'property', 'location' => '.location.' .$values[$column_name]));

				$this->init_process();

				$this->oProc->m_odb->transaction_begin();
				$this->db->transaction_begin();
				if($this->oProc->AddColumn('fm_location'.$values[$column_name],$column_name, $column_info) &&
					$this->oProc->AddColumn('fm_location'.$values[$column_name] . '_history',$column_name, $column_info))
				{
					if($column_name=='street_id')
					{
						$this->oProc->AddColumn('fm_location'.$values[$column_name],'street_number', array('type'=>'varchar','precision'=>10));
						$this->oProc->AddColumn('fm_location'.$values[$column_name] . '_history','street_number', array('type'=>'varchar','precision'=>10));
						$this->oProc->DropColumn('fm_location' .$location_type ,$table_def['fm_location'.$location_type],'street_number');
						$this->oProc->DropColumn('fm_location' .$location_type . '_history',$history_table_def['fm_location'.$location_type] . '_history','street_number');
					}

					$this->oProc->DropColumn('fm_location' .$location_type ,$table_def['fm_location'.$location_type], $column_name);
					$this->oProc->DropColumn('fm_location' .$location_type . '_history',$history_table_def['fm_location'.$location_type . '_history'], $column_name);

					$this->db->query("UPDATE fm_location_config set
						location_type = '". $values[$column_name]	. "' WHERE column_name='" . $column_name . "'",__LINE__,__FILE__);

					$values= array(
						'property',
						'.location.' . $values[$column_name],
						$id,
						$column_name,
						$column_name,
						$column_name,
						$column_info['type'],
						$column_info['precision'],
						$column_info['scale'],
						$column_info['default'],
						$column_info['nullable'],
						''
					);

					$values	= $this->bocommon->validate_db_insert($values);

					$this->db->query("INSERT INTO phpgw_cust_attribute (appname,location,id,column_name, input_text, statustext,datatype,precision_,scale,default_value,nullable,custom) "
						. "VALUES ($values)",__LINE__,__FILE__);

					$this->db->query("DELETE from phpgw_cust_attribute WHERE appname = 'property' AND location = '.location.". $location_type . "' AND column_name = '" . $column_name . "'",__LINE__,__FILE__);

					$ok = true;
				}
				
				if(isset($ok) && $ok)
				{
					$this->db->transaction_commit();
					$this->oProc->m_odb->transaction_commit();				

					$receipt['message'][] = array('msg'	=> lang('column %1 has been moved',$column_name));
				}
				else
				{
					$receipt['message'][] = array('msg'	=> lang('column %1 could not be moved',$column_name));

				}
			}

			return $receipt;
		}


		function select_location_type()
		{
			$this->db->query("SELECT * FROM fm_location_type ORDER BY id ");

			while ($this->db->next_record())
			{
				$location_type[]	= array(
					'id' 			=> $this->db->f('id'),
					'name'			=> stripslashes($this->db->f('name')),
					'descr'			=> stripslashes($this->db->f('descr')),
					'list_info'		=> unserialize($this->db->f('list_info')),
					'list_address'	=> $this->db->f('list_address')
					);
			}
//_debug_array($location_type);

			return $location_type;
		}
	}
?>
