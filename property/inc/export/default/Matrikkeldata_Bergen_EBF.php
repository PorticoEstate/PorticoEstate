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
			$buildings = $this->get_buildings();

			$name	 = array_keys($buildings[0]);
			$descr	 = array_keys($buildings[0]);

			CreateObject('property.bocommon')->download($buildings, $name, $descr);

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
				'db_port'	 => $config->config_data['matrikkelen']['port'],//'21525',
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

//			$this->db->debug = false;
//			$this->db->Host = 'oradb36i.srv.bergenkom.no';
//			$this->db->Port = 21525;
//			$this->db->Type = 'oci8';
//			$this->db->Database = 'MATPROD';
//			$this->db->User = 'GIS_BRUKER';
//			$this->db->Password = '3ruFrAdr';
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

				$value['etableringsdato'] = $this->db->f('ETABLERINGSDATO');
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
				$value['BYGNINGSTYPEKODE']	 = $this->db->f('BYGNINGSTYPEKODE');
			}
		}

		function get_buildings()
		{
			$db = & $GLOBALS['phpgw']->db;

			$sql = "SELECT DISTINCT bygningsnr,fm_location1.loc1 as objekt, loc1_name as navn, fm_owner_category.descr as eiertype FROM fm_location4
			JOIN fm_location1 on fm_location4.loc1 = fm_location1.loc1
			JOIN fm_owner ON fm_owner.id = fm_location1.owner_id
			JOIN fm_owner_category ON fm_owner.category = fm_owner_category.id
			WHERE fm_owner_category.id IN (2, 4)
			AND fm_location4.category != 99
			AND bygningsnr IS NOT NULL
			--AND bygningsnr = 300383295
			ORDER BY bygningsnr";

			$db->query($sql, __LINE__, __FILE__);
			$values = array();
			while ($db->next_record())
			{
				$values[] = $db->Record;
			}

			$this->get_matrikkel_info($values);
			return $values;
		}
	}