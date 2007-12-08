<?php

//_debug_array($values);
//_debug_array($values_attribute);
//_debug_array($action);

		// this routine will only work with the exact configuration of Bergen Bolig og Byfornyelse - but can serve as an example
		
		$this->db = $this->bocommon->new_db();
		
		//'property'	= $GLOBALS['phpgw_info']['flags']['currentapp'];
		
		$sql = "SELECT ext_meter_id as maaler_nr FROM fm_entity_1_11 WHERE location_code='" . $values['location_code'] . "'";
		$this->db->query($sql,__LINE__,__FILE__);
		$this->db->next_record();
		$maaler_nr = $this->db->f('maaler_nr');
		
		if(!$values_attribute)
		{
			if ($maaler_nr)
			{
				$sql = "UPDATE fm_entity_2_1 set maaler_nr= '$maaler_nr' WHERE location_code ='" . $values['location_code'] . "'";
				$this->db->query($sql,__LINE__,__FILE__);
			}
		}		
		
		$sql = "SELECT besiktet_dato FROM fm_entity_2_1 WHERE id ='" . $values['id'] . "'";
		$this->db->query($sql,__LINE__,__FILE__);
		$this->db->next_record();
		if($this->db->f('besiktet_dato'))
		{
			$besiktet_dato = $this->db->from_timestamp($this->db->f('besiktet_dato'));
		}

		if (isSet($values_attribute) AND is_array($values_attribute))
		{
			foreach($values_attribute as $entry)
			{
				switch($entry['name'])
				{
					case 'maaler_nr':
						if($entry['value'] && ($entry['value'] != $maaler_nr))
						{
							$this->soproject = CreateObject('property.soproject');
							if ($values['street_name'])
							{
								$address = $this->db->db_addslashes($values['street_name'] . ' ' .$values['street_number']);
							}
							else
							{
								$address = $this->db->db_addslashes($values['location_name']);
							}				
							$this->soproject->update_power_meter($entry['value'],$values['location_code'],$address);
							
							$maaler_nr = $entry['value'];						
						}
					
						break;
					case 'maalerstand':
						if($entry['value'])
						{
							$new_value = $entry['value'];
						
							$this->db->query("select maaler_stand, id from fm_entity_1_11 WHERE ext_meter_id = '$maaler_nr' AND location_code ='" . $values['location_code']. "'",__LINE__,__FILE__);
							$this->db->next_record();
							$old_value = $this->db->f('maaler_stand');
							$id = $this->db->f('id');
							if($id)
							{
								$attrib_id = 8;
								if($new_value != $old_value)
								{
									$historylog	= CreateObject('property.historylog','entity_1_11');
									$historylog->add('SO',$id,$new_value,False, $attrib_id,$besiktet_dato);
									$this->db->query("UPDATE fm_entity_1_11 set maaler_stand = '$new_value' WHERE ext_meter_id = '$maaler_nr' AND location_code ='" . $values['location_code']. "'",__LINE__,__FILE__);								
								}
							}
						}
						break;	
				}
			}
		}


