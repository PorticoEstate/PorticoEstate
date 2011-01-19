<?php
/*
 * Business logic class for creating new BIM models in Portico
 * This class is designed with dependancy injection in mind
 */
/*phpgw::import_class('property.bimExceptions');
phpgw::import_class('property.sobim');
phpgw::import_class('property.sobimmodel');
phpgw::import_class('property.sovfs');*/
interface bobimmodel {
	public function addUploadedIfcModel();
	public function createBimModelList();
	public function setVfsObject(sovfs $vfs);
	public function setSobimmodel(sobimmodel $sobimmodel);
	public function checkBimModelExists();
	public function checkBimModelExistsByModelId();
	public function removeIfcModel();
	public function removeIfcModelByModelId();
	public function setModelName($name);
	public function getModelName();
}

class bobimmodel_impl implements bobimmodel {
	private $sovfs;
	private $sobimmodel;
	private $modelName;
	function __construct() {
		
	}
	
	public function setVfsObject(sovfs $vfs) {
		$this->sovfs = $vfs;
	}
	public function setSobimmodel(sobimmodel $sobimmodel) {
		$this->sobimmodel = $sobimmodel;
	}
	/*
      * taken from calss.uitts.inc.php
      * @return boolean true if success
      * @throws FileExistsException if filename is already used
      * @throws CopyFailureException if there is a failure copying
      */
	
	public function addUploadedIfcModel() {
		if(!$this->sovfs) {
			throw new Exception('Missing vfs object!');
		}
		try {
			$filename = $this->saveUploadedBimData();
			$this->applyModelName($filename);
			$file_database_id = $this->sovfs->retrieveVfsFileId();
			$this->sobimmodel->setVfsdatabaseid($file_database_id);
			$this->sobimmodel->setModelName($this->modelName);
			$this->sobimmodel->addBimModel();
			return true;
		} catch (FileExistsException $e) {
     		throw $e;
		} catch (CopyFailureException $e) {
			throw $e;
		} catch ( ModelExistsException $e) {
			throw $e; 
		}catch (Exception $e) {
			throw $e;
		}
	}
	
	private function applyModelName($filename) {
		if(!($this->modelName && strlen($this->modelName)>0)) {
			$this->modelName = $filename;
		} 
	}
	/*
	 * requires sobimmodel with db set
	 */
	public function createBimModelList() {
		if(!$this->sobimmodel) {
			throw new Exception('Missing sobimodel object!');
		}
		$BimModelArray = $this->sobimmodel->retrieveBimModelList();
		if(!$BimModelArray) {
			return null;
		} else {
			$outputArray = array();
			
			foreach($BimModelArray as $BimModel) {
				//var_dump($BimModel);
				array_push($outputArray, $this->transformObjectToArray($BimModel));
			}
			return $outputArray;
		}
		
	}
	/*
	 * Takes an object, gets the varibles from the public getter function and returns
	 * an associative array
	 */
	private function transformObjectToArray($object) {
		$reflection = new ReflectionObject($object);
		$publicMethods = $reflection->getMethods(ReflectionProperty::IS_PUBLIC);
		$result = array();
		foreach($publicMethods as $method) {
			/* @var $method ReflectionMethod */
			if(preg_match("/^get(.+)/", $method->getName(), $matches)) {
				$memberVarible = lcfirst($matches[1]);
				$value = $method->invoke($object);
				$result[$memberVarible] = $value;
			}
		}
		return $result;
	}
	/*
	 * This function requires:
	 * An SOvfs object with the filename and the submodule set
	 * An sobimmodel object with the modelname and the vfs_database_id
	 */
	public function checkBimModelExists() {
		if(!$this->sobimmodel || !$this->sovfs) {
			throw new InvalidArgumentException($this->displayArguments());
		}
		if($this->sovfs->checkIfFileExists()) {
			$fileId = $this->sovfs->retrieveVfsFileId();
			$this->sobimmodel->setVfsdatabaseid($fileId);
			if($this->sobimmodel->checkIfModelExists() ) {
				return true;
			}
			
		} 
		return false;
		
	}
	/*
	 * needs sobimmodel object with db and  modelId set
	 * @return boolean
	 */
	public function checkBimModelExistsByModelId() {
		$this->checkIdArguments();
		$bimModel = $this->sobimmodel->retrieveBimModelInformationById();
		return ($bimModel != null);
	}
	
	private function displayArguments() {
		$string = "Argument list:\n".
				"Model name:\t $this->modelName \n".
				"Model id:\t $this->modelId \n".
				"SOvfs:\t ".gettype($this->sovfs)."\n".
				"SObimmodel:\t ".gettype($this->sobimmodel)."\n";
		return $string;
		
	}
	/*
	 * This function needs:
	 * An SOvfs object with the filename and the submodule set
	 * An sobimmodel object with the modelname and the vfs_database_id
	 */
	public function removeIfcModel() {
		if(!$this->sobimmodel || !$this->sovfs) {
			throw new InvalidArgumentException($this->displayArguments());
		}
		$this->sobimmodel->removeBimModelFromDatabase();
		$this->sovfs->removeFileFromVfs();
		return true;
	}
	/*
	 * needs sobimmodel object with db and  modelId set
	 * needs sovfs object, with submodule set
	 */
	public function removeIfcModelByModelId() {
		try {
			$this->checkIdArguments();
		} catch (InvalidArgumentException $e) {
			throw $e;
		}
		/* @var $bimModel BimModel */
		$bimModel = $this->sobimmodel->retrieveBimModelInformationById();
		$this->sobimmodel->setModelName($bimModel->getName());
		$this->sobimmodel->setVfsdatabaseid($bimModel->getVfsFileId());
		
		$this->sovfs->setFilename($bimModel->getFileName());
		$this->removeIfcModel();
	}
	
	private function checkIdArguments() {
		if(!$this->sobimmodel || !$this->sovfs || !$this->sobimmodel->getModelId() || !$this->sovfs->getSubModule()) {
			throw new InvalidArgumentException($this->displayArguments());
		}
	}
	/*
	 * expects item from the $_FILES object in the form:
	 * @param $uploadedFileArray  Array ( 	[name] => <name> 
	 *  			[type] => <mimeType> 
	 *  			[tmp_name] => <filename with path> 
	 *  			[error] => <error status> 
	 *  			[size] => <file size> )
	 * @throws FileExistsException|Exception
	 * @return String|null filename of the uploaded file
	 */
	private function saveUploadedBimData() {
		try {
			return $this->sovfs->addFileToVfs();
		} catch (FileExistsException $e) {
     		throw $e;
		} catch (CopyFailureException $e) {
			throw $e;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public function setModelName($name) {
		$this->modelName = $name;
	}
	public function getModelName() {
		return $this->modelName;
	}
}