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
 	* @version $Id: class.bolookup.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bolookup
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
		(
			'read'				=> True,
			'read_single'		=> True,
			'save'				=> True,
			'delete'			=> True,
			'check_perms'		=> True
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

		function property_bolookup($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('property.solookup');
			$this->solocation = CreateObject('property.solocation');


			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start			= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query			= phpgw::get_var('query');
			$sort			= phpgw::get_var('sort');
			$order			= phpgw::get_var('order');
			$filter			= phpgw::get_var('filter', 'int');
			$cat_id			= phpgw::get_var('cat_id', 'int');
			$district_id	= phpgw::get_var('district_id', 'int');

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
			if(!empty($filter))
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
			if(isset($district_id))
			{
				$this->district_id = $district_id;
			}
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


		function read_addressbook()
		{
//			$contact = $this->so->read_addressbook(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
//											'filter' => $this->filter,'cat_id' => $this->cat_id));
//			$this->total_records = $this->so->total_records;


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


			$addressbook	= CreateObject('addressbook.boaddressbook');

			$criteria = $addressbook->criteria_contacts(1, -3, 'person', $this->query, $fields_search);

//_debug_array($criteria);
			$entries = $addressbook->get_persons($fields, $this->limit, $this->start, $this->order, $this->sort, '', $criteria);

//_debug_array($entries);
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
											'filter' => $this->filter,'cat_id' => $this->cat_id));
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
	}
?>
