<?php

	phpgw::import_class('bim.bimmodelinformation');
	
	interface bobimitem {
		public function setIfcXml(SimpleXMLElement  $xml);
		public function setSobimitem(sobimitem $sobimitem);
		public function setSobimtype(sobimtype $sobimtype);
		public function setSobimmodelinformation(sobimmodelinformation  $sobimmodelinformation);
		public function loadIfcItemsIntoDatabase();
		public function fetchItemsByModelId();
	}
	
	class bobimitem_impl implements bobimitem {
		private $ifcXml;
		/* @var $sobimitem sobimitem */
		private $sobimitem;
		private $sobimtype;
		private $sobimmodelinformation;
		
		public function __construct() {
			
		}
		
		public function loadIfcItemsIntoDatabase() {
			$this->checkArguments();
			/* @var $modelInfo SimpleXMLElement */
			$modelInfo = $this->ifcXml->modelInformation[0];
			$modelInfoXml =  $modelInfo->asXML();
			$bimmodelInformation = new BimModelInformation();
			$bimmodelInformation->loadVariablesFromXml($modelInfo);
			$this->sobimmodelinformation->setBimModelInformation($bimmodelInformation);
			try {
				$this->sobimmodelinformation->updateModelInformation();
			} catch (Exception $e) {
				throw $e;
			}
			//var_dump($this->ifcXml);
			
			$BimItems = $this->loopThrough();
			
			foreach($BimItems as $item) {
				$type = $item->getType();
				try {
					$this->sobimtype->addBimObjectType($type);
				} catch (Exception $e) {
					// do nothing
				}
				try {
					$this->sobimitem->addBimItem($item);
				} catch (BimDataException $e) {
					throw new BimDataException("Data exception\n MSG:".$e->getMessage()."\nReason:".$e->getPrevious()->getMessage(), $e);
					//echo "Data exception with message:".$e->getMessage()."\n";
					//echo "Reason:".$e->getPrevious()->getMessage();
					//break;
				}
			}
			
		}
		
		private function loopThrough() {
			$BimItemArray = array();
			/* @var $second_gen SimpleXMLElement */
			foreach ($this->ifcXml->children() as $second_gen) {
				//echo "Child with name:".$second_gen->getName()."\n";
				if ( $second_gen['ifcObjectType']) {
					$bimItem = $this->createBimItem($second_gen);
					array_push($BimItemArray, $bimItem);
					
				} else if($second_gen->getName() != "modelInformation") {
					/* @var $third_gen SimpleXMLElement */
					foreach ($second_gen->children() as $third_gen) {
						if ( $third_gen['ifcObjectType']) {
							$bimItem = $this->createBimItem($third_gen);
							array_push($BimItemArray, $bimItem);
							
						} else {
							echo "Could not add item, missing attribute, item:".$third_gen->asXML();
						}
					}
				}
			}
			return $BimItemArray;
		}
		/*
		 * Needs the following variables set
		 * sobimitem (with modelId set)
		 * 
		 */
		public function fetchItemsByModelId() {
			$this->checkFetchArguments();
			return $this->sobimitem->retrieveItemsByModelId();
		}
		/*
		 * @throws IncompleteItemException if the ifc object is missing anything
		 */
		private function createBimItem(& $ifcObject) {
			$guid = $ifcObject->attributes->guid;
			$type = $ifcObject['ifcObjectType'];
			$xml = $ifcObject->asXML();
			if(empty($guid) || empty($type) || empty($xml)) {
				$currentItem = 	"GUID:".$guid."\n".
								"Type:".$type."\n".
								"XML:".$xml;
				throw new IncompleteItemException($currentItem);
			}
		//	return new BimItem(null, $guid, $type, $xml, $this->sobimitem->getModelId());
			return new BimItem(null, $guid, $type, $xml, $this->sobimmodelinformation->getModelId());// Sigurd 15.feb 2011: this one seems to work
		}
		
		private function checkFetchArguments() {
			if(!$this->sobimitem) {
				throw new InvalidArgumentException("Missing sobimitem");
			}
		}
		private function checkArguments() {
			if(empty($this->ifcXml) || empty($this->sobimitem) || empty($this->sobimtype) || empty($this->sobimmodelinformation)) {
				$args = "IfcXml type:".gettype($this->ifcXml)."\n".
						"Sobimitem type:".gettype($this->sobimitem)."\n".
						"Sobimtype type:".gettype($this->sobimtype)."\n".
						"Sobimmodelinformation type:".gettype($this->sobimmodelinformation)."\n";
				throw new InvalidArgumentException("BObimitem:Incorrect arguments\b".$args);
			}
		}
		public function setSobimmodelinformation(sobimmodelinformation  $sobimmodelinformation) {
			$this->sobimmodelinformation = $sobimmodelinformation;
		}
		public function setIfcXml(SimpleXMLElement  $xml) {
			$this->ifcXml = $xml;
		}
		public function setSobimitem(sobimitem $sobimitem) {
			$this->sobimitem = $sobimitem;
		}
		public function setSobimtype(sobimtype  $sobimType){
			$this->sobimtype = $sobimType;
		}
	}
