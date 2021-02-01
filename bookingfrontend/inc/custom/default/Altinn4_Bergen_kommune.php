<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package phpgroupware
	 * @subpackage communication
	 * @category core
	 * @version $Id: Altinn_Bergen_kommune.php 4887 2010-02-23 10:33:44Z sigurd $
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
	class bookingfrontend_external_user extends bookingfrontend_bouser
	{

		public function __construct()
		{
			parent::__construct();
		}

		protected function get_user_org_id()
		{
			$data = $this->validate_ssn_login();
			$fodselsnr = (string)$data['ssn'];
			$bregorgs = $this->get_breg_orgs($fodselsnr);

			if ($bregorgs == array())
			{
				$external_user = (object)'ciao';
				$external_user->login = '000000000';
			}
			else
			{
				if (count($bregorgs) > 1)
				{
					$external_user = (object)'ciao';
					$external_user->login = $bregorgs[0]['orgnr'];
					$orgs = array();
					foreach ($bregorgs as $org)
					{
						$orgs[] = array('orgnumber' => $org['orgnr'], 'orgname' => $this->get_orgname_from_db($org['orgnr'], $org['customer_ssn']));
					}
					phpgwapi_cache::session_set($this->get_module(), self::ORGARRAY_SESSION_KEY, $orgs);
				}
				elseif (count($bregorgs) > 0)
				{
					phpgwapi_cache::session_set($this->get_module(), self::ORGARRAY_SESSION_KEY, NULL);
					$external_user = (object)'ciao';
					$external_user->login = $bregorgs[0]['orgnr'];
				}
			}


			$this->log('External user', print_r($external_user, true));

			try
			{
				return createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($external_user->login);
			}
			catch (sfValidatorError $e)
			{
				if ($this->debug)
				{
					echo $e->getMessage();
					die();
				}
				return null;
			}
		}

		/**
		 * Henter organisasjonsnummer som personen har en rolle i
		 * @param type $fodselsnr
		 * @return array $results organisasjonsnr
		 */
		private function get_breg_orgs( $fodselsnr )
		{
			$results = array();
			$orgs_validate = array(-1);

			/**
			 * Her kaller du tjenesten som gjør spørringen mot Brønnøysund.
			 *	$fodselsnr er som det skal være (ikke hash)
			 */
			try
			{
				$orgs = $this->get_orgs_from_external_service($fodselsnr);
			}
			catch (Exception $e)
			{
				$log =& $GLOBALS['phpgw']->log;
				$log->error(array(
					'text'	=> "<b>Exception:</b>\n". $e->getMessage() . "\n" . $e->getTraceAsString(),
					'line'	=> $e->getline(),
					'file'	=> $e->getfile()
				));
			}

			if($orgs && is_array($orgs))
			{
				foreach ($orgs as $org)
				{
					$this->db->query("SELECT organization_number"
						. " FROM bb_organization"
						. " WHERE active = 1 AND organization_number = '{$org['orgnr']}'", __LINE__, __FILE__);

					if (!$this->db->next_record())
					{
						continue;
					}

					$results[] = array
					(
						'orgnr' => $org['organizationNumber'],
						'customer_ssn'	 => null
					);

					$orgs_validate[] = $org['organizationNumber'];
				}
			}

			$hash = sha1($fodselsnr);
			$ssn =  '{SHA1}' . base64_encode($hash);

			$this->db->query("SELECT DISTINCT * FROM (SELECT bb_organization.customer_ssn, bb_organization.organization_number, bb_organization.name AS organization_name"
				. " FROM bb_delegate"
				. " JOIN  bb_organization ON bb_delegate.organization_id = bb_organization.id"
				. " WHERE bb_delegate.active = 1 AND bb_delegate.ssn = '{$ssn}'"
				. " UNION"
				. " SELECT customer_ssn, organization_number, name AS organization_name"
				. " FROM bb_organization"
				. " WHERE (customer_ssn = '{$fodselsnr}' AND customer_identifier_type = 'ssn')"
				. " OR organization_number IN ('". implode("','", $orgs_validate) ."') ) as t", __LINE__, __FILE__);

			while($this->db->next_record())
			{
				$customer_ssn = $this->db->f('customer_ssn');
				$organization_number = $this->db->f('organization_number');
				
				if($organization_number && in_array($organization_number, $orgs_validate))
				{
					continue;
				}

				if($customer_ssn && !$organization_number)
				{
					$organization_number = '000000000';
				}
				
				$results[] = array
				(
					'orgnr'			 => $organization_number,
					'customer_ssn'	 => $customer_ssn
				);

				$orgs_validate[] = $organization_number;

			}

			$test_organizations = (array)explode(',', $this->config->config_data['test_organization']);
			if ($this->debug && $test_organizations)
			{
				foreach ($test_organizations as $test_organization)
				{
					if(in_array($test_organization, $orgs_validate))
					{
						continue;
					}
					$results[] = array
					(
						'orgnr'			 => $test_organization,
						'customer_ssn'	 => null
					);					
				}
			}

			return $results;
		}


		private function get_orgs_from_external_service($fodselsnr)
		{
			//curl -s -u portico:*** https://tjenester.usrv.ubergenkom.no/api/altinnservice/getAvgiver/***********

			$location_URL = !empty($this->config->config_data['soap_location']) ? $this->config->config_data['soap_location'] : "https://tjenester.srv.bergenkom.no";

			$username = $this->config->config_data['soap_login'];
			$password = $this->config->config_data['soap_password'];

			$url = rtrim($location_URL, '/') . "/api/altinnservice/getAvgiver/{$fodselsnr}";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if (!$httpCode)
			{
				throw new Exception("No connection: {$url}");
			}

			$orgs = json_decode($result, true);
			$this->log('webservice returdata as json', print_r($result, true));

			return $orgs;
		}

		private function log( $what, $value = '' )
		{
			if (!empty($GLOBALS['phpgw_info']['server']['log_levels']['module']['login']))
			{
				$bt = debug_backtrace();
				$GLOBALS['phpgw']->log->debug(array(
					'text' => "what: %1, <br/>value: %2",
					'p1' => $what,
					'p2' => $value ? $value : ' ',
					'line' => __LINE__,
					'file' => __FILE__
				));
				unset($bt);
			}
		}

	}