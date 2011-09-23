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

	class property_bowo_hour
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
			(
				'read'				=> true,
				'read_single'		=> true,
				'save'				=> true,
				'delete'			=> true,
				'check_perms'		=> true
			);

		function property_bowo_hour($session=false)
		{
			$this->so 			= CreateObject('property.sowo_hour');
			$this->bocommon 	= CreateObject('property.bocommon');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start		= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query		= phpgw::get_var('query');
			$sort		= phpgw::get_var('sort');
			$order		= phpgw::get_var('order');
			$filter		= phpgw::get_var('filter', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$chapter_id	= phpgw::get_var('chapter_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(isset($query))
			{
				$this->query = $query;
			}
			if(isset($filter))
			{
				$this->filter = $filter;
			}
			if(isset($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order))
			{
				$this->order = $order;
			}
			if(isset($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
			if(isset($chapter_id))
			{
				$this->chapter_id = $chapter_id;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','wo_hour',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','wo_hour');

			$this->start		= $data['start'];
			$this->query		= $data['query'];
			$this->filter		= $data['filter'];
			$this->sort			= $data['sort'];
			$this->order		= $data['order'];
			$this->cat_id		= $data['cat_id'];
			$this->allrows		= $data['allrows'];
			$this->chapter_id	= $data['chapter_id'];
		}

		function get_chapter_list($format='',$selected='')
		{
			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('chapter_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('chapter_filter'));
				break;
			}

			$chapters= $this->so->get_chapter_list();

			return $this->bocommon->select_list($selected,$chapters);
		}

		function get_tolerance_list($selected='')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('tolerance_select'));

			$tolerances[0]['id']= 1;
			$tolerances[1]['id']= 2;
			$tolerances[2]['id']= 3;

			while (is_array($tolerances) && list(,$tolerance_entry) = each($tolerances))
			{
				$sel_tolerance_entry = '';
				if ($tolerance_entry['id']==$selected)
				{
					$sel_tolerance_entry = 'selected';
				}

				$tolerance_list[] = array
					(
						'id'		=> $tolerance_entry['id'],
						'name'		=> $tolerance_entry['id'],
						'selected'	=> $sel_tolerance_entry
					);
			}

			for ($i=0;$i<count($tolerance_list);$i++)
			{
				if ($tolerance_list[$i]['selected'] != 'selected')
				{
					unset($tolerance_list[$i]['selected']);
				}
			}

			return $tolerance_list;
		}

		function get_grouping_list($selected='',$workorder_id)
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('grouping_select'));
			$groupings= $this->so->get_grouping_list($workorder_id);
			return $this->bocommon->select_list($selected,$groupings);
		}

		function get_building_part_list($selected='')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('building_part_select'));

			$building_parts= $this->so->get_building_part_list();

			while (is_array($building_parts) && list(,$building_part_entry) = each($building_parts))
			{
				$sel_building_part_entry = '';
				if ($building_part_entry['id']==$selected)
				{
					$sel_building_part_entry = 'selected';
				}

				$building_part_list[] = array
					(
						'id'		=> $building_part_entry['id'],
						'name'		=> '[ ' . $building_part_entry['id'] . ' ] ' . $building_part_entry['name'],
						'selected'	=> $sel_building_part_entry
					);
			}

			for ($i=0;$i<count($building_part_list);$i++)
			{
				if ($building_part_list[$i]['selected'] != 'selected')
				{
					unset($building_part_list[$i]['selected']);
				}
			}

			return $building_part_list;
		}

		function read($workorder_id='')
		{
			$hour = $this->so->read(array('workorder_id' => $workorder_id));
			$this->total_records = $this->so->total_records;
			return $hour;
		}

		function read_deviation($data)
		{
			$deviation = $this->so->read_deviation(array('workorder_id' => $data['workorder_id'],'hour_id' => $data['hour_id']));
			$this->total_records = $this->so->total_records;
			return $deviation;
		}

		function read_single_deviation($data)
		{
			return	$this->so->read_single_deviation($data);
		}

		function update_deviation($data)
		{
			$this->so->update_deviation($data);
		}

		function update_calculation($data)
		{
			$this->so->update_calculation($data);
		}

		function save_deviation($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_deviation($values);
				}
			}
			else
			{
				$receipt = $this->so->add_deviation($values);
			}
			return $receipt;
		}

		function add_template($values,$workorder_id)
		{
			return $this->so->add_template($values,$workorder_id);
		}

		function add_hour($values,$workorder_id)
		{
			foreach($values['quantity'] as $n => $quantity)
			{
				if(!$quantity)
				{
					continue;
				}

				if ($values['wo_hour_cat'][$n] && !$values['cat_per_cent'][$n])
				{
					$values['cat_per_cent'][$n] = 100;
				}
				$quantity		= str_replace(",",".",$quantity);

				$cost=($values['total_cost'][$n]*$quantity);

				$hour[] = array
					(
						'activity_id'		=> $values['activity_id'][$n],
						'activity_num'		=> $values['activity_num'][$n],
						'hours_descr'		=> $values['descr'][$n],
						'unit'				=> $values['unit'][$n],
						'cost' 				=> $cost,
						'quantity'			=> $quantity,
						'billperae'			=> $values['total_cost'][$n],
						'ns3420_id'			=> $values['ns3420_id'][$n],
						'dim_d'				=> $values['dim_d'][$n],
						'workorder_id'		=> $workorder_id,
						'wo_hour_cat'		=> $values['wo_hour_cat'][$n],
						'cat_per_cent'		=> $values['cat_per_cent'][$n]
					);
			}

			if($hour)
			{
				$receipt = 	$this->so->add_hour($hour);
			}
			else
			{
				$receipt['message'][] = array('msg'=>lang('Nothing to do!'));
			}

			return $receipt;

		}

		function add_hour_from_template($values,$workorder_id)
		{

			//_debug_array($values);

			foreach($values['quantity'] as $n => $quantity)
			{
				if(!$quantity)
				{
					continue;
				}

				if ($values['wo_hour_cat'][$n] && !$values['cat_per_cent'][$n])
				{
					$values['cat_per_cent'][$n] = 100;
				}

				$quantity		= str_replace(",",".",$quantity);
				$cost=($values['billperae'][$n]*$quantity);

				$hour[]= array
					(
						'chapter_id'		=> $values['chapter_id'][$n],
						'activity_id'		=> $values['activity_id'][$n],
						'activity_num'		=> $values['activity_num'][$n],
						'hours_descr'		=> $values['hours_descr'][$n],
						'remark'			=> $values['remark'][$n],
						'unit'				=> $values['unit'][$n],
						'cost' 				=> $cost,
						'quantity'			=> $quantity,
						'new_grouping'		=> $values['grouping_descr'][$n],
						'billperae'			=> $values['billperae'][$n],
						'ns3420_id'			=> $values['ns3420_id'][$n],
						'tolerance'			=> $values['tolerance'][$n],
						'building_part'		=> $values['building_part'][$n],
						'dim_d'				=> $values['dim_d'][$n],
						'workorder_id'		=> $workorder_id,
						'wo_hour_cat'		=> $values['wo_hour_cat'][$n],
						'cat_per_cent'		=> $values['cat_per_cent'][$n]
					);

			}
			//_debug_array($hour);

			if($hour)
			{
				$receipt = 	$this->so->add_hour_from_template($hour,$workorder_id);
			}
			else
			{
				$receipt['message'][] = array('msg'=>lang('Nothing to do!'));
			}

			return $receipt;

		}

		function read_single_hour($hour_id)
		{
			$hour	= $this->so->read_single_hour($hour_id);
			return $hour;
		}

		function save_hour($values,$workorder_id)
		{
			$values['billperae']	= str_replace(",",".",$values['billperae']);
			$values['quantity']		= str_replace(",",".",$values['quantity']);
			$values['cost']			= $values['billperae']*$values['quantity'];
			if($values['ns3420_descr'])
			{
				$values['descr']=$values['ns3420_descr'];
			}

			if ($values['hour_id'])
			{
				if ($values['hour_id'] != 0)
				{
					$receipt = $this->so->edit($values,$workorder_id);
				}
			}
			else
			{
				//_debug_array($values);
				$receipt = $this->so->add_custom_hour($values,$workorder_id);
			}
			return $receipt;
		}

		function get_email($selected, $vendor_id)
		{
			$email_list = $this->so->get_email($vendor_id);

			foreach( $email_list as &$email_entry )
			{
				$email_entry['selected'] = trim($email_entry['email']) == trim($selected) ? 1 : 0;
			}

			return  $email_list;
		}

		function update_email($to_email,$workorder_id)
		{
			$this->so->update_email($to_email,$workorder_id);
		}


		function delete($hour_id,$workorder_id)
		{
			return $this->so->delete($hour_id,$workorder_id);
		}

		function delete_deviation($workorder_id,$hour_id,$id)
		{
			return $this->so->delete_deviation($workorder_id,$hour_id,$id);
		}
	}
