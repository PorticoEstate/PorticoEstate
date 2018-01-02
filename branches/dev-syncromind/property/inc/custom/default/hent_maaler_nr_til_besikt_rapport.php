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
			$this->db = & $GLOBALS['phpgw']->db;
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			if ($this->acl_location != '.entity.2.17')
			{
				throw new Exception("'hent maaler til rapport' is intended for location = '.entity.2.17'");
			}
		}

		function update_data( $values, $values_attribute = array() )
		{

			$location_id_maaler = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.11');

//			$sql = "SELECT maaler_nr as maaler_nr FROM fm_entity_1_11 WHERE location_code='{$values['location_code']}'";
			$sql = "SELECT json_representation->>'maaler_nr' as maaler_nr FROM fm_bim_item"
				. " WHERE location_id = {$location_id_maaler}"
				. " AND location_code='{$values['location_code']}'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$maaler_nr = $this->db->f('maaler_nr');

			if ($action != 'edit')
			{
				if ($maaler_nr)
				{
					$location_id_rapport = $GLOBALS['phpgw']->locations->get_id('property', '.entity.2.17');
//					$sql = "UPDATE fm_entity_2_17 SET maaler_nr= '{$maaler_nr}' WHERE location_code='{$values['location_code']}'";
					$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{maaler_nr}', '\"{$maaler_nr}\"', true)"
						. " WHERE location_id = {$location_id_rapport}"
						. " AND location_code='{$values['location_code']}'"
						. " AND json_representation->>'maaler_nr' IS NULL";
					$this->db->query($sql, __LINE__, __FILE__);
				}
			}

			$location_id_bod = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.14');
//			$sql = "SELECT beskrivelse FROM fm_entity_1_14 WHERE location_code='{$values['location_code']}'";
			$sql = "SELECT json_representation->>'beskrivelse' as beskrivelse FROM fm_bim_item"
				. " WHERE location_id = {$location_id_bod}"
				. " AND location_code='{$values['location_code']}'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$bod_beskrivelse = $this->db->f('beskrivelse');


			if ($action != 'edit')
			{
				if ($bod_beskrivelse)
				{
//					$sql = "UPDATE fm_entity_2_17 SET boder_antall= '{$bod_beskrivelse}' WHERE location_code='{$values['location_code']}'";
					$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{boder_antall}', '\"{$bod_beskrivelse}\"', true)"
						. " WHERE location_id = {$location_id_rapport}"
						. " AND location_code='{$values['location_code']}'"
						. " AND json_representation->>'boder_antall' IS NULL";
					$this->db->query($sql, __LINE__, __FILE__);
				}
			}


			$besiktet_dato = time();

			if (isset($values_attribute) && is_array($values_attribute))
			{
				foreach ($values_attribute as $entry)
				{
					switch ($entry['name'])
					{
						case 'maaler_nr':
							if ($entry['value'] && ($entry['value'] != $maaler_nr))
							{
								$this->soproject = CreateObject('property.soproject');
								if ($values['street_name'])
								{
									$address = $this->db->db_addslashes($values['street_name'] . ' ' . $values['street_number']);
								}
								else
								{
									$address = $this->db->db_addslashes($values['location_name']);
								}
								$this->soproject->update_power_meter($entry['value'], $values['location_code'], $address);

								$maaler_nr = $entry['value'];
							}

							break;
						case 'maalerstand':
							if ($entry['value'])
							{
								$new_value = $entry['value'];

//								$sql = "SELECT maaler_stand, id FROM fm_entity_1_11 WHERE maaler_nr = '{$maaler_nr}' AND location_code ='{$values['location_code']}'";
								$sql = "SELECT json_representation->>'maaler_stand' as maaler_stand, id FROM fm_bim_item"
									. " WHERE location_id = {$location_id_maaler}"
									. " AND location_code='{$values['location_code']}'"
									. " AND json_representation->>'maaler_nr' = '{$maaler_nr}'";
								$this->db->query($sql, __LINE__, __FILE__);
								$this->db->next_record();
								$old_value = $this->db->f('maaler_stand');
								$id = $this->db->f('id');
								if ($id)
								{
									$attrib_id = 8;
									if ($new_value != $old_value)
									{
										$historylog = CreateObject('property.historylog', 'entity_1_11');
										$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);

//										$sql ="UPDATE fm_entity_1_11 SET maaler_stand = '{$new_value}' WHERE maaler_nr = '{$maaler_nr}' AND location_code ='{$values['location_code']}'";
										$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{maaler_stand}', '\"{$new_value}\"', true)"
											. " WHERE location_id = {$location_id_maaler}"
											. " AND location_code='{$values['location_code']}'";
										$this->db->query($sql, __LINE__, __FILE__);
									}
								}
							}
							break;

						case 'boder_antall':
							if ($entry['value'])
							{
								$new_value = $entry['value'];

								//	$location_id_bod
//								$sql = "SELECT beskrivelse, id FROM fm_entity_1_14 WHERE location_code ='{$values['location_code']}'";
								$sql = "SELECT json_representation->>'beskrivelse' as beskrivelse, id FROM fm_bim_item"
									. " WHERE location_id = {$location_id_bod}"
									. " AND location_code='{$values['location_code']}'";
								$this->db->query($sql, __LINE__, __FILE__);
								$this->db->next_record();
								$old_value = $this->db->f('beskrivelse');
								$id = $this->db->f('id');
								if ($id)
								{
									$attrib_id = 1;
									if ($new_value != $old_value)
									{
										$historylog = CreateObject('property.historylog', 'entity_1_14');
										$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);
//										$sql = "UPDATE fm_entity_1_14 SET beskrivelse = '{$new_value}' WHERE location_code ='{$values['location_code']}'";
										$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{beskrivelse}', '\"{$new_value}\"', true)"
											. " WHERE location_id = {$location_id_bod}"
											. " AND location_code='{$values['location_code']}'";
										$this->db->query($sql, __LINE__, __FILE__);
									}
								}
								else
								{
									if ($values['street_name'])
									{
										$address = $this->db->db_addslashes($values['street_name'] . ' ' . $values['street_number']);
									}
									else
									{
										$address = $this->db->db_addslashes($values['location_name']);
									}

									$this->add_bod($new_value, $values['location_code'], $address);
								}
							}
							break;
						case 'br_slokk_app_skiftet':
							if ($entry['value'])
							{
								$location_id_br_slokk_app = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.10');
								$new_value = $entry['value'];
//								$sql = "SELECT skiftet, id FROM fm_entity_1_10 WHERE location_code ='{$values['location_code']}'";
								$sql = "SELECT json_representation->>'skiftet' as skiftet, id FROM fm_bim_item"
									. " WHERE location_id = {$location_id_br_slokk_app}"
									. " AND location_code='{$values['location_code']}'";
								$this->db->query($sql, __LINE__, __FILE__);
								$this->db->next_record();
								$old_value = $this->db->f('skiftet');
								$id = $this->db->f('id');
								if ($id)
								{
									$attrib_id = 2;
									if (strtotime($new_value) != strtotime($old_value))
									{
										$historylog = CreateObject('property.historylog', 'entity_1_10');
										$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);
										//									$sql = "UPDATE fm_entity_1_10 SET skiftet = '{$new_value}' WHERE location_code ='{$values['location_code']}'";
										$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{skiftet}', '\"{$new_value}\"', true)"
											. " WHERE location_id = {$location_id_br_slokk_app}"
											. " AND location_code='{$values['location_code']}'";

										$this->db->query($sql, __LINE__, __FILE__);
									}
								}
								else
								{
									if ($values['street_name'])
									{
										$address = $this->db->db_addslashes($values['street_name'] . ' ' . $values['street_number']);
									}
									else
									{
										$address = $this->db->db_addslashes($values['location_name']);
									}

									$this->br_slokk_app($new_value, $values['location_code'], $address);
								}
							}
							break;
						default:
					}
				}
			}
		}

		private function add_bod( $beskrivelse, $location_code, $address )
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.14');
			$location = explode('-', $location_code);
			$value_set = array();

			$i = 1;
			if (isset($location) AND is_array($location))
			{
				foreach ($location as $location_entry)
				{
					$value_set["loc{$i}"] = $location_entry;

					$i++;
				}
			}

			$value_set['address'] = $address;
			$value_set['beskrivelse'] = $beskrivelse;
			$value_set['location_code'] = $location_code;
			$value_set['entry_date'] = time();
			$value_set['user_id'] = $this->account;

			$soentity = CreateObject('property.soentity');
			$id = $soentity->_save_eav( $value_set, $location_id );

			$historylog = CreateObject('property.historylog', 'entity_1_14');
			$historylog->add('SO', $id, $beskrivelse, false, $attrib_id = 1, time());
		}

		private function br_slokk_app( $date, $location_code, $address )
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.10');
			$location = explode('-', $location_code);
			$value_set = array();

			$i = 1;
			if (isset($location) AND is_array($location))
			{
				foreach ($location as $location_entry)
				{
					$value_set["loc{$i}"] = $location_entry;

					$i++;
				}
			}

			$value_set['address'] = $address;
			$value_set['skiftet'] = $date;
			$value_set['location_code'] = $location_code;
			$value_set['entry_date'] = time();
			$value_set['user_id'] = $this->account;

			$soentity = CreateObject('property.soentity');
			$id = $soentity->_save_eav( $value_set, $location_id );

			$historylog = CreateObject('property.historylog', 'entity_1_10');
			$historylog->add('SO', $id, $date, false, $attrib_id = 2, time());
		}
	}
	$data_sync = new entity_data_sync();
	$data_sync->update_data($values, $values_attribute, $action);

