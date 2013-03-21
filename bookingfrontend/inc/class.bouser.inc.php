<?php
	class bookingfrontend_bouser
	{
		const ORGNR_SESSION_KEY = 'orgnr';

		public
			$orgnr = null;

		protected
			$default_module = 'bookingfrontend',
			$module,
			$config;

		/**
		 * Debug for testing
		 * @access public
		 * @var bool
		 */
		public $debug = false;

		public function __construct() {
			$this->set_module();
			$this->orgnr = $this->get_user_orgnr_from_session();
			$this->config		= CreateObject('phpgwapi.config','bookingfrontend');
			$this->config->read();
		}

		protected function set_module($module = null)
		{
			$this->module = is_string($module) ? $module : $this->default_module;
		}

		public function get_module()
		{
			return $this->module;
		}

		public function log_in()
		{
			$this->log_off();

			$auth_provider = isset($this->config->config_data['auth_provider']) && $this->config->config_data['auth_provider'] ? $this->config->config_data['auth_provider'] : 'altinn1';

			if($auth_provider =='altinn2')
			{
				$this->orgnr = $this->log_in_altinn2();
			}
			else
			{
				$this->orgnr = $this->get_user_orgnr_from_auth_header();
			}

			if ($this->is_logged_in())
			{
				$this->write_user_orgnr_to_session();
			}

			if($this->debug)
			{
				echo 'is_logged_in():<br>';
				_debug_array($this->is_logged_in());
				echo 'Session:<br>';
				_debug_array($_SESSION);
				die();
			}

			return $this->is_logged_in();
		}

		public function log_off()
		{
			$this->clear_user_orgnr();
			$this->clear_user_orgnr_from_session();
		}

		protected function clear_user_orgnr()
		{
			$this->orgnr = null;
		}

		public function get_user_orgnr()
		{
			if(!$this->orgnr)
			{
				$this->orgnr = $this->get_user_orgnr_from_session();
			}
			return $this->orgnr;
		}

		public function is_logged_in()
		{
			return !!$this->get_user_orgnr();
		}

		public function is_organization_admin($organization_id = null)
		{
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if (strcmp($_SERVER['SERVER_NAME'], 'dev.redpill.se') == 0 || strcmp($_SERVER['SERVER_NAME'], 'bk.localhost') == 0)
			{
				//return true;
			}
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if(!$this->is_logged_in()) {
				//return false;
			}
			$so = CreateObject('booking.soorganization');
			$organization = $so->read_single($organization_id);

			if ($organization['organization_number'] == '')
			{
				return false;
			}

			return $organization['organization_number'] == $this->orgnr;
		}

		public function is_group_admin($group_id = null)
		{
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if (strcmp($_SERVER['SERVER_NAME'], 'dev.redpill.se') == 0 || strcmp($_SERVER['SERVER_NAME'], 'bk.localhost') == 0)
			{
				//return true;
			}
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if(!$this->is_logged_in()) {
				//return false;
			}
			$so = CreateObject('booking.sogroup');
			$group = $so->read_single($group_id);
			return $this->is_organization_admin($group['organization_id']);
		}

		protected function write_user_orgnr_to_session()
		{
			if (!$this->is_logged_in())
			{
				throw new LogicException('Cannot write orgnr to session unless user is logged on');
			}

			phpgwapi_cache::session_set($this->get_module(), self::ORGNR_SESSION_KEY, $this->get_user_orgnr());
		}

		protected function clear_user_orgnr_from_session()
		{
			phpgwapi_cache::session_clear($this->get_module(), self::ORGNR_SESSION_KEY);
		}

		protected function get_user_orgnr_from_session()
		{
			try {
				return createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean(phpgwapi_cache::session_get($this->get_module(), self::ORGNR_SESSION_KEY));
			} catch (sfValidatorError $e) {
				return null;
			}
		}

		protected function log_in_altinn2()
		{
			$headers = getallheaders();
			if(isset($this->config->config_data['debug']) && $this->config->config_data['debug'])
			{
				$this->debug = true;
				echo 'headers:<br>';
				_debug_array($headers);
			}

			$fodsels_nr = substr($headers['Osso-User-Dn'],2, 11);
//			$fodsels_nr = '02035701829'; // test

			$request =
			"<soapenv:Envelope
				 xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
				 xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"
				 xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\"
				 xmlns:v1=\"http://bergen.kommune.no/biz/bk/altinn/altinnreporteesservice/v1\">
				<soapenv:Body>
					<v1:getOrganisasjonsAvgivere>
						<fodselsNr>{$fodsels_nr}</fodselsNr>
					</v1:getOrganisasjonsAvgivere>
				</soapenv:Body>
			</soapenv:Envelope>";

			$location_URL = "http://wsm01e-t.usrv.ubergenkom.no:8888/gateway/services/AltinnReporteesService"; #A-test
		
			$client = new SoapClient(null, array(
						'location' => $location_URL,
						'uri'      => "",
						'trace'    => 1,
						));
	
			try
			{
				$response = $client->__doRequest($request,$location_URL,$location_URL,1);

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
							'id'	=> $_org_id,
							'name'	=> $xml->getElementsByTagName('name')->item(0)->nodeValue,
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

			if($stage == 2 && $fodsels_nr && in_array($org_id, $orgs_validate))
			{
				try
				{
					return createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($org_id);
				}
				catch (sfValidatorError $e)
				{
					if($this->debug)
					{
						echo $e->getMessage();
						die();
					}
					return null;
				}

			}

			foreach ( $orgs as $org)
			{
				$selected = '';
				if ( $org_id == $org['id'])
				{
					$selected = 'selected = "selected"';
				}

				$org_option .=  <<<HTML
				<option value='{$org['id']}'{$selected}>{$org['name']}</option>

HTML;
			}

			if($orgs)
			{
				$action =  $GLOBALS['phpgw']->link('/bookingfrontend/login.php', array('stage' => 2));
				$message = 'Velg organisasjon';

				$org_select =  <<<HTML
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
				$action =  $GLOBALS['phpgw']->link('/bookingfrontend/index.php');
				$message = 'Ikke representant for noen organisasjon';
				$org_select = '';
			}

			$html = <<<HTML
			﻿<!DOCTYPE html>
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

		protected function get_user_orgnr_from_auth_header()
		{
			$header_key = isset($this->config->config_data['header_key']) && $this->config->config_data['header_key'] ? $this->config->config_data['header_key'] : 'Osso-User-Dn';
			$header_regular_expression = isset($this->config->config_data['header_regular_expression']) && $this->config->config_data['header_regular_expression'] ? $this->config->config_data['header_regular_expression'] : '/^cn=(.*),cn=users.*$/';

			$headers = getallheaders();

			if(isset($this->config->config_data['debug']) && $this->config->config_data['debug'])
			{
				$this->debug = true;
				echo 'headers:<br>';
				_debug_array($headers);
			}

			if(isset($headers[$header_key]) && $headers[$header_key])
			{
				$matches = array();
				preg_match_all($header_regular_expression,$headers[$header_key], $matches);
				$userid = $matches[1][0];

				if($this->debug)
				{
					echo 'matches:<br>';
					_debug_array($matches);
				}

			}

			$options = array();
			$options['soap_version'] = SOAP_1_1;
			$options['location']	= isset($this->config->config_data['soap_location']) && $this->config->config_data['soap_location'] ? $this->config->config_data['soap_location'] : '';// 'http://soat1a.srv.bergenkom.no:8888/gateway/services/BrukerService-v1';
			$options['uri']			= isset($this->config->config_data['soap_uri']) && $this->config->config_data['soap_uri'] ? $this->config->config_data['soap_uri'] : '';// 'http://soat1a.srv.bergenkom.no';
			$options['trace']		= 1;

			if(isset($this->config->config_data['soap_proxy_host']) && $this->config->config_data['soap_proxy_host'])
			{
				$options['proxy_host']	= $this->config->config_data['soap_proxy_host'];
			}

			if(isset($this->config->config_data['soap_proxy_port']) && $this->config->config_data['soap_proxy_port'])
			{
				$options['proxy_port']	= $this->config->config_data['soap_proxy_port'];
			}
			$options['encoding']	= isset($this->config->config_data['soap_encoding']) && $this->config->config_data['soap_encoding'] ? $this->config->config_data['soap_encoding'] : 'UTF-8';
			$options['login']		= isset($this->config->config_data['soap_login']) && $this->config->config_data['soap_login'] ? $this->config->config_data['soap_login'] : '';
			$options['password']	= isset($this->config->config_data['soap_password']) && $this->config->config_data['soap_password'] ? $this->config->config_data['soap_password'] : '';

			$wsdl = isset($this->config->config_data['soap_wsdl']) && $this->config->config_data['soap_wsdl'] ? $this->config->config_data['soap_wsdl'] : '';// 'http://soat1a.srv.bergenkom.no:8888/gateway/services/BrukerService-v1?wsdl';

			$authentication_method	= isset($this->config->config_data['authentication_method']) && $this->config->config_data['authentication_method'] ? $this->config->config_data['authentication_method'] : '';

			require_once PHPGW_SERVER_ROOT."/bookingfrontend/inc/custom/default/{$authentication_method}";

			$external_user = new booking_external_user($wsdl, $options, $userid, $this->debug);
			// test values
			//$external_user = (object) 'ciao'; $external_user->login = 994239929;

			if($this->debug)
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
				if($this->debug)
				{
					echo $e->getMessage();
					die();
				}
				return null;
			}
		}
	}
