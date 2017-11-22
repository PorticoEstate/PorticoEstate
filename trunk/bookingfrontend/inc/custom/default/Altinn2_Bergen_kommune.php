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
	class bookingfrontend_external_user extends bookingfrontend_bouser
	{

		public function __construct()
		{
			parent::__construct();
		}

		public function get_user_org_id()
		{
			$headers = getallheaders();
			if (isset($this->config->config_data['debug']) && $this->config->config_data['debug'])
			{
				$this->debug = true;
				echo 'headers:<br>';
				_debug_array($headers);
			}

			$fodsels_nr = $headers['uid'];

			if ($this->debug)
			{
				echo 'fødselsnr:<br>';
				_debug_array($fodsels_nr);
			}

			$request = "<soapenv:Envelope
				 xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
				 xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"
				 xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\"
				 xmlns:v1=\"http://bergen.kommune.no/biz/bk/authorization/ReporteesService/v1\">
				<soapenv:Body>
					<v1:getOrganisasjonsAvgivere>
						<fodselsNr>{$fodsels_nr}</fodselsNr>
					</v1:getOrganisasjonsAvgivere>
				</soapenv:Body>
			</soapenv:Envelope>";

			$location_URL = isset($this->config->config_data['soap_location']) && $this->config->config_data['soap_location'] ? $this->config->config_data['soap_location'] : "http://wsm01e-t.usrv.ubergenkom.no:8888/gateway/services/AltinnReporteesService"; #A-test

			$test_organization = $this->config->config_data['test_organization'];
			$soap_login = $this->config->config_data['soap_login'];
			$soap_password = $this->config->config_data['soap_password'];

			$client = new SoapClient(null, array(
				'location' => $location_URL,
				'uri' => "",
				'trace' => 1,
				'login' => $soap_login,
				'password' => $soap_password
			));

			try
			{
				$action = "";
				$response = $client->__doRequest($request, $location_URL, $action, 1);
				$reader = new XMLReader();
				$reader->xml($response);

				$orgs = array();
				$orgs_validate = array();
				while ($reader->read())
				{
					if ($reader->nodeType == XMLREADER::ELEMENT && $reader->localName == 'return')
					{
						$xml = new DOMDocument('1.0', 'utf-8');
						$xml->formatOutput = true;
						$domnode = $reader->expand();
						$xml->appendChild($domnode);
						unset($domnode);
						$_org_id = $xml->getElementsByTagName('organizationNumber')->item(0)->nodeValue;
						$orgs[] = array
							(
							'id' => $_org_id,
							'name' => $xml->getElementsByTagName('name')->item(0)->nodeValue,
						);
						$orgs_validate[] = $_org_id;
					}
				}
			}
			catch (SoapFault $exception)
			{
				echo "Dette gikk ikke så bra.";
				var_dump(get_class($exception));
				var_dump($exception);
			}

			$stage = phpgw::get_var('stage');
			$org_id = phpgw::get_var('org_id');

			if ($this->debug && $test_organization)
			{
				$orgs[] = array
					(
					'id' => $test_organization,
					'name' => 'Bølleball',
				);
				$orgs_validate[] = $test_organization;
				if(!$fodsels_nr)
				{
					$fodsels_nr = 1;
				}
			}

			$hash = sha1($fodsels_nr);
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

				$orgs[] = array(
					'id' => $organization_number,
					'name' => $this->db->f('organization_name', true),
				);
				$orgs_validate[] = $organization_number;

			}

			if ($stage == 2 && $fodsels_nr && in_array($org_id, $orgs_validate))
			{
				try
				{
					return createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($org_id);
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

			foreach ($orgs as $org)
			{
				$selected = '';
				if ($org_id == $org['id'])
				{
					$selected = 'selected = "selected"';
				}

				$org_option .= <<<HTML
				<option value='{$org['id']}' {$selected}>{$org['name']}</option>

HTML;
			}

			if ($orgs)
			{
				$action = $GLOBALS['phpgw']->link('/bookingfrontend/login.php', array('stage' => 2));
				$message = 'Velg organisasjon';

				$org_select = <<<HTML
							<p>
								<label for="org_id">Velg Organisasjon:</label>
								<select name="org_id" id="org_id">
									{$org_option}
								</select>
							</p>
HTML;
			}
			else
			{
				$action = $GLOBALS['phpgw']->link('/bookingfrontend/index.php');
				$message = 'Ikke representant for noen organisasjon';
				$org_select = '';
			}

			$html = <<<HTML
<!DOCTYPE html>
			<html>
				<head>
					<title>Velg organisasjon</title>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				</head>
				<body>
					<h2>{$message}</h2>
					<form action="{$action}" method="post">
						<fieldset>
							<legend>
								Organisasjon
							</legend>
							$org_select
							<p>
								<input type="submit" name="submit" value="Fortsett"  />
							</p>
			 			</fieldset>
					</form>
				</body>
			</html>
HTML;

			echo $html;

			$GLOBALS['phpgw']->common->phpgw_exit();
		}
	}