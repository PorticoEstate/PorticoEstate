<?php

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'		=> true,
		'nonavbar'		=> true,
		'currentapp'	=> 'property'
	);

	include_once('../header.inc.php');


	$boei = new boei();
	$checklist = $boei->get_checklist();

	$fellesdata = new fellesdata();
	$deceased = $fellesdata->get_deceased($checklist);


	$cols = array('person_nr', 'navn', 'f_dato', 'd_dato','postens_adresse', 'objekt_id', 'leie_id');

	$html =<<<HTML
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
				<th>Personnr</th>
				<th>Navn</th>
				<th>Født dato</th>
				<th>Død dato</th>
				<th>Postens adresse</th>
				<th>Objekt ID</th>
				<th>Leie ID</th>
			</tr>
HTML;

	foreach ($deceased as &$entry)
	{
		$entry = array_merge($entry, $checklist[$entry['person_nr']]);
		$html .=<<<HTML
			<tr>
HTML;

		foreach ($cols as $key)
		{
			$html .=<<<HTML
				<td>{$entry[$key]}</td>
HTML;

		}

		$html .=<<<HTML
			</tr>
HTML;

	}

	$html .=<<<HTML
			</table>
		</body>
	</html>
HTML;

	$subject = 'Leietakere som er registrert som død i folkeregisteret';

	$toarray = array('hc483@bergen.kommune.no','Bjørvik, Ole Christian <Ole.Bjorvik@bergen.kommune.no>','Lillestrøm Synneve <Synneve.Lillestrom@bergen.kommune.no>' );
	$to = implode(';', $toarray);

	try
	{
		$rc = CreateObject('phpgwapi.send')->msg('email', $to, $subject, $html, '', $cc='', $bcc='', 'hc483@bergen.kommune.no', 'Ikke svar', 'html');
	}
	catch (Exception $e)
	{
		echo $e->getMessage();
	}

	class boei
	{
		protected $db;

		function __construct()
		{
			$this->db = $this->get_db();
		}

		public function get_db()
		{
			if($this->db && is_object($this->db))
			{
				return $this->db;
			}

			$boei_db = array (
				'db_host' => '10.11.12.120:1434',
				'db_name' => 'BOEIdb',
				'db_user' => 'SPAN',
				'db_pass' => 'ITSsp321',
				'db_type' => 'mssql'
			);

			$host_info = explode(':', $boei_db['db_host']);

			$host = $host_info[0];
			$port = isset($host_info[1]) && $host_info[1] ? $host_info[1] : $boei_db['db_port'];

			$db           	= createObject('phpgwapi.db_adodb', null, null, true);
			$db->Host     	= $host;
			$db->Port     	= $port;
			$db->Type     	= $boei_db['db_type'];
			$db->Database 	= $boei_db['db_name'];
			$db->User     	= $boei_db['db_user'];
			$db->Password 	= $boei_db['db_pass'];
			$db->Halt_On_Error 	= 'yes';

			try
			{
				$db->connect();
				$this->connected = true;
			}
			catch(Exception $e)
			{
				$status = lang('unable_to_connect_to_database');
			}

			$this->db = $db;
			return $db;
		}

		function get_checklist()
		{
			$sql = "SELECT TOP 100 PERCENT Leietaker.Fodt_dato, Leietaker.Personnr, Leieobjekt.Objekt_ID, Leieobjekt.Leie_ID"
				. " FROM Leieobjekt INNER JOIN Leietaker ON Leieobjekt.Leietaker_ID = Leietaker.Leietaker_ID"
				. " WHERE (Leietaker.Personnr Is Not Null) AND (Leietaker.DodDato = '' OR Leietaker.DodDato IS NULL)";

			$this->db->query($sql, __LINE__, __FILE__);
			$checklist = array();
			while($this->db->next_record())
			{
				$Fodt_dato = explode('.', $this->db->f('Fodt_dato'));
				$person_nr =  $Fodt_dato[0]. $Fodt_dato[1]. substr($Fodt_dato[2], -2) . $this->db->f('Personnr');

				if(strlen($person_nr) !== 11)
				{
					continue;
				}


				$checklist[$person_nr] = array(
					'person_nr'	=> $person_nr,
					'objekt_id'	=> $this->db->f('Objekt_ID'),
					'leie_id'	=> $this->db->f('Leie_ID'),
				);

			}
			return $checklist;
		}
	}

	class fellesdata
	{
    	// Instance variable
		protected $connected = false;
		protected $status;
		public $db = null;
		protected $debug = false;

		function __construct()
		{
			$this->db = $this->get_db();
		}


		/* our simple php ping function */
		function ping($host)
		{
	        exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
	        return $rval === 0;
		}

		public function get_db()
		{
			if($this->db && is_object($this->db))
			{
				return $this->db;
			}

			$db_info = array
			(
				'db_host'	=> 'oradb31i.srv.bergenkom.no',
				'db_type'	=> 'oracle',
				'db_port'	=> '21521',
				'db_name'	=> 'FELPROD',
				'db_user'	=> 'PERSON_EBF',
				'db_pass'	=> 'EBF_FOR_0916',
			);

			if(! $db_info['db_host'] || !$this->ping($db_info['db_host']))
			{
				$message ="Database server {$db_info['db_host']} is not accessible";
				phpgwapi_cache::message_set($message, 'error');
				return false;
			}

			$db = createObject('phpgwapi.db_adodb', null, null, true);

			$db->debug 		= false;
			$db->Host		= $db_info['db_host'];
			$db->Port		= $db_info['db_port'];
			$db->Type		= $db_info['db_type'];
			$db->Database	= $db_info['db_name'];
			$db->User		= $db_info['db_user'];
			$db->Password	= $db_info['db_pass'];

			try
			{
				$db->connect();
				$this->connected = true;
			}
			catch(Exception $e)
			{
				$status = lang('unable_to_connect_to_database');
			}

			$this->db = $db;
			return $db;
		}


		function get_deceased($checklist)
		{

			$checklist_set = array_chunk($checklist, 500);

			$person_data = array();

			foreach($checklist_set as $_checklist)
			{
				$person_nr = array();
				foreach ($_checklist as $person)
				{
					$person_nr[] = "'{$person['person_nr']}'";
				}


				$person_list = implode(',', $person_nr);

				if($person_nr)
				{
					$sql = "SELECT * FROM V_INNBYGGER_FORTROLIG WHERE DODSDATO IS NOT NULL"
						. " AND FODSELSNR IN ($person_list)";

					$this->db->query($sql, __LINE__, __FILE__);

					while ($this->db->next_record())
					{
						$person_data[] = array(
							'person_nr'			=> $this->db->f('FODSELSNR'),
							'navn'				=> $this->db->f('FORKORTET_NAVN'),
							'f_dato'			=> $this->db->f('FODSELSDATO'),
							'd_dato'			=> $this->db->f('DODSDATO'),
							'postens_adresse'	=> $this->db->f('POSTENS_ADRESSE1'),
						);
					}
				}
			}
			return $person_data;
		}
	}

