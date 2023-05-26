<?php
	/*
	 * this routine will only work with the exact configuration of Bergen Bolig og Byfornyelse - but can serve as an example
	 *
	 */

	class entity_data_sync extends property_boentity
	{

		protected $db;
		protected $config		 = array();
		protected $status_text	 = array();
		protected $custom_config;
		protected $account;

		function __construct()
		{
			parent::__construct();
			$this->db		 = & $GLOBALS['phpgw']->db;
			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];

			if ($this->acl_location != '.entity.2.17')
			{
				throw new Exception("'hent maaler til rapport' is intended for location = '.entity.2.17'");
			}
		}

		function update_data( $values, $values_attribute = array(), $action )
		{
			if (empty($values['location_code']))
			{
				return;
			}

			$location_id_maaler = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.11');

			$sql		 = "SELECT json_representation->>'maaler_nr' as maaler_nr FROM fm_bim_item"
				. " WHERE location_id = {$location_id_maaler}"
				. " AND location_code='{$values['location_code']}'";
			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$maaler_nr	 = $this->db->f('maaler_nr');

			$location_id_rapport = $GLOBALS['phpgw']->locations->get_id('property', '.entity.2.17');
			$location_id_bod	 = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.14');

			if ($action != 'edit')
			{
				if ($maaler_nr)
				{
					$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{maaler_nr}', '\"{$maaler_nr}\"', true)"
						. " WHERE location_id = {$location_id_rapport}"
						. " AND id='{$values['id']}'";
					$this->db->query($sql, __LINE__, __FILE__);
				}

				$sql			 = "SELECT json_representation->>'beskrivelse' as beskrivelse FROM fm_bim_item"
					. " WHERE location_id = {$location_id_bod}"
					. " AND location_code='{$values['location_code']}'";
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$bod_beskrivelse = $this->db->f('beskrivelse');


				if ($bod_beskrivelse)
				{
					$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{boder_antall}', '\"{$bod_beskrivelse}\"', true)"
						. " WHERE location_id = {$location_id_rapport}"
						. " AND id='{$values['id']}'";
					$this->db->query($sql, __LINE__, __FILE__);
				}

				$this->db->query("SELECT tv_signal FROM fm_location4 WHERE location_code = '{$values['location_code']}'", __LINE__, __FILE__);
				$this->db->next_record();
				$tv_signal = $this->db->f('tv_signal');

				if ($tv_signal)
				{
					$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{tv_signal}', '\"{$tv_signal}\"', true)"
						. " WHERE location_id = {$location_id_rapport}"
						. " AND id='{$values['id']}'";
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
								$soproject = CreateObject('property.soproject');
								if ($values['street_name'])
								{
									$address = $this->db->db_addslashes($values['street_name'] . ' ' . $values['street_number']);
								}
								else
								{
									$address = $this->db->db_addslashes($values['location_name']);
								}

								$id = $soproject->update_power_meter($entry['value'], $values['location_code'], $address);

								$maaler_nr = $entry['value'];
							}

							break;
						case 'maalerstand':
							if ($entry['value'])
							{
								$new_value = $entry['value'];

								$sql		 = "SELECT json_representation->>'maaler_stand' as maaler_stand, id FROM fm_bim_item"
									. " WHERE location_id = {$location_id_maaler}"
									. " AND location_code='{$values['location_code']}'"
									. " AND json_representation->>'maaler_nr' = '{$maaler_nr}'";
								$this->db->query($sql, __LINE__, __FILE__);
								$this->db->next_record();
								$old_value	 = $this->db->f('maaler_stand');
								$id			 = $this->db->f('id');
								if ($id)
								{
									$attrib_id = 8;
									if ($new_value != $old_value)
									{
										$historylog = CreateObject('property.historylog', 'entity_1_11');
										$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);

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
								$sql		 = "SELECT json_representation->>'beskrivelse' as beskrivelse, id FROM fm_bim_item"
									. " WHERE location_id = {$location_id_bod}"
									. " AND location_code='{$values['location_code']}'";
								$this->db->query($sql, __LINE__, __FILE__);
								$this->db->next_record();
								$old_value	 = $this->db->f('beskrivelse');
								$id			 = $this->db->f('id');
								if ($id)
								{
									$attrib_id = 1;
									if ($new_value != $old_value)
									{
										$historylog	 = CreateObject('property.historylog', 'entity_1_14');
										$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);
										$sql		 = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{beskrivelse}', '\"{$new_value}\"', true)"
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
								$location_id_br_slokk_app	 = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.10');
								$new_value					 = $entry['value'];
								$sql						 = "SELECT json_representation->>'skiftet' as skiftet, id FROM fm_bim_item"
									. " WHERE location_id = {$location_id_br_slokk_app}"
									. " AND location_code='{$values['location_code']}'";
								$this->db->query($sql, __LINE__, __FILE__);
								$this->db->next_record();
								$old_value					 = $this->db->f('skiftet');
								$id							 = $this->db->f('id');
								$update_interlink = false;
								if ($id)
								{
									$attrib_id = 2;
									if (strtotime($new_value) != strtotime($old_value))
									{
										$historylog	 = CreateObject('property.historylog', 'entity_1_10');
										$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);
										$sql		 = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{skiftet}', '\"{$new_value}\"', true)"
											. " WHERE location_id = {$location_id_br_slokk_app}"
											. " AND location_code='{$values['location_code']}'";

										$this->db->query($sql, __LINE__, __FILE__);
										$update_interlink = true;
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

									$id = $this->br_slokk_app($new_value, $values['location_code'], $address);
									$update_interlink = true;

								}

								if($update_interlink)
								{
									$interlink_data = array
									(
										'location1_id'		 => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location),
										'location1_item_id'	 => $values['id'],
										'location2_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.10'),
										'location2_item_id'	 => $id,
										'account_id'		 => $this->account
									);

									CreateObject('property.interlink')->add($interlink_data, $this->db);
								}

							}
							break;

						case 'roykvarsler':
							if ($entry['value'] && ($entry['value'] == 2 || $entry['value'] == 3))
							{
								$db_dateformat 		= phpgwapi_db::date_format();

								//2 = "Skiftet batteri"
								//3 = "Skiftet røykvarsler"

								$location_id_roykvarsler	 = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.23');
								$new_value					 = date($db_dateformat, time());
								$sql						 = "SELECT json_representation->>'skiftet' as skiftet, json_representation->>'batteriskift' as batteriskift, id FROM fm_bim_item"
									. " WHERE location_id = {$location_id_roykvarsler}"
									. " AND location_code='{$values['location_code']}'";
								$this->db->query($sql, __LINE__, __FILE__);
								$this->db->next_record();
								$old_skiftet				 = $this->db->f('skiftet');
								$old_batteriskift			 = $this->db->f('batteriskift');
								$id							 = $this->db->f('id');
								$update_interlink = false;
								if ($id)
								{
									if($entry['value'] == 3) //Skiftet røykvarsler"
									{
										$attrib_id = 2;
										if ( date('Y-m-d', strtotime($new_value)) != date('Y-m-d', strtotime($old_skiftet)) )
										{
											$historylog	 = CreateObject('property.historylog', 'entity_1_23');
											$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);
											$sql		 = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{skiftet}', '\"{$new_value}\"', true)"
												. " WHERE location_id = {$location_id_roykvarsler}"
												. " AND location_code='{$values['location_code']}'";

											$this->db->query($sql, __LINE__, __FILE__);
											$update_interlink = true;
										}
									}
									else // Skiftet batteri
									{
										$attrib_id = 3;
										if ( date('Y-m-d', strtotime($new_value)) != date('Y-m-d', strtotime($old_batteriskift)) )
										{
											$historylog	 = CreateObject('property.historylog', 'entity_1_23');
											$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);
											$sql		 = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{batteriskift}', '\"{$new_value}\"', true)"
												. " WHERE location_id = {$location_id_roykvarsler}"
												. " AND location_code='{$values['location_code']}'";

											$this->db->query($sql, __LINE__, __FILE__);
											$update_interlink = true;
										}
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

									$id = $this->add_roykvarsler($new_value, $values['location_code'], $address);
									$update_interlink = true;

								}

								if($update_interlink)
								{
									$interlink_data = array
									(
										'location1_id'		 => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location),
										'location1_item_id'	 => $values['id'],
										'location2_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.23'),
										'location2_item_id'	 => $id,
										'account_id'		 => $this->account
									);

									CreateObject('property.interlink')->add($interlink_data, $this->db);
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
			$location	 = explode('-', $location_code);
			$value_set	 = array();

			$i = 1;
			if (isset($location) AND is_array($location))
			{
				foreach ($location as $location_entry)
				{
					$value_set["loc{$i}"] = $location_entry;

					$i++;
				}
			}

			$value_set['address']		 = $address;
			$value_set['beskrivelse']	 = $beskrivelse;
			$value_set['location_code']	 = $location_code;
			$value_set['entry_date']	 = time();
			$value_set['user_id']		 = $this->account;

			$soentity	 = CreateObject('property.soentity');
			$id			 = $soentity->_save_eav($value_set, $location_id);

			$historylog	 = CreateObject('property.historylog', 'entity_1_14');
			$historylog->add('SO', $id, $beskrivelse, false, $attrib_id	 = 1, time());
		}

		private function br_slokk_app( $date, $location_code, $address )
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.10');
			$location	 = explode('-', $location_code);
			$value_set	 = array();

			$i = 1;
			if (isset($location) AND is_array($location))
			{
				foreach ($location as $location_entry)
				{
					$value_set["loc{$i}"] = $location_entry;

					$i++;
				}
			}

			$value_set['address']		 = $address;
			$value_set['skiftet']		 = $date;
			$value_set['location_code']	 = $location_code;
			$value_set['entry_date']	 = time();
			$value_set['user_id']		 = $this->account;

			$soentity	 = CreateObject('property.soentity');
			$id			 = $soentity->_save_eav($value_set, $location_id);

			$historylog	 = CreateObject('property.historylog', 'entity_1_10');
			$historylog->add('SO', $id, $date, false, $attrib_id	 = 2, time());
			return $id;
		}
		private function add_roykvarsler( $date, $location_code, $address )
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.23');
			$location	 = explode('-', $location_code);
			$value_set	 = array();

			$i = 1;
			if (isset($location) AND is_array($location))
			{
				foreach ($location as $location_entry)
				{
					$value_set["loc{$i}"] = $location_entry;

					$i++;
				}
			}

			$value_set['status']		 = 1;
			$value_set['address']		 = $address;
			$value_set['skiftet']		 = $date;
			$value_set['location_code']	 = $location_code;
			$value_set['entry_date']	 = time();
			$value_set['user_id']		 = $this->account;

			$soentity	 = CreateObject('property.soentity');
			$id			 = $soentity->_save_eav($value_set, $location_id);

			$historylog	 = CreateObject('property.historylog', 'entity_1_23');
			$historylog->add('SO', $id, $date, false, $attrib_id = 2, time());
			return $id;
		}
	}
	$data_sync = new entity_data_sync();
	$data_sync->update_data($values, $values_attribute, $action);
