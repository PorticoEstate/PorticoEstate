<?php
	phpgw::import_class('bim.bimExceptions');
	phpgw::import_class('bim.bimmodelinformation');

	/*
 * @see sobimmodel_impl
 */

	interface sobimmodelinformation extends sobim
	{

	public function setModelId(int $id);

	public function setDb(phpgwapi_db_ $db);

	public function setBimModelInformation(BimModelInformation $binfo);
	/*
	 * Will need db, modelId set and modelinformation object set
	 * @throws InvalidArgumentException
	 */

	public function updateModelInformation();
	/*
	 * Will need db and modelId
	 */

	public function getModelInformation();
	}
	
	class sobimmodelinformation_impl implements sobimmodelinformation
	{
	
	private $db;
	private $modelId;
	private $modelInformation;
	private $modelInformationColumns;// = array('authorization_value', 'author', 'changedate', 'description', 'organization', 'originatingsystem', 'preprocessor', 'valdate', 'nativeSchema');
	private $modelInformationMapping;

		public function __construct(phpgwapi_db_ & $db, int $modelId = null, BimModelInformation $modelInformation = null)
		{
		$this->db = $db;
		$db->Halt_On_Error = 'no';
		$db->Exception_On_Error = true;
		$this->modelId = $modelId;
		
		$this->modelInformation = $modelInformation;
		$this->initModelInformationColumns();
	}
	/*
	 * MAP db column names to BimModelInformation methods, but without the get/set prefix
	 */

		private function initModelInformationColumns()
		{
		$modelInformationMapping = array();
		$modelInformationMapping['authorization_value'] = "Authorization";
		$modelInformationMapping['author'] = "Author";
		$modelInformationMapping['changedate'] = "ChangeDate";
		$modelInformationMapping['description'] = "Description";
		$modelInformationMapping['organization'] = "Organization";
		$modelInformationMapping['originatingsystem'] = "OriginatingSystem";
		$modelInformationMapping['preprocessor'] = "PreProcessor";
		$modelInformationMapping['valdate'] = "ValDate";
		$modelInformationMapping['nativeschema'] = "NativeSchema";
		$this->modelInformationMapping = $modelInformationMapping;
		$this->modelInformationColumns = array_keys($modelInformationMapping);
	}

		public function updateModelInformation()
		{
		$this->checkArgumentsForUpdate();
		$modelTable = self::bimModelTable;
			$sql = "update $modelTable set " .
			"(" . implode(",", $this->modelInformationColumns) . ") =" .
			$this->getModelInfoSqlValueString($this->modelInformation) .
			" where id=" . $this->modelId;

			try
			{
				if(is_null($this->db->query($sql, __LINE__, __FILE__)))
				{
				throw new Exception('Query to add model information was unsuccessful');
				}
				else
				{
				return $this->db->num_rows();
			}
			}
			catch(Exception $e)
			{
			throw $e;
		} 
	}

		private function checkArgumentsForUpdate()
		{
			if(empty($this->db) || empty($this->modelId) || empty($this->modelInformation) || !($this->modelInformation instanceof BimModelInformation))
			{
				$error = "(sobimmodelinformation)Model id:" . $this->modelId . " \n" .
				"Model information:" . gettype($this->modelInformation) . "\n" .
				"Model information class:" . get_class($this->modelInformation) . "\n" .
				"DB:" . gettype($this->db) . "\n";
			throw new InvalidArgumentException($error);
		}
	}

		private function checkArgumentsForRead()
		{
			if(empty($this->db) || empty($this->modelId))
			{
				$error = "Model id:" . $this->modelId . " \n" .
				"DB:" . gettype($this->db) . "\n";
			throw new InvalidArgumentException($error);
		}
	}
	/*
	 * Adds the values in the correct order into an array
	 */

		private function getModelInfoSqlValueString(BimModelInformation $modelInfo)
		{
		$modelInfoValues = array();
			foreach(array_values($this->modelInformationMapping) as $methodName)
			{
				$cmd = 'array_push($modelInfoValues,$this->modelInformation->get' . $methodName . '());';
			eval($cmd);
		}
			foreach($modelInfoValues as &$value)
			{
				$value = (empty($value)) ? 'null' : "'" . addslashes($value) . "'";
		}
			$sqlString = "(" . implode(",", $modelInfoValues) . ")";
		return $sqlString;
		
			/* array_push($modelInfoArray,$this->modelInformation->getAuthorization());
		array_push($modelInfoArray,$this->modelInformation->getAuthor());
		array_push($modelInfoArray,$this->modelInformation->getChangeDate());
		array_push($modelInfoArray,$this->modelInformation->getDescription());
		array_push($modelInfoArray,$this->modelInformation->getOrganization());
		array_push($modelInfoArray,$this->modelInformation->getOriginatingSystem());
		array_push($modelInfoArray,$this->modelInformation->getPreProcessor());
		array_push($modelInfoArray,$this->modelInformation->getValDate());
		array_push($modelInfoArray,$this->modelInformation->getNativeSchema());
		$sqlString = "(";
		foreach($modelInfoArray as $value) {
			$valueString = (empty($value)) ? 'null' : "'".addslashes($value)."'";
			$sqlString = $sqlString.$valueString.",";
		}
		$sqlString = substr($sqlString,0,-1);
		$sqlString = $sqlString.")";
			  return $sqlString; */
	}
	
		public function getModelInformation()
		{
		$this->checkArgumentsForRead();
		$modelTable = self::bimModelTable;
			$sql = "select * from $modelTable where id=" . $this->modelId;
			try
			{
				if(is_null($this->db->query($sql, __LINE__, __FILE__)))
				{
				throw new Exception('Query to get model information was unsuccessful');
			} 
				if($this->db->num_rows() == 0)
				{
				throw new ModelDoesNotExistException();
			}
			$this->db->next_record();
			return $this->assembleBimModelInfoFromCurrentDatabaseRecord();
			}
			catch(Exception $e)
			{
			echo "An exception was caught while updating!";
			throw $e;
		}
	}

		private function assembleBimModelInfoFromCurrentDatabaseRecord()
		{
		$bimModelInformation = new BimModelInformation();
			foreach($this->modelInformationMapping as $columnName => $methodName)
			{
				$cmd = '$bimModelInformation->set' . $methodName . '($this->db->f(\'' . $columnName . '\'));';
			eval($cmd);
		}
		return $bimModelInformation;
	}
	
		public function setDb(phpgwapi_db_ $db)
		{
		$this->db = $db;
	}

		public function setBimModelInformation(BimModelInformation $binfo)
		{
		$this->modelInformation = $binfo;
	}

		public function getModelId()
		{
		return $this->modelId;
	}

		public function setModelId(int $id)
		{
		$this->modelId = $id;
	}
	}