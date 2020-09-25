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


		public function export_data( $title, $id, $ssn, $files )
		{
			if(!$this->archive_user_id)
			{
				phpgwapi_cache::message_set( 'Ansvarlig arkiv-bruker er ikke angitt under innstillinger', 'error');
				return;
			}

			$case_result = $this->create_case($title, $id, $ssn, $files);

			$document_result = array();

			if($case_result['Successful'])
			{
				$document_result = $this->create_document($case_result, $title, $files);
			}

			return array(
				'external_archive_key'	 => $case_result['CaseNumber'],
				'case_result'			 => $case_result,
				'document_result'		 => $document_result
			);
		}

		public function create_case( $title, $id, $ssn, $files )
		{
			$data = array(
				'Title' => $title,
				'ExternalId' => array('Id' => $id, 'Type' => 'portico'),
				'Status' => 'B',//'Under behandling',
				'AccessCodeCode' => 'U',
//				'ResponsibleEnterprise' => Array
//					(
//						'Recno' => '201665',
//					),
				'ResponsiblePerson' => array
					(
						'Recno' => $this->archive_user_id,
					),
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
						'ExternalId' => $ssn,
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

		public function create_document( $case_data, $title, $files )
		{
			$data = array(
				'CaseNumber' => $case_data['CaseNumber'],
				'Title' => $title,
				'Category' => 60006, //Vedtak
				'Status'	=> 'J', //Journalført
				'Files'		=> array()
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