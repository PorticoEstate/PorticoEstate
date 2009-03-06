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

	class property_bocategory
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

		var $soap_functions = array(
			'list' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			),
			'read' => array(
				'in'  => array('int','struct'),
				'out' => array('array')
			),
			'save' => array(
				'in'  => array('int','struct'),
				'out' => array()
			),
			'delete' => array(
				'in'  => array('int','struct'),
				'out' => array()
			)
		);

		function property_bocategory($session=false)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('property.socategory');
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
			$filter				= phpgw::get_var('filter', 'int');
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$allrows			= phpgw::get_var('allrows', 'bool');

			$this->start		= $start ? $start : 0;
			$this->query		= isset($query) ? $query : $this->query;
			$this->sort			= isset($sort) && $sort ? $sort : '';
			$this->order		= isset($order) && $order ? $order : '';
			$this->filter		= isset($filter) && $filter ? $filter : '';
			$this->cat_id		= isset($cat_id) && $cat_id ? $cat_id : '';
			$this->allrows		= isset($allrows) && $allrows ? $allrows : '';
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','category',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','category');

			//_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->allrows	= $data['allrows'];
		}

		function get_location_info($type,$type_id)
		{
			return $this->so->get_location_info($type,$type_id);
		}

		function read($type='',$type_id='')
		{
			$category = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'type' => $type,type_id=>$type_id,'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;

			return $category;
		}

		function read_single($data)
		{
			$location_info = $this->get_location_info($data['type'],$data['type_id']);
			
			$custom_fields = false;
			if($GLOBALS['phpgw']->locations->get_attrib_table($appname, $location))
			{
				$custom_fields = true;
				$values = array();
				$values['attributes'] = $this->custom->find('property',$location_info['acl_location'], 0, '', 'ASC', 'attrib_sort', true, true);
			}
			if(isset($data['id']) && $data['id'])
			{
				$values = $this->so->read_single($data, $values);
			}
			if($custom_fields)
			{
				$values = $this->custom->prepare($values, 'property',$location_info['acl_location'], $data['view']);
			}
			return $values;
		}

		function select_part_of_town($part_of_town_id)
		{
			return $this->socommon->select_part_of_town($part_of_town_id);
		}

		function save($category,$action='',$type ='',$type_id)
		{
			if ($action=='edit')
			{
				if ($category['id'] != '')
				{

					$receipt = $this->so->edit($category,$type,$type_id);
				}
			}
			else
			{
				$receipt = $this->so->add($category,$type,$type_id);
			}

			return $receipt;
		}

		function delete($id,$type,$type_id)
		{
			$this->so->delete($id,$type,$type_id);
		}
	}

