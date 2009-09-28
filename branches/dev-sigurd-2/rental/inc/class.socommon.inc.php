<?php
abstract class rental_socommon
{
	protected $db;
	protected $like;
	protected $join;
	protected $left_join;
	protected static $so;
	
	public function __construct()
	{
		$this->db           = clone $GLOBALS['phpgw']->db;
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
			return intval($value);
		}
		return "'" . $this->db->db_addslashes($value) . "'";
	}

	/**
	 * Unmarchal database values according to type
	 * @param $value the field value
	 * @param $type	a string dictating value type
	 * @return the php value
	 */
	protected function unmarshal($value, $type)
	{
		if($value === null || $value == 'NULL')
		{
			return null;
		}
		else if($type == 'int')
		{
			return intval($value);
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

	/**
	 * Convenience method for getting one single object. Calls get() with the
	 * specified id as a filter.
	 * 
	 * @param $id int with id of object to return.
	 * @return object with the specified id, null if not found.
	 */
	public function get_single(int $id)
	{
		$objects = $this->get(null, null, null, null, null, null, array('id' => $id), false);
		if(count($objects) > 0)
		{
			return $objects[0];
		}
		return null;
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
		$results = array();
		$reult_ids = array(); // We store the ids here to know how to deal with the start index and number of objects
		$sql = $this->get_query($sort_field, $ascending, $search_for, $search_type, $filters, false);
		$this->db->query($sql,__LINE__, __FILE__);
		$counter = 1;
		if($start_index < 0){
			$start_index = 0;
		}
		
		while ($this->db->next_record()) // Runs through all of the results
		{	
			$result_id = $this->unmarshal($this->db->f($this->get_id_field_name(), true), 'int');
			$result = &$results[$result_id];
			$results[$result_id] = $this->populate($result_id,$result);
			if($num_of_objects != null && count($results) > ($start_index + $num_of_objects)) // We've found as many objects as we want (+ 1)
			{
				array_pop($results); // We remove the last object as we don't need it
				break;
			}
		}
		return array_slice($results, $start_index);
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
	
	protected abstract function add(&$object);
	
	protected abstract function update($object);
	
	protected abstract function populate(int $object_id, &$object);
	
	protected abstract function get_id_field_name();
	
	/**
	* Store the object in the database.  If the object has no ID it is assumed to be new and
	* inserted for the first time.  The object is then updated with the new insert id.
	*/
	public function store($object)
	{
		if ($object->validates()) {
			if ($object->get_id() > 0) {
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
?>