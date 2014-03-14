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
			$this->so 			= CreateObject('property.solookup');
			$this->solocation	= CreateObject('property.solocation');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$filter					= phpgw::get_var('filter', 'int');
			$cat_id					= phpgw::get_var('cat_id', 'int');
			$district_id			= phpgw::get_var('district_id', 'int');
			$allrows				= phpgw::get_var('allrows', 'bool');

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
				$limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$limit = 15;
			}

			$limit		= $this->allrows ? 0 : $limit;

			$fields = array
				(
					'per_first_name',
					'per_last_name',
					'owner',
					'contact_id',
				);

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

			$contacts = $addressbook->get_persons($fields, $this->start, $limit, $this->order, $this->sort, '', $criteria);

			$accounts = $GLOBALS['phpgw']->accounts->get_list();
			$user_contacts = array();

			$socommon			= CreateObject('property.socommon');
			$prefs = array();
			foreach($accounts as $account)
			{
				if(isset($account->person_id) && $account->person_id)
				{
					$user_contacts[] = $account->person_id;

					$prefs[$account->person_id] = $socommon->create_preferences('property',$account->id);
				}
			}

//_debug_array($prefs);die();
			foreach($contacts as &$contact)
			{
				$comms = $addressbook->get_comm_contact_data($contact['contact_id'], $fields_comms='', $simple=false);

				if ( is_array($comms) && count($comms) )
				{
					$contact['email'] = isset($comms[$contact['contact_id']]['work email']) && $comms[$contact['contact_id']]['work email'] ? $comms[$contact['contact_id']]['work email'] :$prefs[$contact['contact_id']]['email'];
					$contact['wphone'] = isset($comms[$contact['contact_id']]['work phone']) && $comms[$contact['contact_id']]['work phone'] ?  $comms[$contact['contact_id']]['work phone'] : '';
					$contact['mobile'] = isset($comms[$contact['contact_id']]['mobile (cell) phone']) &&  $comms[$contact['contact_id']]['mobile (cell) phone'] ?  $comms[$contact['contact_id']]['mobile (cell) phone'] : $prefs[$contact['contact_id']]['cellphone'];
				}
				if (in_array($contact['contact_id'], $user_contacts) )
				{
					$contact['is_user'] = 'X';

					$contact['email'] = isset($contact['email']) && $contact['email'] ? $contact['email'] :$prefs[$contact['contact_id']]['email'];
					$contact['wphone'] = isset($contact['wphone']) && $contact['wphone'] ?  $contact['wphone'] : '';
					$contact['mobile'] = isset($contact['mobile']) && $contact['mobile'] ?  $contact['mobile'] : $prefs[$contact['contact_id']]['cellphone'];
				}
			}

			return $contacts;
		}

		/**
		 * Read list of organisation from the addressbook
		 *
		 * @return array of contacts
		 */

		function read_organisation()
		{
			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] &&
				$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$limit = 15;
			}

			$limit		= $this->allrows ? 0 : $limit;

			$fields = array
				(
					'contact_id',
					'org_name'
				);

			if($this->cat_id && $this->cat_id != 0)
			{
				$category_filter = $this->cat_id;
			}
			else
			{
				$category_filter = -3;
			}

			$addressbook	= CreateObject('addressbook.boaddressbook');

			$qfield = 'org';

			$criteria		= $addressbook->criteria_contacts(PHPGW_CONTACTS_ALL,PHPGW_CONTACTS_CATEGORIES_ALL,array(),'',$fields);
			$token_criteria	= $addressbook->criteria_contacts($access = 1, $category_filter, $qfield, $this->query, $fields);

			$orgs = $addressbook->get_orgs($fields, $this->start, $limit, $orderby='org_name', $sort='ASC', $criteria='', $token_criteria);

			$this->total = $addressbook->total;

			foreach($orgs as &$contact)
			{
				$comms = $addressbook->get_comm_contact_data($contact['contact_id'], $fields_comms='', $simple=false);
				if ( is_array($comms) && count($comms) )
				{
					$contact['email'] = isset($comms[$contact['contact_id']]['work email']) ? $comms[$contact['contact_id']]['work email'] : '';
					$contact['wphone'] = isset($comms[$contact['contact_id']]['work phone']) ?  $comms[$contact['contact_id']]['work phone'] : '';
				}
			}

			return $orgs;
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

		function read_vendor($filter = array())
		{
			$sogeneric 	= CreateObject('property.sogeneric');

			$location_info = $sogeneric->get_location_info('vendor');
			
			$this->order = $this->order ? $this->order : 'org_name';
			$this->sort = $this->sort ? $this->sort : 'ASC';

			if (! $filter )
			{
				foreach ( $location_info['fields'] as $field )
				{
					if (isset($field['filter']) && $field['filter'])
					{
						if($field['name'] == 'member_of')
						{
							$filter[$field['name']] = phpgw::get_var('cat_id');
						}
						else
						{
							$filter[$field['name']] = phpgw::get_var($field_name);
						}
					}
				}
			}

			$values = $sogeneric->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'allrows'=>$this->allrows),$filter);

			$this->total_records = $sogeneric->total_records;

			return $values;
		}

		function read_b_account($data)
		{
			$b_account = $this->so->read_b_account(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id, 'allrows' => $this->allrows,
				'role' => $data['role'], 'parent' => $data['parent']));
			$this->total_records = $this->so->total_records;

			return $b_account;
		}

		function read_street()
		{
			$street = $this->so->read_street(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id, 'allrows' => $this->allrows));
			$this->total_records = $this->so->total_records;

			return $street;
		}

		function read_tenant()
		{
			$tenant = $this->so->read_tenant(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id, 'allrows' => $this->allrows));
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
			$project_group	= CreateObject('property.sogeneric');
			$project_group->get_location_info('project_group',false);
			$values = $project_group->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'type' => 'project_group','allrows'=>$this->allrows));

			$this->total_records = $project_group->total_records;

			return $values;
		}
		function read_ecodimb()
		{
			$config				= CreateObject('phpgwapi.config','property');
			$config->read();

			$custom_criteria = array();
			if(isset($config->config_data['invoice_acl']) && $config->config_data['invoice_acl'] == 'dimb')
			{
				$custom_criteria = array('dimb_role_user');
			}

			$ecodimb	= CreateObject('property.sogeneric');
			$ecodimb->get_location_info('dimb',false);
			$values = $ecodimb->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'allrows'=>$this->allrows, 'custom_criteria' => $custom_criteria));

			$this->total_records = $ecodimb->total_records;

			return $values;
		}
	}
