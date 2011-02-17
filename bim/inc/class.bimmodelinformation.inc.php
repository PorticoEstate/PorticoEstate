<?php
phpgw::import_class('bim.bimobject');

class BimModelInformation extends BimObject{
	private $authorization;
	private $author;
	private $changeDate;
	private $description;
	private $organization;
	private $originatingSystem;
	private $preProcessor;
	private $valDate;
	private $nativeSchema;

	public function loadVariablesFromXml(SimpleXMLElement $modelInformation) {
		if(!empty($modelInformation->authorization)){
			$this->setAuthorization((string)$modelInformation->authorization);
		}
		if(!empty($modelInformation->author)) {
			$this->setAuthor((string)$modelInformation->author);
		}
		if(!empty($modelInformation->changeDate)) {
			$this->setChangeDate($modelInformation->changeDate);
		}
		if(!empty($modelInformation->valDate)) {
			$this->setValDate($modelInformation->valDate);
		}
		if(!empty($modelInformation->description)) {
			$this->setDescription((string)$modelInformation->description);
		}
		if(!empty($modelInformation->organization)) {
			$this->setOrganization((string)$modelInformation->organization);
		}
		if(!empty($modelInformation->originatingSystem)) {
			$this->setOriginatingSystem((string)$modelInformation->originatingSystem);
		}
		if(!empty($modelInformation->originatingSystem)) {
			$this->setOriginatingSystem((string)$modelInformation->originatingSystem);
		}
		if(!empty($modelInformation->preProcessor)) {
			$this->setPreProcessor((string)$modelInformation->preProcessor);
		}
		if(!empty($modelInformation->nativeSchema)) {
			$this->setNativeSchema((string)$modelInformation->nativeSchema);
		}
	}

	/* 
	 *Converts ISO8601 string to timestamp
	 * Not needed, postgres accepts iso8601 as input
	 */
	public function convertToTimestamp($iso8601date) {
		return strtotime($iso8601date);
	}

	public function getOrganization() {
		return $this->organization;
	}

	public function setOrganization($organization) {
		$this->organization = $organization;
	}

	public function getOriginatingSystem() {
		return $this->originatingSystem;
	}

	public function setOriginatingSystem($originatingSystem) {
		$this->originatingSystem = $originatingSystem;
	}

	public function getPreProcessor() {
		return $this->preProcessor;
	}

	public function setPreProcessor($preProcessor) {
		$this->preProcessor = $preProcessor;
	}

	public function getAuthorization() {
		return $this->authorization;
	}

	public function setAuthorization($authorization) {
		$this->authorization =  $authorization;
	}

	public function getAuthor() {
		return $this->author;
	}

	public function setAuthor($author) {
		$this->author = $author;
	}

	public function getChangeDate() {
		return $this->changeDate;
	}

	public function setChangeDate($changeDate) {
		$this->changeDate = $changeDate;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getValDate() {
		return $this->valDate;
	}

	public function setValDate($valDate) {
		$this->valDate = $valDate;
	}

	public function setNativeSchema($nativeSchema) {
		$this->nativeSchema = $nativeSchema;
	}

	public function getNativeSchema() {
		return $this->nativeSchema;
	}
}
