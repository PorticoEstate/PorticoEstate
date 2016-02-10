<?php

	/**
	 * phpGroupWare - logistic: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage logistic
	 * @version $Id $
	 */

	include_class('logistic', 'model', '/inc/model/');

	class logistic_requirement extends logistic_model
	{
		public static $so;

		protected $id;
		protected $activity_id;
		protected $start_date;
		protected $end_date;
		protected $no_of_items;
		protected $location_id;
		protected $create_user;

		protected $error_msg_array = array();
		
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 *
		 * @param int $id the id of this project
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int) $id;
		}

		public function set_id($id)
		{
			$this->id = $id;
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_no_of_items($no_of_items)
		{
			$this->no_of_items = $no_of_items;
		}

		public function get_no_of_items()
		{
			return $this->no_of_items;
		}

		public function set_location_id($location_id)
		{
			$this->location_id = $location_id;
		}

		public function get_location_id()
		{
			return $this->location_id;
		}

		public function set_activity_id($activity_id)
		{
			$this->activity_id = $activity_id;
		}

		public function get_activity_id()
		{
			return $this->activity_id;
		}

		public function set_end_date($end_date)
		{
			$this->end_date = $end_date;
		}

		public function get_end_date() { return $this->end_date; }

		public function set_start_date($start_date)
		{
			$this->start_date = $start_date;
		}

		public function get_start_date() { return $this->start_date; }

		public function set_create_user($create_user)
		{
			$this->create_user = $create_user;
		}

		public function get_create_user() { return $this->create_user; }

		public function get_error_msg_array() { return $this->error_msg_array; }
		
		public function set_error_msg_array( $error_msg_array )
		{
			$this->error_msg_array = $error_msg_array;
		}
		
		/**
		* Get a static reference to the storage object associated with this model object
		*
		* @return the storage object
		*/
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('logistic.sorequirement');
			}

			return self::$so;
		}

		public function serialize()
		{
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$entity_so	= CreateObject('property.soadmin_entity');
			$project_so = CreateObject('logistic.soproject');
			$loc_arr = $GLOBALS['phpgw']->locations->get_name($this->get_location_id());
			$entity_arr = explode('.',$loc_arr['location']);

			$entity = $entity_so->read_single($entity_arr[2]);
			$category = $entity_so->read_single_category($entity_arr[2],$entity_arr[3]);
			$entity_label = $entity['name'];
			$category_label = $category['name'];

			return array(
				'id' => $this->get_id(),
				'activity_id' => $this->get_activity_id(),
				'start_date' => $this->get_start_date() ? date($date_format, $this->get_start_date()): '',
				'end_date' => $this->get_end_date() ? date($date_format, $this->get_end_date()): '',
				'no_of_items' => $this->get_no_of_items(),
				'location_id' => $this->get_location_id(),
				'location_label' => $category_label,
			);
		}
		
		public function populate()
		{
			$this->set_id( phpgw::get_var('id') );
			$this->set_activity_id( phpgw::get_var('activity_id') );
			$this->set_no_of_items( phpgw::get_var('no_of_items') );
			$this->set_location_id( phpgw::get_var('location_id') );
			$this->set_create_user( phpgw::get_var('create_user') );
										
			if( $this->get_id() == '' | $this->get_id() == 0){
				$user_id = $GLOBALS['phpgw_info']['user']['id'];
				$this->set_create_user( $user_id );	
			}
			
			if(phpgw::get_var('start_date','string') != '')
			{
				$start_date_ts = phpgwapi_datetime::date_to_timestamp( phpgw::get_var('start_date','string') );
				$this->set_start_date($start_date_ts);
			}
										
			if( phpgw::get_var('end_date','string') != '')
			{
				$end_date_ts = phpgwapi_datetime::date_to_timestamp( phpgw::get_var('end_date','string') );
				$this->set_end_date($end_date_ts);
			}
		}
		
		public function validate()
		{
			$status = true;
			
			// Validate ACTIVITY
		  if( empty($this->activity_id) )
		  {
		  	$status = false;
		  	$this->error_msg_array['activity_id'] = "error_msg_1";
		  }
		  
			// Validate NUMBER OF ITEMS
		  if( empty($this->no_of_items) )
		  {
		  	$status = false;
		  	$this->error_msg_array['no_of_items'] = "error_msg_1";
		  }
		  
			// Validate LOCATION ID
		  if( empty($this->location_id) )
		  {
		  	$status = false;
		  	$this->error_msg_array['location_id'] = "error_msg_1";
		  }
	
		  // Validate START DATE
			if( empty($this->start_date) )
		  {
		  	$status = false;
		  	$this->error_msg_array['start_date'] = "error_msg_1";
		  }

		  // Validate END DATE
			if( empty($this->end_date) )
		  {
		   	$status = false;
		  	$this->error_msg_array['end_date'] = "error_msg_1";
		  }
		  else if( !empty($this->end_date) && ($this->end_date < $this->start_date) )
		  {
		   	$status = false;
		  	$this->error_msg_array['end_date'] = "error_msg_3";
		  }  
		  		  
		  return $status;
		}
	}
