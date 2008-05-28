<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage property
	* @category core
 	* @version $Id: class.uiresponsible.inc.php 732 2008-02-10 16:21:14Z sigurd $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * ResponsibleMatrix - handles automated assigning of tasks based on (physical)location and category.
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */

	class property_boresponsible
	{
		protected $use_session;
		public $start;
		public $location;
		public $query;
		public $total_records = 0;
		public $cat_id;
		public $allrows;
		public $acl_location = '.admin';

		/**
		* Constructor
		*
		* @param bool $session whether to use stored session data or not
		*/

		public function __construct($session = false)
		{
			$this->so				= CreateObject('property.soresponsible');
			$this->so->acl_location = $this->acl_location;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			if(array_key_exists('query', $_POST) || array_key_exists('query', $_GET))
			{
				$this->query = phpgw::get_var('query');
			}
			if(array_key_exists('start', $_POST) || array_key_exists('start', $_GET))
			{
				$this->start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			}
			if(array_key_exists('location', $_POST) || array_key_exists('location', $_GET))
			{
				$this->location = phpgw::get_var('location');
			}
			if(array_key_exists('sort', $_POST) || array_key_exists('sort', $_GET))
			{
				$this->sort = phpgw::get_var('sort');
			}
			if(array_key_exists('order', $_POST) || array_key_exists('order', $_GET))
			{
				$this->order = phpgw::get_var('order');
			}
			if(array_key_exists('allrows', $_POST) || array_key_exists('allrows', $_GET))
			{
				$this->allrows = phpgw::get_var('allrows');
			}
			if(array_key_exists('cat_id', $_POST) || array_key_exists('cat_id', $_GET))
			{
				$this->cat_id = phpgw::get_var('cat_id');
			}


			$this->cats					= CreateObject('phpgwapi.categories');
			$this->cats->app_name		= "property{$this->location}";
			$this->dateformat 			= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		}

		/**
		* Save sessiondata for later use
		*
		* @param array $data session data to store
		*
		* @return void
		*/

		public function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data', 'responsible', $data);
			}
		}

		/**
		* Read previously saved sessiondata
		*
		* @return void
		*/

		private function read_sessiondata()
		{
			$referer = parse_url(phpgw::get_var('HTTP_REFERER', 'string', 'SERVER'));
			parse_str($referer['query'], $referer_out);
			$self = parse_url(phpgw::get_var('QUERY_STRING', 'string', 'SERVER'));
			parse_str($self['path'], $self_out);

			if(isset($referer_out['menuaction']) && isset($self_out['menuaction']) && $referer_out['menuaction'] == $self_out['menuaction'])
			{
				$data = $GLOBALS['phpgw']->session->appsession('session_data', 'responsible');
			}

			$this->cat_id		= isset($data['cat_id']) ? $data['cat_id'] : '';
			$this->sort			= isset($data['sort']) ? $data['sort'] : '';
			$this->order		= isset($data['order']) ? $data['order'] : '';
			$this->start		= isset($data['start']) ? $data['start'] : '';
			$this->query		= isset($data['query']) ? $data['query'] : '';
			$this->location		= isset($data['location']) ? $data['location'] : '';
		}

		/**
		* Read list of responsibility types
		*
		* @return array of types
		*/

		public function read_type()
		{
			$categories = $this->cats->return_array('', 0, false);
			$filter = array();
			if($categories)
			{
				foreach($categories as $cat)
				{
					$filter[] = $cat['id'];
				}
			}
			else
			{
				$filter[] = 0;
			}

			$values = $this->so->read_type(array('start' => $this->start, 'query' => $this->query, 'sort' => $this->sort,
												'order' => $this->order, 'location' => $this->location, 'allrows'=>$this->allrows,
												'filter' => $filter));
			$this->total_records = $this->so->total_records;
			
			foreach($values as & $value)
			{
				$category = $this->cats->return_single($value['cat_id']);
				$value['category']		= $category[0]['name'];
				$value['app_name']		= $category[0]['app_name'];
				$value['created_by']	= $GLOBALS['phpgw']->accounts->id2name($value['created_by']);
				$value['created_on']	= $GLOBALS['phpgw']->common->show_date($value['created_on'], $this->dateformat);
			
			}

			return $values;
		}

		/**
		* Read list of contacts given responsibilities within locations
		*
		* @param integer $type_id filter by responsibility type
		*
		* @return array of contacts_responsibilities
		*/

		public function read_contact($type_id = '')
		{
			$values = $this->so->read_contact(array('start' => $this->start, 'query' => $this->query, 'sort' => $this->sort,
												'order' => $this->order, 'allrows'=>$this->allrows, 'type_id' => $type_id));

			$this->total_records = $this->so->total_records;
			$soadmin_entity	= CreateObject('property.soadmin_entity');
			$solocation 	= CreateObject('property.solocation');
			$bocontact		= CreateObject('addressbook.boaddressbook');

			foreach($values as & $value)
			{
				$contact					= $bocontact->get_principal_persons_data($value['contact_id']);
				$value['contact_name']		= $contact['per_full_name'];
				$value['created_by']		= $GLOBALS['phpgw']->accounts->id2name($value['created_by']);
				$value['created_on']		= $GLOBALS['phpgw']->common->show_date($value['created_on'], $this->dateformat);
				if(isset($value['expired_on']) && $value['expired_on'])
				{
					$value['expired_by']	= $GLOBALS['phpgw']->accounts->id2name($value['expired_by']);
					$value['expired_on']	= $GLOBALS['phpgw']->common->show_date($value['expired_on'], $this->dateformat);
				}
				$value['active_from']		= $GLOBALS['phpgw']->common->show_date($value['active_from'], $this->dateformat);
				$value['active_to']			= $GLOBALS['phpgw']->common->show_date($value['active_to'], $this->dateformat);
				if(isset($value['p_cat_id']) && $value['p_cat_id'])
				{
					$value['p_cat_name']	= $soadmin_entity->read_category_name($value['p_entity_id'], $value['p_cat_id']);
					$value['item']			= "{$value['p_cat_name']}::{$value['p_num']}";
				}
				$value['location_data']		= $solocation->read_single($value['location_code']);
			
			}
			return $values;
		}


		/**
		* Save responsibility type
		*
		* @param array $values values to be stored/edited and referencing ID if editing
		*
		* @return array $receip with result on the action(failed/success)
		*/

		public function save_type($values)
		{
			if (isset($values['id']) && $values['id'])
			{
				$receipt = $this->so->edit_type($values);
			}
			else
			{
				$receipt = $this->so->add_type($values);
			}
			return $receipt;
		}

		/**
		* Save responsibility contact
		*
		* @param array $values values to be stored/edited and referencing ID if editing
		*
		* @return array $receip with result on the action(failed/success)
		*/

		public function save_contact($values)
		{
			phpgw::import_class('phpgwapi.datetime');

			if(isset($values['active_from']))
			{
				$values['active_from'] = phpgwapi_datetime::date_to_timestamp($values['active_from']);
			}
			if(isset($values['active_to']))
			{
				$values['active_to'] = phpgwapi_datetime::date_to_timestamp($values['active_to']);
			}
			if (isset($values['id']) && $values['id'])
			{
				$receipt = $this->so->edit_contact($values);
			}
			else
			{
				$receipt = $this->so->add_contact($values);
			}
			return $receipt;
		}

		/**
		* Read single responsibility type
		*
		* @param integer $id ID of responsibility type
		*
		* @return array holding data of responsibility type
		*/

		public function read_single_type($id)
		{
			$values = $this->so->read_single_type($id);
			$values['entry_date'] = $GLOBALS['phpgw']->common->show_date($values['created_on'], $this->dateformat);
			return $values;
		}

		/**
		* Read single contact for responsibility type at physical location
		*
		* @param integer $id ID of responsibility type
		*
		* @return array holding data of contact for responsibility type
		*/

		public function read_single_contact($id)
		{
			$values 				= $this->so->read_single_contact($id);
			$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['created_on'], $this->dateformat);
			$contacts				= CreateObject('phpgwapi.contacts');
			$contact_data			= $contacts->read_single_entry($values['contact_id'], array
															(
																'n_given'	=>'n_given',
																'n_family'	=>'n_family',
																'email'		=>'email'
															));
			$values['contact_name'] = "{$contact_data[0]['n_family']}, {$contact_data[0]['n_given']}";
			$values['active_from']	= $GLOBALS['phpgw']->common->show_date($values['active_from'], $this->dateformat);
			$values['active_to']	= $GLOBALS['phpgw']->common->show_date($values['active_to'], $this->dateformat);

			$solocation 	= CreateObject('property.solocation');
			$values['location_data'] = $solocation->read_single($values['location_code']);
			
			if($values['p_num'])
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');

				$values['p'][$values['p_entity_id']]['p_num']=$values['p_num'];
				$values['p'][$values['p_entity_id']]['p_entity_id']=$values['p_entity_id'];
				$values['p'][$values['p_entity_id']]['p_cat_id']=$values['p_cat_id'];
				$values['p'][$values['p_entity_id']]['p_cat_name'] = $soadmin_entity->read_category_name($values['p_entity_id'], $values['p_cat_id']);
			}

			return $values;
		}

		/**
		* Delete single responsibility type
		*
		* @param integer $id ID of responsibility type
		*
		* @return void
		*/

		public function delete_type($id)
		{
			$this->so->delete_type($id);
		}

		/**
		* Get the responsibility for a particular category conserning a given location or item
		*
		* @param array $values containing cat_id, location_code and optional item-information
		*
		* @return user_id
		*/

		public function get_responsible($values = array())
		{
			$contact_id 	= $this->so->get_responsible($values);
			return $this->so->get_contact_user_id($contact_id);
		}
	}
