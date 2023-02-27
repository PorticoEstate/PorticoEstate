<?php

	class export_conv
	{

		var $db;

		public function __construct()
		{
			$this->get_db();
		}

		public function overfor( $download = 'on' )
		{
			$addresses = $this->get_addresses();

			$name	 = array_keys($addresses[0]);
			$descr	 = array_keys($addresses[0]);

			CreateObject('property.bocommon')->download($addresses, $name, $descr);

			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		/* php ping function
		 */

		private function ping( $host )
		{
			exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
			return $rval === 0;
		}

		function get_db()
		{
			if ($this->db)
			{
				return $this->db;
			}

			$config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.admin'));

			$db_info = array(
				'db_host'	 => $config->config_data['matrikkelen']['host'], //'oradb36i.srv.bergenkom.no',
				'db_type'	 => 'oci8',
				'db_port'	 => $config->config_data['matrikkelen']['port'], //'21525',
				'db_name'	 => $config->config_data['matrikkelen']['db_name'], //'MATPROD',
				'db_user'	 => $config->config_data['matrikkelen']['user'], //'GIS_BRUKER',
				'db_pass'	 => $config->config_data['matrikkelen']['password'],
			);

//			_debug_array($db_info);

			if (!$db_info['db_host'] || !$this->ping($db_info['db_host']))
			{
				$message = "Database server {$db_info['db_host']} is not accessible";
				echo $message;
				return false;
			}

			$this->db = createObject('phpgwapi.db_adodb', null, null, true);

			$this->db->debug	 = false;
			$this->db->Type		 = 'oci8';
			$this->db->Host		 = $db_info['db_host'];
			$this->db->Port		 = $db_info['db_port'];
			$this->db->Database	 = $db_info['db_name'];
			$this->db->User		 = $db_info['db_user'];
			$this->db->Password	 = $db_info['db_pass'];

			try
			{
				$this->db->connect();
			}
			catch (Exception $e)
			{
				$message = lang('unable_to_connect_to_database');
				echo $message;
				return false;
			}

			return $this->db;
		}

		function get_matrikkel_info( & $values )
		{
			$sql = "SELECT DISTINCT MATRIKKELENHET.ID, GATE.GATENAVN, ADRESSE.HUSNR, ADRESSE.BOKSTAV,
			MATRIKKELENHET.CLASS as MATRIKKELENHET_CLASS, MATRIKKELENHET.ETABLERINGSDATO, MATRIKKELENHET.OPPGITTAREAL, BYGG.BYGNINGSNR, BYGG.CLASS as BYGG_CLASS, BYGG.BEBYGDAREAL, BYGG.ANTALLBOENHETER as BYGG_ANTALLBOENHETER, BYGG.BRUKSAREALTILBOLIG, BYGG.BRUKSAREALTILANNET, BYGG.BRUKSAREALTOTALT, BYGNINGSTYPEKODE
			FROM MATRIKKELENHET, BYGG, BRUKSENHET, GATE, ADRESSE
			WHERE MATRIKKELENHET.ID = BRUKSENHET.MATRIKKELENHETID
			AND BRUKSENHET.BYGGID = BYGG.ID
			AND BRUKSENHET.ADRESSEID = ADRESSE.ID
			AND GATE.ID = ADRESSE.GATEID
			AND BYGG.CLASS = 'Bygning'
			AND MATRIKKELENHET.UTGATT = 0";

			foreach ($values as &$value)
			{

				$bygningsnr = (int)$value['bygningsnr'];

				$sql = "SELECT DISTINCT
			-- BYGNINGSTATUSHISTORIKK.*,
			--MATRIKKELENHET.*,
			--BYGG.*,
				GATE.GATENAVN, ADRESSE.HUSNR, ADRESSE.BOKSTAV,
				BYGNINGSTATUSHISTORIKK.REGISTRERTDATO as DATO,
				MATRIKKELENHET.ID,
				MATRIKKELENHET.CLASS as MATRIKKELENHET_CLASS,
				MATRIKKELENHET.ETABLERINGSDATO,
				MATRIKKELENHET.OPPGITTAREAL,
				BYGG.BYGNINGSNR,
				BYGG.CLASS as BYGG_CLASS,
				BYGG.BEBYGDAREAL,
				BYGG.ANTALLBOENHETER as BYGG_ANTALLBOENHETER,
				BYGG.BRUKSAREALTILBOLIG as BYGG_BRUKSAREALTILBOLIG,
				BYGG.BRUKSAREALTILANNET as BYGG_BRUKSAREALTILANNET,
				BYGG.BRUKSAREALTOTALT as BYGG_BRUKSAREALTOTALT,
				BYGNINGSTYPEKODE,
				MATRIKKELENHET.KOMMUNEID,
				MATRIKKELENHET.GARDSNR,
				MATRIKKELENHET.BRUKSNR,
				MATRIKKELENHET.FESTENR,
				MATRIKKELENHET.SEKSJONSNR
				FROM MATRIKKELENHET, BYGG, BRUKSENHET, GATE, ADRESSE, BYGNINGSTATUSHISTORIKK
				WHERE MATRIKKELENHET.ID = BRUKSENHET.MATRIKKELENHETID
				AND BRUKSENHET.BYGGID = BYGG.ID
				AND BYGNINGSTATUSHISTORIKK.BYGGID = BYGG.ID
				AND BRUKSENHET.ADRESSEID = ADRESSE.ID
				AND GATE.ID = ADRESSE.GATEID
				AND BYGNINGSNR = {$bygningsnr}
				AND BYGG.CLASS = 'Bygning'
				AND MATRIKKELENHET.UTGATT = 0
				AND BYGNINGSTATUSHISTORIKK.BYGNINGSTATUSKODE IN ('FA', 'TB', 'MB')";

				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				{
					$debug = false;
					if ($debug)
					{
						$result = $this->db->Record;
						_debug_array($result);
						die();
					}
				}

				$value['Matrikkel_Adresse']	 = $this->db->f('GATENAVN') . " " . $this->db->f('HUSNR') . " " . $this->db->f('BOKSTAV');
				$value['etableringsdato']	 = $this->db->f('ETABLERINGSDATO');
				if (!$value['etableringsdato'])
				{
					$value['etableringsdato'] = $this->db->f('DATO');
				}
				$value['bruksareal_bolig']	 = $this->db->f('BYGG_BRUKSAREALTILBOLIG');
				$value['bruksareal_annet']	 = $this->db->f('BYGG_BRUKSAREALTILANNET');
				$value['bruksareal_totalt']	 = $this->db->f('BYGG_BRUKSAREALTOTALT');
				$value['antall_boenheter']	 = $this->db->f('BYGG_ANTALLBOENHETER');
				$value['kommune_id']		 = $this->db->f('KOMMUNEID');
				$value['gardsnr']			 = $this->db->f('GARDSNR');
				$value['bruksnr']			 = $this->db->f('BRUKSNR');
				$value['Bygningstypekode']	 = $this->db->f('BYGNINGSTYPEKODE');
			}
		}

		function get_addresses()
		{
			$db = & $GLOBALS['phpgw']->db;

			$sql = "SELECT DISTINCT bygningsnr,fm_location1.loc1 as objekt, loc1_name as navn, fm_owner_category.descr as eiertype, sum(boareal) as leieareal,
				street_id, fm_streetaddress.descr as gatenavn, street_number as gatenummer
			FROM fm_location4
			JOIN fm_location1 on fm_location4.loc1 = fm_location1.loc1
			JOIN fm_owner ON fm_owner.id = fm_location1.owner_id
			JOIN fm_owner_category ON fm_owner.category = fm_owner_category.id
			JOIN fm_streetaddress ON fm_location4.street_id = fm_streetaddress.id
			-- WHERE fm_owner_category.id IN (4)
			WHERE fm_location4.category != 99
			AND bygningsnr IS NOT NULL
			--AND bygningsnr = 300383295
			--AND fm_location1.loc1 = '5001'
			GROUP BY bygningsnr, objekt, navn, eiertype, street_id, gatenavn, gatenummer
            ORDER BY objekt";

			$db->query($sql, __LINE__, __FILE__);

			$addresses = array();
			while ($db->next_record())
			{
				$addresses[] = $db->Record;
			}

			$this->get_matrikkel_info($addresses);

			$solocation = CreateObject('property.solocation');

			$return				 = array();
			$maalepunkter_total	 = array();
			$get_maalepunkt = false;

			foreach ($addresses as & $addresse)
			{


				$sql		 = "SELECT DISTINCT fm_location4_category.descr as formaal"
					. " FROM fm_location4"
					. " JOIN fm_location4_category ON fm_location4.category = fm_location4_category.id"
					. " WHERE street_id = {$addresse['street_id']}"
					. " AND street_number = '{$addresse['gatenummer']}'"
					. " AND fm_location4_category.id NOT IN (99)"
					. " ORDER BY fm_location4_category.descr";
				$db->query($sql, __LINE__, __FILE__);
				$categories	 = array();
				while ($db->next_record())
				{
					$categories[] = $db->f('formaal', true);
				}

				$addresse['formaal'] = implode(', ', $categories);

				$zip_info = $solocation->get_zip_info($addresse['objekt']);

				$addresse['postnr']		 = $zip_info['zip_code'];
				$addresse['poststed']	 = $zip_info['city'];

				$sql = "SELECT DISTINCT loc1, loc2, loc3  FROM fm_location4 WHERE street_id = {$addresse['street_id']}"
					. " AND street_number = '{$addresse['gatenummer']}'"
					. " AND fm_location4.category NOT IN (99)";

				$db->query($sql, __LINE__, __FILE__);
				$location_codes = array();
				while ($db->next_record())
				{
					$location_codes[] = $db->f('loc1') . '-' . $db->f('loc2') . '-' . $db->f('loc3');
				}

				$addresse['lokation_code']	 = implode(', ', $location_codes);
				$addresse['Heis']			 = '';
				$addresse['nhk_nummer']		 = '';

				if ($get_maalepunkt)
				{

					$maalepunkter	 = array();
					$heiser			 = array();
					foreach ($location_codes as $location_code)
					{
						$sql = "SELECT DISTINCT location_code, json_representation->>'maalepunkt_id' as maalepunkt_id FROM fm_bim_item "
							. " WHERE fm_bim_item.location_id = 25" // el-anlegg
							. " AND fm_bim_item.location_code like '{$location_code}%'"
							. " AND (json_representation->>'maalepunkt_id' IS NOT NULL )"
							. " AND (json_representation->>'category' = '2' )"; // felles

						$db->query($sql, __LINE__, __FILE__);

						while ($db->next_record())
						{
							$addresse['lokation_code']	 = $db->f('location_code');
							$maalepunkt_id				 = $db->f('maalepunkt_id');
							if ($maalepunkt_id && strlen($maalepunkt_id) < 9)
							{
								$maalepunkt_id = '7070575000' . $maalepunkt_id;
							}

							$maalepunkter[]				 = true;
							$addresse['maalepkunkt_id']	 = $maalepunkt_id;

							if (!isset($maalepunkter_total[$maalepunkt_id]))
							{
								$return[]							 = $addresse;
								$maalepunkter_total[$maalepunkt_id]	 = true;
							}
						}

						$sql = "SELECT DISTINCT location_code, json_representation->>'nhk_nummer' as nhk_nummer FROM fm_bim_item "
							. " WHERE fm_bim_item.location_id = 23" // Heis
							. " AND fm_bim_item.location_code like '{$location_code}%'";

						$db->query($sql, __LINE__, __FILE__);

						$nhk_nummer = array();
						while ($db->next_record())
						{
							$heiser[] = true;

							$addresse['Heis']	 = 'Ja';
							$nhk_nummer[]		 = $db->f('nhk_nummer');
						}
						$addresse['nhk_nummer'] = implode(', ', $nhk_nummer);

						if (!$maalepunkter)
						{
							$addresse['maalepkunkt_id']	 = '';
							$return[]					 = $addresse;
						}
					}
				}
				else
				{
					$return[]					 = $addresse;
				}
			}

			return $return;
		}
	}