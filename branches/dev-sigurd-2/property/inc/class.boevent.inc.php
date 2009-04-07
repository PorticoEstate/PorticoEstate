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
 	* @version $Id: class.bocategory.inc.php 2530 2009-03-08 19:53:28Z sigurd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_boevent
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
	//		$this->so 			= CreateObject('property.socategory');
			$this->custom 		= CreateObject('property.custom_fields');//& $this->so->custom;
	//		$this->bocommon 	= CreateObject('property.bocommon');
			$this->sbox 		= CreateObject('phpgwapi.sbox');

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

			//$this->location_info = $this->so->get_location_info($type, $type_id);

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

		public function delete($id)
		{
			$this->so->delete($id);
		}

		public function get_rpt_type_list($selected='')
		{
			$rpt_type = array
			(
				0	=> 'None',
				1	=> 'Daily',
				2	=> 'Weekly',
				3	=> 'Monthly (by day)',
				4	=> 'Monthly (by date)',
				5	=> 'Yearly'
			);


			return $this->sbox->getArrayItem('values[rpt_type]', $selected, $rpt_type);
		}

		public function get_rpt_day_list($selected=array())
		{
			$rpt_day = array
			(
				1		=> 'Sunday',
				2		=> 'Monday',
				4		=> 'Tuesday',
				8		=> 'Wednesday',
				16		=> 'Thursday',
				32		=> 'Friday',
				64		=> 'Saturday'
			);

			$title = lang('(for weekly)');
			$i = 0; $boxes = '';
			foreach ($rpt_day as $mask => $name)
			{
				$boxes .= '<input type="checkbox" title = "' . $title . '"name="values[rpt_day][]" value="'.$mask.'"'.(isset($selected[$mask]) && $selected[$mask] & $mask ? ' checked' : '').'></input> '.lang($name)."\n";
				if (++$i == 5) $boxes .= '<br />';
			}
			return $boxes;
		}

		public function get_responsible($selected = '')
		{
			$responsible = CreateObject('property.soresponsible');
			
			$location = '.invoice.dimb';//phpgw::get_var('location');
			$values = $responsible->read_type(array('start' => 0, 'query' =>'', 'sort' => '',
												'order' => '', 'location' => $location, 'allrows'=>true,
												'filter' => ''));

			$list = array(0 => lang('none'));
			foreach($values as $entry)
			{
				$list[$entry['id']] = $entry['name'];
			}
			return $this->sbox->getArrayItem('values[responsible]', $selected, $list, true);
		}
	}
