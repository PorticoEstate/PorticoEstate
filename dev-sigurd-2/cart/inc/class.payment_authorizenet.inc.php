<?php
  /**************************************************************************\
  * phpGroupWare - phpgw echo test                                           *
  * http://www.phpgroupware.org                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	class payment
	{
		# Authorizenet userid.
		var $login = 'testdrive';

		# Authorizenet password.
		var $password = 'testdrive';

		var $transaction_type = 'NA';
		var $transaction_types = array(
			'AUTH_CAPTURE',
			'AUTH_ONLY',
			'PRIOR_AUTH_CAPTURE',
			'CAPTURE_ONLY',
			'CREDIT',
			'VOID'
		);

		var $adc_delim_data     = 'TRUE';
		var $adc_url            = 'FALSE';
		var $authorization_type = 'AUTH_CAPTURE';
		var $an_version         = '3.0';

		var $form_data = array(
			'full_name'       => 'John Doe',
			'charge_amount'   => '49.95',
			'payment_method'  => 'VISA',
			'expiration_date' => '09/2001',
			'card_num'        => '4007000000027'
		);

		var $test_request = 'TRUE';
		var $echo_data = 'TRUE';

		var $host = array(
			'auth'     => 'www.authorize.net',
			'transact' => 'secure.authorize.net'
		);

		var $script = array(
			'auth'     => '/scripts/authnet25/AuthRequest.asp',
			'transact' => '/gateway/transact.dll'
		);
		var $port = '443';

		# authrequest.asp response codes:
		var $response_codes = array(
			1 => 'Accepted/Authorized',
			2 => 'Declined', /* (treated s Accepted/Authorized; applies to ACH or Post Auth transactions) */
			3 => 'Error'
		);

		/* Address Verification System (AVS) checks. */
		var $avs_codes = array(
			'A' => 'Address (Street) matches, ZIP does not',
			'B' => 'Address Information Not Provided for AVS Check',
			'E' => 'AVS error',
			'G' => 'Non U.S. Card Issuing Bank',
			'N' => 'No Match on Address (Street) or ZIP',
			'P' => 'AVS not applicable for this transaction',
			'R' => 'Retry - System unavailable or timed out',
			'S' => 'Service not supported by issuer',
			'U' => 'Address information is unavailable',
			'W' => '9 digit ZIP matches, Address (Street) does not',
			'X' => 'Address (Street) and 9 digit ZIP match',
			'Y' => 'Address (Street) and 5 digit ZIP match',
			'Z' => '5 digit ZIP matches, Address (Street) does not'
		);

		var $response_array = array(
			'response_code'        => '',
			'response_sub_code'    => '',
			'response_reason_code' => '', 
			'response_reason_text' => '',
			'auth_code'            => '',
			'avs_code'             => '',
			'trans_id'             => ''
		);

		var $response = array();
		var $resp_data = '';

		function authorize()
		{
			$form_data = $this->build_req(array(
				'x_Login'        => $this->login,
				'x_Password'     => $this->password,
				'x_Test_Request' => $this->test_request,
				'x_Echo_Data'    => $this->echo_data,
				'x_Type'         => $this->transaction_type,
				'x_Method'       => $this->form_data['payment_method'],
				'x_First_Name'   => $this->form_data['first_name'],
				'x_Last_Name'    => $this->form_data['last_name'],
				'x_Amount'       => $this->form_data['charge_amount'],
				'x_Card_Num'     => $this->form_data['card_num'],
				'x_Exp_Date'     => $this->form_data['expiration_date']
			));

			list($reply_data, $reply_type, $reply_headers) = $this->send($this->host['auth'], $port, $this->script['auth'], '', $form_data);
			$this->debug_reply($this->script['auth'], $reply_data, $reply_type, $reply_headers);
		}

		function transact()
		{
			/* No password required */
			$form_data = $this->build_req(array(
				'x_Login'          => $this->login,
				'x_Version'        => $this->an_version,
				'x_ADC_Delim_Data' => $this->adc_delim_data,
				'x_ADC_URL'        => $this->adc_url,
				'x_Type'           => $this->authorization_type,
				'x_Test_Request'   => $this->test_request,
				'x_Method'         => $this->form_data['payment_method'],
				'x_First_Name'     => $this->form_data['first_name'],
				'x_Last_Name'      => $this->form_data['last_name'],
				'x_Amount'         => $this->form_data['charge_amount'],
				'x_Card_Num'       => $this->form_data['card_num'],
				'x_Exp_Date'       => $this->form_data['expiration_date'],
				'x_Echo_Data'      => $this->echo_data
			));

			list($reply_data, $reply_type, $reply_headers) = $this->send($this->host['transact'], $this->port, $this->script['transact'], '', $form_data);
			$this->debug_reply($this->script['transact'], $reply_data, $reply_type, $reply_headers);
		}

		function add_text($data)
		{
			$data['response_code_text'] = $this->response_codes[$data['response_code']];
			$data['avs_code_text'] = $this->avs_codes[$data['avs_code']];
			/* ksort($data); */
			return $data;
		}

		function build_req($form_fields)
		{
			/* _debug_array($form_fields); */
			$rtrn = '?';
			while(list($key,$val) = @each($form_fields))
			{
				$rtrn .= $key . '=' . $val . '&';
			}
			$rtrn = substr($rtrn,0,-1);
			/* echo $rtrn;exit; */
			return $rtrn;
		}

		function send($server,$port,$script,$null,$extra)
		{
			/* $curl = curl_init('https://' . $server . ':' . $port . $script . $extra); */
			$str = 'https://' . $server . $script . $extra;
			/* echo $str; exit; */
			$curl = curl_init($str);

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_USERAGENT, 'PHPGW ' . $GLOBALS['phpgw_info']['server']['version']);
			curl_setopt($curl, CURLOPT_HEADER, 1);

			$result = curl_exec($curl);
			/* _debug_array($result); */
			list($data,$type,$headers) = $this->parse_response($result);
			return array($data,$type,$headers);
		}

		function parse_response($in)
		{
			$this->response = $this->response_array;
			$this->resp_data = '';
			$data = split("\r\n",$in);
			$header = True;
			while(list(,$line) = @each($data))
			{
				/* echo $line . "<br>\n"; */
				$line = trim($line);
				if(ereg('Content:',$line))
				{
					/* echo '<br>now parsing data:' . "\n"; */
					$header = False;
					continue;
				}
				if($line)
				{
					if(!$header)
					{
						$resp_data[] = $line;
					}
					else
					{
						$resp_header[] = $line;
					}
				}
			}
			/* _debug_array($resp_data); */
			$this->resp_data = $resp_data[0];
			$resp_data = substr($this->resp_data,0,-1);
			$resp_data = substr($this->resp_data,1);
			$resp_data = split('","',$this->resp_data);
			/* _debug_array($this->resp_data); */

			$i = 0;
			$data = array();
			reset($this->response_array);
			while(list($key,$val) = each($this->response_array))
			{
				$data[$key] = $resp_data[$i];
				$i++;
			}
			$this->response = $this->add_text($data);

			/* _debug_array($resp_header); */
			/* _debug_array($this->response); */
			return array($this->response,'',$resp_header);
		}

		function debug_reply($scriptname, $reply_data, $reply_type, $reply_headers)
		{
			echo '<br><br><br>Reply headers received from ' . $scriptname . ':' . "\n";
			while(list($key,$val) = @each($reply_headers))
			{
				echo '<br>' . $key . ': ' . $val . "\n";
			}

			echo '<br><br>Reply type received from ' . $scriptname . ': ' . $reply_type . "\n";

			echo '<br><br>Unparsed reply data:<br> ' . $this->resp_data . "\n";

			echo '<br><br>Parsed reply data:' . "\n";
			while (list($key,$val) = @each($reply_data))
			{
				echo '<br>' . $key . ': ' . $val . "\n";
			}
		}
	}
