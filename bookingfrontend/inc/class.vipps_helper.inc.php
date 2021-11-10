<?php

	/**
	 * https://vippsas.github.io/vipps-ecom-api/shins/index.html?php#vipps-ecommerce-api
	 */
	class bookingfrontend_vipps_helper
	{

		public $public_functions = array(
			'initiate'				 => true,
			'get_payment_details'	 => true,
			'check_payment_status'	 => true,
		);
		private $client_id,
			$client_secret,
			$subscription_key,
			$headers		 = array(),
			$proxy,
			$accesstoken,
			$debug;

		public function __construct()
		{
			$location_id		 = $GLOBALS['phpgw']->locations->get_id('booking', 'run');
			$custom_config		 = CreateObject('admin.soconfig', $location_id);
			$custom_config_data	 = $custom_config->config_data['Vipps'];
			$config				 = CreateObject('phpgwapi.config', 'booking')->read();

			if (!empty($custom_config_data['debug']))
			{
				$this->debug = true;
			}

			$this->base_url			 = !empty($custom_config_data['base_url']) ? $custom_config_data['base_url'] : 'https://apitest.vipps.no';
			$this->client_id		 = !empty($custom_config_data['client_id']) ? $custom_config_data['client_id'] : '';
			$this->client_secret	 = !empty($custom_config_data['client_secret']) ? $custom_config_data['client_secret'] : '';
			$this->subscription_key	 = !empty($custom_config_data['subscription_key']) ? $custom_config_data['subscription_key'] : '';
			$this->msn				 = !empty($custom_config_data['msn']) ? $custom_config_data['msn'] : '';
			$this->proxy			 = !empty($config['proxy']) ? $config['proxy'] : '';

			require_once PHPGW_API_INC . '/guzzle/vendor/autoload.php';

			$this->accesstoken = $this->get_accesstoken();
		}

		public function initiate()
		{
			$application_ids = phpgw::get_var('application_id');
			return $this->initiate_payment($application_ids);
		}

		/**
		 * POST /accesstoken/get
		 */
		private function get_accesstoken()
		{
			$path	 = '/accesstoken/get';
			$url	 = "{$this->base_url}{$path}";

			$client = new GuzzleHttp\Client();

			$request		 = array();
			$request_body	 = array();

			$request['headers'] = array(
				'Accept'					 => 'application/json;charset=UTF-8',
				'client_id'					 => $this->client_id,
				'client_secret'				 => $this->client_secret,
				'Ocp-Apim-Subscription-Key'	 => $this->subscription_key,
			);

			if ($this->proxy)
			{
				$request['proxy'] = array(
					'http'	 => $this->proxy,
					'https'	 => $this->proxy
				);
			}

			$request['json'] = $request_body;

			try
			{
				$response	 = $client->request('POST', $url, $request);
				$ret		 = json_decode($response->getBody()->getContents(), true);
			}
			catch (\GuzzleHttp\Exception\BadResponseException $e)
			{
				// handle exception or api errors.
				print_r($e->getMessage());
			}

			return !empty($ret['access_token']) ? $ret['access_token'] : null;
		}

		function get_item_name( $line )
		{
			return $line['name'];
		}

		function get_date_range( $dates )
		{
			return "{$dates['from_']} - {$dates['to_']}";
		}

		private function initiate_payment( $application_ids )
		{

			$orderId		 = null;
			$payment_attempt = 1;
			$soapplication	 = CreateObject('booking.soapplication');
			$filters		 = array('id' => $application_ids);
			$params			 = array('filters' => $filters, 'results' => 'all');
			$applications	 = $soapplication->read($params);

			$soapplication->get_purchase_order($applications);

			foreach ($applications['results'] as $application)
			{
				$dates = implode(', ', array_map(array($this, 'get_date_range'), $application['dates']));

				foreach ($application['orders'] as $order)
				{
					if (empty($order['paid']))
					{
						$orderId	 = $soapplication->add_payment($order['order_id'], $this->msn);
						$transaction = [
							"amount"					 => (float)$order['sum'] * 100,
							"orderId"					 => $orderId,
							"transactionText"			 => 'Aktiv kommune, bookingdato: ' . $dates,
							"skipLandingPage"			 => false,
							"scope"						 => "name address email",
							"useExplicitCheckoutFlow"	 => true
						];
						break 2;
					}
				}
			}

			$path	 = '/ecomm/v2/payments';
			$url	 = "{$this->base_url}{$path}";

			$client = new GuzzleHttp\Client();

			$request = array();

			$request['headers'] = array(
				'Accept'					 => 'application/json;charset=UTF-8',
				'Authorization'				 => $this->accesstoken,
				'Ocp-Apim-Subscription-Key'	 => $this->subscription_key,
			);

			if ($this->proxy)
			{
				$request['proxy'] = array(
					'http'	 => $this->proxy,
					'https'	 => $this->proxy
				);
			}

			$session_id = $GLOBALS['phpgw']->session->get_session_id();

			$request_body = [
				"customerInfo"	 => [
					"mobileNumber" => 90665164
				],
				"merchantInfo"	 => [
					"authToken"				 => $session_id,
					"callbackPrefix"		 => "https://example.com/vipps/callbacks-for-payment-updates",
					"consentRemovalPrefix"	 => "https://example.com/vipps/consent-removal",
//					"fallBack"				 => "https://example.com/vipps/fallback-order-result-page/Ak-shop-{$order_id}-order{$order_id}abc",
					"fallBack"				 => "http://127.0.0.1/~hc483/github_trunk/bookingfrontend/?menuaction=bookingfrontend.uiapplication.add_contact&payment_order_id={$orderId}",
					"isApp"					 => false,
					"merchantSerialNumber"	 => $this->msn,
					//				"paymentType"			 => "eComm Express Payment",
					"paymentType"			 => "eComm Regular Payment"
				],
				"transaction"	 => $transaction
			];

			$request['json'] = $request_body;

			try
			{
				$response	 = $client->request('POST', $url, $request);
				$ret		 = json_decode($response->getBody()->getContents(), true);
			}
			catch (\GuzzleHttp\Exception\BadResponseException $e)
			{
				// handle exception or api errors.
				print_r($e->getMessage());
			}

			return $ret;
		}

		private function capture_payment( $order_id, $amount )
		{
			$path	 = "/ecomm/v2/payments/{$order_id}/capture";
			$url	 = "{$this->base_url}{$path}";

			$client = new GuzzleHttp\Client();

			$request = array();

			$request['headers'] = array(
				'Accept'					 => 'application/json;charset=UTF-8',
				'Authorization'				 => $this->accesstoken,
				'Ocp-Apim-Subscription-Key'	 => $this->subscription_key,
			);

			if ($this->proxy)
			{
				$request['proxy'] = array(
					'http'	 => $this->proxy,
					'https'	 => $this->proxy
				);
			}

			$transaction = [
				"amount"			 => $amount,
				"transactionText"	 => 'Booking i Aktiv kommune',
			];

			$request_body = [
				"merchantInfo"	 => [
					"merchantSerialNumber" => $this->msn,
				],
				"transaction"	 => $transaction
			];

			$request['json'] = $request_body;

			try
			{
				$response	 = $client->request('POST', $url, $request);
				$ret		 = json_decode($response->getBody()->getContents(), true);
			}
			catch (\GuzzleHttp\Exception\BadResponseException $e)
			{
				// handle exception or api errors.
				print_r($e->getMessage());
			}
		}

		private function cancel_order( $payment_order_id )
		{
			$soapplication	 = CreateObject('booking.soapplication');
			$id				 = $soapplication->get_application_from_payment_order($payment_order_id);
			$status			 = array('deleted' => false);
			$session_id		 = $GLOBALS['phpgw']->session->get_session_id();
			if (!empty($session_id) && $id > 0)
			{
				$partials = CreateObject('booking.uiapplication')->get_partials($session_id);

				$GLOBALS['phpgw']->db->transaction_begin();

				$bo_block = createObject('booking.boblock');

				$exists = false;
				foreach ($partials['list'] as $partial)
				{
					if ($partial['id'] == $id)
					{
						$bo_block->cancel_block($session_id, $partial['dates'], $partial['resources']);
						$exists = true;
						break;
					}
				}
				if ($exists)
				{
					$application_id		 = $id;
					$soapplication->delete_purchase_order($application_id);
					$soapplication->update_payment_status($payment_order_id, 'voided');
					$soapplication->delete_application($application_id);
					$status['deleted']	 = true;
				}

				$GLOBALS['phpgw']->db->transaction_commit();
			}

			phpgwapi_cache::message_set('cancelled');
		}

		private function cancel_payment( $order_id )
		{
			$path	 = "/ecomm/v2/payments/{$order_id}/cancel";
			$url	 = "{$this->base_url}{$path}";

			$soapplication = CreateObject('booking.soapplication');

			$client = new GuzzleHttp\Client();

			$request = array();

			$request['headers'] = array(
				'Accept'					 => 'application/json;charset=UTF-8',
				'Authorization'				 => $this->accesstoken,
				'Ocp-Apim-Subscription-Key'	 => $this->subscription_key,
			);

			if ($this->proxy)
			{
				$request['proxy'] = array(
					'http'	 => $this->proxy,
					'https'	 => $this->proxy
				);
			}

			$transaction = [
				"transactionText" => 'Booking i Aktiv kommune',
			];

			$request_body = [
				"merchantInfo"	 => [
					"merchantSerialNumber" => $this->msn,
				],
				"transaction"	 => $transaction
			];

			$request['json'] = $request_body;

			try
			{
				$response	 = $client->request('PUT', $url, $request);
				$ret		 = json_decode($response->getBody()->getContents(), true);
			}
			catch (\GuzzleHttp\Exception\BadResponseException $e)
			{
				// handle exception or api errors.
				print_r($e->getMessage());
			}
		}

		private function authorize_payment( $param )
		{

		}

		private function refund_payment( $param )
		{

		}

		private function force_approve_payment( $param )
		{

		}

		public function check_payment_status( $payment_order_id = '' )
		{
			$boapplication	 = CreateObject('booking.boapplication');
			$soapplication	 = CreateObject('booking.soapplication');
			if (!$payment_order_id)
			{
				$payment_order_id = phpgw::get_var('payment_order_id');
			}

			static $attempts = 0;

//		    Start after 3 seconds
//		    Check every 2 seconds
			$cancel_array = array('CANCEL', 'VOID', 'FAILED', 'REJECTED');

			$approved_array = array('RESERVE', 'RESERVED');

			while ($attempts < 6)
			{
				if (!$attempts)
				{
					sleep(3);
				}
				else
				{
					sleep(2);
				}

				$data = $this->get_payment_details($payment_order_id);

				if (isset($data['transactionLogHistory'][0]['operation']))
				{
					if ($data['transactionLogHistory'][0]['operationSuccess'] && in_array($data['transactionLogHistory'][0]['operation'], $cancel_array))
					{
						$this->cancel_order($payment_order_id);
					}
					if ($data['transactionLogHistory'][0]['operationSuccess'] && in_array($data['transactionLogHistory'][0]['operation'], $approved_array))
					{
						$soapplication->update_payment_status($payment_order_id, 'pending');

						$capture = $this->capture_payment($payment_order_id, (int)$data['transactionLogHistory'][0]['amount']);
						if ($capture['transactionInfo']['status'] == 'Captured')
						{
							$GLOBALS['phpgw']->db->transaction_begin();
							$soapplication->update_payment_status($payment_order_id, 'completed');
							$this->approve_application($payment_order_id);
							$GLOBALS['phpgw']->db->transaction_commit();
						}
					}

					return $data;
				}

				$attempts++;
			}

			return array(
				'status'	 => 'error',
				'message'	 => 'not found'
			);
		}

		/**
		 * 
		 * @param string $payment_order_id
		 * @return boolean
		 */
		private function approve_application( $payment_order_id )
		{
			$boapplication = CreateObject('booking.soapplication');

			$application_id				 = $boapplication->so->get_application_from_payment_order($payment_order_id);
			$application				 = $boapplication->so->read_single($application_id);
			$application['status']		 = 'ACCEPTED';
			$receipt					 = $boapplication->update($application);
			$event						 = $application;
			unset($event['id']);
			unset($event['id_string']);
			$event['application_id']	 = $application['id'];
			$event['completed']			 = '0';
			$event['is_public']			 = 0;
			$event['include_in_list']	 = 0;
			$event['reminder']			 = 0;
			$event['customer_internal']	 = 0;
			$event['cost']				 = 0;

			$building_info			 = $boapplication->so->get_building_info($application['id']);
			$event['building_id']	 = $building_info['id'];
			$booking_boevent		 = createObject('booking.boevent');
			$errors					 = array();

			/**
			 * Validate timeslots
			 */
			foreach ($application['dates'] as $checkdate)
			{
				$event['from_']	 = $checkdate['from_'];
				$event['to_']	 = $checkdate['to_'];
				$errors			 = array_merge($errors, $booking_boevent->validate($event));
			}
			unset($checkdate);

			$ret = false;
			if (!$errors)
			{
				$session_id = $GLOBALS['phpgw']->session->get_session_id();

				CreateObject('booking.souser')->collect_users($application['customer_ssn']);
				$bo_block = createObject('booking.boblock');
				$bo_block->cancel_block($session_id, $application['dates'], $application['resources']);

				/**
				 * Add event for each timeslot
				 */
				foreach ($application['dates'] as $checkdate)
				{
					$event['from_']	 = $checkdate['from_'];
					$event['to_']	 = $checkdate['to_'];
					$receipt		 = $booking_boevent->so->add($event);
				}

				$booking_boevent->so->update_id_string();
				$boapplication->send_notification($application);
				$ret = true;
			}

			return $ret;
		}

		/**
		 * 
		 * @param string $payment_order_id
		 * @return type
		 */
		public function get_payment_details( $payment_order_id = '' )
		{

			if (!$payment_order_id)
			{
				$payment_order_id = phpgw::get_var('payment_order_id');
			}

			$path	 = "/ecomm/v2/payments/{$payment_order_id}/details";
			$url	 = "{$this->base_url}{$path}";
			$client	 = new GuzzleHttp\Client();

			$request = array();

			$request['headers'] = array(
				'Accept'					 => 'application/json;charset=UTF-8',
				'Authorization'				 => $this->accesstoken,
				'Ocp-Apim-Subscription-Key'	 => $this->subscription_key,
			);

			if ($this->proxy)
			{
				$request['proxy'] = array(
					'http'	 => $this->proxy,
					'https'	 => $this->proxy
				);
			}


			$request_body = array();

			$request['json'] = $request_body;

			try
			{
				$response	 = $client->request('GET', $url, $request);
				$ret		 = json_decode($response->getBody()->getContents(), true);
			}
			catch (\GuzzleHttp\Exception\BadResponseException $e)
			{
				// handle exception or api errors.
//				print_r($e->getMessage());
			}

			return $ret;
		}
	}