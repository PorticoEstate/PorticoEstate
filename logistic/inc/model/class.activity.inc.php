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

				protected static $id;
				protected static $name;
				protected static $parent_id;
				protected static $project_id;
				protected static $start_date;
				protected static $end_date;
				protected static $responsible_user_id;
				protected static $update_user;
				protected static $update_date;

				/**
				* Constructor.  Takes an optional ID.  If a contract is created from outside
				* the database the ID should be empty so the database can add one according to its logic.
				*
				* @param int $id the id of this project
				*/
				public function __construct(int $id = null)
				{
					echo "1";
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
					return $this->title;
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

				public function set_update_user($user_id)
				{
					$this->update_user = $user_id;
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
					return array(
						'id' => $this->get_id(),
						'name' => $this->get_name(),
						'parent_id' => $this->get_parent_id(),
						'project_id' => $this->get_project_id(),
						'start_date' => $this->get_start_date(),
						'end_date' => $this->get_end_date(),
						'responsible_user_id' => $this->get_responsible_user_id()
					);
				}
		}
