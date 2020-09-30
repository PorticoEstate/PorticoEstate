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

	phpgw::import_class('phpgwapi.datetime');

	class booking_public360
	{

		private $debug, $webservicehost, $authkey, $proxy, $archive_user_id;

		public function __construct()
		{
			$location_id = $GLOBALS['phpgw']->locations->get_id('booking', 'run');
			$custom_config = CreateObject('admin.soconfig', $location_id);
			$custom_config_data = $custom_config->config_data['public360'];
			$config	= CreateObject('phpgwapi.config', 'booking')->read();

			if (!empty($custom_config_data['debug']))
			{
				$this->debug = true;
			}

			$this->webservicehost	 = !empty($custom_config_data['webservicehost']) ? $custom_config_data['webservicehost'] : '';
			$this->authkey			 = !empty($custom_config_data['authkey']) ? $custom_config_data['authkey'] : '';
			$this->proxy			 = !empty($config['proxy']) ? $config['proxy'] : '';
			$this->archive_user_id	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['archive_user_id'];
		}

		public function get_cases()
		{
			$data = array
			(
				'parameter' => array(
					'Recno'=> '201362',
				//	'CaseNumber' => '2020000693'
					)
			);

			$method = 'CaseService/GetCases';
			$ret = $this->transfer_data($method, $data);

			return $ret;
		}


		public function export_data( $title, $application, $files )
		{
			if(!$this->archive_user_id)
			{
				phpgwapi_cache::message_set( 'Ansvarlig arkiv-bruker er ikke angitt under innstillinger', 'error');
				return;
			}

			$person_data = $this->get_person( $application['customer_ssn'] );

			if($person_data[0] && empty($person_data[0]['PostAddress']['StreetAddress']))
			{
				$person_data = $this->add_update_person( $application, $person_data[0] );
			}

			if(!empty($application['customer_organization_number']))
			{
				$enterprise_data = $this->get_enterprise( $application['customer_organization_number']);
				if($enterprise_data[0] && empty($enterprise_data[0]['Name']))
				{
					$enterprise_data = $this->add_update_enterprise( $application, $enterprise_data[0]);
				}
			}

			$case_result = $this->create_case($title, $application);

			$document_result = array();

			if($case_result['Successful'])
			{
				$document_result = $this->create_document($case_result, $title, $files, $application);
			}

			return array(
				'external_archive_key'	 => $case_result['CaseNumber'],
				'case_result'			 => $case_result,
				'document_result'		 => $document_result
			);
		}


		public function get_person( $ssn )
		{

			$data = array(
				'ExternalID' => $ssn
				);

			$input = array('parameter' => $data);
			$method = 'ContactService/GetPrivatePersons';
			$person_data = $this->transfer_data($method, $input);
			return $person_data;
		}

		public function add_update_person ( $application, $person_data )
		{
			if(!empty($person_data['PostAddress']))
			{
				$PostAddress = $person_data['PostAddress'];
			}
			else
			{
				$PostAddress		 = array(
					'StreetAddress'	 => $application['responsible_street'],
					'ZipCode'		 => $application['responsible_zip_code'],
					'ZipPlace'		 => $application['responsible_city'],
					'Country'		 => 'NOR',
				);
			}

			$name_array = explode(' ', trim(str_replace('  ', ' ', $application['contact_name'])));
			$last_name = end($name_array);
			$first_name = implode(' ', array_pop($name_array));

			$data = array(
				'ExternalID'		 => $application['customer_ssn'],
				'PersonalIdNumber'	 => $application['customer_ssn'],
				'FirstName'			 => $person_data['FirstName'] ? $person_data['FirstName'] : $first_name,
				'MiddleName'		 => $person_data['MiddleName'] ? $person_data['MiddleName'] : '',
				'LastName'			 => $person_data['LastName'] ? $person_data['LastName'] : $last_name,
				'Email'				 => $person_data['Email'] ? $person_data['Email'] : $application['contact_email'],
				'PhoneNumber'		 => $person_data['PhoneNumber'] ? $person_data['PhoneNumber'] : $application['contact_phone'],
				'PostAddress'		 => $PostAddress
			);

			if(!empty($person_data['PrivateAddress']))
			{
				$data['PrivateAddress'] = $person_data['PrivateAddress'];
			}
			if(!empty($person_data['WorkAddress']))
			{
				$data['WorkAddress'] = $person_data['WorkAddress'];
			}

			$input = array('parameter' => $data);
			$method = 'ContactService/SynchronizePrivatePerson';
			$result = $this->transfer_data($method, $input);
			return $result;

		}

		public function get_enterprise( $organization_number )
		{

			$data = array(
				'EnterpriseNumber' => $organization_number
				);

			$input = array('parameter' => $data);
			$method = 'ContactService/GetEnterprises';
			$enterprise_data = $this->transfer_data($method, $input);
			return $enterprise_data;
		}

		public function add_update_enterprise ( $application, $enterprise_data )
		{
			$bo_soorganization = createObject('booking.boorganization');
			$org_id = $bo_soorganization->so->get_orgid( $application['customer_organization_number'] );
			$organization = $bo_soorganization->read_single($org_id);

			if(!empty($enterprise_data['PostAddress']['StreetAddress']))
			{
				$PostAddress = $enterprise_data['PostAddress'];
			}
			else
			{
				$PostAddress		 = array(
					'StreetAddress'	 => $organization['street'],
					'ZipCode'		 => $organization['zip_code'],
					'ZipPlace'		 => $organization['city'],
					'Country'		 => 'NOR',
				);
			}

			if (!empty($enterprise_data['OfficeAddress']['StreetAddress']))
			{
				$OfficeAddress = $enterprise_data['OfficeAddress'];
			}
			else
			{
				$OfficeAddress = array(
					'StreetAddress'	 => $organization['street'],
					'ZipCode'		 => $organization['zip_code'],
					'ZipPlace'		 => $organization['city'],
					'Country'		 => 'NOR',
				);
			}

			$data = array(
				'EnterpriseNumber'	 => $application['customer_organization_number'],
				'Name'				 => $enterprise_data['Name'] ? $enterprise_data['Name'] : $organization['name'],
				'Email'				 => $enterprise_data['Email'] ? $enterprise_data['Email'] : $organization['email'],
				'PhoneNumber'		 => $enterprise_data['PhoneNumber'] ? $enterprise_data['PhoneNumber'] : $organization['phone'],
				'PostAddress'		 => $PostAddress,
				'OfficeAddress'		 => $OfficeAddress
			);
	
			$input = array('parameter' => $data);
			$method = 'ContactService/SynchronizeEnterprise';
			$result = $this->transfer_data($method, $input);
			return $result;
		}

		public function create_case( $title, $application )
		{
			$data = array(
				'Title' => $title,
				'ExternalId' => array('Id' => $application['id'], 'Type' => 'portico'),
				'Status' => 'B',//'Under behandling',
				'AccessCodeCode' => 'U',
//				'ResponsibleEnterprise' => Array
//					(
//						'Recno' => '201665',
//					),
//				'ResponsiblePerson' => array
//					(
//						'Recno' => $this->archive_user_id,
//					),
				'ResponsiblePersonRecno' => $this->archive_user_id,
				'ArchiveCodes' => array
				(
					array
					(
						'Sort' => 1,
						'ArchiveCode' => '614',
						'ArchiveType' => 'FELLESKLASSE PRINSIPP',
					)
				),
				'Contacts' => array(
					array(
						'Role' => 'Sakspart', //Sakspart
						'ExternalId' => $application['customer_ssn'],
				//		'ReferenceNumber' => $ssn,
					)
				),
				'SubArchive' => '60001',
				'SubArchiveCode' => 'SAK',
			);
			$method = 'CaseService/CreateCase';

			$input = array('parameter' => $data);
			$case_data = $this->transfer_data($method, $input);
			return $case_data;
		}

		public function create_document( $case_data, $title, $files, $application )
		{
			$data = array(
				'CaseNumber' => $case_data['CaseNumber'],
				'Title' => $title,
				'Category' => 110, //Dokument inn
				'Status'	=> 'J', //Journalført
				'Files'		=> array(),
				'Contacts' => array(
					array(
						'Role' => 'Sakspart', //Sakspart
						'ExternalId' => $application['customer_ssn'],
				//		'ReferenceNumber' => $ssn,
					)
				),
				'ResponsiblePersonRecno' => $this->archive_user_id,
				'DocumentDate'			=> date('Y-m-d\TH:i:s.v', phpgwapi_datetime::user_localtime()) . 'Z',
			);

			foreach ($files as $file)
			{
				$path_parts = pathinfo($file['file_name']);
				$data['Files'][] = array(
					'Title' => $file['file_name'],
					'Format' => $path_parts['extension'],
					'Base64Data' => base64_encode($file['file_data'])
				);
			}

			$method = 'DocumentService/CreateDocument';
			$input = array('parameter' => $data);
			$cocument_data = $this->transfer_data($method, $input);
			return $cocument_data;
		}

		private function transfer_data( $method, $data )
		{
			$data_json	 = json_encode($data);

			$url = "{$this->webservicehost}/{$method}?authkey={$this->authkey}";

			$this->log('webservicehost', print_r($url, true));
			$this->log('POST data', print_r($data, true));

			$ch		 = curl_init();
			if ($this->proxy)
			{
				curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'accept: application/json',
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_json)
				));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);


			if($this->debug)
			{
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				$verbose = fopen('php://temp', 'w+');
				curl_setopt($ch, CURLOPT_STDERR, $verbose);
			}
			$result	 = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);

			if ($this->debug)
			{
				rewind($verbose);
				$verboseLog = stream_get_contents($verbose);
				echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
			}

			$this->log('webservice httpCode', print_r($httpCode, true));
			$this->log('webservice returdata as json', $result);
			$this->log('webservice returdata as array', print_r($ret, true));

			return $ret;
		}

		private function log( $what, $value = '' )
		{
			if (!empty($GLOBALS['phpgw_info']['server']['log_levels']['module']['booking']))
			{
				$GLOBALS['phpgw']->log->debug(array(
					'text' => "what: %1, <br/>value: %2",
					'p1' => $what,
					'p2' => $value ? $value : ' ',
					'line' => __LINE__,
					'file' => __FILE__
				));
			}
		}
	}