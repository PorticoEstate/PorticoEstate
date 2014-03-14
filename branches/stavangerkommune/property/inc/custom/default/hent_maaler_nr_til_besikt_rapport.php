<?php

	/*
	* this routine will only work with the exact configuration of Bergen Bolig og Byfornyelse - but can serve as an example
	* 
	*/

	class entity_data_sync extends property_boentity
	{
		protected $db;
		protected $config = array();
		protected $status_text = array();
		protected $custom_config;
		protected $account;

		function __construct()
		{
			parent::__construct();
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			if($this->acl_location != '.entity.2.17')
			{
				throw new Exception("'hent maaler til rapport' is intended for location = '.entity.2.17'");
			}
		}

		function update_data($values,$values_attribute = array())
		{
			$sql = "SELECT maaler_nr as maaler_nr FROM fm_entity_1_11 WHERE location_code='{$values['location_code']}'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$maaler_nr = $this->db->f('maaler_nr');

			if($action!='edit')
			{
				if ($maaler_nr)
				{
					$sql = "UPDATE fm_entity_2_17 SET maaler_nr= '{$maaler_nr}' WHERE location_code='{$values['location_code']}'";
					$this->db->query($sql,__LINE__,__FILE__);
				}
			}

			$sql = "SELECT beskrivelse FROM fm_entity_1_14 WHERE location_code='{$values['location_code']}'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$bod_beskrivelse = $this->db->f('beskrivelse');


			if($action!='edit')
			{
				if ($bod_beskrivelse)
				{
					$sql = "UPDATE fm_entity_2_17 SET boder_antall= '{$bod_beskrivelse}' WHERE location_code='{$values['location_code']}'";
					$this->db->query($sql,__LINE__,__FILE__);
				}
			}


			$besiktet_dato = time();

			if (isset($values_attribute) && is_array($values_attribute))
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

								$this->db->query("SELECT maaler_stand, id FROM fm_entity_1_11 WHERE maaler_nr = '{$maaler_nr}' AND location_code ='{$values['location_code']}'",__LINE__,__FILE__);
								$this->db->next_record();
								$old_value = $this->db->f('maaler_stand');
								$id = $this->db->f('id');
								if($id)
								{
									$attrib_id = 8;
									if($new_value != $old_value)
									{
										$historylog	= CreateObject('property.historylog','entity_1_11');
										$historylog->add('SO',$id,$new_value,false, $attrib_id,$besiktet_dato);
										$this->db->query("UPDATE fm_entity_1_11 SET maaler_stand = '{$new_value}' WHERE maaler_nr = '{$maaler_nr}' AND location_code ='{$values['location_code']}'",__LINE__,__FILE__);
									}
								}
							}
							break;

						case 'boder_antall':
							if($entry['value'])
							{
								$new_value = $entry['value'];

								$this->db->query("SELECT beskrivelse, id FROM fm_entity_1_14 WHERE location_code ='{$values['location_code']}'",__LINE__,__FILE__);
								$this->db->next_record();
								$old_value = $this->db->f('beskrivelse');
								$id = $this->db->f('id');
								if($id)
								{
									$attrib_id = 1;
									if($new_value != $old_value)
									{
										$historylog	= CreateObject('property.historylog','entity_1_14');
										$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);
										$this->db->query("UPDATE fm_entity_1_14 SET beskrivelse = '{$new_value}' WHERE location_code ='{$values['location_code']}'",__LINE__,__FILE__);
									}
								}
								else
								{
									if ($values['street_name'])
									{
										$address = $this->db->db_addslashes($values['street_name'] . ' ' .$values['street_number']);
									}
									else
									{
										$address = $this->db->db_addslashes($values['location_name']);
									}

									$this->add_bod($new_value, $values['location_code'], $address);
								}
							}
							break;
					}
				}
			}
		}

		private function add_bod($beskrivelse,$location_code,$address)
		{
			$table = 'fm_entity_1_14';
			$location=explode('-',$location_code);
			$value_set = array();

			$i=1;
			if (isset($location) AND is_array($location))
			{
				foreach($location as $location_entry)
				{
					$value_set["loc{$i}"] = $location_entry;

					$i++;
				}
			}

			$value_set['id'] = $this->bocommon->next_id($table);
			$value_set['num'] = $value_set['id'];
			$value_set['address'] = $address;
			$value_set['beskrivelse'] = $beskrivelse;
			$value_set['location_code'] = $location_code;
			$value_set['entry_date'] = time();
			$value_set['user_id'] = $this->account;
		
			$cols = implode(',', array_keys($value_set));
			$values	= $this->db->validate_insert(array_values($value_set));
			$this->db->query("INSERT INTO $table ({$cols}) VALUES ({$values})",__LINE__,__FILE__);
			$historylog	= CreateObject('property.historylog','entity_1_14');
			$historylog->add('SO', $value_set['id'], $beskrivelse, false, $attrib_id = 1, time());
		}
	}

	$data_sync = new entity_data_sync();
	$data_sync->update_data($values, $values_attribute, $action);

