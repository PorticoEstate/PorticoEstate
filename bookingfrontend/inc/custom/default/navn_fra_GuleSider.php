<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2019 Free Software Foundation, Inc. http://www.fsf.org/
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


		function get_name_from_external_service(& $data)
		{
			/**
			 * Bruker foreløpig Gulesider direkte, men bør nok skifte til det offisielle api'et, om det finnes
			 * http://api.eniro.com/documentation/cs/proximity/basic
			 *
			 *
			 */
			if(empty($data['phone']))
			{
				return;
			}

			$apikey = !empty($this->config->config_data['apikey_external_user']) ? $this->config->config_data['apikey_external_user'] : '';
			$profile = 'sigurdne';
			$country = 'no';

			$webservicehost = !empty($this->config->config_data['webservicehost_external_user']) ? $this->config->config_data['webservicehost_external_user'] : 'https://api.eniro.com/cs/search/basic';

			$webservicehost = 'https://www.gulesider.no/api/ps';

			//       "https://www.gulesider.no/api/ps?query=90665164&sortOrder=default&profile=no&page=1&lat=0&lng=0&limit=25&client=1"

			if(!$webservicehost || !$apikey)
			{
	//			throw new Exception('Missing parametres for webservice');
			}

			$post_data = array
			(
				'query'	=> $data['phone'],
				'sortOrder' => 'default',
				'profile'	=> 'no',
				'page'		=> 1,
				'lat'		=> 0,
				'lng'		=> 0,
				'limit'		=> 2,
				'client'	=> 'true'
			);

			$post_string = http_build_query($post_data);

			$url = "{$webservicehost}?{$post_string}";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.90 Safari/537.36'));
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);

			if(!empty($ret['items'][0]['name']))
			{
				$data['name'] = $ret['items'][0]['name'];
				$data['street'] = "{$ret['items'][0]['address'][0]['streetName']} {$ret['items'][0]['address'][0]['streetNumber']}";
				$data['zip_code'] = "{$ret['items'][0]['address'][0]['postCode']}";
				$data['city'] = "{$ret['items'][0]['address'][0]['postArea']}";
			}

			if($this->debug)
			{
				$this->log('webservicehost', print_r($url, true));
				$this->log('POST data', print_r($post_data, true));
				$this->log('webservice httpCode', print_r($httpCode, true));
				$this->log('webservice returdata as json', $result);
				$this->log('webservice returdata as array', print_r($ret, true));
			}

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