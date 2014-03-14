<?php
		if(!isset($this->db) || !is_object($this->db))
		{
			$this->db		= & $GLOBALS['phpgw']->db;
			$this->like		= & $this->db->like;		
		}

		$this->db2		= clone($this->db);

		if(!isset($target_table) || !$target_table)
		{
			$target_table = "fm_{$this->type_app[$this->type]}_{$entity_id}_{$cat_id}";
		}

		$sql = "SELECT id FROM {$target_table} WHERE (location_code is NULL OR location_code = '')";
		$metadata = $this->db->metadata($target_table);

		if(isset($metadata['target_id']))
		{
			$sql .= 'OR (target_id IS NOT NULL AND location_id IS NOT NULL AND p_num IS NULL)';
		}

		$ids = array();
		$this->db->query($sql,__LINE__,__FILE__);
		while ($this->db->next_record())
		{
			$ids[] = $this->db->f('id');
		}

		foreach ($ids as $_id)
		{
			$value_set = array();
			$this->db->query("SELECT * FROM $target_table WHERE id = {$_id}",__LINE__,__FILE__);
			$this->db->next_record();

			$location_id = $this->db->f('location_id');
			$target_id = $this->db->f('target_id');

			if($location_id  && $target_id)
			{
				$origin = $GLOBALS['phpgw']->locations->get_name($location_id);
				$origin_table =  $GLOBALS['phpgw']->locations->get_attrib_table($origin['appname'], $origin['location']);
				$origin_arr = explode('_', $origin_table);
				$p_entity_id = $origin_arr[2];
				$p_cat_id = $origin_arr[3];
				if($origin_table)
				{
					$this->db2->query("SELECT location_code, address, id AS target_id FROM {$origin_table} WHERE num = '{$target_id}'",__LINE__,__FILE__);
					$this->db2->next_record();
					$origin_location_code 		= $this->db2->f('location_code');
					$origin_address 			= $this->db2->f('address');
					$value_set['location_code'] = $origin_location_code;
				//	$value_set['p_num'] 		= $target_id;
					$value_set['p_num'] 		= $this->db2->f('target_id');
					$value_set['p_entity_id']	= $p_entity_id;
					$value_set['p_cat_id']		= $p_cat_id;
					$origin_location_code_arr 	= explode('-',$origin_location_code);
					if($origin_location_code_arr)
					{
						$j=1;
						foreach($origin_location_code_arr as $_loc)
						{
							$value_set["loc{$j}"]	= $_loc;
							$j++;
						}
					}
					$value_set['address']	= $origin_address;
				}
			}
			else
			{
				$loc_fields = array
				(
					'eiendomid',
					'byggid',
					'etasjeid',
					'bruksenhetid',
					'romid'
				);

				$location = array();
				$j = 1;
				foreach ($loc_fields as $loc)
				{
					if($this->db->f($loc))
					{

// 8.April 11: 'romid' is now loc5
/*
						if($loc == 'romid')
						{
							$this->db2->query("SELECT loc5 FROM fm_location5 WHERE rom_nr_id = '" . $this->db->f($loc) . "' AND location_code {$this->like} '" . implode('-', $location) . "%'",__LINE__,__FILE__);
	
							$this->db2->next_record();
							if($this->db2->f('loc5'))
							{
								$location[] = $this->db2->f('loc5');
								$value_set["loc{$j}"] = $this->db2->f('loc5');
							}
						}
						else
*/
						{
							$location[] = $this->db->f($loc);
							$value_set["loc{$j}"] = $this->db->f($loc);
						}
					}
					else
					{
						break;
					}
					$j++;
				}
				if($location)
				{
					$value_set['location_code'] = implode('-', $location);
				}
			}

			if($value_set)
			{
				$value_set	= $this->db->validate_update($value_set);
				$this->db->query("UPDATE $target_table SET $value_set WHERE id={$_id}",__LINE__,__FILE__);
			}
		}
