<?php
	/*
	 * this routine will only work with the exact configuration of Bergen Bolig og Byfornyelse - but can serve as an example
	 *
	 */

	class overfoer_stroem extends property_boentity
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
			$this->account	 = (int)$GLOBALS['phpgw_info']['user']['account_id'];

			if ($this->acl_location != '.entity.2.17')
			{
				throw new Exception("'overfør måler vil bare fungere for location = '.entity.2.17'");
			}
		}

		function update_data( $values, $values_attribute = array(), $action )
		{
			if (empty($values['location_code']))
			{
				return;
			}

			if ($action != 'edit')
			{
				return;
			}

			$location_id_rapport = $GLOBALS['phpgw']->locations->get_id('property', '.entity.2.17');

			/**
			 * Sjekk om epost allerede er sendt
			 */
			$this->db->query("SELECT json_representation->>'epost_bkk' as epost_bkk, json_representation->>'innmeldingsdato' AS innmeldingsdato"
				. " FROM fm_bim_item"
				. " WHERE location_id = {$location_id_rapport}"
				. " AND id='{$values['id']}'", __LINE__, __FILE__);

			$this->db->next_record();
			if ($this->db->f('epost_bkk'))
			{
				return;
			}

			$innmeldingsdato = $this->db->f('innmeldingsdato');

			$_innmeldingsdato = $innmeldingsdato ? strtotime($innmeldingsdato) : time();

			$kundenummer		 = '';
			$objekt				 = $values['location_code'];
			$fra_dato			 = date("d/m-Y", $_innmeldingsdato);
			$tidligere_person	 = "{$values['location_data']['last_name']}, {$values['location_data']['first_name']}";

			if ($values['street_name'])
			{
				$address = $this->db->db_addslashes($values['street_name'] . ' ' . $values['street_number']);
			}
			else
			{
				$address = $this->db->db_addslashes($values['location_name']);
			}

			if (isset($values_attribute) && is_array($values_attribute))
			{
				foreach ($values_attribute as $entry)
				{
					switch ($entry['name'])
					{
						case 'maaler_nr':
							$maaler_nr = $entry['value'];
							break;
						case 'maalerstand':
							if ($entry['value'])
							{
								$maalerstand = $entry['value'];
							}
							break;

						default:
					}
				}
			}


			$error = false;
			if (!$maaler_nr)
			{
				phpgwapi_cache::message_set("Målernummer er ikke registrert", 'error');
				$error = true;
			}

			if (!$maalerstand)
			{
				phpgwapi_cache::message_set("Målerstand er ikke registrert", 'error');
				$error = true;
			}

			if ($error)
			{
				return;
			}

			$location_arr = explode('-', $values['location_code']);

			$loc1 = $location_arr[0];

			$this->db->query("SELECT district_id AS kunde_nr_id"
				. " FROM fm_location1 JOIN fm_part_of_town ON fm_location1.part_of_town_id = fm_part_of_town.id"
				. " WHERE loc1 = '{$loc1}'", __LINE__, __FILE__);
			$this->db->next_record();
			$kunde_nr_id = $this->db->f('kunde_nr_id');


			$location_id_meter_register = $GLOBALS['phpgw']->locations->get_id('property', '.entity.1.11');

			$this->db->query("SELECT id"
				. " FROM fm_bim_item"
				. " WHERE location_id = {$location_id_meter_register}"
				. " AND location_code='{$location_code}'"
				. " AND json_representation->>'maaler_nr' = '{$maaler_nr}'"
				. " AND json_representation->>'category' = '1'", __LINE__, __FILE__);

			$this->db->next_record();
			$id = $this->db->f('id');

			$maaler = createObject('property.boentity')->read_single(array(
				'entity_id'	 => 1,
				'cat_id'	 => 11,
				'id'		 => $id,
				'view'		 => true
			));


			foreach ($maaler['attributes'] as $attribute)
			{
				switch ($attribute['name'])
				{
					case 'kunde_nr':
						if (!empty($attribute['value']))
						{
							$kunde_nr_id = $attribute['value'];
						}
						$choice_list = $attribute['choice'];
						break;
					default:
				}
			}

			foreach ($choice_list as $choice)
			{
				if ($choice['id'] == $kunde_nr_id)
				{
					$kundenummer = $choice['value'];
				}
			}

			$this->db->query("SELECT preference_json->>'ressursnr' AS ressursnr FROM phpgw_preferences"
				. " WHERE phpgw_preferences.preference_owner = {$this->account}"
				. " AND phpgw_preferences.preference_app = 'property'", __LINE__, __FILE__);

			$this->db->next_record();
			$ressursnr =  $this->db->f('ressursnr');

			$subject = "Måleroverføring: {$address}";

			$toarray = array('support.norway@entelios.com');
//			$toarray = array('hc483@bergen.kommune.no' );
			$to		 = implode(';', $toarray);

			$from_name	 = $GLOBALS['phpgw_info']['user']['fullname'];
			$from_email	 = "{$from_name}<{$GLOBALS['phpgw_info']['user']['preferences']['property']['email']}>";

			$ccarray = array(
				'Muhammed.Ibrahim@bergen.kommune.no',
				'Kenneth.Tertnaes@bergen.kommune.no',
				$from_email
			);

			$cc		 = implode(';', $ccarray);

			$bcc = 'hc483@bergen.kommune.no';

			$html = <<<HTML
			<!DOCTYPE html>
			<html>
				<head>
					<style>
					table, th, td {
						border: 1px solid black;
					}
					th, td {
						padding: 10px;
					}
					th {
						text-align: left;
					}
					</style>
				</head>
				<body>
					<table>
					<caption>Vi ønsker at følgende anlegg blir overført til EBF (Etat for boligforvaltning)</caption>
						<tr>
							<td>Kundenummer</td>
							<td>{$kundenummer}</td>
						</tr>
						<tr>
							<td>Objekt</td>
							<td>{$objekt}</td>
						</tr>
						<tr>
							<td>Adresse</td>
							<td>{$address}</td>
						</tr>
						<tr>
							<td>Målernummer</td>
							<td>{$maaler_nr}</td>
						</tr>
						<tr>
							<td>Målerstand</td>
							<td>{$maalerstand}</td>
						</tr>
						<tr>
							<td>Fra dato</td>
							<td>{$fra_dato}</td>
						</tr>
						<tr>
							<td>Etter</td>
							<td>{$tidligere_person}</td>
						</tr>
					</table>
					<br/><br/>

					Mvh<br/><br/>
					$from_name<br/>
					Bergen kommune, Etat for boligforvaltning<br/>
					Postboks 7700, 5020 Bergen<br/>
					Besøksadresse: Kaigaten 4, inngang fra Peter Motzfeldts gate<br/>
					Epost: {$GLOBALS['phpgw_info']['user']['preferences']['property']['email']}<br/>
					Ressursnr: {$ressursnr}
				</body>
			</html>
HTML;

			try
			{
				$rc = CreateObject('phpgwapi.send')->msg('email', $to, $subject, $html, '', $cc, $bcc, $from_email, $from_name, 'html');
			}
			catch (Exception $e)
			{
				phpgwapi_cache::message_set($e->getMessage(), 'error');
			}

			/**
			 * Lagre kvittering på utført
			 */
			if ($rc)
			{

				phpgwapi_cache::message_set("Epost er sendt til support.norway@entelios.com	 om overføring av måler", 'message');

				$now = date(phpgwapi_db::date_format());

				$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{epost_bkk}', '\"{$now}\"', true)"
					. " WHERE location_id = {$location_id_rapport}"
					. " AND id='{$values['id']}'";
				$this->db->query($sql, __LINE__, __FILE__);
			}
		}
	}
	$data_sync = new overfoer_stroem();
	$data_sync->update_data($values, $values_attribute, $action);
