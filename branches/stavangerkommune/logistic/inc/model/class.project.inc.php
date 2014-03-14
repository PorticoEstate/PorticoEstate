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

  class logistic_project extends logistic_model
	{
	  public static $so;

		protected $id;
		protected $name;
		protected $project_type_id;
		protected $project_type_label;
		protected $description;
		protected $start_date;
		protected $end_date;
		
		protected $error_msg_array = array();

		/**
		* Constructor.  Takes an optional ID.  If a contract is created from outside
		* the database the ID should be empty so the database can add one according to its logic.
		*
		* @param int $id the id of this project
		*/
		public function __construct(int $id = null)
		{
			$this->id = (int)$id;
		}

		public function set_id($id)
		{
			$this->id = $id;
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_name($name)
		{
			$this->name = $name;
		}

		public function get_name()
		{
			return $this->name;
		}

		public function set_project_type_id($project_type_id)
		{
			$this->project_type_id = $project_type_id;
		}

		public function get_project_type_id()
		{
			return $this->project_type_id;
		}

		public function set_project_type_label($project_type_label)
		{
			$this->project_type_label = $project_type_label;
		}

		public function get_project_type_label()
		{
			return $this->project_type_label;
		}

		public function set_description($description)
		{
			$this->description = $description;
		}

		public function get_description()
		{
			return $this->description;
		}
		
		public function set_start_date($start_date)
		{
			$this->start_date = $start_date;
		}

		public function get_start_date()
		{
			return $this->start_date;
		}

		public function set_end_date($end_date)
		{
			$this->end_date = $end_date;
		}

		public function get_end_date()
		{
			return $this->end_date;
		}
		
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
				self::$so = CreateObject('logistic.soproject');
			}

			return self::$so;
		}

		public function serialize()
		{
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			
			return array(
				'id' => $this->get_id(),
				'name' => $this->get_name(),
				'project_type_id' => $this->get_project_type_id(),
				'project_type_label' => $this->get_project_type_label(),
				'description' => $this->get_description(),
				'start_date' => $this->get_start_date() ? date($date_format, $this->get_start_date()): '',
				'end_date' => $this->get_end_date() ? date($date_format, $this->get_end_date()): '',
			);
		}
				
		public function populate()
		{
			$this->set_id( phpgw::get_var('id') );
			$this->set_name( phpgw::get_var('name') );
			$this->set_project_type_id( phpgw::get_var('project_type_id') );
			$this->set_description( phpgw::get_var('description') );
			
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
			
			// Validate NAME
		  if( empty($this->name) )
		  {
		  	$status = false;
		  	$this->error_msg_array['name'] = "error_msg_1";
		  }
		  
			// Validate DESCRIPTION
		  if( empty($this->description) )
		  {
		  	$status = false;
		  	$this->error_msg_array['description'] = "error_msg_1";
		  }
		  
			// Validate PROJECT TYPE ID
		  if( empty($this->project_type_id) )
		  {
		  	$status = false;
		  	$this->error_msg_array['project_type_id'] = "error_msg_1";
		  }
		  
		   // Validate START DATE
			if( empty($this->start_date) )
		  {
		  	$status = false;
		  	$this->error_msg_array['start_date'] = "error_msg_1";
		  }

		  // Validate END DATE
			if( !empty($this->end_date) && ($this->end_date < $this->start_date) )
		  {
		   	$status = false;
		  	$this->error_msg_array['end_date'] = "error_msg_3";
		  }  
	
		  return $status;
		}
	}
