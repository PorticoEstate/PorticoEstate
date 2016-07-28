<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package rental
	 * @subpackage application
	 * @version $Id: $
	 */

	class rental_soapplication
	{

		protected $db;
		protected $like;
		protected $join;
		protected $left_join;
		protected $sort_field;
		protected $skip_limit_query;
		protected static $so;

		public function __construct()
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->like = & $this->db->like;
			$this->join = & $this->db->join;
			$this->left_join = & $this->db->left_join;
			$this->sort_field = null;
			$this->skip_limit_query = null;
		}

		/**
		 * Begin transaction
		 *
		 * @return integer|bool current transaction id
		 */
		public function transaction_begin()
		{
			return $this->db->transaction_begin();
		}

		/**
		 * Complete the transaction
		 *
		 * @return bool True if sucessful, False if fails
		 */
		public function transaction_commit()
		{
			return $this->db->transaction_commit();
		}

		/**
		 * Rollback the current transaction
		 *
		 * @return bool True if sucessful, False if fails
		 */
		public function transaction_abort()
		{
			return $this->db->transaction_abort();
		}

		/**
		 * Marshal values according to type
		 * @param $value the value
		 * @param $type the type of value
		 * @return database value
		 */
		protected function marshal( $value, $type )
		{
			if ($value === null)
			{
				return 'NULL';
			}
			else if ($type == 'int')
			{
				if ($value == '')
				{
					return 'NULL';
				}
				return intval($value);
			}
			else if ($type == 'float')
			{
				return str_replace(',', '.', $value);
			}
			else if ($type == 'field')
			{
				return $this->db->db_addslashes($value);
			}
			return "'" . $this->db->db_addslashes($value) . "'";
		}

		/**
		 * Unmarchal database values according to type
		 * @param $value the field value
		 * @param $type	a string dictating value type
		 * @return the php value
		 */
		protected function unmarshal( $value, $type )
		{
			if ($type == 'bool')
			{
				return (bool)$value;
			}
			elseif ($type == 'int')
			{
				return (int)$value;
			}
			elseif ($value === null || $value == 'NULL')
			{
				return null;
			}
			elseif ($type == 'float')
			{
				return floatval($value);
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
		protected function get_query_count( $sql )
		{
			$result = $this->db->query($sql);
			if ($result && $this->db->next_record())
			{
				return $this->unmarshal($this->db->f('count', true), 'int');
			}
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *  
		 * @return the class instance.
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.soapplication');
			}
			return self::$so;
			
		}

		/**
		 * Convenience method for getting one single object. Calls get() with the
		 * specified id as a filter.
		 *
		 * @param $id int with id of object to return.
		 * @return object with the specified id, null if not found.
		 */
		public function get_single( int $id )
		{
			$objects = $this->get(0, 0, '', false, '', '', array($this->get_id_field_name() => $id));
			if (count($objects) > 0)
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
		 * @param $ascending bool true for ascending sort on sort field, false
		 * for descending.
		 * @param $search_for string with free text search query.
		 * @param $search_type string with the query type.
		 * @param $filters array with key => value of filters.
		 * @return array of objects. May return an empty
		 * array, never null. The array keys are the respective index numbers.
		 */
		public function get( int $start_index, int $num_of_objects, string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters )
		{
			$results = array();   // Array to store result objects

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
		public function get_count( string $search_for, string $search_type, array $filters )
		{
			return $this->get_query_count($this->get_query('', false, $search_for, $search_type, $filters, true));
		}

		/**
		 * Implementing classes must return the name of the field used in the query
		 * returned from get_query().
		 * 
		 * @return string with name of id field.
		 */
		protected function get_id_field_name()
		{
			
		}

		/**
		 * Returns SQL for retrieving matching objects or object count.
		 *
		 * @param $start_index int with index of first object.
		 * @param $num_of_objects int with max number of objects to return.
		 * @param $sort_field string representing the object field to sort on.
		 * @param $ascending bool true for ascending sort on sort field, false
		 * for descending.
		 * @param $search_for string with free text search query.
		 * @param $search_type string with the query type.
		 * @param $filters array with key => value of filters.
		 * @param $return_count bool telling to return only the count of the
		 * matching objects, or the objects themself.
		 * @return string with SQL.
		 */
		protected function get_query( string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters, bool $return_count )
		{

		}

		protected function populate( int $object_id, &$object )
		{

		}

		protected function add( &$object )
		{
			_debug_array($object);

		}

		protected function update( $object )
		{
			
		}

		/**
		 * Store the object in the database.  If the object has no ID it is assumed to be new and
		 * inserted for the first time.  The object is then updated with the new insert id.
		 */
		public function store( &$object )
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