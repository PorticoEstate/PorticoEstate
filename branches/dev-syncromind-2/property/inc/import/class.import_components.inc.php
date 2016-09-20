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
			//$this->bo_entity = CreateObject('property.boentity', true);
			$this->custom = CreateObject('property.custom_fields');
			$this->bocommon = CreateObject('property.bocommon');
		}

		private function _get_attributes($entity_id, $cat_id)
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
		
		private function _set_attributes_values($values, $attributes)
		{
			foreach($attributes as &$attribute)
			{
				$attribute['value'] = $values[$attribute['name']];
			}
			
			return $attributes;
		}
		
		public function prepare_preview_components($entity_categories)
		{
			$components = array();
		
			foreach ($entity_categories as $entity) 
			{
				foreach ($entity['components'] as $values)
				{
					unset($values['building_part']);
					unset($values['category_name']);
					$components[] = $values;
				}	
			}
			
			return $components;

			/*$config = createObject('phpgwapi.config', 'component_import');
			$config->read_repository();
			$config->value('import_preview_components', serialize($components));
			$config->save_repository();*/
		}
		
		public function add_components($entity_categories, $location_code)
		{
			$components_added = array();
			$message = array();
			
			$location_code_values = explode('-', $location_code);
			$i = 0;
			$location = array();
			foreach ($location_code_values as $loc) 
			{
				$i++;
				$location['loc'.$i] = $loc;
			}
			
			$this->db->transaction_begin();
			
			try
			{
				$this->db->Exception_On_Error = true;
				foreach ($entity_categories as $entity) 
				{
					$attributes = $this->_get_attributes($entity['entity_id'], $entity['cat_id']);

					foreach ($entity['components'] as $values)
					{
						$attributes_values = $this->_set_attributes_values($values, $attributes);
						
						$values_insert = $this->_populate(array('location_code'=>$location_code, 'location'=>$location), $attributes_values);

						$receipt = $this->_save_eav($values_insert, $entity['entity_id'], $entity['cat_id']);
						if (!$receipt['id'])
						{
							throw new Exception("component {$values_insert['benevnelse']} not added");
						}
						$components_added[$values_insert['benevnelse']] = $receipt;
					}	
				}
				$this->db->Exception_On_Error = false;
			}
			catch (Exception $e)
			{
				if ($e)
				{
					$this->db->transaction_abort();
					$message['error'][] = array('msg' => $e->getMessage());
					return $message;
				}
			}

			$this->db->transaction_commit();
			$message['message'][] = array('msg' => 'all components saved successfully');
			
			return $message; 
		}
		
		private function _save_eav( $data, $entity_id, $cat_id )
		{
			$location_id = (int) $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}");
			//$location_name = "_{$this->type}_{$entity_id}_{$cat_id}";

			$this->db->query("SELECT id as type FROM fm_bim_type WHERE location_id = {$location_id}", __LINE__, __FILE__);
			$this->db->next_record();
			$type = $this->db->f('type');
			$id = $this->db->next_id('fm_bim_item', array('type' => $type));

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
				'json_representation' => json_encode($data),
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
				return array('id' => $id, 'location_id' => $location_id); 
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