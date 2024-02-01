<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage cron
	 * @version $Id$
	 */
	/**
	 * Description
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default deceased_tenants_BK_EBF
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');

	class deceased_tenants_BK_EBF extends property_cron_parent
	{

		var $b_accounts = array();
		var $join, $connected, $boei;

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location	 = lang('property');
			$this->function_msg	 = '1) Leietakere med adressebeskyttelse 2) Leietakere registrert som død i folkeregisteret - men ikke i BOEI';
			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->join			 = & $this->db->join;
		}

		function populate_database($checklist)
		{

			$this->db->limit_query("SELECT timestamp FROM fm_fiks_data", 0, __LINE__, __FILE__, 1);
			$this->db->next_record();
			$timestamp = $this->db->f('timestamp');
			// Not more frequent than once a day

			$yesterday = time() - (24 * 3600);
			if($timestamp && $timestamp > $yesterday)
			{
				return $timestamp;
			}

			$fiks = new fiks();
			
//			_debug_array(date('Y-m-d G:i:s'));
			$valueset	 = array();

			$persons = array();

			$second_attempt = array();

			foreach ($checklist  as $person)
			{
				$data = $fiks->get_name_from_external_service($person['person_nr']);

				$data['person_nr'] = $person['person_nr'];
				$data['objekt_id'] = $person['objekt_id'];
				$data['leie_id'] = $person['leie_id'];
				$data['flyttenr'] = $person['flyttenr'];
				
				if(!empty($data['first_name']))
				{
					$persons[] = $data;
				}
				else
				{
					$second_attempt[] = $person;
				}
			}

			unset($person);

			foreach ($second_attempt  as $person)
			{
				$data = $fiks->get_name_from_external_service($person['person_nr']);

				$data['person_nr'] = $person['person_nr'];
				$data['objekt_id'] = $person['objekt_id'];
				$data['leie_id'] = $person['leie_id'];
				$data['flyttenr'] = $person['flyttenr'];
				$persons[] = $data;

			}

			unset($person);

			foreach ($persons  as $person)
			{

				$valueset[] = array
					(
					1	 => array
						(
						'value'	 => $person['person_nr'],
						'type'	 => PDO::PARAM_STR
					),
					2	 => array
						(
						'value'	 => $person['first_name'],
						'type'	 => PDO::PARAM_STR
					),
					3	 => array
						(
						'value'	 => $person['last_name'],
						'type'	 => PDO::PARAM_STR
					),
					4	 => array
						(
						'value'	 => $person['street'],
						'type'	 => PDO::PARAM_STR
					),
					5	 => array
						(
						'value'	 => (int)$person['zip_code'],
						'type'	 => PDO::PARAM_INT
					),
					6	 => array
						(
						'value'	 => $person['city'],
						'type'	 => PDO::PARAM_STR
					),
					7	 => array
						(
						'value'	 => $person['foedselsdato'],
						'type'	 => PDO::PARAM_STR
					),
					8	 => array
						(
						'value'	 => $person['adressebeskyttelse'],
						'type'	 => PDO::PARAM_STR
					),
					9	 => array
						(
						'value'	 => $person['status'],
						'type'	 => PDO::PARAM_STR
					),
					10	 => array
						(
						'value'	 => $person['doedsdato'] ? $person['doedsdato'] : null,
						'type'	 => PDO::PARAM_STR
					),
					11	 => array
						(
						'value'	 => time(),
						'type'	 => PDO::PARAM_INT
					),
					12	 => array
						(
						'value'	 => $person['objekt_id'],
						'type'	 => PDO::PARAM_STR
					),
					13	 => array
						(
						'value'	 => $person['leie_id'],
						'type'	 => PDO::PARAM_STR
					),
					14	 => array
						(
						'value'	 => (int)$person['flyttenr'],
						'type'	 => PDO::PARAM_INT
					),
				);

			}

			$this->db		 = clone($GLOBALS['phpgw']->db);

			if($valueset)
			{
				$this->db->query("DELETE FROM fm_fiks_data",__LINE__, __FILE__);
				$sql = 'INSERT INTO fm_fiks_data (ssn, first_name, last_name, street, zip_code, city, foedselsdato,'
					. ' adressebeskyttelse, status, doedsdato, timestamp, objekt_id, leie_id, flyttenr)'
						. ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

				$this->db->insert($sql, $valueset, __LINE__, __FILE__);

			}
//			_debug_array(date('Y-m-d G:i:s'));

			return 0;

		}

		function get_deceased( $checklist )
		{

			$checklist_set = array_chunk($checklist, 500);

			$person_data = array();

			foreach ($checklist_set as $_checklist)
			{
				$person_nr = array();
				foreach ($_checklist as $person)
				{
					$person_nr[] = "'{$person['person_nr']}'";
				}


				$person_list = implode(',', $person_nr);

				if ($person_nr)
				{
					$sql = "SELECT * FROM fm_fiks_data WHERE doedsdato IS NOT NULL"
						. " AND ssn IN ($person_list)";

					$this->db->query($sql, __LINE__, __FILE__);

					while ($this->db->next_record())
					{
						$person_data[] = array(
							'person_nr'			 => $this->db->f('ssn'),
							'navn'				 => $this->db->f('last_name', true) . ' ' . $this->db->f('first_name', true),
							'f_dato'			 => $this->db->f('foedselsdato'),
							'd_dato'			 => $this->db->f('doedsdato'),
							'postens_adresse'	 => $this->db->f('street', true)
						);
					}
				}
			}
			return $person_data;
		}

		function get_shielded( $checklist )
		{

			$checklist_set = array_chunk($checklist, 500);


			$person_data = array();

			foreach ($checklist_set as $_checklist)
			{
				$person_nr = array();
				foreach ($_checklist as $person)
				{
					$person_nr[] = "'{$person['person_nr']}'";
				}


				$person_list = implode(',', $person_nr);

				if ($person_nr)
				{
					$sql = "SELECT * FROM fm_fiks_data WHERE doedsdato IS NULL"
						. " AND adressebeskyttelse != 'ugradert'"
						. " AND ssn IN ($person_list)";

					$this->db->query($sql, __LINE__, __FILE__);

					while ($this->db->next_record())
					{
						$person_nr = $this->db->f('ssn');
						$person_data[$person_nr] = array(
							'person_nr'			 => $person_nr,
							'navn'				 => $this->db->f('last_name', true) . ' ' . $this->db->f('first_name', true),
							'f_dato'			 => $this->db->f('foedselsdato'),
							'd_dato'			 => $this->db->f('doedsdato'),
							'beste_adresse'		 => $this->db->f('street', true),
							'adressebeskyttelse' => $this->db->f('adressebeskyttelse', true),
						);
					}
				}
			}
			return $person_data;
		}

		function execute()
		{
			$this->boei	 = new boei();
			$checklist	 = $this->boei->get_checklist();

			$timestamp = $this->populate_database($checklist);

			$yesterday = time() - (24 * 3600);
			if($timestamp && $timestamp > $yesterday)
			{
				return $timestamp;
			}

			$this->handle_deceased($checklist);
			$this->handle_shielded();

		}

		function handle_deceased($checklist)
		{
			$deceased	 = $this->get_deceased($checklist);

			$cols = array('navn', 'f_dato', 'd_dato', 'BOEI_adresse', 'objekt_id', 'leie_id',
				'flyttenr');

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
					 <caption>Leietakere som er registrert som død i folkeregisteret - men ikke i BOEI</caption>
					<tr>
						<th>Navn</th>
						<th>Født dato</th>
						<th>Død dato</th>
						<th>BOEI adresse</th>
						<th>Objekt ID</th>
						<th>Leie ID</th>
						<th>Flytte NR</th>
					</tr>
HTML;

			foreach ($deceased as &$entry)
			{
				$entry	 = array_merge($entry, $checklist[$entry['person_nr']]);
				$html	 .= <<<HTML
					<tr>
HTML;

				foreach ($cols as $key)
				{
					$html .= <<<HTML
						<td>{$entry[$key]}</td>
HTML;
				}

				$html .= <<<HTML
			</tr>
HTML;
			}

			$html .= <<<HTML
					</table>
				</body>
			</html>
HTML;

//echo $html;
//return;
			$subject = 'Leietakere som er registrert som død i folkeregisteret';

			$toarray = array(
				'hc483@bergen.kommune.no',
				'Bjørvik, Ole Christian <Ole.Bjorvik@bergen.kommune.no>',
				);
			$to		 = implode(';', $toarray);

			try
			{
				$rc	 = CreateObject('phpgwapi.send')->msg('email', $to, $subject, $html, '', $cc	 = '', $bcc = '', 'hc483@bergen.kommune.no', 'Ikke svar', 'html');
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}

			$msg						 = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);
			echo "$msg\n";
			$this->receipt['message'][]	 = array('msg' => $msg);
		}

		function handle_shielded()
		{
			$checklist	 = $this->boei->get_checklist_shielded();
			$shielded	 = $this->get_shielded($checklist);

			$boei_hemmelig = array();

			foreach ($checklist as $_person_nr => $entry)
			{
				if($entry['hemmeligadresse'])
				{
					$boei_hemmelig[] = $_person_nr;
					$shielded[$_person_nr] = array_merge((array)$shielded[$_person_nr], $entry);
				}
			}
			unset($_person_nr);
			unset($entry);


			foreach ($shielded as $_person_nr => &$entry)
			{
				if($entry['hemmeligadresse'] && empty($entry['adressebeskyttelse']))
				{
					$entry['adressebeskyttelse'] = "<table border='0'><tr><td>Folkeregister</td><td>: Ugradert</tr><tr><td>BOEI</td><td>: Hemmelig adresse</td></tr></table>";
				}
				else if($entry['hemmeligadresse'] && !empty($entry['adressebeskyttelse']))
				{
					$entry['adressebeskyttelse'] = "<table border='0'><tr><td>Folkeregister</td><td>: <b>{$entry['adressebeskyttelse']}</b></tr><tr><td>BOEI</td><td>: Hemmelig adresse</td></tr></table>";
				}

				if($entry['adressebeskyttelse'] == 'strengtFortrolig')
				{
					$entry['beste_adresse'] = 'Hemmelig';
				}
			}


			$cols = array('navn', 'f_dato', 'beste_adresse','adressebeskyttelse', 'objekt_id', 'leie_id',
				'flyttenr');

			$html = <<<HTML
			<!DOCTYPE html>
			<html>
				<head>
					<style>
					table, th, td {
						border: 1px solid black;
						border-collapse: collapse;
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
					 <caption>Leietakere som er registrert med skjermet adresse</caption>
					<tr>
						<th>Navn</th>
						<th>Født dato</th>
						<th>Adresse</th>
						<th>Adressebeskyttelse</th>
						<th>Objekt ID</th>
						<th>Leie ID</th>
						<th>Flytte NR</th>
					</tr>
HTML;

			foreach ($shielded as &$entry)
			{
				$entry	 = array_merge($entry, $checklist[$entry['person_nr']]);
				$html	 .= <<<HTML
					<tr>
HTML;

				foreach ($cols as $key)
				{
					$html .= <<<HTML
						<td>{$entry[$key]}</td>
HTML;
				}

				$html .= <<<HTML
			</tr>
HTML;
			}

			$html .= <<<HTML
					</table>
				</body>
			</html>
HTML;


//echo $html;
//return;
			$subject = 'Leietakere som er registrert med skjermet adresse i folkeregisteret';

			$toarray = array(
				'hc483@bergen.kommune.no',
				'Bjørvik, Ole Christian <Ole.Bjorvik@bergen.kommune.no>',
				);
			$to		 = implode(';', $toarray);

			try
			{
				$rc	 = CreateObject('phpgwapi.send')->msg('email', $to, $subject, $html, '', $cc	 = '', $bcc = '', 'hc483@bergen.kommune.no', 'Ikke svar', 'html');
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}

			$msg						 = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);
			echo "$msg\n";
			$this->receipt['message'][]	 = array('msg' => $msg);


		}
		function cron_log( $receipt = '' )
		{

			$insert_values = array(
				$this->cron,
				date($this->db->datetime_format()),
				$this->function_name,
				$receipt
			);

			$insert_values = $this->db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
				. "VALUES ($insert_values)";
			$this->db->query($sql, __LINE__, __FILE__);
		}
	}

	class boei
	{

		protected $db,$connected;

		function __construct()
		{
			$this->db = $this->get_db();
		}

		public function get_db()
		{
			if ($this->db && is_object($this->db))
			{
				return $this->db;
			}

			$db	 = createObject('phpgwapi.db_adodb', null, null, true);
			$host_info		 = explode(':', $GLOBALS['external_db']['boei']['db_host']);

			$host	 = $host_info[0];
			$port	 = isset($host_info[1]) && $host_info[1] ? $host_info[1] : $GLOBALS['external_db']['boei']['db_port'];

			$db->Host			 = $host;
			$db->Type			 = $GLOBALS['external_db']['boei']['db_type'];
			$db->Database		 = $GLOBALS['external_db']['boei']['db_name'];
			$db->Port			 = $port;
			$db->User			 = $GLOBALS['external_db']['boei']['db_user'];
			$db->Password		 = $GLOBALS['external_db']['boei']['db_pass'];
			$db->Halt_On_Error	 = 'yes';

			try
			{
				$db->connect();
				$this->connected = true;
			}
			catch (Exception $e)
			{
				$status = lang('unable_to_connect_to_database');
			}

			$this->db = $db;
			return $db;
		}

		function get_checklist()
		{
			$sql = "SELECT TOP 100 PERCENT Leietaker.Fodt_dato, Leietaker.Personnr, Leieobjekt.Objekt_ID, Leieobjekt.Leie_ID, Leieobjekt.Flyttenr,"
				. " Gateadresse.GateNavn, Leieobjekt.Gatenr"
				. " FROM Leieobjekt "
				. " INNER JOIN Leietaker ON Leieobjekt.Leietaker_ID = Leietaker.Leietaker_ID"
				. " INNER JOIN Gateadresse ON Leieobjekt.Gateadresse_ID = Gateadresse.Gateadresse_ID"
				. " WHERE (Leietaker.Personnr Is Not Null) AND (Leietaker.DodDato = '' OR Leietaker.DodDato IS NULL)";

			$this->db->query($sql, __LINE__, __FILE__);
			$checklist = array();
			while ($this->db->next_record())
			{
				$Fodt_dato	 = explode('.', $this->db->f('Fodt_dato'));
				$person_nr	 = $Fodt_dato[0] . $Fodt_dato[1] . substr($Fodt_dato[2], -2) . $this->db->f('Personnr');

				if (strlen($person_nr) !== 11)
				{
					continue;
				}

				$checklist[$person_nr] = array(
					'person_nr'		 => $person_nr,
					'objekt_id'		 => $this->db->f('Objekt_ID'),
					'leie_id'		 => $this->db->f('Leie_ID'),
					'flyttenr'		 => $this->db->f('Flyttenr'),
					'BOEI_adresse'	 => $this->db->f('GateNavn') . ' ' . $this->db->f('Gatenr'),
				);
			}
			return $checklist;
		}

		function get_checklist_shielded()
		{
			$sql = "SELECT TOP 100 PERCENT Leietaker.Fodt_dato, Leietaker.Personnr,Leietaker.hemmeligAdresse,"
				. " Leieobjekt.Objekt_ID, Leieobjekt.Leie_ID, Leieobjekt.Flyttenr,"
				. " CAST(Leietaker.Fornavn as TEXT) AS Fornavn, CAST(Leietaker.Etternavn as TEXT) AS Etternavn,"
				. " Gateadresse.GateNavn, Leieobjekt.Gatenr"
				. " FROM Leieobjekt"
				. " INNER JOIN Leietaker ON Leieobjekt.Leietaker_ID = Leietaker.Leietaker_ID"
				. " INNER JOIN Gateadresse ON Leieobjekt.Gateadresse_ID = Gateadresse.Gateadresse_ID"
				. " WHERE (Leietaker.Personnr Is Not Null) AND (Leietaker.DodDato = '' OR Leietaker.DodDato IS NULL)";

			$this->db->query($sql, __LINE__, __FILE__);
			$checklist = array();
			while ($this->db->next_record())
			{
				$Fodt_dato	 = explode('.', $this->db->f('Fodt_dato'));
				$person_nr	 = $Fodt_dato[0] . $Fodt_dato[1] . substr($Fodt_dato[2], -2) . $this->db->f('Personnr');

				if (strlen($person_nr) !== 11)
				{
					continue;
				}

				$checklist[$person_nr] = array(
					'person_nr'			 => $person_nr,
					'objekt_id'			 => $this->db->f('Objekt_ID'),
					'leie_id'			 => $this->db->f('Leie_ID'),
					'flyttenr'			 => $this->db->f('Flyttenr'),
					'hemmeligadresse'	 => $this->db->f('hemmeligAdresse'),
					'navn'				 => $this->db->f('Etternavn', true) . ', ' . $this->db->f('Fornavn', true),
					'f_dato'			 => implode('.', $Fodt_dato),
					'beste_adresse'		 => $this->db->f('GateNavn') . ' ' . $this->db->f('Gatenr'),
				);
			}
			return $checklist;
		}

	}

	class fiks
	{

		private $apikey;
		private $webservicehost;

		public function __construct()
		{
			$config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.admin'));

			$apikey	 = $config->config_data['fiks']['apikey'];
			$webservicehost	 = $config->config_data['fiks']['webservicehost'];

			$this->apikey			 = $apikey;
			$this->webservicehost	 = $webservicehost;
		}

		function get_name_from_external_service($ssn)
		{

			if(empty($ssn))
			{
				return;
			}

			$apikey = $this->apikey;

			$webservicehost = !empty($this->webservicehost) ? $this->webservicehost : 'http://fiks/get.php:8210';

			if(!$webservicehost || !$apikey)
			{
				throw new Exception('Missing parametres for webservice');
			}

			$post_data = array
			(
				'id'	=> $ssn,
				'apikey' => $apikey,
			);

			$post_string = http_build_query($post_data);

			$url = $webservicehost;

	//		$this->log('url', print_r($url, true));
	//		$this->log('POST data', print_r($post_data, true));

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);


			if(!empty($ret['postadresse']) && count($ret['postadresse']) > 2)
			{
				$street	 = $this->mb_ucfirst(mb_convert_case($ret['postadresse'][0], MB_CASE_TITLE)) . ', '. $this->mb_ucfirst(mb_convert_case($ret['postadresse'][1], MB_CASE_LOWER));
			}
			else
			{
				$street	 = $this->mb_ucfirst(mb_convert_case($ret['postadresse'][0], MB_CASE_LOWER));
			}
			
			$ret['fornavn']			 = ucwords(mb_convert_case($ret['fornavn'],  MB_CASE_TITLE), "'");
			$ret['etternavn']		 = ucwords(mb_convert_case($ret['etternavn'],  MB_CASE_TITLE), "'");

			if(!empty($ret['postadresse']))
			{
				$poststed = explode(' ', end($ret['postadresse']));
			}

			$data						 = array();
			$data['ssn']				 = $ssn;
			$data['first_name']			 = $ret['fornavn'];
			$data['last_name']			 = $ret['etternavn'];
			$data['name']				 = "{$ret['fornavn']} {$ret['etternavn']}";
			$data['street']				 = $street;
			$data['zip_code']			 = $poststed[0];
			$data['city']				 = mb_convert_case($poststed[1], MB_CASE_TITLE);
			$data['foedselsdato']		 = $ret['foedselsdato'];//"fortrolig"
			$data['adressebeskyttelse']	 = $ret['adressebeskyttelse'];//"fortrolig"
			$data['status']				 = $ret['status'];
			$data['doedsdato']			 = !empty($ret['doedsdato']) ? $ret['doedsdato'] : '';

			return $data;
		}

		private function mb_ucfirst($string)
		{
			$encoding = 'UTF-8';
			$firstChar = mb_substr($string, 0, 1, $encoding);
			$then = mb_substr($string, 1, null, $encoding);
			return mb_strtoupper($firstChar, $encoding) . $then;
		}


	}
