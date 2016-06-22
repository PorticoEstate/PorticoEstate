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
			$this->custom = createObject('property.custom_fields');
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
		
		public function add_entity_categories ($buildingpart_out_table)
		{
			$buildingparts = array();
			
			foreach ($buildingpart_out_table as $k => $v)
			{	
				if ($v['parent'])
				{
					$values = array();
					$childs = $this->get_entity_categories(array('parent_id' => $v['parent']['id']));
					if (count($childs))
					{
						$child = array_values($childs)[0];
						$attrib_list = $this->bo->read_attrib(array('entity_id' => $child['entity_id'], 'cat_id' => $child['id'], 'allrows' => true));
						
						foreach ($attrib_list as $attrib) 
						{
							$values['template_attrib'][] = $attrib['id'];
						}
						$values['category_template'] = $child['entity_id'].'_'.$child['id'];
					}
					$values['parent_id'] = $v['parent']['id'];
					$values['name'] = $v['name'];
					$values['descr'] = $v['name'];
					$values['entity_id'] = 3;
					$values['fileupload'] = 1;
					$values['loc_link'] = 1;
					$values['is_eav'] = 1;
					
					$receipt = $this->bo->save_category($values);
					
					if ($receipt['id'])
					{
						$buildingparts['added'][$k] = array('id'=> $receipt['id'], 'entity_id' => 3);
					}
					else {
						$buildingparts['not_added'][$k] = $k;
					}
				}
			}
			
			return $buildingparts;
		}
		
		public function add_bim_item($entity_categories)
		{
			foreach ($entity_categories as $entity) 
			{
				foreach ($entity['components'] as $component)
				{
					$receipt = $this->bo_entity->save(array(), $component, 'add', $entity['entity_id'], $entity['cat_id']);
				}
			}
			
			return;
		}
		
	}