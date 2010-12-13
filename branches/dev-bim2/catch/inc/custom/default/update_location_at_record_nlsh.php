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
		
		$ids = array();
		$this->db->query("SELECT id FROM $target_table WHERE location_code is NULL",__LINE__,__FILE__);
		while ($this->db->next_record())
		{
			$ids[] = $this->db->f('id');
		}
		
		foreach ($ids as $_id)
		{
			$this->db->query("SELECT * FROM $target_table WHERE id = {$_id}",__LINE__,__FILE__);
			$this->db->next_record();
		
			$loc_fields = array
			(
				'eiendomid',
				'byggid',
				'etasjeid',
				'bruksenhetid',
				'romid'
			);

			$location = array();
			$value_set = array();
			$j = 1;
			foreach ($loc_fields as $loc)
			{
				if($this->db->f($loc))
				{
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
				$value_set	= $this->db->validate_update($value_set);
				$this->db->query("UPDATE $target_table SET $value_set WHERE id= {$_id}",__LINE__,__FILE__);
			}
		}
