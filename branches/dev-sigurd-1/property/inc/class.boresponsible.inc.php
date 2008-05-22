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

		public function __construct($session = false)
		{
			$this->acl_location 	= '.admin';
			$this->so				= CreateObject('property.soresponsible');
			$this->so->acl_location = $this->acl_location;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			if(array_key_exists('query',$_POST) || array_key_exists('query',$_GET))
			{
				$this->query = phpgw::get_var('query');
			}
			if(array_key_exists('start',$_POST) || array_key_exists('start',$_GET))
			{
				$this->start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			}
			if(array_key_exists('location',$_POST) || array_key_exists('location',$_GET))
			{
				$this->location = phpgw::get_var('location');
			}
			if(array_key_exists('sort',$_POST) || array_key_exists('sort',$_GET))
			{
				$this->sort = phpgw::get_var('sort');
			}
			if(array_key_exists('order',$_POST) || array_key_exists('order',$_GET))
			{
				$this->order = phpgw::get_var('order');
			}
			if(array_key_exists('allrows',$_POST) || array_key_exists('allrows',$_GET))
			{
				$this->allrows = phpgw::get_var('allrows');
			}
			if(array_key_exists('cat_id',$_POST) || array_key_exists('cat_id',$_GET))
			{
				$this->cat_id = phpgw::get_var('cat_id');
			}


			$this->cats					= CreateObject('phpgwapi.categories');
			$this->cats->app_name		= "property{$this->location}";
			$this->dateformat 			= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		}

		public function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','responsible', $data);
			}
		}

		private function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data', 'responsible');
			$this->cat_id		= isset($data['cat_id']) ? $data['cat_id'] : '';
			$this->sort			= isset($data['sort']) ? $data['sort'] : '';
			$this->order		= isset($data['order']) ? $data['order'] : '';
			$this->start		= isset($data['start']) ? $data['start'] : '';
			$this->query		= isset($data['query']) ? $data['query'] : '';
			$this->location		= isset($data['location']) ? $data['location'] : '';
			$this->allrows		= isset($data['allrows']) ? $data['allrows'] : '';
		}

		public function get_acl_location()
		{
			return $this->acl_location;
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
				$value['created_on']	= $GLOBALS['phpgw']->common->show_date($value['created_on'],$this->dateformat);
			
			}

			return $values;
		}

		/**
		* Read list of contacts given responsibilities within locations
		*
		* @return array of contacts_responsibilities
		*/

		public function read_contact()
		{

$values=array();
//			$values = $this->so->read_contact(array('start' => $this->start, 'query' => $this->query, 'sort' => $this->sort,
//												'order' => $this->order, 'location' => $this->location, 'allrows'=>$this->allrows,
//												'filter' => $filter));
			$this->total_records = $this->so->total_records;
			

			return $values;
		}


		/**
		* Save responsibility type
		*
		* @param array $values  values to be stored/edited and referencing ID if editing
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
		* @param array $values  values to be stored/edited and referencing ID if editing
		*
		* @return array $receip with result on the action(failed/success)
		*/

		public function save_contact($values)
		{
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
		* @param integer $id  ID of responsibility type
		*
		* @return array holding data of responsibility type
		*/

		public function read_single_type($id)
		{
			$values = $this->so->read_single_type($id);
			$values['entry_date'] = $GLOBALS['phpgw']->common->show_date($values['created_on'],$this->dateformat);
			return $values;
		}

		/**
		* Delete single responsibility type
		*
		* @param integer $id  ID of responsibility type
		*
		* @return void
		*/

		function delete_type($id)
		{
			$this->so->delete_type($id);
		}
	}
?>
