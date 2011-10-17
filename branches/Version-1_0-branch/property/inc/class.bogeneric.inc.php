<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
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

	class property_bogeneric
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $location_info = array();


		function __construct($session=false)
		{
			$this->so 			= CreateObject('property.sogeneric');
			$this->custom 		= & $this->so->custom;
			$this->bocommon		= CreateObject('property.bocommon');

			$start				= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query				= phpgw::get_var('query');
			$sort				= phpgw::get_var('sort');
			$order				= phpgw::get_var('order');
			$filter				= phpgw::get_var('filter', 'int');
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$allrows			= phpgw::get_var('allrows', 'bool');
			$type				= phpgw::get_var('type');
			$type_id			= phpgw::get_var('type_id', 'int', 'REQUEST', 0);

			$this->type 		= $type;
			$this->type_id 		= $type_id;

			if ($session)
			{
				$this->read_sessiondata($type);
				$this->use_session = true;
			}

			$this->start		= $start ? $start : 0;
			$this->query		= isset($_REQUEST['query']) ? $query : $this->query;
			$this->sort			= isset($_REQUEST['sort']) ? $sort : $this->sort;
			$this->order		= isset($_REQUEST['order']) && $_REQUEST['order'] ? $order : $this->order;
			$this->filter		= isset($_REQUEST['filter']) ? $filter : $this->filter;
			$this->cat_id		= isset($_REQUEST['cat_id'])  ? $cat_id :  $this->cat_id;
			$this->allrows		= isset($allrows) ? $allrows : false;

//			$this->location_info = $this->so->get_location_info($type, $type_id);

		}

		public function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data',"generic_{$data['type']}",$data);
			}
		}

		function read_sessiondata($type)
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data',"generic_{$type}");

			//		_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->allrows	= $data['allrows'];
		}

		public function get_location_info($type = '', $type_id = 0)
		{
			$type = $type ? $type : $this->type;
			$type_id = $type_id ? $type_id : $this->type_id;
			return $this->location_info = $this->so->get_location_info($type,$type_id);
		}

		function column_list($selected='',$allrows='')
		{
			if(!$selected)
			{
				$selected = $GLOBALS['phpgw_info']['user']['preferences'][$this->location_info['acl_app']]["generic_columns_{$this->type}_{$this->type_id}"];
			}

			$filter = array('list' => ''); // translates to "list IS NULL"
			$columns = $this->custom->find($this->location_info['acl_app'],$this->location_info['acl_location'], 0, '','','',true, false, $filter);
			$column_list=$this->bocommon->select_multi_list($selected,$columns);

			return $column_list;
		}

		public function read($filter = array())
		{
			if (! $filter )
			{
				foreach ( $this->location_info['fields'] as $field )
				{
					if (isset($field['filter']) && $field['filter'])
					{
						$filter[$field['name']] = phpgw::get_var($field['name']);
					}
				}
			}

			$values = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'allrows'=>$this->allrows),$filter);

			$this->total_records = $this->so->total_records;
			$this->uicols = $this->so->uicols;

			return $values;
		}

		public function read_single($data=array())
		{
			$custom_fields = false;
			if($GLOBALS['phpgw']->locations->get_attrib_table($this->location_info['acl_app'], $this->location_info['acl_location']))
			{
				$custom_fields = true;
				$values = array();
				$values['attributes'] = $this->custom->find($this->location_info['acl_app'], $this->location_info['acl_location'], 0, '', 'ASC', 'attrib_sort', true, true);
			}

			if(isset($data['id']) && $data['id'])
			{
				$values = $this->so->read_single($data, $values);
			}
			if($custom_fields)
			{
				$values = $this->custom->prepare($values, $this->location_info['acl_app'],$this->location_info['acl_location'], $data['view']);
			}
			return $values;
		}

		public function save($data,$action='',$values_attribute = array())
		{
			if(is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($action=='edit')
			{
				if ($data['id'] != '')
				{

					$receipt = $this->so->edit($data,$values_attribute);
				}
			}
			else
			{
				$receipt = $this->so->add($data,$values_attribute);
			}

			return $receipt;
		}

		/**
		 * Get a list from and tag the selected item
		 *
		 * @param array $data
		 *
		 * @return array with information to include in forms
		 */

		public function get_list($data)
		{

			if(isset($data['role']) && $data['role'] == 'parent')
			{
				$values = $this->so->get_tree2($data);
			}
			else
			{
				$values = $this->so->get_list($data);
			}

			if(isset($data['selected']) && is_array($data['selected']))
			{
				foreach ($values as &$entry)
				{
					$entry['selected'] = in_array($entry['id'],$data['selected']);
				}
			}
			else
			{
				foreach ($values as &$entry)
				{
					$entry['selected'] = isset($data['selected']) && trim($data['selected']) == trim($entry['id']) ? 1 : 0;
				}
			}
			return $values;
		}

		public function delete($id)
		{
			$this->so->delete($id);
		}

		public function get_children2($id, $level, $reset = false)
		{
			return $this->so->get_children2($id, $level, $reset);
		}
	}
