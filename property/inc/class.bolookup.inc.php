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

		function __construct( $session = false )
		{
			$this->so = CreateObject('property.solookup');
			$this->solocation = CreateObject('property.solocation');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query = phpgw::get_var('query');
			$sort = phpgw::get_var('sort');
			$order = phpgw::get_var('order');
			$filter = phpgw::get_var('filter', 'int');
			$cat_id = phpgw::get_var('cat_id', 'int');
			$district_id = phpgw::get_var('district_id', 'int');
			$allrows = phpgw::get_var('allrows', 'bool');

			$this->start = $start ? $start : 0;
			$this->query = isset($query) ? $query : $this->query;
			$this->sort = isset($sort) && $sort ? $sort : '';
			$this->order = isset($order) && $order ? $order : '';
			$this->filter = isset($filter) && $filter ? $filter : '';
			$this->district_id = isset($district_id) && $district_id ? $district_id : '';
			$this->cat_id = isset($cat_id) && $cat_id ? $cat_id : '';
			$this->allrows = isset($allrows) && $allrows ? $allrows : '';
		}

		function save_sessiondata( $data )
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data', 'lookup', $data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data', 'lookup');

			//_debug_array($data);

			$this->start = $data['start'];
			//	$this->query	= $data['query'];
			$this->filter = $data['filter'];
			$this->sort = $data['sort'];
			$this->order = $data['order'];
			$this->cat_id = $data['cat_id'];
			$this->district_id = $data['district_id'];
		}

		/**
		 * Read list of contacts from the addressbook
		 *
		 * @return array of contacts
		 */
		function read_addressbook_old( $data = array() )
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$limit = 15;
			}

			$limit = $data['allrows'] ? 0 : $limit;

			$fields = array
				(
				'per_first_name',
				'per_last_name',
				'owner',
				'contact_id',
			);

			if ($this->cat_id && $this->cat_id != 0)
			{
				$category_filter = $this->cat_id;
			}
			else
			{
				$category_filter = -3;
			}

			$addressbook = CreateObject('addressbook.boaddressbook');

			$criteria = $addressbook->criteria_contacts(1, $category_filter, 'person', $data['query'], $fields_search);
			$this->total_records = $addressbook->get_count_persons($criteria);

			$contacts = $addressbook->get_persons($fields, $data['start'], $limit, $data['order'], $data['dir'], '', $criteria);

			$accounts = $GLOBALS['phpgw']->accounts->get_list();
			$user_contacts = array();

			$socommon = CreateObject('property.socommon');
			$prefs = array();
			foreach ($accounts as $account)
			{
				if (isset($account->person_id) && $account->person_id)
				{
					$user_contacts[] = $account->person_id;

					$prefs[$account->person_id] = $socommon->create_preferences('property', $account->id);
				}
			}

//_debug_array($prefs);die();
			foreach ($contacts as &$contact)
			{
				$comms = $addressbook->get_comm_contact_data($contact['contact_id'], $fields_comms = '', $simple = false);
				$contact['contact_name'] = "{$contact['per_last_name']}, {$contact['per_first_name']}";

				if (is_array($comms) && count($comms))
				{
					$contact['email'] = isset($comms[$contact['contact_id']]['work email']) && $comms[$contact['contact_id']]['work email'] ? $comms[$contact['contact_id']]['work email'] : $prefs[$contact['contact_id']]['email'];
					$contact['wphone'] = isset($comms[$contact['contact_id']]['work phone']) && $comms[$contact['contact_id']]['work phone'] ? $comms[$contact['contact_id']]['work phone'] : '';
					$contact['mobile'] = isset($comms[$contact['contact_id']]['mobile (cell) phone']) && $comms[$contact['contact_id']]['mobile (cell) phone'] ? $comms[$contact['contact_id']]['mobile (cell) phone'] : $prefs[$contact['contact_id']]['cellphone'];
				}
				if (in_array($contact['contact_id'], $user_contacts))
				{
					$contact['is_user'] = 'X';

					$contact['email'] = isset($contact['email']) && $contact['email'] ? $contact['email'] : $prefs[$contact['contact_id']]['email'];
					$contact['wphone'] = isset($contact['wphone']) && $contact['wphone'] ? $contact['wphone'] : '';
					$contact['mobile'] = isset($contact['mobile']) && $contact['mobile'] ? $contact['mobile'] : $prefs[$contact['contact_id']]['cellphone'];
				}
			}

			return $contacts;
		}
		function read_addressbook( $data = array() )
		{
			$accounts = & $GLOBALS['phpgw']->accounts;
			$users = $accounts->get_list('accounts', $data['start'], $data['sort'], $data['order'], $data['query'], $data['offset'], array('active' => true));
			$values = array();
			$addressbook = CreateObject('addressbook.boaddressbook');
			$socommon = CreateObject('property.socommon');

			foreach ($users as $account_id => $user)
			{

				$comms = $addressbook->get_comm_contact_data($user->person_id, $fields_comms = '', $simple = false);

				if(!empty($comms[$user->person_id]['work email']))
				{
					$email = $comms[$user->person_id]['work email'];
				}
				else
				{
					$prefs = $socommon->create_preferences('property', $user->id);
					$email  = $prefs['email'];
				}
				if(!empty($comms[$user->person_id]['mobile (cell) phone']))
				{
					$mobile = $comms[$user->person_id]['mobile (cell) phone'];
				}
				else
				{
					$prefs = $socommon->create_preferences('property', $user->id);
					$mobile  = $prefs['cellphone'];
				}

				$values[] = array(
					'id'			=> $user->id,
					'lid'			=> $user->lid,
					'fullname'		=> $user->__toString(),
					'firstname'		=> $user->firstname,
					'lastname'		=> $user->lastname,
					'enabled'		=> $user->enabled,
					'contact_id'	=> $user->person_id,
					'email'			=> $email,
					'mobile'		=> $mobile
				);

			}
	

			$this->total_records = $accounts->total;

			return $values;
		}

		/**
		 * Read list of organisation from the addressbook
		 *
		 * @return array of contacts
		 */
		function read_organisation( $data = array() )
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$limit = 15;
			}

			$limit = $data['allrows'] ? 0 : $limit;

			$fields = array
				(
				'contact_id',
				'org_name'
			);

			if ($this->cat_id && $this->cat_id != 0)
			{
				$category_filter = $this->cat_id;
			}
			else
			{
				$category_filter = -3;
			}

			$addressbook = CreateObject('addressbook.boaddressbook');

			$qfield = 'org';

			$criteria = $addressbook->criteria_contacts(PHPGW_CONTACTS_ALL, PHPGW_CONTACTS_CATEGORIES_ALL, array(), '', $fields);
			$token_criteria = $addressbook->criteria_contacts($access = 1, $category_filter, $qfield, $data['query'], $fields);

			$orgs = $addressbook->get_orgs($fields, $data['start'], $limit, $orderby = 'org_name', $sort = 'ASC', $criteria = '', $token_criteria);

			$this->total_records = $addressbook->total;

			foreach ($orgs as &$contact)
			{
				$comms = $addressbook->get_comm_contact_data($contact['contact_id'], $fields_comms = '', $simple = false);
				if (is_array($comms) && count($comms))
				{
					$contact['email'] = isset($comms[$contact['contact_id']]['work email']) ? $comms[$contact['contact_id']]['work email'] : '';
					$contact['wphone'] = isset($comms[$contact['contact_id']]['work phone']) ? $comms[$contact['contact_id']]['work phone'] : '';
				}
			}

			return $orgs;
		}

		function read_vendor( $data = array() )
		{
			$sogeneric = CreateObject('property.sogeneric');

			$location_info = $sogeneric->get_location_info('vendor');

			$data['order'] = $data['order'] ? $data['order'] : 'org_name';
			$data['sort'] = $data['sort'] ? $data['sort'] : 'ASC';

			$filter = $data['filter'];
			if (!$filter)
			{
				foreach ($location_info['fields'] as $field)
				{
					if (isset($field['filter']) && $field['filter'])
					{
						if ($field['name'] == 'member_of')
						{
							$filter[$field['name']] = $this->cat_id;
						}
						else
						{
							$filter[$field['name']] = phpgw::get_var($field_name);
						}
					}
				}
			}
			$filter['active'] = 1;
			$data['filter'] = $filter;

			$values = $sogeneric->read($data);

			$this->total_records = $sogeneric->total_records;

			return $values;
		}

		function read_b_account( $data )
		{
			$b_account = $this->so->read_b_account(array('start' => $data['start'], 'query' => $data['query'],
				'sort' => $data['sort'], 'order' => $data['order'],
				'filter' => $data['filter'], 'cat_id' => $this->cat_id, 'allrows' => $data['allrows'],
				'role' => $data['role'], 'parent' => $data['parent']));
			$this->total_records = $this->so->total_records;

			return $b_account;
		}

		function read_phpgw_user( $data = array() )
		{
			if ($data['acl_app'] && $data['acl_location'] && $data['acl_required'])
			{
				$users = $GLOBALS['phpgw']->acl->get_user_list_right($data['acl_required'], $data['acl_location'], $data['acl_app']);
				$user_list = array();
				foreach ($users as $user)
				{

					if ($data['query'] && (!preg_match("/{$data['query']}/i", $user['account_lastname']) || !preg_match("/{$data['query']}/i", $user['account_lastname'])))
					{
						continue;
					}

					$user_list[] = array
						(
						'id' => $user['account_id'],
						'last_name' => $user['account_lastname'],
						'first_name' => $user['account_firstname'],
					);
				}
				$this->total_records = count($user_list);

				$allrows = $data['allrows'];
				$start = $data['start'];
				$total_records = $this->total_records;
				$num_rows = $data['results'];

				if ($allrows)
				{
					$out = $user_list;
				}
				else
				{
					if ($total_records > $num_rows)
					{
						$page = ceil(( $start / $total_records ) * ($total_records / $num_rows));
						$values_part = array_chunk($user_list, $num_rows);
						$out = $values_part[$page];
					}
					else
					{
						$out = $user_list;
					}
				}
				return $out;
			}
			else
			{

				$phpgw_user = $this->so->read_phpgw_user($data);
				$this->total_records = $this->so->total_records;

				return $phpgw_user;
			}
		}

		function read_ecodimb( $data = array() )
		{
			$config = CreateObject('phpgwapi.config', 'property');
			$config->read();

			$custom_criteria = array();
			if (isset($config->config_data['invoice_acl']) && $config->config_data['invoice_acl'] == 'dimb')
			{
				$custom_criteria = array('dimb_role_user');
			}

			$ecodimb = CreateObject('property.sogeneric');
			$ecodimb->get_location_info('dimb', false);
			$values = $ecodimb->read(array('start' => $data['start'], 'query' => $data['query'],
				'sort' => $data['sort'], 'order' => $data['order'],
				'allrows' => $data['allrows'], 'custom_criteria' => $custom_criteria));

			$this->total_records = $ecodimb->total_records;

			return $values;
		}
	}