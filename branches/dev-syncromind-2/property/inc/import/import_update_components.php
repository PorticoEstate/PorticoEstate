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
			foreach ($buildingpart_out_table as $k => $v)
			{
				if ($v['parent'])
				{
					$childs = $this->get_entity_categories(array('parent_id' => $v['parent']['id']));
					if (count($childs))
					{
						$category = array_values($childs)[0];
						$attrib_list = $this->bo->read_attrib(array('entity_id' => $category['entity_id'], 'cat_id' => $category['id'], 'allrows' => true));
					}
				}
			}
			
			return $attrib_list;
		}
		
	}