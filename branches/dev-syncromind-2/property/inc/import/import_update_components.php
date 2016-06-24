<?php

	class import_components
	{

		protected $db;

		public function __construct()
		{
			$this->account = (int)$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db = & $GLOBALS['phpgw']->db;
			$this->join = $this->db->join;
			$this->bo = CreateObject('property.boadmin_entity', true);
			$this->bo_entity = CreateObject('property.boentity', true);
			$this->type = $this->bo_entity->type;
			$this->type_app = $this->bo_entity->type_app;
			$this->custom = CreateObject('property.custom_fields');
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
		
		public function add_bim_item($entity_categories)
		{
			$components_not_added = array();
			foreach ($entity_categories as $entity) 
			{
				if ($entity['cat_id'])
				{
					$attributes = $this->get_attributes($entity['entity_id'], $entity['cat_id']);

					$not_added = array();
					foreach ($entity['components'] as $values)
					{
						$attributes_values = $this->set_attributes_values($values, $attributes);
						$receipt = $this->bo_entity->save(array('cat_id' => $entity['cat_id']), $attributes_values, 'add', $entity['entity_id'], $entity['cat_id']);
						if (!$receipt['id'])
						{
							$not_added[] = 1;
						}
					}
					if (count($not_added))
					{
						$components_not_added[$entity['name']] = count($not_added);
					}
				}
			}
			
			return $components_not_added;
		}
		
	}