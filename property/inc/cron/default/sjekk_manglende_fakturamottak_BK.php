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
	 * @version $Id: sjekk_manglende_fakturamottak_BK.php 16075 2016-12-12 15:26:41Z sigurdne $
	 */
	/**
	 * Description
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default sjekk_manglende_fakturamottak_BK
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');
	phpgw::import_class('phpgwapi.datetime');

	class sjekk_manglende_fakturamottak_BK extends property_cron_parent
	{

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('property');
			$this->function_msg = 'oppdater bestillinger med grunnlag i betalte faktura';
			/**
			 * Bruker konffigurasjon fra '.ticket' - fordi denne definerer oppslaget mot fullmaktsregisteret ved bestilling.
			 */
			$config					= CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.ticket'));
			$this->soap_url			= $config->config_data['external_register']['url'];
			$this->soap_username	= $config->config_data['external_register']['username'];
			$this->soap_password	= $config->config_data['external_register']['password'];
		}

		function execute()
		{
			$start = time();

			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/art
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/ansvar?id=013000
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/objekt?id=5001
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/prosjekt?id=5001
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/tjeneste?id=88010

			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/leverandorer?leverandorNr=722920
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/manglendevaremottak


			/*
			 *
			 * Nye:
			 * agresso/ordre?id=
			 * agresso/bilag?id=
			 *
			 */


			if ($this->debug)
			{
			}
			set_time_limit(2000);

			try
			{
				$this->check_missing();
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}

			$msg = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
			$this->cron_log($msg, $cron);
			echo "$msg\n";
			$this->receipt['message'][] = array('msg' => $msg);

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

		private function check_missing()
		{
			$data = (array)$this->get_data($voucher['voucher_id']);
			$send = CreateObject('phpgwapi.send');

			$subject = 'Manglende varemottak';
			if (!isset($GLOBALS['phpgw_info']['server']['smtp_server']) || !$GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				_debug_array($data);
			}

			$subject = 'Manglende fakturamottak i Agresso';
			$from = "Ikke svar<IkkeSvar@Bergen.kommune.no>";
			$to = "Sigurd.Nes@bergen.kommune.no";
			$cc = "";
			$bcc = "";

			if ($data)
			{
				$body = '<pre>' . print_r($data,true) . '</pre>';
				try
				{
					$rc = $send->msg('email', $to, $subject, $body, '', $cc, $bcc, $from, '', 'html');
				}
				catch (Exception $e)
				{
					$this->receipt['error'][] = array('msg' => $e->getMessage());
				}
			}

		}

		function get_data( $voucher_id )
		{
			//curl -s -u portico:******** http://tjenester.usrv.ubergenkom.no/api/agresso/manglendevaremottak
			$url = "{$this->soap_url}/manglendevaremottak";
			$username	= $this->soap_username; //'portico';
			$password	= $this->soap_password; //'********';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if(!$httpCode)
			{
				throw new Exception("No connection: {$url}");
			}
			curl_close($ch);

			$result = json_decode($result, true);

			return $result;

		}

	}
