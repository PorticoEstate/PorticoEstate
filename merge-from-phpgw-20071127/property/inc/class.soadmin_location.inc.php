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
 	* @version $Id: class.soadmin_location.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soadmin_location
	{
		var $grants;
		var $currentapp;

		function property_soadmin_location($currentapp='')
		{
			if($currentapp)
			{
				$this->currentapp	= $currentapp;
			}
			else
			{
				$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];			
			}

			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon',$this->currentapp);
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();

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
			$query	= (isset($data['query'])?$data['query']:'');
			$sort	= (isset($data['sort'])?$data['sort']:'DESC');
			$order	= (isset($data['order'])?$data['order']:'');

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
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " where name $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();
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
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " where name $this->like '%$query%' or column_name $this->like '%$query%'";
			}

			$sql = "SELECT fm_location_config.* ,fm_location_type.name as name FROM fm_location_config  $this->join fm_location_type on fm_location_config.location_type=fm_location_type.id $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

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
				$fk['loc' . $i]	= $fk_table . '.loc' . $i;
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
						$standard['id'],
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

					$this->db->query("INSERT INTO fm_location_attrib (type_id,id,column_name,datatype,precision_,input_text,statustext,attrib_sort,custom,nullable) "
						. "VALUES ($values_insert)",__LINE__,__FILE__);
				}

				$type_id=$standard['id'];

				$this->db->query("INSERT INTO fm_location_choice (type_id,attrib_id,id,value) "
					. "VALUES ($type_id,$status_id,1,'ok')",__LINE__,__FILE__);
				$this->db->query("INSERT INTO fm_location_choice (type_id,attrib_id,id,value) "
					. "VALUES ($type_id,$status_id,2,'Not Ok')",__LINE__,__FILE__);
				$this->db->query("INSERT INTO fm_location{$type_id}_category (id,descr) "
					. "VALUES (1,'Category 1')",__LINE__,__FILE__);
				$this->db->query("INSERT INTO fm_location{$type_id}_category (id,descr) "
					. "VALUES (99,'Not active')",__LINE__,__FILE__);

				$this->db->query("INSERT INTO phpgw_acl_location (appname, id, descr)"
		 			. " VALUES ('" . $this->currentapp ."','" . '.location.' . $standard['id'] ."', '" . $standard['name'] . "')");
		 			
	//			$GLOBALS['phpgw']->acl->add_location('.location.' . $standard['id'], $standard['name'], $this->currentapp, $allow_grant = false, $custom_tbl = '');

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

		function delete($type_id,$id,$attrib)
		{
			$this->init_process();
			$this->oProc->m_odb->transaction_begin();
			$this->db->transaction_begin();

			if($attrib)
			{
				$table_def = $this->get_table_def($type_id);
				$table = 'fm_location_attrib';
				$this->db->query("SELECT column_name,type_id FROM fm_location_attrib WHERE type_id = '$type_id' AND id='" . $id . "'",__LINE__,__FILE__);
				$this->db->next_record();
				$ColumnName = $this->db->f('column_name');

				$this->oProc->DropColumn('fm_location' .$type_id ,$table_def['fm_location'.$type_id], $ColumnName);
				$this->oProc->DropColumn('fm_location' .$type_id . '_history',$table_def['fm_location'.$type_id . '_history'], $ColumnName);
				$this->db->query("DELETE FROM $table WHERE type_id = '$type_id' AND id='" . $id . "'",__LINE__,__FILE__);
			}
			else
			{
				$table 		= 'fm_location_type';
				$this->db->query("SELECT max(id) as id FROM $table",__LINE__,__FILE__);
				$this->db->next_record();
				if($this->db->f('id') > $id)
				{
					$this->db->transaction_abort();
					$this->oProc->m_odb->transaction_abort();
					$receipt['error'][] = array('msg' => lang('please delete from the bottom'));
					$GLOBALS['phpgw']->session->appsession('receipt',$this->currentapp,$receipt);
					
					return;
				}
				
				$this->oProc->DropTable('fm_location' . $id);
				$this->oProc->DropTable('fm_location' . $id . '_category');
				$this->oProc->DropTable('fm_location' . $id . '_history');

				$attrib_table 	= 'fm_location_attrib';
				$this->db->query("DELETE FROM $attrib_table WHERE type_id='" . $id . "'",__LINE__,__FILE__);
				$this->db->query("DELETE FROM fm_location_choice WHERE type_id='" . $id . "'",__LINE__,__FILE__);
				$this->db->query("DELETE FROM $table WHERE id='" . $id . "'",__LINE__,__FILE__);
			}

			$this->db->transaction_commit();
			$this->oProc->m_odb->transaction_commit();
		}

		function read_attrib($data)
		{

//_debug_array($data);
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
				$type_id = (isset($data['type_id'])?$data['type_id']:0);
				$lookup_type = (isset($data['lookup_type'])?$data['lookup_type']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by fm_location_attrib.attrib_sort asc';
			}

			$table 		= 'fm_location_attrib';
			$type_table = 'fm_location_type';

			$filtermethod = '';
			if ($lookup_type)
			{
				$filtermethod = " OR (type_id < $lookup_type AND lookup_form=1) ";

			}

			$querymethod = '';
			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " and ($table.name $this->like '%$query%' or $table.descr $this->like '%$query%')";
			}

			$sql = "SELECT $table.id,$table.type_id,$table.list,$table.attrib_sort,$table.location_form,lookup_form,$table.column_name,$table.size ,statustext,$table.input_text,"
				. " $table.datatype ,$type_table.name as type FROM $type_table $this->join $table on $table.type_id = $type_table.id "
				. " WHERE $table.type_id= '$type_id' AND custom = 1 $filtermethod $querymethod";

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
					'id'			=> $this->db->f('id'),
					'location_type'	=> $this->db->f('type_id'),
					'attrib_sort'	=> $this->db->f('attrib_sort'),
					'list'			=> $this->db->f('list'),
					'lookup_form'	=> $this->db->f('lookup_form'),
					'location_form'	=> $this->db->f('location_form'),
					'column_name'	=> $this->db->f('column_name'),
					'name'			=> $this->db->f('input_text'),
					'size'			=> $this->db->f('size'),
					'statustext'	=> $this->db->f('statustext'),
					'input_text'	=> $this->db->f('input_text'),
					'type_name'		=> $this->db->f('type'),
					'datatype'		=> $this->db->f('datatype')
				);
			}
			if (isset($attrib))
			{
				return $attrib;
			}
		}

		function read_single_attrib($type_id,$id)
		{

			$table 		= 'fm_location_attrib';
			$type_table = 'fm_location_type';

			$sql = "SELECT $table.* ,$type_table.name as type_name FROM $type_table $this->join $table on $table.type_id = $type_id where $table.id= '$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$attrib['id']				= $this->db->f('id');
				$attrib['column_name']			= $this->db->f('column_name');
				$attrib['input_text']			= $this->db->f('input_text');
				$attrib['statustext']			= $this->db->f('statustext');
				$attrib['column_info']['precision']	= $this->db->f('precision_');
				$attrib['column_info']['scale']		= $this->db->f('scale');
				$attrib['column_info']['default']	= $this->db->f('default_value');
				$attrib['column_info']['nullable']	= $this->db->f('nullable');
				$attrib['column_info']['type']		= $this->db->f('datatype');
				$attrib['type_id']			= $type_id;
				$attrib['type_name']			= $this->db->f('type_name');
				$attrib['lookup_form']			= $this->db->f('lookup_form');
				$attrib['list']				= $this->db->f('list');
				if($this->db->f('datatype')=='R' || $this->db->f('datatype')=='CH' || $this->db->f('datatype')=='LB')
				{
					$attrib['choice'] = $this->read_attrib_choice($type_id,$id);
				}

				return $attrib;
			}
		}

		function read_attrib_choice($type_id,$attrib_id)
		{
			$choice_table = 'fm_location_choice';
			$sql = "SELECT * FROM $choice_table WHERE type_id=$type_id AND attrib_id=$attrib_id";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$choice[] = array
				(
					'id'	=> $this->db->f('id'),
					'value'	=> $this->db->f('value')
				);
			}

			if(isset($choice))
			{
				return $choice;
			}
		}

		function add_attrib($attrib)
		{
			$attrib['column_name'] = strtolower($this->db->db_addslashes($attrib['column_name']));
			$attrib['input_text'] = $this->db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->db->db_addslashes($attrib['statustext']);
			$attrib['default'] = $this->db->db_addslashes($attrib['default']);
			$attrib['id'] = $this->bocommon->next_id('fm_location_attrib',array('type_id'=>$attrib['type_id']));

			$sql = "SELECT * FROM fm_location_attrib WHERE type_id= '{$attrib['type_id']}' AND column_name = '{$attrib['column_name']}'";
			$this->db->query($sql,__LINE__,__FILE__);
			if ( $this->db->next_record() )
			{
				$receipt['id'] = '';
				$receipt['error'] = array();
				$receipt['error'][] = array('msg' => lang('field already exists, please choose another name'));
				$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				return $receipt; //no point continuing
			}

			$sql = "SELECT max(attrib_sort) as max_sort FROM fm_location_attrib where type_id=" . $attrib['type_id'];
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('max_sort')+1;

			if($precision = $this->bocommon->translate_datatype_precision($attrib['column_info']['type']))
			{
				$attrib['column_info']['precision']=$precision;
			}

			$values= array(
				$attrib['id'],
				$attrib['column_name'],
				$attrib['input_text'],
				$attrib['statustext'],
				$attrib['type_id'],
				$attrib['lookup_form'],
				$attrib['list'],
				$attrib_sort,
				$attrib['column_info']['type'],
				$attrib['column_info']['precision'],
				$attrib['column_info']['scale'],
				$attrib['column_info']['default'],
				$attrib['column_info']['nullable'],
				1
				);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_location_attrib (id,column_name, input_text, statustext, type_id,lookup_form,list,attrib_sort,datatype,precision_,scale,default_value,nullable,custom) "
				. "VALUES ($values)",__LINE__,__FILE__);

			$receipt['id']= $attrib['id'];

			$attrib['column_info']['type']  = $this->bocommon->translate_datatype_insert($attrib['column_info']['type']);

			if(!$attrib['column_info']['default'])
			{
				unset($attrib['column_info']['default']);
			}

			$this->init_process();

			if($this->oProc->AddColumn('fm_location'.$attrib['type_id'],$attrib['column_name'], $attrib['column_info'])
				&& $this->oProc->AddColumn('fm_location'.$attrib['type_id'] .'_history',$attrib['column_name'], $attrib['column_info']))
			{
				$receipt['message'][] = array('msg' => lang('Attribute has been saved'));
				$this->db->transaction_commit();

			}
			else
			{
				$receipt['error'][] = array('msg' => lang('column could not be added'));
				if($this->db->Transaction)
				{
					$this->db->transaction_abort();
				}
				else
				{
					$this->db->query("DELETE FROM fm_location_attrib WHERE id=" . $receipt['id'] . " AND type_id=" . $attrib['type_id'],__LINE__,__FILE__);
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


		function save_config($values='',$column_name='')
		{
			$this->db->query("SELECT * FROM fm_location_config  WHERE column_name='$column_name' ",__LINE__,__FILE__);
			$this->db->next_record();

			$column_info['type']		= $this->db->f('datatype');
			$column_info['precision']	= $this->db->f('precision_');
			$column_info['scale']		= $this->db->f('scale');
			$column_info['default']		= $this->db->f('default_value');
			$column_info['nullable']	= $this->db->f('nullable');
			$location_type			= $this->db->f('location_type');

			$table_def = $this->get_table_def($location_type);
			$history_table_def = $this->get_table_history_def($table_def,$location_type);
//_debug_array($table_def);
//_debug_array($history_table_def);
//die();
			if(!($location_type==$values[$column_name]))
			{
				$id = $this->bocommon->next_id('fm_location_attrib',array('type_id'=>$values[$column_name]));
				
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
						$id,
						$column_name,
						$column_name,
						$column_name,
						$values[$column_name],
						$column_info['type'],
						$column_info['precision'],
						$column_info['scale'],
						$column_info['default'],
						$column_info['nullable'],
					);

					$values	= $this->bocommon->validate_db_insert($values);

					$this->db->query("INSERT INTO fm_location_attrib (id,column_name, input_text, statustext, type_id,datatype,precision_,scale,default_value,nullable) "
						. "VALUES ($values)",__LINE__,__FILE__);

					$this->db->query("DELETE from fm_location_attrib WHERE column_name = '$column_name' AND type_id = '$location_type'",__LINE__,__FILE__);

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


		function edit_attrib($attrib)
		{
			$choice_table ='fm_location_choice';

			$attrib['column_name'] = strtolower($this->db->db_addslashes($attrib['column_name']));
			$attrib['input_text'] = $this->db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->db->db_addslashes($attrib['statustext']);
			$attrib['default'] = $this->db->db_addslashes($attrib['default']);

			$this->db->query("SELECT * FROM fm_location_attrib WHERE type_id = " . $attrib['type_id'] ." AND id=" . $attrib['id'],__LINE__,__FILE__);
			$this->db->next_record();
			$OldColumnName		= $this->db->f('column_name');
			$OldColumnType		= $this->db->f('datatype');
			$OldColumnPrecision	= $this->db->f('precision_');

			$table_def = $this->get_table_def($attrib['type_id']);
			$history_table_def = $this->get_table_history_def($table_def,$attrib['type_id']);
	
			if($this->receipt['error'])
			{
				return $this->receipt;
			}

			$this->db->transaction_begin();

			$value_set=array(
				'input_text'	=> $attrib['input_text'],
				'statustext'	=> $attrib['statustext'],
				'list'		=> $attrib['list'],
				'lookup_form'	=> $attrib['lookup_form'],
				);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE fm_location_attrib set $value_set WHERE type_id = " . $attrib['type_id'] ." AND id=" . $attrib['id'],__LINE__,__FILE__);

			$this->init_process();

			$this->oProc->m_odb->transaction_begin();

			if($OldColumnName !=$attrib['column_name'])
			{
				$value_set=array('column_name'		=> $attrib['column_name']);

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("UPDATE fm_location_attrib set $value_set WHERE type_id = " . $attrib['type_id'] ." AND id=" . $attrib['id'],__LINE__,__FILE__);

				$this->oProc->m_aTables = $table_def;
				$this->oProc->RenameColumn('fm_location'.$attrib['type_id'], $OldColumnName, $attrib['column_name']);
				$this->oProc->m_aTables = $history_table_def;
				$this->oProc->RenameColumn('fm_location'.$attrib['type_id'] . '_history', $OldColumnName, $attrib['column_name']);
			}

			if (($OldDataType != $attrib['column_info']['type']) || ($OldPrecision != $attrib['column_info']['precision']) )
			{
				if($attrib['column_info']['type']!='R' && $attrib['column_info']['type']!='CH' && $attrib['column_info']['type']!='LB')
				{
					$this->db->query("DELETE FROM $choice_table WHERE type_id=" . $attrib['type_id']. " AND attrib_id=" . $attrib['id'],__LINE__,__FILE__);
				}

				if(!$attrib['column_info']['precision'])
				{
					if($precision = $this->bocommon->translate_datatype_precision($attrib['column_info']['type']))
					{
						$attrib['column_info']['precision']=$precision;
					}
				}

				if(!$attrib['column_info']['default'])
				{
					unset($attrib['column_info']['default']);
				}

				$value_set=array(
					'type_id'		=> $attrib['type_id'],
					'datatype'		=> $attrib['column_info']['type'],
					'precision_'		=> $attrib['column_info']['precision'],
					'scale'			=> $attrib['column_info']['scale'],
					'default_value'		=> $attrib['column_info']['default'],
					'nullable'		=> $attrib['column_info']['nullable']
					);

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("UPDATE fm_location_attrib set $value_set WHERE type_id = " . $attrib['type_id'] ." AND id=" . $attrib['id'],__LINE__,__FILE__);

				$attrib['column_info']['type']  = $this->bocommon->translate_datatype_insert($attrib['column_info']['type']);

				$this->oProc->m_aTables = $table_def;
				$this->oProc->AlterColumn('fm_location'.$attrib['type_id'],$attrib['column_name'],$attrib['column_info']);
				$this->oProc->m_aTables = $history_table_def;
				$this->oProc->AlterColumn('fm_location'.$attrib['type_id'] . '_history',$attrib['column_name'],$attrib['column_info']);
			}

			if($attrib['new_choice'])
			{
				$choice_id = $this->bocommon->next_id($choice_table ,array('type_id'=>$attrib['type_id'],'attrib_id'=>$attrib['id']));

				$values= array(
					$attrib['type_id'],
					$attrib['id'],
					$choice_id,
					$attrib['new_choice']
					);

				$values	= $this->bocommon->validate_db_insert($values);

				$this->db->query("INSERT INTO $choice_table (type_id,attrib_id,id,value) "
				. "VALUES ($values)",__LINE__,__FILE__);
			}

			if($attrib['delete_choice'])
			{
				for ($i=0;$i<count($attrib['delete_choice']);$i++)
				{
					$this->db->query("DELETE FROM $choice_table WHERE type_id=" . $attrib['type_id']. " AND attrib_id=" . $attrib['id']  ." AND id=" . $attrib['delete_choice'][$i],__LINE__,__FILE__);
				}
			}


			$this->db->transaction_commit();
			$this->oProc->m_odb->transaction_commit();

			$receipt['message'][] = array('msg'	=> lang('Attribute has been edited'));

			return $receipt;

		}

		function get_table_def($id='')
		{
			$metadata = $this->db->metadata('fm_location'.$id);

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

//	_debug_array($metadata);

			for ($i=0; $i<count($metadata); $i++)
			{
				$sql = "SELECT * FROM fm_location_attrib WHERE type_id=$id AND column_name = '" . $metadata[$i]['name'] . "'";
//	_debug_array($sql);

				$this->db->query($sql,__LINE__,__FILE__);
				if($this->db->next_record())
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
				else
				{
					$this->receipt['error'][] = array('msg'	=> lang('Column %1 is missing from metadata',$metadata[$i]['name']));
				}
			}

			$this->db->query("SELECT * FROM fm_location_type WHERE id=$id");
			$this->db->next_record();


			if($id>1)
			{
				$fk_table='fm_location'. ($id-1);
				for ($i=1; $i<$id; $i++)
				{
					$fk['loc' . $i]	= $fk_table . '.loc' . $i;
				}
			}

			$table_def = array(
				'fm_location'.$id =>	array(
					'fd' => $fd
					)
				);

			if($this->db->f('pk'))
			{
				$table_def['fm_location'.$id]['pk'] = explode(',',$this->db->f('pk'));
			}
			else
			{
				$table_def['fm_location'.$id]['pk'] = array();			
			}

			if($fk)
			{
				$table_def['fm_location'.$id]['fk'] = $fk;
			}
			else
			{
				$table_def['fm_location'.$id]['fk'] = array();			
			}

			if($this->db->f('ix'))
			{
				$table_def['fm_location'.$id]['ix'] = explode(',',$this->db->f('ix'));
			}
			else
			{
				$table_def['fm_location'.$id]['ix'] = array();			
			}

			if($this->db->f('uc'))
			{
				$table_def['fm_location'.$id]['uc'] = explode(',',$this->db->f('uc'));
			}
			else
			{
				$table_def['fm_location'.$id]['uc'] = array();			
			}

			$fd['exp_date'] = array('type' => 'timestamp','nullable' => True,'default' => 'current_timestamp');

			$table_def['fm_location'.$id . '_history']['fd'] =  $fd;
			$table_def['fm_location'.$id . '_history']['pk'] =   array();
			$table_def['fm_location'.$id . '_history']['fk'] =   array();
			$table_def['fm_location'.$id . '_history']['ix'] =   array();
			$table_def['fm_location'.$id . '_history']['uc'] =   array();	
		
			return $table_def;
		}


		function get_table_history_def($table_def,$type_id)
		{

			$history_table_def['fm_location'.$type_id . '_history'] = $table_def['fm_location'.$type_id];
			$history_table_def['fm_location'.$type_id . '_history']['fd']['exp_date']=array('type' => 'timestamp','nullable' => False,'default' => 'current_timestamp');
			
			$history_table_def['fm_location'.$type_id . '_history']['pk'] = array();
			$history_table_def['fm_location'.$type_id . '_history']['fk'] = array();
			$history_table_def['fm_location'.$type_id . '_history']['ix'] = array();
			$history_table_def['fm_location'.$type_id . '_history']['uc'] = array();
			
			return $history_table_def;
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

		function resort_attrib($data)
		{
			if(is_array($data))
			{
				$resort = (isset($data['resort'])?$data['resort']:'up');
				$type_id = (isset($data['type_id'])?$data['type_id']:'');
				$id = (isset($data['id'])?$data['id']:'');
			}

			if(!$type_id)
			{
				return;
			}

			$sql = "SELECT attrib_sort FROM fm_location_attrib where type_id=$type_id AND id=$id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('attrib_sort');
			$sql2 = "SELECT max(attrib_sort) as max_sort FROM fm_location_attrib where type_id=$type_id";
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');

			switch($resort)
			{
				case 'up':
					if($attrib_sort>1)
					{
						$sql = "UPDATE fm_location_attrib set attrib_sort=$attrib_sort WHERE type_id=$type_id AND attrib_sort =" . ($attrib_sort-1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE fm_location_attrib set attrib_sort=" . ($attrib_sort-1) ." WHERE type_id=$type_id AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				case 'down':
					if($max_sort > $attrib_sort)
					{
						$sql = "UPDATE fm_location_attrib set attrib_sort=$attrib_sort WHERE type_id=$type_id AND attrib_sort =" . ($attrib_sort+1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE fm_location_attrib set attrib_sort=" . ($attrib_sort+1) ." WHERE type_id=$type_id AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				default:
					return;
					break;
			}
		}
	}
?>
