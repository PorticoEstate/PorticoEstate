<?php

	class import_components
	{
		
		protected $db;
		var $type = 'entity';
		protected $sql;
		protected $type_app = array
			(
			'entity' => 'property',
			'catch' => 'catch'
		);
		
		public function __construct()
		{
			$this->account = (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db = & $GLOBALS['phpgw']->db;
			$this->join = $this->db->join;
			$this->bo = CreateObject('property.boadmin_entity', true);
			$this->bo_entity = CreateObject('property.boentity', true);
			$this->custom = CreateObject('property.custom_fields');
			$this->bocommon = CreateObject('property.bocommon');
		}

		public function get_entity_categories ($data = array())
		{
			$querymethod = '';
			if ($data['parent_id'])
			{
				$querymethod .= " AND parent_id = ".$data['parent_id'];
			}
			
			$sql = "SELECT * FROM fm_entity_category WHERE entity_id = 3 {$querymethod}";
			$this->db->query($sql, __LINE__, __FILE__);
			
			while ($this->db->next_record())
			{
				$buildingpart = explode(' ', trim($this->db->f('name')))[0];
					
				$values[$buildingpart] = array
					(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('name'),
					'buildingpart' => $buildingpart,
					'location_id' => $this->db->f('location_id'),
					'parent_id' => $this->db->f('parent_id'),
					'entity_id' => $this->db->f('entity_id')
				);
			}
			
			return $values;
		}
		
		public function get_attributes($entity_id, $cat_id)
		{
			$attributes = $this->custom->find($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", 0, '', 'ASC', 'attrib_sort', true, true);

			$values = array();
			foreach($attributes as $attribute)
			{
				$values[] = array(
								'name' => $attribute['name'],
								'datatype' => $attribute['datatype'],
								'precision' => $attribute['precision'],
								'history' => $attribute['history'],
								'attrib_id' => $attribute['attrib_id'],
								'nullable' => $attribute['nullable'],
								'input_text' => $attribute['input_text'],
								'disabled' => $attribute['disabled'],
								'value' => $attribute['value']
							);
			}
			
			return $values;
		}
		
		public function set_attributes_values($values, $attributes)
		{
			foreach($attributes as &$attribute)
			{
				foreach ($values as $value)
				{
					if ($attribute['name'] ==  $value['name'])
					{
						$attribute['value'] = $value['value'];
					}
				}
			}
			
			return $attributes;
		}
		
		/*public function set_parent($values, $attributes)
		{
			foreach($attributes as &$attribute)
			{
				foreach ($values as $value)
				{
					if ($attribute['name'] ==  $value['name'])
					{
						$attribute['value'] = $value['value'];
					}
				}
			}
			
			return $attributes;
		}*/
		
		public function add_entity_categories ($buildingpart_out_table)
		{
			$buildingparts = array();
			
			foreach ($buildingpart_out_table as $k => $v)
			{	
				if ($v['parent'])
				{
					$cat_id = '1'; /*  Id of "211 Klargjøring av tomt" */
					$entity_id = '3';
					$values = array();

					$attrib_list = $this->bo->read_attrib(array('entity_id' => $entity_id, 'cat_id' => $cat_id, 'allrows' => true));
					foreach ($attrib_list as $attrib) 
					{
						$values['template_attrib'][] = $attrib['id'];
					}
					$values['category_template'] = $entity_id.'_'.$cat_id;
					$values['parent_id'] = $v['parent']['id'];
					$values['name'] = $v['name'];
					$values['descr'] = $v['name'];
					$values['entity_id'] = $entity_id;
					$values['fileupload'] = 1;
					$values['loc_link'] = 1;
					$values['is_eav'] = 1;
					
					$receipt = $this->bo->save_category($values);
					
					if ($receipt['id'])
					{
						$buildingparts['added'][$k] = array('id' => $receipt['id'], 'entity_id' => $entity_id, 'name' => $v['name']);
					}
					else {
						$buildingparts['not_added'][$k] = array('name' => $v['name']);
					}
				} else {
					$buildingparts['not_added'][$k] = array('name' => $v['name']);
				}
			}
			
			return $buildingparts;
		}
		
		public function add_bim_item($entity_categories, $location_code)
		{
			$components_added = array();
			$count = 0;
			
			$this->db->transaction_begin();
			
			try
			{
				$this->db->Exception_On_Error = true;
				foreach ($entity_categories as $entity) 
				{
					if ($entity['cat_id'])
					{
						$attributes = $this->get_attributes($entity['entity_id'], $entity['cat_id']);

						$not_added = array();
						foreach ($entity['components'] as $values)
						{
							$attributes_values = $this->set_attributes_values($values, $attributes);
							$values_insert = $this->_populate(array('location_code' => $location_code), $attributes_values);
							
							$receipt = $this->_save_eav($values_insert, $entity['entity_id'], $entity['cat_id']);
							if (!$receipt['id'])
							{
								throw new Exception('import - missing id');
							}
							$components_added[$values_insert['benevnelse']] = $receipt;
						}
					}
				}
				$this->db->Exception_On_Error = false;
			}
			catch (Exception $e)
			{
				if ($e)
				{
					$this->db->transaction_abort();
					throw $e;
				}
			}

			$this->db->transaction_commit();
			
			return $components_added;
		}
		
		private function _save_eav( $data, $entity_id, $cat_id )
		{
			$location_id = (int) $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");
			$location_name = "_{$this->type}_{$entity_id}_{$cat_id}";

			$this->db->query("SELECT id as type FROM fm_bim_type WHERE location_id = {$location_id}", __LINE__, __FILE__);
			$this->db->next_record();
			$type = $this->db->f('type');
			$id = $this->db->next_id('fm_bim_item', array('type' => $type));

			phpgw::import_class('phpgwapi.xmlhelper');
			$xmldata = phpgwapi_xmlhelper::toXML($data, $location_name);
			$doc = new DOMDocument;
			$doc->preserveWhiteSpace = true;
			$doc->loadXML($xmldata);
			$domElement = $doc->getElementsByTagName($location_name)->item(0);
			$domAttribute = $doc->createAttribute('appname');
			$domAttribute->value = $this->type_app[$this->type];

			// Don't forget to append it to the element
			$domElement->appendChild($domAttribute);

			// Append it to the document itself
			$doc->appendChild($domElement);
			$doc->formatOutput = true;

			$xml = $doc->saveXML();

			//	_debug_array($xml);

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
				'id' => $id,
				'location_id' => $location_id,
				'type' => $type,
				'guid' => $guid,
				'xml_representation' => $this->db->db_addslashes($xml),
				'model' => 0,
				'p_location_id' => isset($data['p_location_id']) && $data['p_location_id'] ? $data['p_location_id'] : '',
				'p_id' => isset($data['p_id']) && $data['p_id'] ? $data['p_id'] : '',
				'location_code' => $data['location_code'],
				'loc1' => $data['loc1'],
				'address' => $data['address'],
				'entry_date' => time(),
				'user_id' => $this->account
			);

			$result = $this->db->query("INSERT INTO fm_bim_item (" . implode(',', array_keys($values_insert)) . ') VALUES ('
				. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);
			
			if ($result)
			{
				return array('id' => $id, '$location_id' => $location_id); 
			}
			else {
				return array();
			}
		}
		
		private function _populate( $values, $values_attribute )
		{
			
			if (is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}
			
			$values_insert = array();

			if (isset($values['street_name']) && $values['street_name'])
			{
				$address[] = $values['street_name'];
				$address[] = $values['street_number'];
				$address = $this->db->db_addslashes(implode(" ", $address));
			}

			if (!isset($address) || !$address)
			{
				$address = isset($values['location_name']) ? $this->db->db_addslashes($values['location_name']) : '';
			}

			if (isset($address) && $address)
			{
				$values_insert['address'] = $address;
			}

			if (isset($values['location_code']) && $values['location_code'])
			{
				$values_insert['location_code'] = $values['location_code'];
			}

			if (isset($values['location']) && is_array($values['location']))
			{
				foreach ($values['location'] as $input_name => $value)
				{
					if (isset($value) && $value)
					{
						$values_insert[$input_name] = $value;
					}
				}
			}

			if (isset($values['extra']) && is_array($values['extra']))
			{
				foreach ($values['extra'] as $input_name => $value)
				{
					if (isset($value) && $value)
					{
						$values_insert[$input_name] = $value;
					}
				}
			}

			if (isset($values_attribute) && is_array($values_attribute))
			{
				foreach ($values_attribute as $entry)
				{
					if ($entry['value'])
					{
						if ($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$entry['value'] = $this->db->db_addslashes($entry['value']);
						}
						$values_insert[$entry['name']] = $entry['value'];

						if ($entry['history'] == 1)
						{
							$history_set[$entry['attrib_id']] = array
								(
								'value' => $entry['value'],
								'date' => $this->bocommon->date_to_timestamp($entry['date'])
							);
						}
					}
				}
			}

			if (isset($values_insert['p_num']) && $values_insert['p_num'])
			{
				//	$p_category		= $admin_entity->read_single_category($values_insert['p_entity_id'], $values_insert['p_cat_id']);
				//	$p_id			= (int) ltrim($values_insert['p_num'], $p_category['prefix']);
				$p_id = $values_insert['p_num'];
				$p_location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$values_insert['p_entity_id']}.{$values_insert['p_cat_id']}");
			}

			if (isset($values_insert['p_num']) && $values_insert['p_num'])
			{
				$values_insert['p_id'] = $p_id;
				$values_insert['p_location_id'] = $p_location_id;
			}
			
			return $values_insert;
		}
		
	}