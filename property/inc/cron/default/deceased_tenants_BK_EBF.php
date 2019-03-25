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
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default bilagsinfo_BK
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');

	class deceased_tenants_BK_EBF extends property_cron_parent
	{

		var $b_accounts = array();

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location	 = lang('property');
			$this->function_msg	 = 'Leietakere som er registrert som død i folkeregisteret - men ikke i BOEI';
			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->join			 = & $this->db->join;
		}

		function execute()
		{
			$start = time();

			$boei		 = new boei();
			$checklist	 = $boei->get_checklist();

			$fellesdata	 = new fellesdata();
			$deceased	 = $fellesdata->get_deceased($checklist);

			$cols = array('navn', 'f_dato', 'd_dato', 'postens_adresse', 'objekt_id', 'leie_id',
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
						<th>Postens adresse</th>
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

			$subject = 'Leietakere som er registrert som død i folkeregisteret';

			$toarray = array('hc483@bergen.kommune.no', 'Bjørvik, Ole Christian <Ole.Bjorvik@bergen.kommune.no>',
				'Lillestrøm Synneve <Synneve.Lillestrom@bergen.kommune.no>');
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

		protected $db;

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

			$boei_db = array(
				'db_host'	 => $GLOBALS['external_db']['boei']['db_host'],
				'db_name'	 => $GLOBALS['external_db']['boei']['db_name'],
				'db_user'	 => $GLOBALS['external_db']['boei']['db_user'],
				'db_pass'	 => $GLOBALS['external_db']['boei']['db_pass'],
				'db_type'	 => $GLOBALS['external_db']['boei']['db_type']
			);

			$host_info = explode(':', $boei_db['db_host']);

			$host	 = $host_info[0];
			$port	 = isset($host_info[1]) && $host_info[1] ? $host_info[1] : $boei_db['db_port'];

			$db					 = createObject('phpgwapi.db_adodb', null, null, true);
			$db->Host			 = $host;
			$db->Port			 = $port;
			$db->Type			 = $boei_db['db_type'];
			$db->Database		 = $boei_db['db_name'];
			$db->User			 = $boei_db['db_user'];
			$db->Password		 = $boei_db['db_pass'];
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
			$sql = "SELECT TOP 100 PERCENT Leietaker.Fodt_dato, Leietaker.Personnr, Leieobjekt.Objekt_ID, Leieobjekt.Leie_ID, Leieobjekt.Flyttenr"
				. " FROM Leieobjekt INNER JOIN Leietaker ON Leieobjekt.Leietaker_ID = Leietaker.Leietaker_ID"
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
					'person_nr'	 => $person_nr,
					'objekt_id'	 => $this->db->f('Objekt_ID'),
					'leie_id'	 => $this->db->f('Leie_ID'),
					'flyttenr'	 => $this->db->f('Flyttenr'),
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
		public $db			 = null;
		protected $debug	 = false;

		function __construct()
		{
			$this->db = $this->get_db();
		}
		/* our simple php ping function */

		function ping( $host )
		{
			exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
			return $rval === 0;
		}

		public function get_db()
		{
			if ($this->db && is_object($this->db))
			{
				return $this->db;
			}

			$config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.admin'));

			$db_info = array
				(
				'db_host'	 => $config->config_data['fellesdata']['host'], //'oradb31i.srv.bergenkom.no',
				'db_type'	 => 'oracle',
				'db_port'	 => '21521',
				'db_name'	 => $config->config_data['fellesdata']['db_name'], //'FELPROD',
				'db_user'	 => $config->config_data['fellesdata']['user_person'], //'PERSON_EBF',
				'db_pass'	 => $config->config_data['fellesdata']['password_person'], //'EBF_FOR_0916',
			);

			if (!$db_info['db_host'] || !$this->ping($db_info['db_host']))
			{
				$message = "Database server {$db_info['db_host']} is not accessible";
				phpgwapi_cache::message_set($message, 'error');
				return false;
			}

			$db = createObject('phpgwapi.db_adodb', null, null, true);

			$db->debug		 = false;
			$db->Host		 = $db_info['db_host'];
			$db->Port		 = $db_info['db_port'];
			$db->Type		 = $db_info['db_type'];
			$db->Database	 = $db_info['db_name'];
			$db->User		 = $db_info['db_user'];
			$db->Password	 = $db_info['db_pass'];

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
					$sql = "SELECT * FROM V_INNBYGGER_FORTROLIG WHERE DODSDATO IS NOT NULL"
						. " AND FODSELSNR IN ($person_list)";

					$this->db->query($sql, __LINE__, __FILE__);

					while ($this->db->next_record())
					{
						$person_data[] = array(
							'person_nr'			 => $this->db->f('FODSELSNR'),
							'navn'				 => $this->db->f('FORKORTET_NAVN'),
							'f_dato'			 => $this->db->f('FODSELSDATO'),
							'd_dato'			 => $this->db->f('DODSDATO'),
							'postens_adresse'	 => $this->db->f('POSTENS_ADRESSE1'),
						);
					}
				}
			}
			return $person_data;
		}
	}