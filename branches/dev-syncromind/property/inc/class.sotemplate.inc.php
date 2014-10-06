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

	class property_sotemplate
	{
		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start']:0;
				$filter			= isset($data['filter']) ? $data['filter']:'';
				$query 			= isset($data['query']) ? $data['query']:'';
				$sort 			= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order 			= isset($data['order']) ? $data['order']:'';
				$chapter_id 	= isset($data['chapter_id']) && $data['chapter_id'] ? $data['chapter_id']:0;
				$allrows 		= isset($data['allrows']) ? $data['allrows']:'';
			}

			$filtermethod = '';

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by fm_template.id desc';
			}

			$where= 'WHERE';

			if ($chapter_id > 0)
			{
				$filtermethod .= " $where chapter_id='$chapter_id' ";
				$where= 'AND';
			}

			if ($filter)
			{
				$filtermethod .= " $where fm_template.owner='$filter' ";
				$where= 'AND';
			}

			$querymethod = '';

			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " $where (fm_template.name $this->like '%$query%' OR fm_template.descr $this->like '%$query%')";
			}

			$sql = "SELECT fm_template.id,fm_template.descr,fm_template.name,fm_template.owner,fm_template.entry_date,"
				. " fm_chapter.descr as chapter FROM fm_template $this->left_join fm_chapter  on fm_template.chapter_id=fm_chapter.id"
				. " $filtermethod $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

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
				$template_list[] = array
					(
						'template_id'		=> $this->db->f('id'),
						'name'				=> stripslashes($this->db->f('name')),
						'descr'				=> stripslashes($this->db->f('descr')),
						'owner'				=> $this->db->f('owner'),
						'entry_date'		=> $this->db->f('entry_date'),
						'chapter'			=> $this->db->f('chapter')
					);
			}
			return $template_list;
		}

		function read_template_hour($data)
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
				$query 			= (isset($data['query'])?$data['query']:'');
				$sort 			= (isset($data['sort'])?$data['sort']:'DESC');
				$order 			= (isset($data['order'])?$data['order']:'');
				$chapter_id 	= (isset($data['chapter_id'])?$data['chapter_id']:0);
				$allrows 		= (isset($data['allrows'])?$data['allrows']:'');
				$template_id 	= (isset($data['template_id'])?$data['template_id']:0);
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by record ';
			}


			$filtermethod = " where template_id='$template_id' ";

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " AND (hours_descr $this->like '%$query%' or fm_template_hours.remark $this->like '%$query%' or ns3420_id $this->like '%$query%')";
			}

			$sql = "SELECT fm_template_hours.*, chapter_id, fm_standard_unit.name AS unit_name"
			. " FROM fm_template_hours"
			. " {$this->join} fm_template on fm_template.id=fm_template_hours.template_id"
			. " {$this->join} fm_standard_unit ON fm_template_hours.unit = fm_standard_unit.id"
			. " {$filtermethod} {$querymethod}";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

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
				$hour_list[] = array
					(
						'hour_id'			=> $this->db->f('id'),
						'chapter_id'		=> $this->db->f('chapter_id'),
						'activity_num'		=> $this->db->f('activity_num'),
						'hours_descr'		=> stripslashes($this->db->f('hours_descr')),
						'remark'			=> stripslashes($this->db->f('remark')),
						'grouping_id'		=> $this->db->f('grouping_id'),
						'grouping_descr'	=> $this->db->f('grouping_descr'),
						'ns3420_id'			=> $this->db->f('ns3420_id'),
						'tolerance'			=> $this->db->f('tolerance'),
						'activity_id'		=> $this->db->f('activity_id'),
						'unit'				=> $this->db->f('unit'),
						'unit_name'			=> $this->db->f('unit_name'),
						'record'			=> $this->db->f('record'),
						'cost'				=> $this->db->f('cost'),
						'billperae'			=> $this->db->f('billperae'),
						'building_part'		=> $this->db->f('building_part'),
						'dim_d'				=> $this->db->f('dim_d')
					);
			}
			return $hour_list;
		}

		function read_single_template($template_id)
		{
			$sql = "SELECT * FROM fm_template where id='$template_id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$template['template_id']		= $this->db->f('id');
				$template['name']				= stripslashes($this->db->f('name'));
				$template['descr']				= stripslashes($this->db->f('descr'));
				$template['chapter_id']			= (int)$this->db->f('chapter_id');
			}
			return $template;
		}

		function read_single_hour($hour_id)
		{
			$sql = "SELECT * from fm_template_hours where id='$hour_id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$hour['hour_id']				= $this->db->f('id');
				$hour['record']					= $this->db->f('record');
				$hour['activity_id']			= $this->db->f('activity_id');
				$hour['activity_num']			= $this->db->f('activity_num');
				$hour['grouping_id']			= $this->db->f('grouping_id');
				$hour['hours_descr']			= $this->db->f('hours_descr');
				$hour['remark']					= $this->db->f('remark');
				$hour['billperae']				= $this->db->f('billperae');
				$hour['unit']					= $this->db->f('unit');
				$hour['ns3420_id']				= $this->db->f('ns3420_id');
				$hour['tolerance_id']			= (int)$this->db->f('tolerance');
				$hour['building_part_id']		= (int)$this->db->f('building_part');
				$hour['quantity']				= $this->db->f('quantity');
				$hour['cost']					= $this->db->f('cost');
				$hour['dim_d']					= $this->db->f('dim_d');
			}

			return $hour;
		}

		function next_record($template_id)
		{

			$this->db->query("SELECT  max(record) as record FROM fm_template_hours where template_id='$template_id'",__LINE__,__FILE__);
			$this->db->next_record();
			$record	= $this->db->f('record')+1;
			return $record;

		}

		function add_custom_hour($hour,$template_id)
		{
			$this->db->transaction_begin();

			$hour['record']	= $this->next_record($template_id);

			$this->db->query("UPDATE fm_template set
				chapter_id	='" . $hour['chapter_id'] . "' WHERE id= '$template_id'",__LINE__,__FILE__);

			if($hour['grouping_id'])
			{
				$this->db->query("SELECT grouping_descr , max(record) as record FROM fm_template_hours where grouping_id='" .$hour['grouping_id'] . "' and template_id= '$template_id' GROUP by grouping_descr",__LINE__,__FILE__);
				$this->db->next_record();
				$hour['grouping_descr']	= $this->db->f('grouping_descr');
			}

			if($hour['new_grouping'])
			{
				$this->db->query("SELECT grouping_id FROM fm_template_hours where grouping_descr ='" .$hour['new_grouping'] . "' and template_id= '$template_id'",__LINE__,__FILE__);
				$this->db->next_record();
				if ( $this->db->f('grouping_id'))
				{
					$hour['grouping_id']	= $this->db->f('grouping_id');
				}
				else
				{
					$this->db->query("SELECT max(grouping_id) as grouping_id FROM fm_template_hours where template_id= '$template_id'",__LINE__,__FILE__);
					$this->db->next_record();
					$hour['grouping_id']	= $this->db->f('grouping_id')+1;
				}

				$hour['grouping_descr']	= $hour['new_grouping'];
			}

			$values= array(
				$this->account,
				$this->db->db_addslashes($hour['descr']),
				$hour['unit'],
				$hour['cost'],
				$hour['quantity'],
				$hour['billperae'],
				$hour['ns3420_id'],
				$hour['dim_d'],
				$hour['grouping_id'],
				$this->db->db_addslashes($hour['grouping_descr']),
				$hour['record'],
				$hour['building_part_id'],
				$hour['tolerance_id'],
				$this->db->db_addslashes($hour['remark']),
				time(),
				$template_id);


			$values	= $this->db->validate_insert($values);


			$this->db->query("INSERT INTO fm_template_hours (owner,hours_descr,unit,cost,quantity,billperae,ns3420_id,dim_d,"
				. " grouping_id,grouping_descr,record,building_part,tolerance,remark,entry_date,template_id) "
				. " VALUES ($values)",__LINE__,__FILE__);

			$receipt['hour_id'] = $this->db->get_last_insert_id('fm_template_hours','id');

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('hour %1 is added!',$hour['record']));

			return $receipt;
		}


		function edit_hour($hour,$template_id)
		{

			$this->db->transaction_begin();

			$hour['descr'] = $this->db->db_addslashes($hour['descr']);
			$hour['remark'] = $this->db->db_addslashes($hour['remark']);

			$this->db->query("UPDATE fm_template set
				chapter_id	='" . $hour['chapter_id'] . "' WHERE id= '$template_id'",__LINE__,__FILE__);

			if($hour['new_grouping'])
			{
				$this->db->query("SELECT grouping_id FROM fm_template_hours where grouping_descr ='" .$hour['new_grouping'] . "' and template_id= '$template_id'",__LINE__,__FILE__);
				$this->db->next_record();
				if ( $this->db->f('grouping_id'))
				{
					$hour['grouping_id']	= $this->db->f('grouping_id');
				}
				else
				{

					$this->db->query("UPDATE fm_template_hours set grouping_id = NULL WHERE id ='" .$hour['hour_id'] . "'",__LINE__,__FILE__);
					$this->db->query("SELECT count(grouping_id) as num_grouping FROM fm_template_hours where template_id= '$template_id' and grouping_id >0 ",__LINE__,__FILE__);
					$this->db->next_record();
					if ($this->db->f('num_grouping')==1)
					{
						$hour['grouping_id']=1;
					}
					else
					{
						$this->db->query("SELECT max(grouping_id) as grouping_id FROM fm_template_hours where template_id= '$template_id'",__LINE__,__FILE__);
						$this->db->next_record();
						$hour['grouping_id']	= $this->db->f('grouping_id')+1;
					}
				}
				$hour['grouping_descr']	= $hour['new_grouping'];
			}
			else
			{
				$this->db->query("SELECT grouping_id,grouping_descr FROM fm_template_hours where id ='" .$hour['hour_id'] . "'",__LINE__,__FILE__);
				$this->db->next_record();
				$old_grouping_id	= $this->db->f('grouping_id');

				if ( $old_grouping_id == $hour['grouping_id'])
				{

					$hour['grouping_descr']	= $this->db->f('grouping_descr');
				}
				else
				{
					$this->db->query("SELECT grouping_descr , max(record) as record FROM fm_template_hours where grouping_id='" .$hour['grouping_id'] . "' and template_id= '$template_id' GROUP by grouping_descr",__LINE__,__FILE__);
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

				}
			}

			$this->db->query("SELECT record FROM fm_template_hours where id ='" .$hour['hour_id'] . "'",__LINE__,__FILE__);
			$this->db->next_record();
			$hour['record']	= $this->db->f('record');


			$value_set=array(
				'hours_descr'		=> $hour['descr'],
				'remark'			=> $hour['remark'],
				'billperae'			=> $hour['billperae'],
				'unit'				=> $hour['unit'],
				'quantity'			=> $hour['quantity'],
				'cost'				=> $hour['cost'],
				'ns3420_id'			=> $hour['ns3420_id'],
				'tolerance'			=> $hour['tolerance_id'],
				'building_part'		=> $hour['building_part_id'],
				'dim_d'				=> $hour['dim_d'],
				'grouping_id'		=> $hour['grouping_id'],
				'grouping_descr'	=> $hour['grouping_descr']
			);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE fm_template_hours set $value_set WHERE id= '" . $hour['hour_id'] ."'",__LINE__,__FILE__);

			$receipt['hour_id'] = $hour['hour_id'];

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('hour %1 has been edited',$hour['record']));
			return $receipt;
		}

		function get_grouping_list($template_id='')
		{
			$this->db->query('SELECT grouping_id, grouping_descr FROM fm_template_hours where template_id=' . (int)$template_id . ' AND grouping_id > 0 group by grouping_id, grouping_descr');
			$grouping_entries = array();
			while ($this->db->next_record())
			{
				$grouping_entries[] = array
					(
						'id'	=> $this->db->f('grouping_id'),
						'name'	=> $this->db->f('grouping_descr',true)
					);
			}
			return $grouping_entries;
		}

		function add_template($values)
		{
			$this->db->transaction_begin();
			$values['name'] = $this->db->db_addslashes($values['name']);

			$values= array(
				$this->account,
				$values['name'],
				$values['descr'],
				$values['chapter_id'],
				time()
			);

			$values	= $this->db->validate_insert($values);

			$this->db->query("INSERT INTO fm_template (owner,name,descr,chapter_id,entry_date) "
				. " VALUES ($values)",__LINE__,__FILE__);

			$template_id = $this->db->get_last_insert_id('fm_template','id');

			$this->db->transaction_commit();

			$receipt['template_id'] = $template_id;
			$receipt['message'][] = array('msg'=>lang('template %1 is added',$values['name']));
			return $receipt;
		}

		function edit_template($values)
		{
			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$this->db->transaction_begin();

			$value_set=array(
				'name' 			=> $values['name'],
				'descr'			=> $values['descr'],
				'chapter_id'	=> $values['chapter_id']
			);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE fm_template set $value_set WHERE id='" . $values['template_id'] . "'",__LINE__,__FILE__);

			$this->db->transaction_commit();
			$receipt['message'][]=array('msg'=>lang('template has been edited'));
			return $receipt;
		}

		function delete($id)
		{
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_template WHERE id='$id'",__LINE__,__FILE__);
			$this->db->query("DELETE FROM fm_template_hours  WHERE template_id='$id'",__LINE__,__FILE__);
			$this->db->transaction_commit();
		}

		function delete_hour($hour_id,$template_id )
		{
			$this->db->transaction_begin();
			$this->db->query("SELECT record FROM fm_template_hours where id ='$hour_id'",__LINE__,__FILE__);
			$this->db->next_record();
			$old_record	= $this->db->f('record');

			$this->db->query("DELETE FROM fm_template_hours WHERE id='" . $hour_id . "'",__LINE__,__FILE__);
			if($old_record)
			{
				$this->db->query("UPDATE fm_template_hours set record	= record - 1 where  template_id= '$template_id' and record > $old_record ",__LINE__,__FILE__);
			}

			$this->db->transaction_commit();
			$receipt['message'][] = array('msg'=>lang('hour %1 has been deleted',$hour_id));
			return $receipt;
		}
	}
