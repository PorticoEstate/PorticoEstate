<?php

		$this->db->query("SELECT * FROM $target_table WHERE id = {$id}",__LINE__,__FILE__);
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
		$j = 1;
		foreach ($loc_fields as $loc)
		{
			if($this->db->f($loc))
			{
				$location[] = $this->db->f($loc);
				$value_set["loc{$j}"] = $this->db->f($loc);	
			}
			$j++;
		}
		
		$value_set['location_code'] = implode('-', $location);
		$value_set	= $this->db->validate_update($value_set);
		$this->db->query("UPDATE $target_table SET $value_set WHERE id= {$id}",__LINE__,__FILE__);
