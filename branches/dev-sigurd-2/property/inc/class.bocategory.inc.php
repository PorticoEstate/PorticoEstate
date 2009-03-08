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

	class property_bocategory
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $location_info = array();

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

		function __construct($session=false)
		{
			$this->so 			= CreateObject('property.socategory');
			$this->socommon 	= CreateObject('property.socommon');
			$this->custom 		= & $this->so->custom;

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
			$type				= phpgw::get_var('type');
			$type_id			= phpgw::get_var('type_id', 'int');

			$this->start		= $start ? $start : 0;
			$this->query		= isset($_REQUEST['query']) ? $query : $this->query;
			$this->sort			= isset($_REQUEST['sort']) ? $sort : $this->sort;
			$this->order		= isset($_REQUEST['order']) ? $order : $this->order;
			$this->filter		= isset($_REQUEST['filter']) ? $filter : $this->filter;
			$this->cat_id		= isset($_REQUEST['cat_id'])  ? $cat_id :  $this->cat_id;
			$this->allrows		= isset($allrows) ? $allrows : false;

			$this->location_info = $this->so->get_location_info($type, $type_id);

		}

		public function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','category',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','category');

	//		_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->allrows	= $data['allrows'];
		}

		public function get_location_info($type,$type_id)
		{
			return $this->so->get_location_info($type,$type_id);
		}

		public function read()
		{
			$values = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;
			$this->uicols = $this->so->uicols;

			return $values;
		}

		public function read_single($data=array())
		{
			$custom_fields = false;
			if($GLOBALS['phpgw']->locations->get_attrib_table('property', $this->location_info['acl_location']))
			{
				$custom_fields = true;
				$values = array();
				$values['attributes'] = $this->custom->find('property', $this->location_info['acl_location'], 0, '', 'ASC', 'attrib_sort', true, true);
			}

			if(isset($data['id']) && $data['id'])
			{
				$values = $this->so->read_single($data, $values);
			}
			if($custom_fields)
			{
				$values = $this->custom->prepare($values, 'property',$this->location_info['acl_location'], $data['view']);
			}
			return $values;
		}

		public function save($data,$action='',$values_attribute = array())
		{
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

		public function delete($id)
		{
			$this->so->delete($id);
		}
	}

