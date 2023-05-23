<?php
	/*
	 * this routine will only work with the exact configuration of Bergen Bolig og Byfornyelse - but can serve as an example
	 *
	 */

	class vekterinspeksjon extends property_boentity
	{

		protected $db;
		protected $account;

		function __construct()
		{
			parent::__construct();
			$this->db		 = & $GLOBALS['phpgw']->db;
			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];

			if ($this->acl_location != '.entity.2.19')
			{
				throw new Exception("'hent maaler til rapport' is intended for location = '.entity.2.19'");
			}
		}

		function update_data( $values, $values_attribute = array(), $action )
		{
			if (empty($values['location_code']))
			{
				return;
			}

			if ($action == 'edit')
			{
				return;
			}

			$db_dateformat 		= phpgwapi_db::date_format();

			$besiktet_dato = time();
			$datostempel = null;
			$behov_tilsyn = false;
			$manglende_tilgang = false;
			$merknad = null;

			if (isset($values_attribute) && is_array($values_attribute))
			{
				foreach ($values_attribute as $entry)
				{
					switch ($entry['name'])
					{
						case 'dato_registrert':
							if ($entry['value']) //datostempel
							{
								$datostempel = strtotime($entry['value']);
							}
							break;
						case 'behov_tilsyn':
							if ($entry['value'])
							{
								$behov_tilsyn = true;
							}
							break;
						case 'manglende_tilgang':
							if ($entry['value'])
							{
								$manglende_tilgang = true;
							}
							break;
						case 'merknad':
							if ($entry['value'])
							{
								$merknad = $entry['value'];
							}
							break;

						default :
					}
				}

				$historylog	 = CreateObject('property.historylog', 'entity_1_10');
				foreach ($values_attribute as $entry)
				{
					switch ($entry['name'])
					{
						case 'type_br_slokking':
							if ($entry['value'] && in_array($entry['value'], array(1, 3, 4) ) )
							{
								//1 = "Skiftet brannslokkingsapparat"
								//2 = "Husbrannslange"
								//3 = "Pulver"
								//4 = "Skum"

								$location_id_br_slokk_app	 = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.10');
								$new_value					 = date($db_dateformat, time());
								$sql						 = "SELECT json_representation->>'skiftet' as skiftet, json_representation->>'kontrollert' as kontrollert, id FROM fm_bim_item"
									. " WHERE location_id = {$location_id_br_slokk_app}"
									. " AND location_code='{$values['location_code']}'";
								$this->db->query($sql, __LINE__, __FILE__);
								$this->db->next_record();
								$old_kontrollert			 = $this->db->f('kontrollert');
								$old_value					 = $this->db->f('skiftet');
								$id							 = (int)$this->db->f('id');
								$update_interlink = false;
								if ($id)
								{
									if($entry['value'] == 1)
									{
									//	if (strtotime($new_value) != strtotime($old_value))
										if ( date('Y-m-d', strtotime($new_value)) != date('Y-m-d', strtotime($old_value)) )
										{
											$attrib_id = 2;
											$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);
											$sql		 = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{skiftet}', '\"{$new_value}\"', true)"
												. " WHERE location_id = {$location_id_br_slokk_app}"
												. " AND id = {$id}";

											$this->db->query($sql, __LINE__, __FILE__);
										}
									}

									if ( date('Y-m-d', strtotime($new_value)) != date('Y-m-d', strtotime($old_kontrollert)) )
									{
										$attrib_id = 3;
										$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);
										$sql		 = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{kontrollert}', '\"{$new_value}\"', true)"
											. " WHERE location_id = {$location_id_br_slokk_app}"
											. " AND id = {$id}";

										$this->db->query($sql, __LINE__, __FILE__);
										$update_interlink = true;
									}
									
									
								}
								else
								{
									$id = $this->br_slokk_app($new_value,$datostempel, $values['location_code']);
									$update_interlink = true;
								}


								if($id && $datostempel)
								{
									$new_value					 = date($db_dateformat, $datostempel);
									$attrib_id = 4;
									$historylog->add('SO', $id, $new_value, false, $attrib_id, $besiktet_dato);
									$sql		 = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{dato_registrert}', '\"{$new_value}\"', true)"
										. " WHERE location_id = {$location_id_br_slokk_app}"
										. " AND id = {$id}";

									$this->db->query($sql, __LINE__, __FILE__);
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

						case 'rokvarsler':
							//1 = "Kontrollert - OK"
							//2 = "Skiftet batteri"
							//3 = "Skiftet røykvarsler"
							if ($entry['value'] && in_array($entry['value'], array(1, 2, 3) ) )
							{
								$db_dateformat 		= phpgwapi_db::date_format();

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
								if (!$id)
								{
									$id = $this->add_roykvarsler($values['location_code']);
								}

								if ($id)
								{
									$update_interlink = true;
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
										}
									}
									else if($entry['value'] == 2) // Skiftet batteri
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
										}
									}
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


				if($behov_tilsyn)
				{
					$this->add_ticket($behov_tilsyn, $manglende_tilgang, $merknad, $values['id'], $values['location_code']);
				}
			}
		}


		private function add_ticket( $behov_tilsyn, $manglende_tilgang, $merknad,  $location1_item_id, $location_code)
		{
			$message_cat_id	 = 10006; // Melding fra eksterne
			$priority	 = 1;

			if($behov_tilsyn)
			{
				$message_title = 'Vekterinspeksjon: Behov for ekstra tilsyn';
				if(!$merknad)
				{
					$merknad = $message_title;
				}
			}

			if($manglende_tilgang)
			{
				$message_title = $message_title ? 'Vekterinspeksjon: Behov for ekstra tilsyn' : 'Vekterinspeksjon: Manglende tilgang';
				if(!$merknad)
				{
					$merknad = $message_title;
				}
			}

			$ticket		 = array
			(
				'location_code'		 => $location_code,
				'cat_id'			 => $message_cat_id,
				'priority'			 => $priority, //valgfri (1-3)
				'title'				 => $message_title,
				'details'			 => $merknad,
			);

			$ticket_id = CreateObject('property.botts')->add_ticket($ticket);
			$interlink_data = array
			(
				'location1_id'		 => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location),
				'location1_item_id'	 => $location1_item_id,
				'location2_id'		 => $GLOBALS['phpgw']->locations->get_id('property', '.ticket'),
				'location2_item_id'	 => $ticket_id,
				'account_id'		 => $this->account
			);

			CreateObject('property.interlink')->add($interlink_data, $this->db);

		}

		private function br_slokk_app( $date, $datostempel, $location_code )
		{
			$address	 = $this->get_address($location_code);
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

			$db_dateformat 		= phpgwapi_db::date_format();

			$value_set['status']		 = 1;
			$value_set['address']		 = $address;
			$value_set['skiftet']		 = $date;
			$value_set['datostempel']	 = date($db_dateformat, $datostempel);
			$value_set['location_code']	 = $location_code;
			$value_set['entry_date']	 = time();
			$value_set['user_id']		 = $this->account;

			$soentity	 = CreateObject('property.soentity');
			$id			 = $soentity->_save_eav($value_set, $location_id);

			$historylog	 = CreateObject('property.historylog', 'entity_1_10');
			$historylog->add('SO', $id, $date, false, $attrib_id	 = 2, time());
			return $id;
		}

		private function add_roykvarsler( $location_code )
		{
			$address = $this->get_address($location_code);
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
			$value_set['location_code']	 = $location_code;
			$value_set['entry_date']	 = time();
			$value_set['user_id']		 = $this->account;

			$soentity	 = CreateObject('property.soentity');
			$id			 = $soentity->_save_eav($value_set, $location_id);

			$historylog	 = CreateObject('property.historylog', 'entity_1_23');
			$historylog->add('SO', $id, 'Ny', false, $attrib_id = 2, time());
			return $id;
		}

		private function get_address( $location_code )
		{
			$address = '';
			$solocation		 = CreateObject('property.solocation');
			$location_data	 = $solocation->read_single($location_code);
			if($location_data['street_name'])
			{
				$address	 = "{$location_data['street_name']} {$location_data['street_number']}";
			}
			return $address;
		}
	}
	$vekterinspeksjon = new vekterinspeksjon();
	$vekterinspeksjon->update_data($values, $values_attribute, $action);
