<?php
	/*
	 * This class is made to be unit tested,
	 * do not add any global variables into this code!
	 * Any global variables needed should be injected via methods
	 */
	interface sobimtype extends sobim {
		/*
    	 * @param string type
    	 */
    	public function getBimObjectType($type);
    	/*
    	 * @param $description max 512char string, may be null
    	 * @param $name non empty string
    	 * @throws exception if object type already exists
    	 * @return int id of new object type
    	 */
    	public function addBimObjectType($name, $description);
    	
    	public function deleteBimObjectType($name);
    	
    	public function updateBimObjectTypeDescription($name, $newDescription);
    	
	}
	
	class sobimtype_impl implements sobimtype {
		
		/* @var phpgwapi_db_ */
		private $db;

        public function __construct(& $db) {
           // $this->db = & $GLOBALS['phpgw']->db;
           $this->db = $db;
        }
		/*
         * @return null|BimType
         */
        public function getBimObjectType($type) {
        	$resultAlias = 'test_type_count';
			$sql  = "SELECT  *  FROM public.".self::bimTypeTable." WHERE ".self::bimTypeTable.".name = '".$type."'";
			$q = $this->db->query($sql,__LINE__,__FILE__);
			if(is_null($q)) {
				throw new Exception('Query to get object type was unsuccessful');
			}
	        if($this->db->num_rows() == 0) {
				return null;
			} else {
				$this->db->next_record();
				return new BimType($this->db->f('id'),$this->db->f('name'), $this->db->f('description'));
			}
        }
        
		/*
         * @return boolean
         * @throws exception if query fails
         */
        public function addBimObjectType($name, $description = null) {
        	if($this->getBimObjectType($name) != null) {
        		throw new Exception('Type already exists!');
        	}

			$location_id = $GLOBALS['phpgw']->locations->add($name, $description ? $description : $name , 'bim');

        	if(is_null($description))
        	{
        		$sql = "INSERT INTO ".self::bimTypeTable." (location_id, name) VALUES ($location_id, '$name')";
        	}
        	else
        	{
        		$description = $this->db->db_addslashes($description);
        		$sql = "INSERT INTO ".self::bimTypeTable." (location_id, name, description) VALUES ($location_id, '$name', '$description')";
        	} 

			if(is_null($this->db->query($sql,__LINE__,__FILE__) ))
			{
				throw new Exception("Error adding object type!");
			}
			else
			{
				return true;
			}
        }
        /*
         * @return boolean
         */
        public function deleteBimObjectType($name) {
        	$sql  = "Delete FROM public.".self::bimTypeTable." WHERE ".self::bimTypeTable.".name = '".$name."'";
       		if(is_null($this->db->query($sql,__LINE__,__FILE__) )){
				throw new Exception("Error deleting object type!");
			} else {
				return true;
			}
        }
        
        public function updateBimObjectTypeDescription($name, $newDescription) {
        	$sql = "Update ".self::bimTypeTable." set description='$newDescription' where name='".$name."'";
       	 	if(is_null($this->db->query($sql,__LINE__,__FILE__) )){
				throw new Exception("Error updating description of object type!");
			} else {
				return true;
			}
        }
	
	}
	
	class BimType {
    	private $id;
    	private $name;
    	private $description;
    	function __construct($id = null, $name = null, $description = null) {
    		$this->id = $id;
    		$this->name = $name;
    		$this->description = $description;
    	}
    	function getId() {
    		return $this->id;
    	}
    	function getName() {
    		return $this->name;
    	}
    	function getDescription() {
    		return $this->description;
    	}
    	function setId($id) {
    		$this->id = $id;
    	}
    	
    }
