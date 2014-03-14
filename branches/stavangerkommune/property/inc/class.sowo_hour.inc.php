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

	class property_sowo_hour
	{
		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->db2			= clone($this->db);
			$this->like 		= & $this->db->like;
			$this->join 		= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
		}

		function get_chapter_list()
		{
			$this->db->query("SELECT id, descr FROM fm_chapter ORDER BY id ");

			$chapter_entries = array();
			$i = 0;
			while ($this->db->next_record())
			{
				$chapter_entries[$i]['id']				= $this->db->f('id');
				$chapter_entries[$i]['name']			= $this->db->f('descr',true);
				$i++;
			}
			return $chapter_entries;
		}

		function get_grouping_list($workorder_id='')
		{
			$this->db->query("SELECT grouping_id, grouping_descr FROM fm_wo_hours where workorder_id='$workorder_id' and grouping_id >0 group by grouping_id, grouping_descr");

			$i = 0;
			while ($this->db->next_record())
			{
				$grouping_entries[$i]['id']				= $this->db->f('grouping_id');
				$grouping_entries[$i]['name']			= $this->db->f('grouping_descr',true);
				$i++;
			}
			return $grouping_entries;
		}

		function get_building_part_list()
		{
			$this->db->query("SELECT id, descr FROM fm_building_part ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$building_part_entries[$i]['id']				= $this->db->f('id');
				$building_part_entries[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $building_part_entries;
		}


		function select_branch_list()
		{
			$this->db->query("SELECT id, descr FROM fm_branch ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$branch_entries[$i]['id']				= $this->db->f('id');
				$branch_entries[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $branch_entries;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$workorder_id = (isset($data['workorder_id'])?$data['workorder_id']:0);
			}

			$ordermethod = ' ORDER BY grouping_id, record , id ASC';

			$sql = "SELECT DISTINCT fm_wo_hours.*, fm_wo_hours_category.descr AS wo_hour_category, fm_standard_unit.name AS unit_name"
				. " FROM fm_wo_hours {$this->left_join} fm_wo_hours_category on fm_wo_hours.category = fm_wo_hours_category.id"
				. " {$this->left_join} fm_standard_unit ON fm_wo_hours.unit = fm_standard_unit.id"
				. " WHERE workorder_id='{$workorder_id}'";

			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$hour_list = array();
			while ($this->db->next_record())
			{
				$hour_list[] = array
					(
						'hour_id'			=> $this->db->f('id'),
						'activity_num'		=> $this->db->f('activity_num'),
						'hours_descr'		=> $this->db->f('hours_descr',true),
						'owner'				=> $this->db->f('owner'),
						'quantity'			=> $this->db->f('quantity'),
						'grouping_id'		=> $this->db->f('grouping_id'),
						'grouping_descr'	=> $this->db->f('grouping_descr',true),
						'ns3420_id'			=> $this->db->f('ns3420_id'),
						'tolerance'			=> $this->db->f('tolerance'),
						'activity_id'		=> $this->db->f('activity_id'),
						'unit'				=> $this->db->f('unit'),
						'unit_name'			=> $this->db->f('unit_name'),
						'record'			=> $this->db->f('record'),
						'cost'				=> $this->db->f('cost'),
						'billperae'			=> $this->db->f('billperae'),
						'remark'			=> $this->db->f('remark'),
						'building_part'		=> $this->db->f('building_part'),
						'dim_d'				=> $this->db->f('dim_d'),
						'wo_hour_category'	=> $this->db->f('wo_hour_category'),
						'cat_per_cent'		=> $this->db->f('cat_per_cent')
					);
			}

			for ($i=0; $i<count($hour_list); $i++)
			{
				$sql = "SELECT sum(amount) as deviation, count(amount) as count_deviation FROM fm_wo_h_deviation WHERE workorder_id=$workorder_id and hour_id=". $hour_list[$i]['hour_id'];
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
				$hour_list[$i]['deviation']=$this->db->f('deviation');
				$hour_list[$i]['count_deviation']=$this->db->f('count_deviation');
			}

			return $hour_list;
		}


		function read_deviation($data)
		{
			if(is_array($data))
			{
				$workorder_id = (isset($data['workorder_id'])?$data['workorder_id']:0);
				$hour_id = (isset($data['hour_id'])?$data['hour_id']:0);
			}

			$ordermethod = ' order by id asc';

			$sql = "SELECT *  FROM fm_wo_h_deviation WHERE workorder_id=$workorder_id AND hour_id=$hour_id ";


			$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			while ($this->db->next_record())
			{
				$deviation[] = array
					(
						'entry_date'		=> $this->db->f('entry_date'),
						'workorder_id'		=> $workorder_id,
						'hour_id'			=> $hour_id,
						'id'				=> $this->db->f('id'),
						'amount'			=> $this->db->f('amount'),
						'descr'				=> stripslashes($this->db->f('descr'))
					);
			}
			//_debug_array($deviation);
			return $deviation;
		}


		function read_single_deviation($data)
		{
			if(is_array($data))
			{
				$workorder_id = (isset($data['workorder_id'])?$data['workorder_id']:0);
				$hour_id = (isset($data['hour_id'])?$data['hour_id']:0);
				$id = (isset($data['id'])?$data['id']:0);
			}

			$sql = "SELECT *  FROM fm_wo_h_deviation WHERE workorder_id=$workorder_id AND hour_id=$hour_id AND id = $id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$deviation = array
				(
					'entry_date'		=> $this->db->f('entry_date'),
					'workorder_id'		=> $workorder_id,
					'hour_id'			=> $hour_id,
					'id'				=> $this->db->f('id'),
					'amount'			=> $this->db->f('amount'),
					'descr'				=> stripslashes($this->db->f('descr'))
				);
			return $deviation;
		}

		function add_deviation($values)
		{
			$sql = "SELECT max(id) as current_id FROM fm_wo_h_deviation WHERE  workorder_id=" . $values['workorder_id'] . " AND hour_id=" . $values['hour_id'];
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$id=$this->db->f('current_id')+1;

			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$values_insert= array(
				$values['workorder_id'],
				$values['hour_id'],
				$id,
				$values['amount'],
				$values['descr'],
				time()
			);

			$values_insert	= $this->db->validate_insert($values_insert);

			$this->db->query("INSERT INTO fm_wo_h_deviation (workorder_id,hour_id,id,amount,descr,entry_date) VALUES ($values_insert)",__LINE__,__FILE__);

			$receipt['id']= $id;

			$receipt['message'][] = array('msg'=> lang('deviation has been added'));
			return $receipt;
		}

		function edit_deviation($values)
		{
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$value_set=array(
				'amount'			=> $values['amount'],
				'descr'			=> $this->db->db_addslashes($values['descr'])
			);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE fm_wo_h_deviation set $value_set WHERE workorder_id=" . $values['workorder_id'] . " AND hour_id=" . $values['hour_id'] . " AND id=" . $values['id'],__LINE__,__FILE__);

			$receipt['message'][] = array('msg'=> lang('deviation has been edited'));

			return $receipt;
		}

		function update_deviation($data)
		{
			if(is_array($data))
			{
				$id = (isset($data['workorder_id'])?$data['workorder_id']:0);
				$deviation = (isset($data['sum_deviation'])?$data['sum_deviation']:0);
			}
			$this->db->query("UPDATE fm_workorder set deviation = $deviation WHERE id=$id",__LINE__,__FILE__);
			return $receipt;
		}


		function next_record($workorder_id)
		{
			$this->db->query("SELECT  max(record) as record FROM fm_wo_hours where workorder_id='$workorder_id'",__LINE__,__FILE__);
			$this->db->next_record();
			$record	= $this->db->f('record')+1;
			return $record;
		}

		function add_template($values,$workorder_id)
		{

			$values['name'] = $this->db->db_addslashes($values['name']);
			$soworkorder	= CreateObject('property.soworkorder');
			$workorder		= $soworkorder->read_single($workorder_id);

			$this->db->transaction_begin();

			$values_insert= array(
				$this->account,
				$values['name'],
				$values['descr'],
				$workorder['chapter_id'],
				time()
			);

			$values_insert	= $this->db->validate_insert($values_insert);

			$this->db->query("insert into fm_template (owner,name,descr,chapter_id,entry_date) "
				. " values ($values_insert)",__LINE__,__FILE__);

			unset ($values_insert);

			$template_id = $this->db->get_last_insert_id('fm_template','id');

			$hour = $this->read(array('workorder_id' => $workorder_id));

			$record	= $this->next_record($workorder_id);

			for ($i=0; $i<count($hour); $i++)
			{
				$values_insert= array(
					$hour[$i]['activity_id'],
					$hour[$i]['activity_num'],
					$this->account,
					$hour[$i]['hours_descr'],
					$hour[$i]['unit'],
					$hour[$i]['cost'],
					$hour[$i]['quantity'],
					$hour[$i]['billperae'],
					$hour[$i]['ns3420_id'],
					$hour[$i]['dim_d'],
					$hour[$i]['grouping_id'],
					$hour[$i]['grouping_descr'],
					$hour[$i]['remark'],
					$hour[$i]['tolerance'],
					$hour[$i]['building_part'],
					$record,
					$template_id );

				$values_insert	= $this->db->validate_insert($values_insert);

				$this->db->query("insert into fm_template_hours (activity_id,activity_num,owner,hours_descr,unit,"
					. "cost,quantity,billperae,ns3420_id,dim_d,grouping_id,grouping_descr,remark,tolerance,building_part,record,template_id) "
					. " values ($values_insert)",__LINE__,__FILE__);

				$record++;
			}

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('template %1 is added',$values['name']));
			return $receipt;
		}


		function add_hour($hour)
		{
			$record	= $this->next_record($hour[0]['workorder_id']);

			for ($i=0; $i<count($hour); $i++)
			{
				$values= array(
					$hour[$i]['activity_id'],
					$hour[$i]['activity_num'],
					$this->account,
					$hour[$i]['hours_descr'],
					$hour[$i]['unit'],
					$hour[$i]['cost'],
					(int)$hour[$i]['quantity'],
					$hour[$i]['billperae'],
					$hour[$i]['ns3420_id'],
					$hour[$i]['dim_d'],
					$record,
					time(),
					$hour[$i]['workorder_id'],
					$hour[$i]['wo_hour_cat'],
					$hour[$i]['cat_per_cent']
				);

				$values	= $this->db->validate_insert($values);

				$this->db->query("insert into fm_wo_hours (activity_id,activity_num,owner,hours_descr,unit,cost,quantity,billperae,ns3420_id,dim_d,record,entry_date,workorder_id,category,cat_per_cent) "
					. " values ($values)",__LINE__,__FILE__);

				$record++;
			}

			$receipt['message'][] = array('msg'=>lang('%1 entries is added!',count($hour)));

			$receipt['hour_id'] = $this->db->get_last_insert_id('fm_wo_hours','id');

			return $receipt;
		}

		function add_hour_from_template($hour,$workorder_id)
		{

			$record	= $this->next_record($workorder_id);

			if($hour[0]['chapter_id'])
			{
				$this->db->query("UPDATE fm_workorder set
					chapter_id	='" . $hour[0]['chapter_id'] . "' WHERE id= '$workorder_id'",__LINE__,__FILE__);
			}

			for ($i=0; $i<count($hour); $i++)
			{

				if($hour[$i]['new_grouping'])
				{
					$this->db->query("SELECT grouping_id FROM fm_wo_hours where grouping_descr ='" .$hour[$i]['new_grouping'] . "' and workorder_id= '$workorder_id'",__LINE__,__FILE__);
					$this->db->next_record();
					if ( $this->db->f('grouping_id'))
					{
						$grouping_id	= $this->db->f('grouping_id');
					}
					else
					{
						$this->db->query("SELECT max(grouping_id) as grouping_id FROM fm_wo_hours where workorder_id= '$workorder_id'",__LINE__,__FILE__);
						$this->db->next_record();
						$grouping_id	= $this->db->f('grouping_id')+1;
					}

					$grouping_descr	= $hour[$i]['new_grouping'];
				}

				$values= array(
					$this->account,
					$hour[$i]['activity_id'],
					$hour[$i]['activity_num'],
					$hour[$i]['hours_descr'],
					$hour[$i]['unit'],
					$hour[$i]['cost'],
					$hour[$i]['quantity'],
					$hour[$i]['billperae'],
					$hour[$i]['ns3420_id'],
					$hour[$i]['dim_d'],
					$grouping_id,
					$grouping_descr,
					$record,
					$hour[$i]['building_part_id'],
					$hour[$i]['tolerance_id'],
					$hour[$i]['remark'],
					time(),
					$workorder_id,
					$hour[$i]['wo_hour_cat'],
					$hour[$i]['cat_per_cent']
				);

				$values	= $this->db->validate_insert($values);

				$this->db->query("insert into fm_wo_hours (owner,activity_id,activity_num,hours_descr,unit,cost,quantity,billperae,ns3420_id,dim_d,"
					. " grouping_id,grouping_descr,record,building_part,tolerance,remark,entry_date,workorder_id,category,cat_per_cent) "
					. " values ($values)",__LINE__,__FILE__);

				$record++;
			}

			$receipt['message'][] = array('msg'=>lang('the number of %1 hour is added!',$i));

			return $receipt;
		}

		function add_custom_hour($hour,$workorder_id)
		{
			$hour['record']	= $this->next_record($workorder_id);

			if($hour['chapter_id'])
			{
				$this->db->query("UPDATE fm_workorder set
					chapter_id	='" . $hour['chapter_id'] . "' WHERE id= '$workorder_id'",__LINE__,__FILE__);
			}

			if($hour['grouping_id'])
			{
				$this->db->query("SELECT grouping_descr , max(record) as record FROM fm_wo_hours where grouping_id='" .$hour['grouping_id'] . "' and workorder_id= '$workorder_id' GROUP by grouping_descr",__LINE__,__FILE__);
				$this->db->next_record();
				$hour['grouping_descr']	= $this->db->f('grouping_descr');
//				$hour['record']	= $this->db->f('record')+1;
			}

			if($hour['new_grouping'])
			{
				$this->db->query("SELECT grouping_id FROM fm_wo_hours where grouping_descr ='" .$hour['new_grouping'] . "' and workorder_id= '$workorder_id'",__LINE__,__FILE__);
				$this->db->next_record();
				if ( $this->db->f('grouping_id'))
				{
					$hour['grouping_id']	= $this->db->f('grouping_id');
				}
				else
				{
					$this->db->query("SELECT max(grouping_id) as grouping_id FROM fm_wo_hours where workorder_id= '$workorder_id'",__LINE__,__FILE__);
					$this->db->next_record();
					$hour['grouping_id']	= $this->db->f('grouping_id')+1;
//					$hour['record']	= 1;
				}

				$hour['grouping_descr']	= $hour['new_grouping'];
			}

			if(!$hour['cat_per_cent'])
			{
				$hour['cat_per_cent']= 100;
			}
			//_debug
			$values= array(
				$this->account,
				$hour['descr'],
				$hour['unit'],
				$hour['cost'],
				$hour['quantity'],
				$hour['billperae'],
				$hour['ns3420_id'],
				$hour['dim_d'],
				$hour['grouping_id'],
				$hour['grouping_descr'],
				$hour['record'],
				$hour['building_part_id'],
				$hour['tolerance_id'],
				$hour['remark'],
				time(),
				$workorder_id,
				$hour['wo_hour_cat'],
				$hour['cat_per_cent']
			);

			$values	= $this->db->validate_insert($values);

			$this->db->query("insert into fm_wo_hours (owner,hours_descr,unit,cost,quantity,billperae,ns3420_id,dim_d,"
				. " grouping_id,grouping_descr,record,building_part,tolerance,remark,entry_date,workorder_id,category,cat_per_cent) "
				. "VALUES ( $values )",__LINE__,__FILE__);

			$receipt['hour_id'] = $this->db->get_last_insert_id('fm_wo_hours','id');

			$receipt['message'][] = array('msg'=>lang('hour %1 is added!',$receipt['hour_id']));

			return $receipt;
		}

		function read_single_hour($hour_id)
		{
			$sql = "SELECT * from fm_wo_hours where id='$hour_id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$hour['hour_id']			= $this->db->f('id');
				$hour['record']				= $this->db->f('record');
				$hour['activity_id']		= $this->db->f('activity_id');
				$hour['activity_num']		= $this->db->f('activity_num');
				$hour['grouping_id']		= $this->db->f('grouping_id');
				$hour['hours_descr']		= $this->db->f('hours_descr',true);
				$hour['remark']				= $this->db->f('remark');
				$hour['billperae']			= $this->db->f('billperae');
				$hour['unit']				= $this->db->f('unit');
				$hour['ns3420_id']			= $this->db->f('ns3420_id');
				$hour['tolerance_id']		= (int)$this->db->f('tolerance');
				$hour['building_part_id']	= (int)$this->db->f('building_part');
				$hour['quantity']			= $this->db->f('quantity');
				$hour['cost']				= $this->db->f('cost');
				$hour['dim_d']				= $this->db->f('dim_d');
				$hour['wo_hour_cat']		= $this->db->f('category');
				$hour['cat_per_cent']		= $this->db->f('cat_per_cent');
			}

			return $hour;
		}

		function edit($hour,$workorder_id)
		{

			$hour['descr'] = $this->db->db_addslashes($hour['descr']);
			$hour['remark'] = $this->db->db_addslashes($hour['remark']);
			if(!$hour['cat_per_cent'])
			{
				$hour['cat_per_cent']= 100;
			}
			//_debug_array($hour);


			if($hour['chapter_id'])
			{
				$this->db->query("UPDATE fm_workorder set
					chapter_id	='" . $hour['chapter_id'] . "' WHERE id= '$workorder_id'",__LINE__,__FILE__);
			}

			if($hour['new_grouping'])
			{
				$this->db->query("SELECT grouping_id FROM fm_wo_hours where grouping_descr ='" .$hour['new_grouping'] . "' and workorder_id= '$workorder_id'",__LINE__,__FILE__);
				$this->db->next_record();
				if ( $this->db->f('grouping_id'))
				{
					$hour['grouping_id']	= $this->db->f('grouping_id');
				}
				else
				{

					$this->db->query("UPDATE fm_wo_hours set grouping_id = NULL WHERE id ='" .$hour['hour_id'] . "'",__LINE__,__FILE__);
					$this->db->query("SELECT count(grouping_id) as num_grouping FROM fm_wo_hours where workorder_id= '$workorder_id' and grouping_id >0 ",__LINE__,__FILE__);
					$this->db->next_record();
					if ($this->db->f('num_grouping')==1)
					{
						$hour['grouping_id']=1;
					}
					else
					{
						$this->db->query("SELECT max(grouping_id) as grouping_id FROM fm_wo_hours where workorder_id= '$workorder_id'",__LINE__,__FILE__);
						$this->db->next_record();
						$hour['grouping_id']	= $this->db->f('grouping_id')+1;
					}
				}
				$hour['grouping_descr']	= $hour['new_grouping'];
			}
			else
			{
				$this->db->query("SELECT grouping_id,grouping_descr FROM fm_wo_hours where id ='" .$hour['hour_id'] . "'",__LINE__,__FILE__);
				$this->db->next_record();
				$old_grouping_id	= $this->db->f('grouping_id');

				if ( $old_grouping_id == $hour['grouping_id'])
				{

					$hour['grouping_descr']	= $this->db->f('grouping_descr');
				}
				else
				{
					$this->db->query("SELECT grouping_descr , max(record) as record FROM fm_wo_hours where grouping_id='" .$hour['grouping_id'] . "' and workorder_id= '$workorder_id' GROUP by grouping_descr",__LINE__,__FILE__);
					$this->db->next_record();
					if($this->db->f('grouping_descr'))
					{
						$hour['grouping_descr']	= $this->db->f('grouping_descr');
					}
					else
					{
						$hour['grouping_id']='';
						$hour['grouping_descr']='';
					}

/*					if($old_record>1)
					{
						$this->db->query("UPDATE fm_wo_hours set
							record	= record - 1 where grouping_id='" .$hour['grouping_id'] . "' and workorder_id= '$workorder_id' and record > $old_record ",__LINE__,__FILE__);

					}

 */
				}
			}

			$value_set=array(
				'hours_descr'		=> $hour['descr'],
				'remark'		=> $hour['remark'],
				'billperae'		=> $hour['billperae'],
				'unit'			=> $hour['unit'],
				'quantity'		=> $hour['quantity'],
				'cost'			=> $hour['cost'],
				'ns3420_id'		=> $hour['ns3420_id'],
				'tolerance'		=> $hour['tolerance_id'],
				'building_part'		=> $hour['building_part_id'],
				'dim_d'			=> $hour['dim_d'],
				'grouping_id'		=> $hour['grouping_id'],
				'grouping_descr'	=> $hour['grouping_descr'],
				'category'		=> $hour['wo_hour_cat'],
				'cat_per_cent'		=> $hour['cat_per_cent']
			);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE fm_wo_hours set $value_set WHERE id= '" . $hour['hour_id'] ."'",__LINE__,__FILE__);

			$receipt['hour_id'] = $hour['hour_id'];
			$receipt['message'][] = array('msg'=>lang('hour %1 has been edited',$hour['hour_id']));
			return $receipt;

		}

		function update_email($to_email,$workorder_id)
		{
			$this->db->query("SELECT vendor_id FROM fm_workorder where id ='$workorder_id'",__LINE__,__FILE__);
			$this->db->next_record();
			$vendor_id	= $this->db->f('vendor_id');

			$this->db->query("UPDATE fm_vendor set email ='$to_email' where  id= '$vendor_id'",__LINE__,__FILE__);
		}

		function get_email($vendor_id = 0)
		{
			$vendor_id=(int)$vendor_id;
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.vendor');
			$this->db->query("SELECT column_name FROM phpgw_cust_attribute WHERE location_id = {$location_id} AND datatype='email'",__LINE__,__FILE__);
			$email_list = array();
			while ($this->db->next_record())
			{
				$this->db2->query("SELECT " . $this->db->f('column_name') . " FROM fm_vendor WHERE id=$vendor_id",__LINE__,__FILE__);
				while ($this->db2->next_record())
				{
					if($this->db2->f($this->db->f('column_name')))
					{
						$email_list[] = array
						(
							'email' => $this->db2->f($this->db->f('column_name'))
						);
					}
				}
			}

			return  $email_list;
		}

		function delete($hour_id,$workorder_id )
		{
			$this->db->query("SELECT record FROM fm_wo_hours where id ='$hour_id'",__LINE__,__FILE__);
			$this->db->next_record();
			$old_record	= $this->db->f('record');

			$this->db->transaction_begin();

			$this->db->query("DELETE FROM fm_wo_hours WHERE id='" . $hour_id . "'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_wo_h_deviation WHERE workorder_id=$workorder_id AND hour_id=$hour_id",__LINE__,__FILE__);
			if($old_record)
			{
				$this->db->query("UPDATE fm_wo_hours set record	= record - 1 where  workorder_id= '$workorder_id' and record > $old_record ",__LINE__,__FILE__);
			}

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('hour %1 has been deleted',$hour_id));
			return $receipt;

		}

		function delete_deviation($workorder_id,$hour_id,$id )
		{
			$this->db->query("DELETE FROM fm_wo_h_deviation WHERE workorder_id=$workorder_id AND hour_id=$hour_id AND id=$id",__LINE__,__FILE__);
			$receipt['message'][] = array('msg'=>lang('deviation %1 has been deleted',$id));
			return $receipt;
		}

		function update_calculation($data)
		{
			if(is_array($data))
			{
				$id = (isset($data['workorder_id'])?$data['workorder_id']:0);
				$calculation = (isset($data['calculation'])?$data['calculation']:0);
			}

			$this->db->transaction_begin();
			$this->db->query("UPDATE fm_workorder SET calculation = '{$calculation}' WHERE id = '{$id}'",__LINE__,__FILE__);

			if($calculation > 0)
			{
				$soworkorder	= CreateObject('property.soworkorder');
				$config			= CreateObject('phpgwapi.config','property');
				$config->read();
				$tax = 1+(($config->config_data['fm_tax'])/100);
				$calculation = $calculation * $tax;

				$this->db->query("UPDATE fm_workorder SET combined_cost = '{$calculation}' WHERE id = '{$id}'",__LINE__,__FILE__);

				$this->db->query("SELECT sum(budget) AS budget, sum(contract_sum) as contract_sum FROM fm_workorder_budget WHERE order_id = '{$id}'",__LINE__,__FILE__);
				$this->db->next_record();
				$budget			= $this->db->f('budget');
				$contract_sum	= $this->db->f('contract_sum');
				
				$this->db->query("SELECT periodization_id, contract_sum"
				. " FROM fm_workorder {$this->join} fm_project ON (fm_workorder.project_id = fm_project.id)"
				. " WHERE fm_workorder.id = '{$id}'",__LINE__,__FILE__);

				$this->db->next_record();

				$periodization_id	= $this->db->f('periodization_id');
				$contract_sum		= $this->db->f('contract_sum');
				if(!abs($contract_sum) > 0)
				{
					$soworkorder->_update_order_budget($id, date('Y'), $periodization_id, $budget	 , $contract_sum, $calculation);
				}
			}

			$this->db->transaction_commit();
			return $receipt;
		}
	}
