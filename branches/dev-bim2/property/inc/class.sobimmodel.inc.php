<?php

phpgw::import_class('property.bimExceptions');

/*
 * @see sobimmodel_impl
 */
interface sobimmodel extends sobim{
	 
	public function addBimModel();
	public function retrieveBimModelList();
	public function retrieveBimModelInformationById();
	public function removeBimModelByIdFromDatabase();
	/*
	 * @throws InvalidArgumentException If the arguments are not set 
	 * @throws ModelExistsException if the model does not exist
	 * @throws Exception When sql request fails
	 * @return boolean true if success
	 */
	public function removeBimModelFromDatabase();
	/*
	 * @return boolean
	 */
	public function checkIfModelExists();
	public function setModelName($name);
	public function getModelName();
	/*
	 * set virtual file id from database
	 */
	public function setVfsdatabaseid(int $id);
	public function getVfsdatabaseid();
	
	public function setModelId(int $id);
	public function getModelId();
	
	
}
class sobimmodel_impl implements sobimmodel
{
	
	private $db;
	private $modelName;
	private $vfs_database_id;
	private $modelId;

	public function __construct(& $db, $modelName = null, $vfs_id = null) {
		// $this->db = & $GLOBALS['phpgw']->db;
		$this->db = $db;
		$db->Halt_On_Error = 'no';
		$db->Exception_On_Error = true;
	}
	
	public function retrieveBimModelList() {
		$bimModelArray = array();
		$itemTable = self::bimItemTable;
		$modelTable = self::bimModelTable;
		$sql = "select model.id,model.name,vfs.created,vfs.size as filesize,vfs.name as filename,vfs.file_id as vfs_file_id,(select count(*) from $itemTable where model=model.id) as used_item_count  from $modelTable as model left join phpgw_vfs as vfs on model.vfs_file_id = vfs.file_id";
		if(is_null($this->db->query($sql,__LINE__,__FILE__))) {
			throw new Exception('Query to find bim models failed');
		} else {
			if($this->db->num_rows() == 0) {
				return null;
			} else {
				while($this->db->next_record())
				{
					$bimModel = $this->assembleBimModelFromCurrentDatabaseRecord();
					array_push($bimModelArray, $bimModel);
				}
			}
		}
		return $bimModelArray;
	}
	private function assembleBimModelFromCurrentDatabaseRecord() {
		$bimModel = new BimModel();
		$bimModel->setDatabaseId($this->db->f('id'));
		$bimModel->setName($this->db->f('name'));
		$bimModel->setCreationDate($this->db->f('created'));
		$bimModel->setFileSize($this->db->f('filesize'));
		$bimModel->setFileName($this->db->f('filename'));
		$bimModel->setUsedItemCount($this->db->f('used_item_count'));
		$bimModel->setVfsFileId($this->db->f('vfs_file_id'));
		return $bimModel;
	}
	/*
	 * Needs modelId set
	 * @throws ModelDoesNotExistException
	 * @return null|BimModel
	 */
	public function retrieveBimModelInformationById() {
		$this->checkArgModelId();
		$itemTable = self::bimItemTable;
		$modelTable = self::bimModelTable;
		$sql = "Select * from $modelTable where id=".$this->modelId;
		$sql = "select model.id,model.name,vfs.created,vfs.size as filesize,vfs.name as filename,vfs.file_id as vfs_file_id,".
				"(select count(*) from $itemTable where model=model.id) as used_item_count  from $modelTable as model ".
				"left join phpgw_vfs as vfs on model.vfs_file_id = vfs.file_id where id=".$this->modelId;
		if(is_null($this->db->query($sql,__LINE__,__FILE__))) {
			throw new Exception('Query to find bim model failed');
		} else {
			if($this->db->num_rows() == 0) {
				throw new ModelDoesNotExistException();
			} else {
				$this->db->next_record();
				
//				$bimModel = new BimModel();
//				$bimModel->setDatabaseId($this->db->f('id'));
//				$bimModel->setName($this->db->f('name'));
//				$bimModel->setVfsFileId($this->db->f('vfs_file_id'));
				$bimModel = $this->assembleBimModelFromCurrentDatabaseRecord();
				return $bimModel;
			}
		}
	}
	public function removeBimModelByIdFromDatabase(){
		$this->checkArgModelId();
		$sql = "Delete from ".self::bimModelTable." where id=".$this->modelId;
		if(is_null($this->db->query($sql,__LINE__,__FILE__))) {
			throw new Exception('Query to delete model was unsuccessful');
		} else {
			return $this->db->num_rows();
		}
	}
	public function addBimModel() {
		if(!$this->checkIfModelExists()) {
			$sql = "INSERT INTO ".self::bimModelTable." (name, vfs_file_id) values ('$this->modelName',$this->vfs_database_id)";
			if(is_null($this->db->query($sql,__LINE__,__FILE__))) {
				throw new Exception('Query to add model was unsuccessful');
			} else {
				return $this->db->num_rows();
			}
		} else {
			throw new ModelExistsException('Model already exists');
		}
	}
	
	public function checkIfModelExists() {
		$this->checkArgs();
		$resultAlias = "id";
		
		$sql = "select count(*) as $resultAlias from ".sobim::bimModelTable." where name = '$this->modelName' and vfs_file_id=$this->vfs_database_id";
		if(is_null($this->db->query($sql,__LINE__,__FILE__))) {
			throw new Exception('Error checking if model exists!');
		} else {
			$this->db->next_record();
			$rowCountOfModels =  $this->db->f($resultAlias);
			return ($rowCountOfModels > 0);
		}
	}
	/*
	 * @see sobimmodel
	 */
	public function removeBimModelFromDatabase()  {
		$this->checkArgs();
		if(!$this->checkIfModelExists()) {
			throw new ModelExistsException("Model does not exist");
		}
		if(!$this->removeBimModelDatabaseEntry()) {
			throw new Exception("Error removing sql model");
		}
		return true;
	}
	private function removeBimModelDatabaseEntry() {
		$sql = "delete from ".sobim::bimModelTable." where name = '$this->modelName' and vfs_file_id=$this->vfs_database_id";
		
		if(is_null($this->db->query($sql,__LINE__,__FILE__))) {
			throw new Exception('Query to delete model was unsuccessful');
		} else {
			return ($this->db->num_rows() > 0);
		}
	}
	
	private function checkArgs() {
		if(!$this->modelName || !is_int($this->getVfsdatabaseid()) || $this->getVfsdatabaseid() == 0) {
			throw new InvalidArgumentException("Invalid arguments! \n modelname: $this->modelName \n VFS ID : $this->vfs_database_id");
		}
	}
	private function checkArgModelId() {
		if(!$this->modelId) {
			throw new InvalidArgumentException("Invalid arguments! \n modelid: $this->modelId");
		}
	}
	
	public function setModelName($name) {
		$this->modelName = $name;
	}
	public function getModelName() {
		return $this->modelName;
	}
	public function setVfsdatabaseid(int $id) {
		$this->vfs_database_id = (int) $id;
	}
	public function getVfsdatabaseid() {
		return $this->vfs_database_id;
	}
	public function getModelId() {
		return $this->modelId;
	}
	public function setModelId(int $id) {
		$this->modelId = $id;
	}
}
class BimModel {
	private $databaseId;
	private $name;
	private $creationDate;
	private $fileSize;
	private $fileName;
	private $usedItemCount;
	private $vfsFileId;
	private $used;
	 
	function __construct($databaseId = null, $name = null, $creationDate = null, $fileSize = null,$fileName= null,$usedItemCount= null, $vfsFileId = null) {
		$this->databaseId = (int)$databaseId;
		$this->name = $name;
		$this->creationDate =  $creationDate;
		$this->fileSize  = $fileSize;
		$this->fileName = $fileName;
		$this->usedItemCount = $usedItemCount;
		if($usedItemCount && $usedItemCount > 0) {
			$this->used =  true;
		} else {
			$this->used =  false;
		}
		
	}
	function getDatabaseId() {
		return $this->databaseId;
	}
	function setDatabaseId($databaseId) {
		$this->databaseId = $databaseId;
	}
	function getName() {
		return $this->name;
	}
	function setName($item) {
		$this->name = $item;
	}
	function getCreationDate() {
		return $this->creationDate;
	}
	function setCreationDate($item) {
		$this->creationDate = $item;
	}
	function getFileSize() {
		return $this->fileSize;
	}
	function setFileSize($item) {
		$this->fileSize = $item;
	}
	function getFileName() {
		return $this->fileName;
	}
	function setFileName($item) {
		$this->fileName = $item;
	}
	function getUsedItemCount() {
		return $this->usedItemCount;
	}
	function setUsedItemCount($item) {
		$this->usedItemCount = $item;
	}
	function getVfsFileId() {
		return $this->vfsFileId;
	}
	function setVfsFileId($item) {
		$this->vfsFileId = $item;
	}
	
	function getUsed() {
		if($this->used) {
			return $this->used;
		} else {
			return (bool)($this->usedItemCount > 0);
		}
	}
}

