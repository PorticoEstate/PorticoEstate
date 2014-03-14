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
 	* @version $Id: class.socommon.inc.php 11257 2013-08-10 11:40:56Z sigurdne $
	*/

	abstract class logistic_socommon
	{
		protected $db;
		protected $like;
		protected $join;
		protected $left_join;
		protected $global_lock = false;

		public function __construct()
		{
			$this->db			= & $GLOBALS['phpgw']->db;
			$this->like			= & $this->db->like;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
		}

		/**
		 * Marshal values according to type
		 * @param $value the value
		 * @param $type the type of value
		 * @return database value
		 */
		protected function marshal($value, $type)
		{
			if($value === null)
			{
				return 'NULL';
			}
			else if($type == 'int')
			{
				if($value == '')
				{
					return 'NULL';
				}
				return intval($value);
			}
			else if($type == 'float')
			{
				return str_replace(',', '.', $value);
			}
			else if($type == 'field')
			{
				return $this->db->db_addslashes($value);
			}
			else if($type == 'string' & $value == '')
			{
				return 'NULL';
			}

			return "'" . $this->db->db_addslashes($value) . "'";
		}

		/*

		/**
		 * Unmarchal database values according to type
		 * @param $value the field value
		 * @param $type	a string dictating value type
		 * @return the php value
		 */
		protected function unmarshal($value, $type)
		{
			if($type == 'bool')
			{
				return (boolean)$value;
			}
			else if($type == 'boolean')
			{
				return (boolean) $value;
			}
			else if($type == 'int')
			{
				return (int) $value;
			}
			else if($type == 'float')
			{
				return (float) $value;
			}
			else if($type == 'string')
			{
				return (string) $value;
			}
			else if($value === null || $value == 'NULL')
			{
				return null;
			}
			return $value;
		}

		/**
		 * Get the count of the specified query. Query must return a signel column
		 * called count.
		 * 
		 * @param $sql the sql query
		 * @return the count value
		 */
		protected function get_query_count($sql)
		{
			$result = $this->db->query($sql);
			if($result && $this->db->next_record())
			{
				return $this->unmarshal($this->db->f('count', true), 'int');
			} 
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *  
		 * @return the class instance.
		 */
		public abstract static function get_instance();

		/**
		 * Convenience method for getting one single object. Calls get() with the
		 * specified id as a filter.
		 * 
		 * @param $id int with id of object to return.
		 * @return object with the specified id, null if not found.
		 */
		public function get_single(int $id)
		{
			$objects = $this->get(null, null, null, null, null, null, array($this->get_id_field_name() => $id));
			if(count($objects) > 0)
			{
				$keys = array_keys($objects);
				return $objects[$keys[0]];
			}
			return null;
		}

		/**
		 * Method for retrieving the db-object (security "forgotten")
		 */
		public function get_db()
		{
			return $this->db;
		}

		/**
		 * Method for retreiving objects.
		 * 
		 * @param $start_index int with index of first object.
		 * @param $num_of_objects int with max number of objects to return.
		 * @param $sort_field string representing the object field to sort on.
		 * @param $ascending boolean true for ascending sort on sort field, false
		 * for descending.
		 * @param $search_for string with free text search query.
		 * @param $search_type string with the query type.
		 * @param $filters array with key => value of filters.
		 * @return array of objects. May return an empty
		 * array, never null. The array keys are the respective index numbers.
		 */
		public function get(int $start_index, int $num_of_objects, string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters)
		{
			$results = array();			// Array to store result objects
			$map = array();				// Array to hold number of records per target object
			$check_map = array();		// Array to hold the actual number of record read per target object
			$object_ids = array(); 		// All of the object ids encountered
			$added_object_ids = array();// All of the added objects ids

			// Retrieve information about the table name and the name and alias of id column
			// $break_on_limit - 	flag indicating whether to break the loop when the number of records 
			// 						for all the result objects are traversed
			$id_field_name_info = $this->get_id_field_name(true);
			if(is_array($id_field_name_info))
			{
				$break_on_limit = true;
				$id_field_name = $id_field_name_info['translated'];
			}
			else
			{
				$break_on_limit = false;
				$id_field_name = $id_field_name_info;
			}

			// Special case: Sort on id field. Always changed to the id field name.
			// $break_when_num_of_objects_reached - flag indicating to break the loop when the number of 
			//		results are reached and we are sure that the records are ordered by the id
			if($sort_field == null || $sort_field == 'id' || $sort_field == '')
			{
				$sort_field = $id_field_name;
				$break_when_num_of_objects_reached = true;
			}
			else
			{
				$break_when_num_of_objects_reached = false;
			}

			// Only allow positive start index
			if($start_index < 0)
			{
				$start_index = 0;
			}

			// test-input for break on ordered queries
			$db	 = & $this->db;
			$db2 = clone $this->db;

			$sql = $this->get_query($sort_field, $ascending, $search_for, $search_type, $filters, false);

			$sql_parts = explode('1=1',$sql); // Split the query to insert extra condition on test for break

//			$db->set_fetch_single(true);

			$db->query($sql,__LINE__, __FILE__);

			while ($db->next_record()) // Runs through all of the results
			{
				$should_populate_object = false; // Default value - we won't populate object
				$result_id = $this->unmarshal($db->f($id_field_name), 'int'); // The id of object

				if(in_array($result_id, $added_object_ids)) // Object with this id already added
				{
					$should_populate_object = true; // We should populate this object as we already have it in our result array
				}
				else // Object isn't already added to array
				{
					if(!in_array($result_id, $object_ids)) // Haven't already added this id
					{
						$object_ids[] = $result_id; // We have to add the new id
					}
					// We have to check if we should populate this object
					if(count($object_ids) > $start_index) // We're at index above start index
					{
						if($num_of_objects == null || count($results) < $num_of_objects) // We haven't found all the objects we're looking for
						{
							$should_populate_object = true; // We should populate this object
							$added_object_ids[] = $result_id; // We keep the id
						}
					}
				}
				if($should_populate_object)
				{
					$result = &$results[$result_id];
					$results[$result_id] = $this->populate($result_id,$result);
					$last_result_id = $result_id;
					$map[$result_id] = (int)$map[$result_id] +1;
				}

				//Stop looping when array not sorted on other then id and wanted number of results is reached
				if(count($results) == $num_of_objects  && $last_result_id != $result_id && $break_when_num_of_objects_reached)
				{
					break;
				}
				// else stop looping when wanted number of results is reached all records for result objects are read
				else if($break_on_limit && (count($results) == $num_of_objects)  && $last_result_id != $result_id)
				{
					$id_ok = 0;
					foreach ($map as $_result_id => $_count)
					{
						if(!isset($check_map[$_result_id]))
						{
							// Query the number of records for the specific object in question
							$sql2 = "{$sql_parts[0]} 1=1 AND {$id_field_name_info['table']}.{$id_field_name_info['field']} = {$_result_id} {$sql_parts[1]}";
							$db2->query($sql2,__LINE__, __FILE__);
							$db2->next_record();
							$check_map[$_result_id] = $db2->num_rows();
						}
						if(	$check_map[$_result_id] == $_count )
						{
							$id_ok++;
						}
					}
					if($id_ok == $num_of_objects)
					{
						break;
					}
				}
			}
			$db->set_fetch_single(false);
			return $results;
		}

		/**
		 * Returns count of matching objects.
		 * 
		 * @param $search_for string with free text search query.
		 * @param $search_type string with the query type.
		 * @param $filters array with key => value of filters.
		 * @return int with object count.
		 */
		public function get_count(string $search_for, string $search_type, array $filters)
		{
			return $this->get_query_count($this->get_query(null, null, $search_for, $search_type, $filters, true));
		}

		/**
		 * Implementing classes must return the name of the field used in the query
		 * returned from get_query().
		 * 
		 * @return string with name of id field.
		 */
		protected abstract function get_id_field_name();

		/**
		 * Returns SQL for retrieving matching objects or object count.
		 * 
		 * @param $start_index int with index of first object.
		 * @param $num_of_objects int with max number of objects to return.
		 * @param $sort_field string representing the object field to sort on.
		 * @param $ascending boolean true for ascending sort on sort field, false
		 * for descending.
		 * @param $search_for string with free text search query.
		 * @param $search_type string with the query type.
		 * @param $filters array with key => value of filters.
		 * @param $return_count boolean telling to return only the count of the
		 * matching objects, or the objects themself.
		 * @return string with SQL.
		 */

		protected abstract function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count);

		protected abstract function populate(int $object_id, &$object);

		protected abstract function add(&$object);

		protected abstract function update($object);

		/**
		* Store the object in the database.  If the object has no ID it is assumed to be new and
		* inserted for the first time.  The object is then updated with the new insert id.
		*/

		public function store(&$object)
		{
			if ($object->validates())
			{
				if ($object->get_id() > 0)
				{
					// We can assume this composite came from the database since it has an ID. Update the existing row
					return $this->update($object);
				}
				else
				{
					// This object does not have an ID, so will be saved as a new DB row
					return $this->add($object);
				}
			}

			// The object did not validate
			return false;
		}
	}
