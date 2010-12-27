<?php




interface sobim {
	 
	const bimItemTable = 'fm_bim_data';
	const bimTypeTable = 'fm_bim_type';
	/*
	 * @return array of BIM objects
	 */
	public function getAll();
	/*
	 * @param int id
	 * @return BIMItem
	 */
	public function getBimItem($bimObjectId);
	
	public function addBimItem($bimItem);
	public function deleteBimItem($guid);
	public function checkIfBimItemExists($guid);
	public function updateBimItem($bimItem);
}
class sobim_impl implements sobim
{
	/* @var phpgwapi_db_ */
	private $db;

	public function __construct(& $db) {
		// $this->db = & $GLOBALS['phpgw']->db;
		$this->db = $db;
	}
	/*
	 * @return Array an array of BimItem objects
	 */
	public function getAll() {
		$sql  = 'SELECT fm_bim_data.id, fm_bim_type.name AS type, fm_bim_data.guid, fm_bim_data.xml_representation '.
					'FROM public.fm_bim_data,  public.fm_bim_type '.
					'WHERE  fm_bim_data.type = fm_bim_type.id';
		$bimItemArray = array();
		$this->db->query($sql);
		while($this->db->next_record())
		{
			$bimItem = new BimItem($this->db->f('id'),$this->db->f('guid'), $this->db->f('type'), $this->db->f('xml_representation'));
			array_push($bimItemArray, $bimItem);
		}

		return $bimItemArray;
	}


	public function getBimItem($bimObjectGuid){
		$sql  = 'SELECT fm_bim_data.id, fm_bim_type.name AS type, fm_bim_data.guid, fm_bim_data.xml_representation '.
					'FROM public.fm_bim_data,  public.fm_bim_type '.
					'WHERE  fm_bim_data.type = fm_bim_type.id ' .
        			'AND fm_bim_data.guid =\''.$bimObjectGuid.'\'';
		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			throw new Exception('Item not found!');
		} else {
			$this->db->next_record();
			return new BimItem($this->db->f('id'),$this->db->f('guid'), $this->db->f('type'), $this->db->f('xml_representation'));
		}
	}
	
	public function addBimItem($bimItem) {
		/* @var $bimItem BimItem */
		
		$sql = "INSERT INTO ".self::bimItemTable." (type, guid, xml_representation) values (";
		$sql = $sql."(select id from ".self::bimTypeTable." where name = '".$bimItem->getType()."'),";
		$sql = $sql."'".$bimItem->getGuid()."', '".$bimItem->getXml()."')";
		if(is_null($this->db->query($sql,__LINE__,__FILE__))) {
			throw new Exception('Query to add item was unsuccessful');
		} else {
			return $this->db->num_rows();
		}
	}
	
	public function checkIfBimItemExists($guid) {
		$resultAlias = 'test_item_count';
		$sql = "SELECT count(id) as $resultAlias from public.".self::bimItemTable." where guid = '$guid'";
		
		if(is_null($this->db->query($sql,__LINE__,__FILE__))) {
			throw new Exception('Query to check items was unsuccessful');
		} else {
			$this->db->next_record();
			$rowCountOfItemTypes =  $this->db->f($resultAlias);
			return (bool)$rowCountOfItemTypes;
		}
	}
	/*
	 * @return number of affected rows
	 */
	public function deleteBimItem($guid) {
		$sql = "Delete from public.".self::bimItemTable." where guid = '$guid'";
		if(is_null($this->db->query($sql,__LINE__,__FILE__))) {
			throw new Exception('Query to delete item was unsuccessful');
		} else {
			return $this->db->num_rows();
		}
	}
	
	public function updateBimItem($bimItem) {
		if(!$this->checkIfBimItemExists($bimItem->getGuid())) {
			throw new Exception("Item does not exist!");
		}
		$sql = "Update ".self::bimItemTable." set xml_representation='$bimItem->getXml()' where guid='".$bimItem->getGuid()."'";
        if(is_null($this->db->query($sql,__LINE__,__FILE__) )){
			throw new Exception("Error updating xml of bim item!");
		} else {
			return (bool)$this->db->num_rows();
		}
	}



	/**
	 * Retreive any number of items.
	 * @param array $data
	 * @return array Array of zero or more items
	 */
	public function read(array $data)
	{

		$select_cols = array(
                'i.id',
                'i.group_id',
                'i.location_id',
                'i.vendor_id',
                'i.installed'
                );
                $from_tables = array('fm_item i');
                $joins = array(
                //$this->db->left_join.' fm_item_group g ON i.group_id = g.id',
                $this->db->left_join.' fm_vendor v ON i.vendor_id = v.id'
                );
                $where_clauses = array(' WHERE 1=1');

                if($specific_item_id) {
                	// FIXME Sanitize input!!
                	$where_clauses[] = "i.id = $specific_item_id";
                }

                $sql  = 'SELECT ' . implode($select_cols, ', ') .
                    ' FROM ' . implode($from_tables, ', ') .
                implode($joins, ' ') .
                implode($where_clauses, ' AND ');
                	
                $this->db->query($sql);
                $i = 0;
                while($this->db->next_record())
                {
                	$items[$i]['id']       = $this->db->f('id');
                	$items[$i]['group']    = $this->db->f('group_id');
                	$items[$i]['location'] = $this->db->f('location_id');
                	$items[$i]['vendor']   = $this->db->f('vendor_id');
                	$items[$i]['installed']= $this->db->f('installed');

                	$i++;
                }

                return $items;
	}
}

class BimItem {
	private $databaseId;
	private $guid;
	private $type;
	private $xml;
	 
	function __construct($databaseId = null, $guid = null, $type = null, $xml = null) {
		//$this->databaseId = (is_null($databaseId)) ? null : (int)$databaseId;
		$this->databaseId = (int)$databaseId;
		$this->guid =  $guid;
		$this->type = $type;
		$this->xml = $xml;
	}
	function getDatabaseId() {
		return $this->databaseId;
	}
	function setDatabaseId($databaseId) {
		$this->databaseId = $databaseId;
	}
	function getGuid() {
		return $this->guid;
	}
	function getType() {
		return $this->type;
	}
	function setType($type) {
		$this->type = $type;
	}
	function getXml() {
		return $this->xml;
	}
	function setXml($xml) {
		$this->xml = $xml;
	}
	 
}


