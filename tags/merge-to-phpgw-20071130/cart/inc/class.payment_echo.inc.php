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

  /* $Id: class.payment_echo.inc.php 15854 2005-04-18 09:40:05Z powerstat $ */

    /*==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-*/
    //                                                            //
    //  Name: ECHOPHP v1.4.4                                      //
    //  Description: PHP Class used to interface with             //
    //               ECHO (http://www.echo-inc.com).              //
    //  Requirements: cURL - http://curl.haxx.se/                 //
    //                OpenSSL - http://www.openssl.org            //
    //  Refer to ECHO's documentation for more info               //
    //  https://wwws.echo-inc.com                                 //
    //                                                            //
    /*==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-*/

	class payment
	{
		var $order_type;
		var $transaction_type;
		var $merchant_echo_id;
		var $merchant_pin;
		var $isp_echo_id;
		var $isp_pin;
		var $authorization;
		var $billing_ip_address;
		var $billing_prefix;
		var $billing_name;
		var $billing_address1;
		var $billing_address2;
		var $billing_city;
		var $billing_state;
		var $billing_zip;
		var $billing_country;
		var $billing_phone;
		var $billing_fax;
		var $billing_email;
		var $cc_number;
		var $ccexp_month;
		var $ccexp_year;
		var $counter;
		var $debug;
		var $ec_account;
		var $ec_address1;
		var $ec_address2;
		var $ec_bank_name;
		var $ec_business_acct;
		var $ec_city;
		var $ec_email;
		var $ec_first_name;
		var $ec_id_country;
		var $ec_id_exp_mm;
		var $ec_id_exp_dd;
		var $ec_id_exp_yy;
		var $ec_id_number;
		var $ec_id_state;
		var $ec_id_type;
		var $ec_last_name;
		var $ec_merchant_ref;
		var $ec_nbds_code;
		var $ec_other_name;
		var $ec_payee;
		var $ec_rt;
		var $ec_serial_number;
		var $ec_state;
		var $ec_zip;
		var $grand_total;
		var $merchant_email;
		var $merchant_trace_nbr;
		var $original_amount;
		var $original_trandate_mm;
		var $original_trandate_dd;
		var $original_trandate_yyyy;
		var $original_reference;
		var $order_number;
		var $shipping_flag;
		var $status;
		var $shipping_prefix;
		var $shipping_name;
		var $shipping_address1;
		var $shipping_address2;
		var $shipping_city;
		var $shipping_state;
		var $shipping_zip;
		var $shipping_comments;
		var $shipping_country;
		var $shipping_phone;
		var $shipping_fax;
		var $shipper;
		var $shipper_tracking_nbr;
		var $track1;
		var $track2;
		var $EchoResponse;
		var $echotype1;
		var $echotype2;
		var $echotype3;
		var $openecho;
		var $avs_result;
		var $athorization;
		var $reference;
		var $EchoSuccess;

		function submit()
		{
			if ($this->EchoServer)
			{
				$URL = $this->EchoServer;
			}
			else
			{
				$URL = 'https://wwws.echo-inc.com/scripts/INR300.EXE';
			}

			$this->EchoResponse = '';

			$data = $this->getURLData();

			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_URL, $URL);
			curl_setopt ($ch, CURLOPT_POST, $data);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
			$this->EchoResponse = curl_exec ($ch);
			curl_close ($ch);

			$startpos = strpos($this->EchoResponse, '<ECHOTYPE1>') + 11;
			$endpos = strpos($this->EchoResponse, '</ECHOTYPE1>');
			$this->echotype1 = substr($this->EchoResponse, $startpos, $endpos - $startpos);

			$startpos = strpos($this->EchoResponse, '<ECHOTYPE2>') + 11;
			$endpos = strpos($this->EchoResponse, '</ECHOTYPE2>');
			$this->echotype2 = substr($this->EchoResponse, $startpos, $endpos - $startpos);

			$startpos = strpos($this->EchoResponse, '<ECHOTYPE3>') + 11;
			$endpos = strpos($this->EchoResponse, '</ECHOTYPE3>');
			$this->echotype3 = substr($this->EchoResponse, $startpos, $endpos - $startpos);

			if (strpos($this->EchoResponse, '<OPENECHO>'))
			{
				$startpos = strpos($this->EchoResponse, '<OPENECHO>') + 10;
				$endpos = strpos($this->EchoResponse, '</OPENECHO>');
				$this->openecho = substr($this->EchoResponse, $startpos, $endpos - $startpos);
			}

			// Get all the metadata.
			$this->GetAuthFromEcho();
			$this->GetOrderNumberFromEcho();
			$this->GetReferenceFromEcho();
			$this->status = $this->GetEchoProp($this->echotype3, 'status');
			$this->avs_result = $this->GetEchoProp($this->echotype3, 'avs_result');

			if ($this->transaction_type == 'AD')
			{
				if ($this->avs_result == 'X' or $this->avs_result == 'Y')
				{
					$this->EchoSuccess = true;
				}
				else
				{
					$this->EchoSuccess = false;
				}
			}
			else
			{
				$this->EchoSuccess = !($this->status == 'D');
			}

			if ($this->EchoResponse == '')
			{
				$this->EchoSuccess = False;
			}

			// make sure we assign an integer to EchoSuccess
			($this->EchoSuccess == true) ? ($this->EchoSuccess = true) : ($this->EchoSuccess = false);

			return $this->EchoSuccess;
		} // function submit

		function getURLData()
		{
			$s .=
			'order_type='          . $this->order_type .
			'&transaction_type='   . $this->transaction_type .
			'&merchant_echo_id='   . $this->merchant_echo_id .
			'&merchant_pin='       . $this->merchant_pin .
			'&isp_echo_id='        . $this->isp_echo_id .
			'&isp_pin='            . $this->isp_pin .
			'&authorization='      . $this->authorization .
			'&billing_ip_address=' . $this->billing_ip_address .
			'&billing_prefix='     . $this->billing_prefix .
			'&billing_name='       . $this->billing_name .
			'&billing_address1='   . $this->billing_address1 .
			'&billing_address2='   . $this->billing_address2 .
			'&billing_city='       . $this->billing_city .
			'&billing_state='      . $this->billing_state .
			'&billing_zip='        . $this->billing_zip .
			'&billing_country='    . $this->billing_country .
			'&billing_phone='      . $this->billing_phone .
			'&billing_fax='        . $this->billing_fax .
			'&billing_email='      . $this->billing_email .
			'&cc_number='          . $this->cc_number .
			'&ccexp_month='        . $this->ccexp_month .
			'&ccexp_year='         . $this->ccexp_year .
			'&counter='            . $this->counter .
			'&debug='              . $this->debug .
			'&transaction_type='   . $this->transaction_type;

			if (($this->transaction_type == 'DD') ||
				($this->transaction_type == 'DC') ||
				($this->transaction_type == 'DV'))
			{
				$s .=
				'&ec_account='       . $this->ec_account .
				'&ec_address1='      . $this->ec_address1 .
				'&ec_address2='      . $this->ec_address2 .
				'&ec_bank_name='     . $this->ec_bank_name .
				'&ec_business_acct=' . $this->ec_business_acct .
				'&ec_city='          . $this->ec_city .
				'&ec_email='         . $this->ec_email .
				'&ec_first_name='    . $this->ec_first_name .
				'&ec_id_country='    . $this->ec_id_country .
				'&ec_id_exp_mm='     . $this->ec_id_exp_mm .
				'&ec_id_exp_dd='     . $this->ec_id_exp_dd .
				'&ec_id_exp_yy='     . $this->ec_id_exp_yy .
				'&ec_id_number='     . $this->ec_id_number .
				'&ec_id_state='      . $this->ec_id_state .
				'&ec_id_type='       . $this->ec_id_type .
				'&ec_last_name='     . $this->ec_last_name .
				'&ec_merchant_ref='  . $this->ec_merchant_ref .
				'&ec_nbds_code='     . $this->ec_nbds_code .
				'&ec_other_name='    . $this->ec_other_name .
				'&ec_payee='         . $this->ec_payee .
				'&ec_rt='            . $this->ec_rt .
				'&ec_serial_number=' . $this->ec_serial_number .
				'&ec_state='         . $this->ec_state .
				'&ec_zip='           . $this->ec_zip;
			}

			$s .=
			'&grand_total='            . $this->grand_total .
			'&merchant_email='         . $this->merchant_email .
			'&merchant_trace_nbr='     . $this->merchant_trace_nbr .
			'&original_amount='        . $this->original_amount .
			'&original_trandate_mm='   . $this->original_trandate_mm .
			'&original_trandate_dd='   . $this->original_trandate_dd .
			'&original_trandate_yyyy=' . $this->original_trandate_yyyy .
			'&original_reference='     . $this->original_reference .
			'&order_number='           . $this->order_number .
			'&shipping_flag='          . $this->shipping_flag .
			'&shipping_prefix='        . $this->shipping_prefix .
			'&shipping_name='          . $this->shipping_name .
			'&shipping_address1='      . $this->shipping_address1 .
			'&shipping_address2='      . $this->shipping_address2 .
			'&shipping_city='          . $this->shipping_city .
			'&shipping_state='         . $this->shipping_state .
			'&shipping_zip='           . $this->shipping_zip .
			'&shipping_comments='      . $this->shipping_comments .
			'&shipping_country='       . $this->shipping_country .
			'&shipping_phone='         . $this->shipping_phone .
			'&shipping_fax='           . $this->shipping_fax .
			'&shipper='                . $this->shipper .
			'&shipper_tracking_nbr='   . $this->shipper_tracking_nbr .
			'&track1='                 . $this->track1 .
			'&track2='                 . $this->track2;

			return $s;
		} /* end getURLData */

		/**********************************************
		All the get/set methods for the echo properties
		***********************************************/
		function set_order_type($value)
		{
			$this->order_type = $value;
		}

		function get_order_type()
		{
			return $this->order_type;
		}

		function set_transaction_type($value)
		{
			$this->transaction_type = $value;
		}

		function get_transaction_type()
		{
			return $this->transaction_type;
		}

		function set_merchant_echo_id($value)
		{
			$this->merchant_echo_id = urlencode($value);
		}

		function get_merchant_echo_id()
		{
			return $this->merchant_echo_id;
		}

		function set_merchant_pin($value)
		{
			$this->merchant_pin = urlencode($value);
		}

		function get_merchant_pin()
		{
			return $this->merchant_pin;
		}

		function set_isp_echo_id($value)
		{
			$this->isp_echo_id = urlencode($value);
		}

		function get_isp_echo_id()
		{
			return $this->isp_echo_id;
		}

		function set_isp_pin($value)
		{
			$this->isp_pin = urlencode($value);
		}

		function get_isp_pin()
		{
			return $this->isp_pin;
		}

		function set_authorization($value)
		{
			$this->authorization = $value;
		}

		function get_authorization()
		{
			return $this->authorization;
		}

		function set_billing_ip_address($value)
		{
			$this->billing_ip_address = $value;
		}

		function get_billing_ip_address()
		{
			return $this->billing_ip_address;
		}

		function set_billing_prefix($value)
		{
			$this->billing_prefix = urlencode($value);
		}

		function get_billing_prefix()
		{
			return $this->billing_prefix;
		}

		function set_billing_name($value)
		{
			$this->billing_name = urlencode($value);
		}

		function get_billing_name()
		{
			return $this->billing_name;
		}

		function set_billing_address1($value)
		{
			$this->billing_address1 = urlencode($value);
		}

		function get_billing_address1()
		{
			return $this->billing_address1;
		}

		function set_billing_address2($value)
		{
			$this->billing_address2 = urlencode($value);
		}

		function get_billing_address2()
		{
			return $this->billing_address2;
		}

		function set_billing_city($value)
		{
			$this->billing_city = urlencode($value);
		}

		function get_billing_city()
		{
			return $this->billing_city;
		}

		function set_billing_state($value)
		{
			$this->billing_state = urlencode($value);
		}

		function get_billing_state()
		{
			return $this->billing_state;
		}

		function set_billing_zip($value)
		{
			$this->billing_zip = urlencode($value);
		}

		function get_billing_zip()
		{
			return $this->billing_zip;
		}

		function set_billing_country($value)
		{
			$this->billing_country = urlencode($value);
		}

		function get_billing_country()
		{
			return $this->billing_country;
		}

		function set_billing_phone($value)
		{
			$this->billing_phone = urlencode($value);
		}

		function get_billing_phone()
		{
			return $this->billing_phone;
		}

		function set_billing_fax($value)
		{
			$this->billing_fax = urlencode($value);
		}

		function get_billing_fax()
		{
			return $this->billing_fax;
		}

		function set_billing_email($value)
		{
			$this->billing_email = urlencode($value);
		}

		function get_billing_email()
		{
			return $this->billing_email;
		}

		function set_cc_number($value)
		{
			$this->cc_number = urlencode($value);
		}

		function get_cc_number()
		{
			return $this->cc_number;
		}

		function set_ccexp_month($value)
		{
			$this->ccexp_month = $value;
		}

		function get_ccexp_month()
		{
			return $this->ccexp_month;
		}

		function set_ccexp_year($value)
		{
			$this->ccexp_year = $value;
		}

		function get_ccexp_year()
		{
			return $this->ccexp_year;
		}

		function set_counter($value)
		{
			$this->counter = $value;
		}

		function get_counter()
		{
			return $this->counter;
		}

		function set_debug($value)
		{
			$this->debug = $value;
		}

		function get_debug()
		{
			return $this->debug;
		}

		function set_ec_account($value)
		{
			$this->ec_account = urlencode($value);
		}

		function get_ec_account()
		{
			return $this->ec_account;
		}

		function set_ec_address1($value)
		{
			$this->ec_address1 = urlencode($value);
		}

		function get_ec_address1()
		{
			return $this->ec_address1;
		}

		function set_ec_address2($value)
		{
			$this->ec_address2 = urlencode($value);
		}

		function get_ec_address2()
		{
			return $this->ec_address2;
		}

		function set_ec_bank_name($value)
		{
			$this->ec_bank_name = urlencode($value);
		}

		function get_ec_bank_name()
		{
			return $this->ec_bank_name;
		}

		function set_ec_business_acct($value)
		{
			$this->ec_business_acct = urlencode($value);
		}

		function get_ec_business_acct()
		{
			return $this->ec_business_acct;
		}

		function set_ec_city($value)
		{
			$this->ec_city = $value;
		}

		function get_ec_city()
		{
			return $this->ec_city;
		}

		function set_ec_email($value)
		{
			$this->ec_email = urlencode($value);
		}

		function get_ec_email()
		{
			return $this->ec_email;
		}

		function set_ec_first_name($value)
		{
			$this->ec_first_name = urlencode($value);
		}

		function get_ec_first_name()
		{
			return $this->ec_first_name;
		}

		function set_ec_id_country($value)
		{
			$this->ec_id_country = urlencode($value);
		}

		function get_ec_id_country()
		{
			return $this->ec_id_country;
		}

		function set_ec_id_exp_mm($value)
		{
			$this->ec_id_exp_mm = $value;
		}

		function get_ec_id_exp_mm()
		{
			return $this->ec_id_exp_mm;
		}

		function set_ec_id_exp_dd($value)
		{
			$this->ec_id_exp_dd = $value;
		}

		function get_ec_id_exp_dd()
		{
			return $this->ec_id_exp_dd;
		}

		function set_ec_id_exp_yy($value)
		{
			$this->ec_id_exp_yy = $value;
		}

		function get_ec_id_exp_yy()
		{
			return $this->ec_id_exp_yy;
		}

		function set_ec_id_number($value)
		{
			$this->ec_id_number = urlencode($value);
		}

		function get_ec_id_number()
		{
			return $this->ec_id_number;
		}

		function set_ec_id_state($value)
		{
			$this->ec_id_state = urlencode($value);
		}

		function get_ec_id_state()
		{
			return $this->ec_id_state;
		}

		function set_ec_id_type($value)
		{
			$this->ec_id_type = $value;
		}

		function get_ec_id_type()
		{
			return $this->ec_id_type;
		}

		function set_ec_last_name($value)
		{
			$this->ec_last_name = urlencode($value);
		}

		function get_ec_last_name()
		{
			return $this->ec_last_name;
		}

		function set_ec_merchant_ref($value)
		{
			$this->ec_merchant_ref = $value;
		}

		function get_ec_merchant_ref()
		{
			return $this->ec_merchant_ref;
		}

		function set_ec_nbds_code($value)
		{
			$this->ec_nbds_code = $value;
		}

		function get_ec_nbds_code()
		{
			return $this->ec_nbds_code;
		}

		function set_ec_other_name($value)
		{
			$this->ec_other_name = urlencode($value);
		}

		function get_ec_other_name()
		{
			return $this->ec_other_name;
		}

		function set_ec_payee($value)
		{
			$this->ec_payee = urlencode($value);
		}

		function get_ec_payee()
		{
			return $this->ec_payee;
		}

		function set_ec_rt($value)
		{
			$this->ec_rt = urlencode($value);
		}

		function get_ec_rt()
		{
			return $this->ec_rt;
		}

		function set_ec_serial_number($value)
		{
			$this->ec_serial_number = urlencode($value);
		}

		function get_ec_serial_number()
		{
			return $this->ec_serial_number;
		}

		function set_ec_state($value)
		{
			$this->ec_state = urlencode($value);
		}

		function get_ec_state()
		{
			return $this->ec_state;
		}

		function set_ec_zip($value)
		{
			$this->ec_zip = urlencode($value);
		}

		function get_ec_zip()
		{
			return $this->ec_zip;
		}

		function set_grand_total($value)
		{
			$this->grand_total = sprintf('%01.2f', $value);
		}

		function get_grand_total()
		{
			return $this->grand_total;
		}

		function set_merchant_email($value)
		{
			$this->merchant_email = urlencode($value);
		}

		function get_merchant_email()
		{
			return $this->merchant_email;
		}

		function set_merchant_trace_nbr($value)
		{
			$this->merchant_trace_nbr = $value;
		}

		function get_merchant_trace_nbr()
		{
			return $this->merchant_trace_nbr;
		}

		function set_original_amount($value)
		{
			$this->original_amount = sprintf('%01.2f', $value);
		}

		function get_original_amount()
		{
			return $this->original_amount;
		}

		function set_original_trandate_mm($value)
		{
			$this->original_trandate_mm = $value;
		}

		function get_original_trandate_mm()
		{
			return $this->original_trandate_mm;
		}

		function set_original_trandate_dd($value)
		{
			$this->original_trandate_dd = $value;
		}

		function get_original_trandate_dd()
		{
			return $this->original_trandate_dd;
		}

		function set_original_trandate_yyyy($value)
		{
			$this->original_trandate_yyyy = $value;
		}

		function get_original_trandate_yyyy()
		{
			return $this->original_trandate_yyyy;
		}

		function set_original_reference($value)
		{
			$this->original_reference = $value;
		}

		function get_original_reference()
		{
			return $this->original_reference;
		}

		function set_order_number($value)
		{
			$this->order_number = $value;
		}

		function get_order_number()
		{
			return $this->order_number;
		}

		function set_shipping_flag($value)
		{
			$this->shipping_flag = $value;
		}

		function get_shipping_flag()
		{
			return $this->shipping_flag;
		}

		function set_shipping_prefix($value)
		{
			$this->shipping_prefix = urlencode($value);
		}

		function get_shipping_prefix()
		{
			return $this->shipping_prefix;
		}

		function set_shipping_name($value)
		{
			$this->shipping_name = urlencode($value);
		}

		function get_shipping_name()
		{
			return $this->shipping_name;
		}

		function set_shipping_address1($value)
		{
			$this->shipping_address1 = urlencode($value);
		}

		function get_shipping_address1()
		{
			return $this->shipping_address1;
		}

		function set_shipping_address2($value)
		{
			$this->shipping_address2 = urlencode($value);
		}

		function get_shipping_address2()
		{
			return $this->shipping_address2;
		}

		function set_shipping_city($value)
		{
			$this->shipping_city = urlencode($value);
		}

		function get_shipping_city()
		{
			return $this->shipping_city;
		}

		function set_shipping_state($value)
		{
			$this->shipping_state = urlencode($value);
		}

		function get_shipping_state()
		{
			return $this->shipping_state;
		}

		function set_shipping_zip($value)
		{
			$this->shipping_zip = urlencode($value);
		}

		function get_shipping_zip()
		{
			return $this->shipping_zip;
		}

		function set_shipping_comments($value)
		{
			$this->shipping_comments = urlencode($value);
		}

		function get_shipping_comments()
		{
			return $this->shipping_comments;
		}

		function set_shipping_country($value)
		{
			$this->shipping_country = urlencode($value);
		}

		function get_shipping_country()
		{
			return $this->shipping_country;
		}

		function set_shipping_phone($value)
		{
			$this->shipping_phone = urlencode($value);
		}

		function get_shipping_phone()
		{
			return $this->shipping_phone;
		}

		function set_shipping_fax($value)
		{
			$this->shipping_fax = urlencode($value);
		}

		function get_shipping_fax()
		{
			return $this->shipping_fax;
		}

		function set_shipper($value)
		{
			$this->shipper = urlencode($value);
		}

		function get_shipper()
		{
			return $this->shipper;
		}

		function set_shipper_tracking_nbr($value)
		{
			$this->shipper_tracking_nbr = $value;
		}

		function get_shipper_tracking_nbr()
		{
			return $this->shipper_tracking_nbr;
		}

		function set_track1($value)
		{
			$this->track1 = urlencode($value);
		}

		function get_track1()
		{
			return $this->track1;
		}

		function set_track2($value)
		{
			$this->track2 = urlencode($value);
		}

		function get_track2()
		{
			return $this->track2;
		}

		/************************************************
		Helper functions
		************************************************/
		function get_version()
		{
			return 'ECHOPHP 1.4.4 10/23/2001';
		}

		function getRandomCounter()
		{
			mt_srand ((double) microtime() * 1000000);
			return mt_rand();
		}

		function get_EchoResponse()
		{
			return $this->EchoResponse;
		}

		function get_echotype1()
		{
			return $this->echotype1;
		}

		function get_echotype2()
		{
			return $this->echotype2;
		}

		function get_echotype3()
		{
			return $this->echotype3;
		}

		function get_openecho()
		{
			return $this->openecho;
		}

		function set_EchoServer($value)
		{
			$this->EchoServer = $value;
		}

		function get_avs_result()
		{
			return $this->avs_result;
		}

		function get_reference()
		{
			return $this->reference;
		}

		function get_EchoSuccess()
		{
			return $this->EchoSuccess;
		}

		function get_status()
		{
			return $this->status;
		}

		function GetEchoProp($Haystack, $Prop)
		{
			// prepend garbage in case the property
			// starts at position 0
			$Haystack = 'garbage' . $Haystack;

			if ($StartPos = strpos($Haystack, "<$Prop>"))
			{
				$StartPos = strpos($Haystack, "<$Prop>") + strlen("<$Prop>");
				$EndPos = strpos($Haystack, "</$Prop");
				return substr($Haystack, $StartPos, $EndPos - $StartPos);
			}
			else
			{
				return '';
			}
		}

		function GetAuthFromEcho()
		{
			if ($startpos = strpos($this->echotype3, '<auth_code>'))
			{
				$startpos = strpos($this->echotype3, '<auth_code>') + 11;
				$endpos = strpos($this->echotype3, '</auth_code>');
				$this->authorization = substr($this->echotype3, $startpos, $endpos - $startpos);
			}
		}

		function GetOrderNumberFromEcho()
		{
			if ($startpos = strpos($this->echotype3, '<order_number>'))
			{
				$startpos = strpos($this->echotype3, '<order_number>') + 14;
				$endpos = strpos($this->echotype3, '</order_number>');
				$this->order_number = substr($this->echotype3, $startpos, $endpos - $startpos);
			}
		}

		function GetReferenceFromEcho()
		{
			if ($startpos = strpos($this->echotype3, '<echo_reference>'))
			{
				$startpos = strpos($this->echotype3, '<echo_reference>') + 16;
				$endpos = strpos($this->echotype3, '</echo_reference>');
				$this->reference = substr($this->echotype3, $startpos, $endpos - $startpos);
			}
		}
	} // end of class
?>
