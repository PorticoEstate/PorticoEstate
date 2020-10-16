<?php
	$test = new test_curl_altinn();

	class test_curl_altinn
	{

		public function __construct()
		{
			$this->get_orgs_from_external_service();
		}

		function get_orgs_from_external_service()
		{

			$url ='https://tjenester.srv.bergenkom.no/api/altinnservice/getAvgiver/20056432559';

			$username	 = 'portico';
			$password	 = 'xtrPrt369xR234Srthg';


			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			
			echo '<pre>';
			print_r(json_decode($result, true));
			echo '</pre>';

		}


	}