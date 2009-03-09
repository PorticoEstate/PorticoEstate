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
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bolookup
	{
		public $start;
		public $query;
		public $filter;
		public $sort;
		public $order;
		public $cat_id;
		public $total_records = 0;

		function property_bolookup($session=false)
		{
			$this->so 		= CreateObject('property.solookup');
			$this->solocation = CreateObject('property.solocation');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start			= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query			= phpgw::get_var('query');
			$sort			= phpgw::get_var('sort');
			$order			= phpgw::get_var('order');
			$filter			= phpgw::get_var('filter', 'int');
			$cat_id			= phpgw::get_var('cat_id', 'int');
			$district_id	= phpgw::get_var('district_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');

			$this->start			= $start ? $start : 0;
			$this->query			= isset($query) ? $query : $this->query;
			$this->sort				= isset($sort) && $sort ? $sort : '';
			$this->order			= isset($order) && $order ? $order : '';
			$this->filter			= isset($filter) && $filter ? $filter : '';
			$this->district_id		= isset($district_id) && $district_id ? $district_id : '';
			$this->cat_id			= isset($cat_id) && $cat_id ? $cat_id : '';
			$this->allrows			= isset($allrows) && $allrows ? $allrows : '';
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','lookup',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','lookup');

			//_debug_array($data);

			$this->start	= $data['start'];
		//	$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->district_id	= $data['district_id'];
		}


		/**
		* Read list of contacts from the addressbook
		*
		* @return array of contacts
		*/

		function read_addressbook()
		{

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] &&
				$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$this->limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$this->limit = 15;
			}

		    $fields[0] = 'per_first_name';
		    $fields[1] = 'per_last_name';
		    $fields[2] = 'per_department';
		    $fields[3] = 'per_title';
		    $fields[4] = 'addr_add1';
		    $fields[5] = 'addr_city';
		    $fields['owner'] = 'owner';
		    $fields['contact_id'] = 'contact_id';

			if($this->cat_id && $this->cat_id != 0)
			{
				$category_filter = $this->cat_id;
			}
			else
			{
				$category_filter = -3;
			}

			$addressbook	= CreateObject('addressbook.boaddressbook');

			$criteria = $addressbook->criteria_contacts(1, $category_filter, 'person', $this->query, $fields_search);
			$this->total_records = $addressbook->get_count_persons($criteria);

			$entries = $addressbook->get_persons($fields, $this->limit, $this->start, $this->order, $this->sort, '', $criteria);

			return $entries;
		}


		/**
		* Get the the person data what you want
		*
		* @param array $fields The fields that you can see from person
		* @param integer $limit Limit of records that you want
		* @param integer $ofset Ofset of record that you want start
		* @param string $orderby The field which you want order
		* @param string $sort ASC | DESC depending what you want
		* @param mixed $criteria All criterias what you want
		* @param mixed $criteria_token same like $criteria but builded<br>with sql_criteria class, more powerfull
		* @return array with records
		*/
		function get_persons($fields, $start='', $limit='', $orderby='', $sort='', $criteria='', $token_criteria='')
		{
			$entries =  $this->so->get_persons($fields, $start, $limit, $orderby, $sort, $criteria, $token_criteria);
			if(is_array($entries))
			{
				foreach($entries as $data)
				{
					$persons[$data['contact_id']] = $data;
				}
			}
			else
			{
				$persons = array();
			}
			$this->total = $this->so->contacts->total_records;
			return $persons;
		}





		function read_vendor()
		{
			$vendor = $this->so->read_vendor(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id, 'allrows' => $this->allrows));
			$this->total_records = $this->so->total_records;

			return $vendor;
		}

		function read_b_account()
		{
			$b_account = $this->so->read_b_account(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id));
			$this->total_records = $this->so->total_records;

			return $b_account;
		}

		function read_street()
		{
			$street = $this->so->read_street(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id));
			$this->total_records = $this->so->total_records;

			return $street;
		}

		function read_tenant()
		{
			$tenant = $this->so->read_tenant(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id));
			$this->total_records = $this->so->total_records;

			return $tenant;
		}

		function read_ns3420()
		{
			$ns3420 = $this->so->read_ns3420(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id));
			$this->total_records = $this->so->total_records;

			return $ns3420;
		}

		function read_phpgw_user()
		{
			$phpgw_user = $this->so->read_phpgw_user(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id));
			$this->total_records = $this->so->total_records;

			return $phpgw_user;
		}

		function read_project_group()
		{
			$project_group	= CreateObject('property.socategory');
			$project_group->get_location_info('project_group',false);
			$values = $project_group->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'type' => 'project_group','allrows'=>$this->allrows));

			$this->total_records = $project_group->total_records;

			return $values;
		}
		function read_ecodimb()
		{
			$ecodimb	= CreateObject('property.socategory');
			$ecodimb->get_location_info('dimb',false);
			$values = $ecodimb->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));

			$this->total_records = $ecodimb->total_records;

			return $values;
		}
	}
