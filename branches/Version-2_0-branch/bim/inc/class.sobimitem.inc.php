<?php
	phpgw::import_class('bim.sobim');
	phpgw::import_class('bim.bimitem');

	interface sobimitem extends sobim
	{
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

	public function getBimItemAttributeValue($bimItemGuid, $attribute);

	public function retrieveItemsByModelId();

	public function setModelId($modelId);
	}

	class sobimitem_impl implements sobimitem
	{
	/* @var phpgwapi_db_ */

	private $db;
	private $modelId;

		public function __construct(& $db)
		{
		// $this->db = & $GLOBALS['phpgw']->db;
		$this->db = $db;
		$db->Halt_On_Error = 'no';
		$db->Exception_On_Error = true;
	}
	/*
	 * @return Array an array of BimItem objects
	 */

		public function getAll()
		{
		$itemTable = self::bimItemTable;
		$typeTable = self::bimTypeTable;
			$sql = "SELECT $itemTable.id, fm_bim_type.name AS type,  $itemTable.guid,  $itemTable.json_representation " .
			"FROM public. $itemTable,  public.$typeTable " .
					"WHERE   $itemTable.type = $typeTable.id";
		$bimItemArray = array();
		$this->db->query($sql);
		while($this->db->next_record())
		{
				$bimItem = new BimItem($this->db->f('id'), $this->db->f('guid'), $this->db->f('type'), $this->db->f('json_representation'));
			array_push($bimItemArray, $bimItem);
		}

		return $bimItemArray;
	}

		public function getBimItem($bimObjectGuid)
		{
		$itemTable = self::bimItemTable;
		$typeTable = self::bimTypeTable;
			$sql = "SELECT $itemTable.id, fm_bim_type.name AS type, $itemTable.guid, $itemTable.json_representation, $itemTable.model " .
			"FROM public.$itemTable,  public.$typeTable " .
					"WHERE  $itemTable.type = $typeTable.id " .
        			"AND $itemTable.guid ='$bimObjectGuid'";
			$this->db->query($sql, __LINE__, __FILE__);
			if($this->db->num_rows() == 0)
			{
			throw new Exception('Item not found!');
			}
			else
			{
			$this->db->next_record();
				return new BimItem($this->db->f('id'), $this->db->f('guid'), $this->db->f('type'), $this->db->f('json_representation'), $this->db->f('model'));
		}
	}
	
		public function addBimItem($bimItem)
		{
		/* @var $bimItem BimItem */
		if(!$bimItem->getModelId())
		{
			throw new Exception('ModelId not set');
		}
		
			$this->db->query("SELECT id as type FROM " . self::bimTypeTable . " WHERE name = '" . $bimItem->getType(), __LINE__, __FILE__);
		$this->db->next_record();
		$type = $this->db->f('type');
			$id = $this->db->next_id('fm_bim_item', array('type' => $type));

			$sql = "INSERT INTO " . self::bimItemTable . " (type, id, guid, json_representation, model) values ($type, $id,";
	//	$sql = $sql."(select id from ".self::bimTypeTable." where name = '".$bimItem->getType()."'),";
			$sql = $sql . "'" . $bimItem->getGuid() . "', '" . $this->db->db_addslashes($bimItem->getXml()) . "', " . $bimItem->getModelId() . ")";
			try
			{
				if(is_null($this->db->query($sql, __LINE__, __FILE__)))
				{
				throw new Exception('Query to add item was unsuccessful');
				}
				else
				{
				return $this->db->num_rows();
			}
		}
			catch(PDOException $e)
			{
				throw new BimDataException("Could not add item", $e);
			}
	}
	/*
	 * Checks if the bim item exists
	 * @param string GUID
	 * @return boolean
	 */

		public function checkIfBimItemExists($guid)
		{
		$resultAlias = 'test_item_count';
			$sql = "SELECT count(id) as $resultAlias from public." . self::bimItemTable . " where guid = '$guid'";
		
			if(is_null($this->db->query($sql, __LINE__, __FILE__)))
			{
			throw new Exception('Query to check items was unsuccessful');
			}
			else
			{
			$this->db->next_record();
			$rowCountOfItemTypes =  $this->db->f($resultAlias);
			return (bool)$rowCountOfItemTypes;
		}
	}
	/*
	 * @return number of affected rows
	 */

		public function deleteBimItem($guid)
		{
			$sql = "Delete from public." . self::bimItemTable . " where guid = '$guid'";
			if(is_null($this->db->query($sql, __LINE__, __FILE__)))
			{
			throw new Exception('Query to delete item was unsuccessful');
			}
			else
			{
			return $this->db->num_rows();
		}
	}
	
		public function updateBimItem($bimItem)
		{
			if(!$this->checkIfBimItemExists($bimItem->getGuid()))
			{
			throw new Exception("Item does not exist!");
		}
			$sql = "Update " . self::bimItemTable . " set json_representation='" . $bimItem->getXml() . "' where guid='" . $bimItem->getGuid() . "'";
		
			if(is_null($this->db->query($sql, __LINE__, __FILE__)))
			{
			throw new Exception("Error updating xml of bim item!");
			}
			else
			{
			return (bool)$this->db->num_rows();
		}
	}
	/*
	 * Searches the xml representation and returns the values of any attributes that have the specified name
	 * If there are multiple elements with the same name, all of their value's will be returned
	 * Note: the name can be written in xpath format in relation to it's parent, so instead of 'name',
	 * you could write 'attributes/name'
	 * @access public
	 * @param string $bimItemGuid the guid of the item
	 * @param string $attribute the name of the attribute
	 * @throws Exception if nothing is found
	 * $return array results
	 */

		public function getBimItemAttributeValue($bimItemGuid, $attribute)
		{
		$columnAlias = "attribute_values";
		$itemTable = self::bimItemTable;
		//$sql = "select xpath('descendant-or-self::*[$attribute]/$attribute/text()', (select json_representation from fm_bim_data where guid='$bimItemGuid'))";
		$sql = "select array_to_string(xpath('descendant-or-self::*[$attribute]/$attribute/text()', (select json_representation from $itemTable where guid='$bimItemGuid')), ',') as $columnAlias";
			$this->db->query($sql, __LINE__, __FILE__);
			if($this->db->num_rows() == 0)
			{
			throw new Exception('Error!');
			}
			else
			{
			$this->db->next_record();
				$result = $this->db->f($columnAlias, true);
			return preg_split('/,/', $result);
			//$match; // xpath result from database will look like: '{data1, data2, data3}', or '{}' for no results
			//preg_match('/^\{(.*)\}$/', $result, $match);
				/* if(!$match[1]) {
				throw new Exception('Attribute not found!');
			} else {
				return preg_split('/,/', $match[1]);
				  } */
		}
	}
	/*
	 * Needs the modelId field set
	 */

		public function retrieveItemsByModelId()
		{
			if(empty($this->modelId))
			{
			throw new InvalidArgumentException("Missing modelId!");
		} 
		$itemTable = self::bimItemTable;
		$typeTable = self::bimTypeTable;
		$bimItems = array();
			$sql = "select $itemTable.id, (select name from fm_bim_type where $itemTable.type = $typeTable.id) as type, $itemTable.guid from $itemTable where $itemTable.model =" . $this->modelId;
			try
			{
				$this->db->query($sql, __LINE__, __FILE__);
				if($this->db->num_rows() == 0)
				{
				return null;
				}
				else
				{
				while($this->db->next_record())
				{
						$bimItem = new BimItem($this->db->f('id'), $this->db->f('guid'), $this->db->f('type'));
					array_push($bimItems, $bimItem);
				}
				return $bimItems;
			}
			}
			catch(Exception $e)
			{
			throw $e;
		}
	}
	
		public function setModelId($modelId)
		{
		$this->modelId = $modelId;
	}

		public function getModelId()
		{
		return $this->modelId;
	}
	}