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

  /* $Id: echo_example.php 9801 2002-03-20 13:03:59Z milosch $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader'    => True,
		'noappheader' => True,
		'nofooter'    => True,
		'noappfooter' => True,
		'currentapp'  => 'cart'
	);
	include('../header.inc.php');

	// this was written and tested in PHP4 so I can't
	// vouch for PHP3.. but it should be fine.

	$echoPHP = CreateObject('cart.payment');

	$echoPHP->set_EchoServer('https://wwws.echo-inc.com/scripts/INR300.EXE');
	$echoPHP->set_transaction_type('EV');
	$echoPHP->set_order_type('S');
	$echoPHP->set_merchant_echo_id('123>4681573'); // use your own id here
	$echoPHP->set_merchant_pin('98164734');        // use your onw pin here
	$echoPHP->set_billing_phone('503-123-4567');
	$echoPHP->set_billing_ip_address($REMOTE_ADDR);
	$echoPHP->set_billing_name('Visa');
	$echoPHP->set_billing_address1('18303 Some Street');
	$echoPHP->set_billing_city('Some City');
	$echoPHP->set_billing_state('Some State');
	$echoPHP->set_billing_zip('98223');
	$echoPHP->set_billing_country('USA');
	$echoPHP->set_billing_phone('503-123-4569');
	$echoPHP->set_billing_fax('503-123-4569');
	$echoPHP->set_billing_email('jim@openecho.com');

	// check payment info if supplied...
	/*
	$echoPHP->set_ec_bank_name($ec_bank_name);
	$echoPHP->set_ec_first_name($ec_first_name);
	$echoPHP->set_ec_last_name($ec_last_name);
	$echoPHP->set_ec_address1($ec_address1);
	$echoPHP->set_ec_city($ec_city);
	$echoPHP->set_ec_state($ec_state);
	$echoPHP->set_ec_zip($ec_zip);
	$echoPHP->set_ec_rt($ec_rt);
	$echoPHP->set_ec_account($ec_account);
	$echoPHP->set_ec_serial_number($ec_serial_number);
	$echoPHP->set_ec_payee($ec_payee);
	$echoPHP->set_ec_id_state($ec_id_state);
	$echoPHP->set_ec_id_number($ec_id_number);
	$echoPHP->set_ec_id_type($ec_id_type);
	*/
	$echoPHP->set_debug('F');

	$echoPHP->set_cc_number('4005 5500 0000 0019');
	$echoPHP->set_grand_total(1.00);
	$echoPHP->set_ccexp_month(12);
	$echoPHP->set_ccexp_year(2004);

	$echoPHP->set_counter($echoPHP->getRandomCounter());
	echo $echoPHP->get_version() . '<BR>';
	$echoPHP->Submit();
	echo $echoPHP->EchoResponse;
?>
