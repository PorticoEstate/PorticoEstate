<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2020 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package phpgroupware
	 * @subpackage communication
	 * @category core
	 * @version $Id: navn_fra_GuleSider.php 4887 2010-02-23 10:33:44Z sigurd $
	 */
	/*
	  This program is free software: you can redistribute it and/or modify
	  it under the terms of the GNU General Public License as published by
	  the Free Software Foundation, either version 2 of the License, or
	  (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU General Public License for more details.

	  You should have received a copy of the GNU General Public License
	  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * Wrapper for custom methods
	 *
	 * @package phpgroupware
	 * @subpackage bookingfrontend
	 */

	/**
	 * START WRAPPER
	 */
	class bookingfrontend_external_user_name extends bookingfrontend_bouser
	{

		public function __construct()
		{
			parent::__construct();

			if (!empty($this->config->config_data['debug']))
			{
				$this->debug = true;
			}
		}

		function ping( $host )
		{
			exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
			return $rval === 0;
		}

		public function get_db()
		{
			static $db;

			if ($db && is_object($db))
			{
				return $db;
			}

			$config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.admin'));

			$db_info = array
				(
				'db_host'	 => $config->config_data['fellesdata']['host'], //'oradb31i.srv.bergenkom.no',
				'db_type'	 => 'oracle',
				'db_port'	 => '21521',
				'db_name'	 => $config->config_data['fellesdata']['db_name'],
				'db_user'	 => $config->config_data['fellesdata']['user_person'],
				'db_pass'	 => $config->config_data['fellesdata']['password_person'],
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

			return $db;
		}

		function get_name_from_external_service( & $data )
		{
			if (empty($data['ssn']))
			{
				return;
			}
			$db = $this->get_db();
			
			if(!$db)
			{
				return;
			}

			$sql = "SELECT * FROM V_INNBYGGER_FORTROLIG WHERE FODSELSNR = '{$data['ssn']}'";

			$db->query($sql, __LINE__, __FILE__);
			$db->next_record();

			$adresse = $db->f('BESTE_ADRESSE1', true);

			if(in_array($adresse, array('SPERRET ADRESSE', 'UTEN FAST BOPEL')))
			{
				$data['street']		 = '';
				$data['zip_code']	 = '';
				$data['city']		 = '';
			}
			else
			{
				$data['street']		 = $db->f('POSTENS_ADRESSE1', true);
				$data['zip_code']	 = $db->f('POSTENS_POSTNR', true);
				$data['city']		 = $db->f('POSTENS_POSTSTED', true);
			}

			$data['first_name']	 = $db->f('FORNAVN');
			$data['last_name']	 = $db->f('ETTERNAVN');
			$data['name']		 = $db->f('FORKORTET_NAVN');

			if ($this->debug)
			{
				_debug_array($data);
			}
		}
	}