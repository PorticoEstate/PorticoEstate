<?php

	/**
	 * https://vippsas.github.io/vipps-ecom-api/shins/index.html?php#vipps-ecommerce-api
	 */
	class bookingfrontend_vipps_helper
	{

		public $public_functions = array(
			'initiate' => true,
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
			$this->msn	 = !empty($custom_config_data['msn']) ? $custom_config_data['msn'] : '';
			$this->proxy			 = !empty($config['proxy']) ? $config['proxy'] : '';

//			$this->guzzle = CreateObject('phpgwapi.guzzle');
			require_once PHPGW_API_INC . '/guzzle/vendor/autoload.php';
		}

		public function initiate()
		{
			$order_id			 = 123457; //phpgw::get_var('order_id');
			$this->accesstoken	 = $this->get_accesstoken();
			return $this->initiate_payment($order_id);
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

		private function initiate_payment( $order_id )
		{
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
					"fallBack"				 => "http://127.0.0.1/~hc483/github_trunk/bookingfrontend/?menuaction=bookingfrontend.uiapplication.add_contact&id=764&building_id=153",
					"isApp"					 => false,
					"merchantSerialNumber"	 => $this->msn,
	//				"paymentType"			 => "eComm Express Payment",
					"paymentType"			 => "eComm Regular Payment"
				],
				"transaction"	 => [
					"amount"					 => 100,
					"orderId"					 => "Ak-shop-{$order_id}-order{$order_id}abc",
					"transactionText"			 => "One pair of Vipps socks",
					"skipLandingPage"			 => false,
					"scope"						 => "name address email",
					"useExplicitCheckoutFlow"	 => true
				]
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

		private function capture_payment( $param )
		{
			
		}

		private function cancel_payment( $param )
		{

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

		private function get_payment_details( $param )
		{

		}
	}