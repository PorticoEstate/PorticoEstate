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
	 * @subpackage eventplannerfrontend
	 */
	class eventplannerfrontend_external_user extends eventplannerfrontend_bouser
	{

		var $debug = false;
		public function __construct()
		{
			parent::__construct();
			if (isset($this->config->config_data['debug']) && $this->config->config_data['debug'])
			{
				$this->debug = true;
			}
		}

		public function get_user_org_id()
		{
			$headers = getallheaders();
			if ($this->debug)
			{
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
				echo "Feilet på oppslag mot soaptjeneste.";
				var_dump(get_class($exception));
				var_dump($exception);
			}

			if ($this->debug)
			{
				$orgs[] = array('id' => '994239929', 'name' => 'Bølleball');
				$_org_id = 994239929;
			}
			$_SESSION['orgs'] = $orgs;
			$_SESSION['org_id'] = $_org_id; // one of them..

		}
	}