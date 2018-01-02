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

		private function get_breg_orgs( $fodselsnr = '')
		{
			$hash = sha1($fodselsnr);
			$db = createObject('phpgwapi.db', null, null, true);

			$db->Host = $GLOBALS['phpgw_domain']['default']['db_host'];
			$db->Port = $GLOBALS['phpgw_domain']['default']['db_port'];//'5432';
			$db->Type = 'postgres';
			$db->Database = 'breg';
			$db->User = $GLOBALS['phpgw_domain']['default']['db_user'];
			$db->Password = $GLOBALS['phpgw_domain']['default']['db_pass'];

			try
			{
				$db->connect();
			}
			catch (Exception $e)
			{
				$GLOBALS['phpgw']->log->error(array(
					'text'	=> 'bookingfrontend_external_user::get_breg_orgs() : error when trying to connect. Error: %1',
					'p1'	=> $db->get_error_message(),
					'line'	=> __LINE__,
					'file'	=> __FILE__
				));
			}

			$sql = "SELECT DISTINCT orgnr FROM breg.personcurrent WHERE fodselsnr ='{$hash}'";

			$orgs_validate = array();
			$results = array();
			$db->query($sql, __LINE__, __FILE__);
			while ($db->next_record())
			{
				$organization_number = $db->f('orgnr', true);
				$results[] = array
				(
					'orgnr' => $organization_number
				);
				$orgs_validate[] = $organization_number;
			}

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

			}

		//Testvalues
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

	}