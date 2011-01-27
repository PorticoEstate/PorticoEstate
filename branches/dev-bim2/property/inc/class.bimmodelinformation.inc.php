<?php

class BimModelInformation {
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
		$this->setAuthorization($modelInformation->authorization);
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