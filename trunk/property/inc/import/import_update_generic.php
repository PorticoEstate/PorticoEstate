<?php
	class import_conversion
	{
		protected $db;
		public $messages = array();
		public $warnings = array();
		public $errors = array();
		public $debug = true;
		protected $is_eav;
		protected $location_id;
		protected $bim_type_id = 0;
		protected $table;
		protected $entity_id;
		protected $cat_id;
		protected $metadata = array();

		public function __construct($location_id)
		{
			$location_id = (int) $location_id;
			set_time_limit(10000); //Set the time limit for this request
			$this->account		= (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= $this->db->join;

			if($location_id && !$category = execMethod('property.soadmin_entity.get_single_category', $location_id ))
			{
				throw new Exception("Not a valid location for {$location_id}");
			}

			$this->is_eav = !!$category['is_eav'];
			$this->location_id = $location_id;

			$this->entity_id = $category['entity_id'];
			$this->cat_id = $category['id'];


			if ($this->is_eav)
			{
				$this->table = 'fm_bim_item';
				$sql = "SELECT fm_bim_type.id FROM fm_bim_type WHERE location_id = {$location_id}";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();
				$this->bim_type_id = $this->db->f('id');
				$custom 		= createObject('property.custom_fields');
				$attributes 	= $custom->find2($location_id, 0, '', 'ASC', 'attrib_sort', true, true);
				$this->metadata['id'] = array('primary_key' => true);
				$this->metadata['location_id'] = array('primary_key' => true);
				$this->metadata['model'] = array();
				$this->metadata['p_location_id'] = array();
				$this->metadata['p_id'] = array();
				$this->metadata['location_code'] = array();
				$this->metadata['loc1'] = array();
				$this->metadata['address'] = array();
				$this->metadata['entry_date'] = array();
				$this->metadata['user_id'] = array();

				foreach($attributes as $attribute)
				{
					$this->metadata[$attribute['column_name']] = array();
				}

			}
			else
			{
				$this->table = "fm_entity_{$category['entity_id']}_{$category['id']}";
				$this->metadata = $this->db->metadata($this->table);
			}


		}

		public function set_table($table)
		{
			$this->table = $table;
		}
		public function set_metadata($metadata)
		{
			$this->metadata = $metadata;
		}

		public function add($data)
		{
			if ($this->is_eav)
			{
				$ok = $this->_add_eav($data);
			}
			else
			{
				$ok = $this->_add_sql($data);
			}
			return $ok;
		}

		private function _add_eav($data)
		{
			static $count_records = 0;
// -------- produce data_set

			$error = false;
			$table = $this->table;
			$fields =  $this->fields;

			if(!$table)
			{
				throw new Exception("Tabell er ikke angitt");
			}

			$remove_keys = array();
			foreach($this->metadata as $key => $info)
			{
				if(isset($info['primary_key']) && $info['primary_key'])
				{
					$_value = $data[array_search($key, $fields)];
					if(!array_search($key, $fields) || !$_value)
					{
						if(array_search($key, $fields) === 0 && $_value)
						{
							break;
						}
						if($count_records === 0)// first one
						{
							throw new Exception("Fant ikke verdi for feltet 'primary key' $key");
						}
						else
						{
							$found_data = false;
							foreach($data as $value)
							{
								if($value && !$found_data)
								{
									$found_data = true;
								}
							}
							if($found_data)
							{
								throw new Exception("Fant ikke verdi for feltet 'primary key' $key");
							}
							else
							{
								$this->warnings[] = "Fant ikke verdi for feltet 'primary key' $key";
								return true;
							}
						}
					}
					$remove_keys[] = $key;
				}
			}
			$count_records ++;
			unset($key);
			unset($info);
			unset($_value);

			$value_set = array();
			foreach ($fields as $key => $field)
			{
				if(isset($this->metadata[$field]))
				{
					$value_set[$field] 	= $this->validate_value($data[$key], $field);
				}
			}

			$id = (int) $value_set['id'];
			$filtermethod = "location_id = {$this->location_id} AND id = {$id}";

//---------produce data_set

			$location_id = $this->location_id;
			$sql = "SELECT fm_bim_item.id FROM fm_bim_item WHERE {$filtermethod}";
			$this->db->query($sql,__LINE__,__FILE__);

			$type = (int)$this->bim_type_id;

			$location_name = "_entity_{$this->entity_id}_{$this->cat_id}";

			if($this->db->next_record())
			{
				$this->warnings[] = "ID finnes fra før: {$id}, oppdaterer";

				foreach ($remove_keys as $remove_key)
				{
					unset($value_set[$remove_key]);
				}

				phpgw::import_class('phpgwapi.xmlhelper');

				$xmldata = phpgwapi_xmlhelper::toXML($value_set, $location_name);
				$doc = new DOMDocument;
				$doc->preserveWhiteSpace = true;
				$doc->loadXML( $xmldata );
				$domElement = $doc->getElementsByTagName($location_name)->item(0);
				$domAttribute = $doc->createAttribute('appname');
				$domAttribute->value = 'property';

				// Don't forget to append it to the element
				$domElement->appendChild($domAttribute);

				// Append it to the document itself
				$doc->appendChild($domElement);

				$doc->formatOutput = true;
				$xml = $doc->saveXML();

				$_value_set = array
				(
					'xml_representation'	=> $this->db->db_addslashes($xml),
					'p_location_id'			=> isset($value_set['p_location_id']) && $value_set['p_location_id'] ? $value_set['p_location_id'] : '',
					'p_id'					=> isset($value_set['p_id']) && $value_set['p_id'] ? $value_set['p_id'] : '',
					'location_code'			=> $value_set['location_code'],
					'loc1'					=> $value_set['loc1'],
					'address'				=> $value_set['address'],
				);

				$_value_set	= $this->db->validate_update($_value_set);

				$sql = "UPDATE fm_bim_item SET $_value_set WHERE id = $id AND location_id = {$location_id}";
			}
			else
			{
				$this->warnings[] = "Denne er ny: {$id}, legger til";

				phpgw::import_class('phpgwapi.xmlhelper');
				$xmldata = phpgwapi_xmlhelper::toXML($value_set, $location_name);
				$doc = new DOMDocument;
				$doc->preserveWhiteSpace = true;
				$doc->loadXML( $xmldata );
				$domElement = $doc->getElementsByTagName($location_name)->item(0);
				$domAttribute = $doc->createAttribute('appname');
				$domAttribute->value = 'property';

				// Don't forget to append it to the element
				$domElement->appendChild($domAttribute);

				// Append it to the document itself
				$doc->appendChild($domElement);
				$doc->formatOutput = true;

				$xml = $doc->saveXML();

				if (function_exists('com_create_guid') === true)
				{
					$guid = trim(com_create_guid(), '{}');
				}
				else
				{
					$guid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
				}

				$values_insert = array
				(
	  				'id'					=> $id,
	  				'type'					=> $type,
	  				'location_id'			=> $location_id,
	  				'guid'					=> $guid,
					'xml_representation'	=> $this->db->db_addslashes($xml),
					'model'					=> 0,
					'p_location_id'			=> isset($value_set['p_location_id']) && $value_set['p_location_id'] ? $value_set['p_location_id'] : '',
					'p_id'					=> isset($value_set['p_id']) && $value_set['p_id'] ? $value_set['p_id'] : '',
					'location_code'			=> $value_set['location_code'],
					'loc1'					=> $value_set['loc1'],
					'address'				=> $value_set['address'],
					'entry_date'			=> time(),
					'user_id'				=> $this->account
				);

				$sql = "INSERT INTO fm_bim_item (" . implode(',',array_keys($values_insert)) . ') VALUES ('
				 . $this->db->validate_insert(array_values($values_insert)) . ')';
			}

			$ok = false;
			if($this->debug)
			{
				_debug_array($sql);
			}
			else
			{
				$ok = $this->db->query($sql,__LINE__,__FILE__);
			}

			if($ok)
			{
				$this->messages[] = "Successfully imported record: id ({$id})";
			}
			else
			{
				$this->errors[] = "Error importing record: id ({$id})";
			}
			return $ok;
		}

		private function _add_sql($data)
		{
			$error = false;
			$table = $this->table;
			$fields =  $this->fields;

			if(!$table)
			{
				throw new Exception("Tabell er ikke angitt");
			}

			if(!$fields)
			{
				throw new Exception("Felter er ikke definert");
			}

			$primary_key = array();
			$remove_keys = array();
			foreach($this->metadata as $key => $info)
			{
				if(isset($info->primary_key) && $info->primary_key)
				{
					if(!$_value = $data[array_search($key, $fields)])
					{
						throw new Exception("Fant ikke verdi for feltet 'primary key'");
					}

					$primary_key[] = "$key='{$_value}'";
					$remove_keys[] = $key;
				}
			}
			unset($key);
			unset($info);
			unset($_value);

			$filtermethod = implode(' AND ', $primary_key);

			$value_set = array();
			foreach ($fields as $key => $field)
			{
				if(isset($this->metadata[$field]))
				{
					$value_set[$field] 	= $this->validate_value($data[$key], $field);
				}
			}

			$this->db->query("SELECT count(*) as cnt FROM {$table} WHERE {$filtermethod}",__LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('cnt'))
			{
				foreach ($remove_keys as $remove_key)
				{
					unset($value_set[$remove_key]);
				}

				$this->warnings[] = "ID finnes fra før: {$filtermethod}, oppdaterer";
				$value_set	= $this->db->validate_update($value_set);
				$sql = "UPDATE {$table} SET {$value_set} WHERE {$filtermethod}";
				$action = 'updated';
			}
			else
			{
				$this->warnings[] = "ID fantes ikke fra før: {$filtermethod}";

				$cols = implode(',', array_keys($value_set));
				$values	= $this->db->validate_insert(array_values($value_set));
				$sql = "INSERT INTO {$table} ({$cols}) VALUES ({$values})";

				$action = 'inserted';
			}

			if($this->debug)
			{
				_debug_array($sql);
			}
			else
			{
				$ok = $this->db->query($sql,__LINE__,__FILE__);
			}

			if($ok)
			{
				$this->messages[] = "Successfully {$action} record: " . implode(', ', $primary_key);
			}
			else
			{
				$this->errors[] = "Error importing record: " . implode(', ', $primary_key);
			}
			return $ok;
		}

		protected function validate_value($value,$field)
		{
			$value = trim($value);

			if($value == '#N/A')
			{
				return '';
			}

			if(is_object($this->metadata[$field]))
			{
				$datatype = $this->metadata[$field]->type;
			}
			else
			{
				$datatype = $this->metadata[$field]['type'];
			}
			switch ($datatype)
			{
				case 'char':
				case 'varchar':
				case 'text':
					$ret = $this->db->db_addslashes($value);
					break;
				case 'bool':
					$ret = $value ? 'True' : 'False';
					break;
				default:
					$ret = $value;
			}

			return $ret;
		}

	}
