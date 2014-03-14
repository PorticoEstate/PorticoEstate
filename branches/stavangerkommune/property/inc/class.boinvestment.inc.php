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
	* @subpackage eco
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.datetime');

	class property_boinvestment
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
//		var $part_of_town_id;

		var $public_functions = array
			(
				'read'				=> true,
				'read_single'		=> true,
				'save'				=> true,
				'delete'			=> true,
				'check_perms'		=> true
			);


		function property_boinvestment($session=false)
		{
			$this->so 		= CreateObject('property.soinvestment');
			$this->bocommon = CreateObject('property.bocommon');
			$this->socommon = CreateObject('property.socommon');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start				= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query				= phpgw::get_var('query');
			$sort				= phpgw::get_var('sort');
			$order				= phpgw::get_var('order');
			$filter				= phpgw::get_var('filter');
			$cat_id				= phpgw::get_var('cat_id');
			$part_of_town_id	= phpgw::get_var('part_of_town_id', 'int');
			$allrows			= phpgw::get_var('allrows', 'bool');

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
			if(isset($part_of_town_id))
			{
				$this->part_of_town_id = $part_of_town_id;
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}

		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','investment');
			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->part_of_town_id	= $data['part_of_town_id'];
			$this->allrows	= $data['allrows'];
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','investment',$data);
			}
		}

		function read()
		{

			$investment = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'part_of_town_id' => $this->part_of_town_id,'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;

	/*		for ($i=0; $i<count($investment); $i++)
			{
				$investment[$i]['date']  = $GLOBALS['phpgw']->common->show_date($investment[$i]['date']);
			}
	 */
			return $investment;
		}


		function select_category($format='',$selected='')
		{
			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
				break;
			}

			$categories= $this->so->get_type_list();

			return $this->bocommon->select_list($selected,$categories);
		}

		function write_off_period_list($selected='')
		{

			$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));


			$categories= $this->so->write_off_period_list();

			while (is_array($categories) && list(,$category) = each($categories))
			{
				$sel_category = '';
				if ($category['period']==$selected)
				{
					$sel_category = 'selected';
				}

				$category_list[] = array
					(
						'id'		=> $category['period'],
						'name'		=> $category['period'],
						'selected'	=> $sel_category
					);
			}

			for ($i=0;$i<count($category_list);$i++)
			{
				if ($category_list[$i]['selected'] != 'selected')
				{
					unset($category_list[$i]['selected']);
				}
			}

			return $category_list;
		}




		function save_investment($values)
		{
			while (is_array($values['location']) && list(,$value) = each($values['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$values['location_code']=implode("-", $location);

			//_debug_array($values);

			$values['date']	= $this->bocommon->date_to_timestamp($values['date']);
			$values['date']= date($GLOBALS['phpgw']->db->date_format(),$values['date']);

			$values['initial_value']	= abs($values['initial_value']);

			if($values['type']=='funding')
			{
				$values['initial_value'] = -$values['initial_value'];
			}

			if($values['new_period'])
			{
				$values['period'] = $values['new_period'];
				unset($values['new_period']);
			}

			if($values['extra']['p_num'])
			{
				$boadmin_entity		= CreateObject('property.boadmin_entity');
				$category = $boadmin_entity->read_single_category($values['extra']['p_entity_id'],$values['extra']['p_cat_id']);
				$values['entity_id'] 	= $values['extra']['p_num'];
				$values['entity_type']	=$category['name'];

			}
			else
			{
				$values['entity_id'] 	= $values['location_code'];
				$values['entity_type']	='property';
			}


			$receipt=$this->so->save_investment($values);

			return $receipt;
		}


		function update_investment($values)
		{

			$date_array = phpgwapi_datetime::date_array($values['date']);

			$date = mktime (2,0,0,$date_array['month'],$date_array['day'],$date_array['year']);
			$date= date($GLOBALS['phpgw']->db->date_format(),$date);

			$new_index=str_replace(",",".",$values['new_index']);

			$update = array();
			foreach($values['update'] as $entry)
			{
				$local_error = false;
				$n = $entry;

				if ($values['value'][$n])
				{
					if ((abs($values['value'][$n])- abs(($values['initial_value'][$n]*$new_index)))<0)
					{
						$new_value=0;
						$new_index=$values['value'][$n]/$values['initial_value'][$n];
					}
					else
					{
						$new_value=$values['value'][$n]-($values['initial_value'][$n]*$new_index);
					}

					$update[]=array(
						'entity_id'		=>$values['entity_id'][$n],
						'invest_id'		=>$values['investment_id'][$n],
						'new_index'		=>$new_index,
						'new_value'		=>$new_value,
						'initial_value'	=>$values['initial_value'][$n],
						'date'			=>$date
					);
				}
			}
			return $this->so->update_investment($update);
		}


		function filter($format='',$selected='')
		{
			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('filter_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('filter_filter'));
				break;
			}

			$filters[0]['id']	='investment';
			$filters[0]['name']	=lang('Investment');
			$filters[1]['id']	='funding';
			$filters[1]['name']	=lang('Funding');

			return $this->bocommon->select_list($selected,$filters);
		}

		function read_single($entity_id,$investment_id)
		{
			$history	= $this->so->read_single($entity_id,$investment_id,$this->start,$this->allrows);

			$this->total_records = $this->so->total_records;

			return $history;
		}


		function select_part_of_town($part_of_town_id)
		{
			return $this->socommon->select_part_of_town($part_of_town_id);
		}


		function delete($entity_id,$investment_id,$index_count)
		{
			$this->so->delete($entity_id,$investment_id,$index_count);
		}
	}
