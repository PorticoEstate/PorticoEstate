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

		class logistic_activity extends logistic_model
		{
				public static $so;

				protected $id;
				protected $name;
				protected $description;
				protected $parent_id;
				protected $project_id;
				protected $start_date;
				protected $end_date;
				protected $responsible_user_id;
				protected $create_user;
  		  protected $create_date,
				protected $update_user;
				protected $update_date;
				
				// Arrays
				protected $sub_activities = array();

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

				public function set_description($description)
				{
					$this->description = $description;
				}

				public function get_description()
				{
					return $this->description;
				}

				public function set_parent_id($parent_id)
				{
					$this->parent_id = $parent_id;
				}

				public function get_parent_id()
				{
					return $this->parent_id;
				}

				public function set_project_id($project_id)
				{
					$this->project_id = $project_id;
				}

				public function get_project_id()
				{
					return $this->project_id;
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

				public function set_responsible_user_id($responsible_user_id)
				{
					$this->responsible_user_id = $responsible_user_id;
				}

				public function get_responsible_user_id()
				{
					return $this->responsible_user_id;
				}

				public function set_update_user($update_user)
				{
					$this->update_user = $update_user;
				}

				public function get_update_user()
				{
					return $this->update_user;
				}

				public function set_update_date($date)
				{
					$this->update_date = $date;
				}

				public function get_update_date()
				{
					return $this->update_date;
				}
				
				public function set_create_user($create_user)
				{
					$this->create_user = $create_user;
				}

				public function get_create_user()
				{
					return $this->create_user;
				}

				public function set_create_date($create_date)
				{
					$this->create_date = $create_date;
				}

				public function get_create_date()
				{
					return $this->create_date;
				}
				
				public function set_sub_activities($sub_activities)
				{
					$this->sub_activities = $sub_activities;
				}
				
				public function get_sub_activities()
				{
					return $this->sub_activities;
				}

				/**
				* Get a static reference to the storage object associated with this model object
				*
				* @return the storage object
				*/
				public static function get_so()
				{
					if (self::$so == null) {
						self::$so = CreateObject('logistic.soactivity');
					}

					return self::$so;
				}

				public function serialize()
				{
					$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
					$project_name = $this->get_so()->get_project_name($this->get_project_id());
					$responsible_user = $this->get_so()->get_responsible_user($this->get_responsible_user_id());

					return array(
						'id' => $this->get_id(),
						'parent_id' => $this->get_parent_id(),
						'name' => $this->get_name(),
						'description' => $this->get_description(),
						'project_id' => $this->get_project_id(),
						'project_name' => $project_name,
						'start_date' => $this->get_start_date() ? date($date_format, $this->get_start_date()): '',
						'end_date' => $this->get_end_date() ? date($date_format, $this->get_end_date()): '',
						'responsible_user_id' => $responsible_user
					);
				}

				public function toArray()
				{
					$converted_obj_array = parent::toArray();
					
					$converted_obj_array['sub_activities'] = array();
					
					foreach($this->sub_activities as $activitiy)
					{
						// Checks if object to be converted is not the same as this object, 
						// otherwise an eternal loop will happen
						if($activitiy != $this)
						{
							$converted_obj_array['sub_activities'][] = $activitiy->toArray();
						}
					}
								
					return $converted_obj_array;
				}
		}
