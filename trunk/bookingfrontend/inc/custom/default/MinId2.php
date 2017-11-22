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

			if (!empty($this->config->config_data['debug']))
			{
				$this->debug = true;
			}
		}

		protected function get_user_org_id()
		{
			$ipdp = $_SERVER['HTTP_UID'];
			$bregorgs = $this->get_breg_orgs($ipdp);
			$myorgnr = array();
			if ($bregorgs == array())
			{
				$external_user = (object)'ciao';
				$external_user->login = '000000000';
			}
			else
			{
				foreach ($bregorgs as $org)
				{
					$myorgnr[] = $org['orgnr'];
				}
				if (count($myorgnr) > 1)
				{
					$external_user = (object)'ciao';
					$external_user->login = $myorgnr[0];
					$orgs = array();
					foreach ($myorgnr as $org)
					{
						$orgs[] = array('orgnumber' => $org, 'orgname' => $this->get_orgname_from_db($org));
					}
					phpgwapi_cache::session_set($this->get_module(), self::ORGARRAY_SESSION_KEY, $orgs);
				}
				elseif (count($myorgnr) > 0)
				{
					phpgwapi_cache::session_set($this->get_module(), self::ORGARRAY_SESSION_KEY, NULL);
					$external_user = (object)'ciao';
					$external_user->login = $myorgnr[0];
				}
			}


			if ($this->debug)
			{
				echo 'External user:<br>';
				_debug_array($external_user);
			}
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

			/**
			 * Her kaller du tjenesten som gjør spørringen mot Brønnøysund.
			 *	$fodselsnr er som det skal være (ikke hash)
			 */
			$orgs = $this->get_orgs_from_external_service($fodselsnr);

			if($orgs && is_array($orgs))
			{
				foreach ($orgs as $org)
				{
					$results[] = array
					(
						'orgnr' => $org['orgnr']
					);

					$orgs_validate[] = $org['orgnr'];
				}
			}

			$hash = sha1($fodselsnr);
			$ssn =  '{SHA1}' . base64_encode($hash);

			$this->db->query("SELECT bb_organization.organization_number, bb_organization.name AS organization_name"
				. " FROM bb_delegate"
				. " JOIN  bb_organization ON bb_delegate.organization_id = bb_organization.id"
				. " WHERE bb_delegate.ssn = '{$ssn}'", __LINE__, __FILE__);

			while($this->db->next_record())
			{
				$organization_number = $this->db->f('organization_number');
				if(in_array($organization_number, $orgs_validate))
				{
					continue;
				}
				$results[] = array
				(
					'orgnr' => $organization_number
				);

				$orgs_validate[] = $organization_number;

			}

			$test_organization = $this->config->config_data['test_organization'];
			if ($this->debug && $test_organization)
			{
				$results[] = array
				(
					'orgnr' => $test_organization
				);
			}

			return $results;
		}


		private function get_orgs_from_external_service($fodselsnr)
		{
			$apikey = !empty($this->config->config_data['apikey']) ? $this->config->config_data['apikey'] : '45090934oidtgj3Dtgijr3GrtiorthrtpiRTHSRhoRTHrthoijrtgrsSERgerthoijRDTeortigjesrgERHGeihjoietrh';
			$webservicehost = !empty($this->config->config_data['webservicehost']) ? $this->config->config_data['webservicehost'] : '';

			if(!$webservicehost || !$apikey)
			{
				throw new Exception('Missing parametres for webservice');
			}

			$post_data = array
			(
				'apikey'	=> $apikey,
				'id'		=> $fodselsnr
			);
			foreach ( $post_data as $key => $value)
			{
				$post_items[] = $key . '=' . $value;
			}

			$post_string = implode ('&', $post_items);


			if ($this->debug)
			{
				echo "POST:<br/>";
				_debug_array($webservicehost);
				_debug_array($post_data);
			}


			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $webservicehost);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);

			if ($this->debug)
			{
				echo "httpCode:<br/>";
				_debug_array($httpCode);
				echo "Returdata:<br/>";
				_debug_array($ret);
			}


			if(isset($ret['orgnr']))
			{
				return array($ret);
			}
			else
			{
				return $ret;
			}
		}
	}