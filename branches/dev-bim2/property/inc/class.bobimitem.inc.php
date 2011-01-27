<?php
	interface bobimitem {
		public function setIfcXml(SimpleXMLElement  $xml);
		public function setSobimitem(sobimitem $sobimitem);
		public function setSobimtype(sobimtype $sobimtype);
		public function setModelId(int $id);
		public function loadIfcItemsIntoDatabase();
	}
	
	class bobimitem_impl implements bobimitem {
		private $ifcXml;
		/* @var $sobimitem sobimitem */
		private $sobimitem;
		private $sobimtype;
		private $modelId;
		
		public function __construct() {
			
		}
		
		public function loadIfcItemsIntoDatabase() {
			$this->checkArguments();
			/* @var $modelInfo SimpleXMLElement */
			$modelInfo = $this->ifcXml->modelInformation[0];
			$modelInfoXml =  $modelInfo->asXML();
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
					echo "Data exception with message:".$e->getMessage()."\n";
					echo "Reason:".$e->getPrevious()->getMessage();
					break;
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
			return new BimItem(null, $guid, $type, $xml, $this->modelId);
		}
		
		private function checkArguments() {
			if(empty($this->ifcXml) || empty($this->sobimitem) || empty($this->modelId) || empty($this->sobimtype)) {
				throw new InvalidArgumentException("Incorrect arguments");
			}
		}
		public function setModelId(int $id) {
			$this->modelId = $id;
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