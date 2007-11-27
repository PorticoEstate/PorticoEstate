<?php
  /**************************************************************************\
  * phpGroupWare - phpgw pfp test                                            *
  * http://www.phpgroupware.org                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: pfp_example.php,v 1.2 2002/03/20 13:03:59 milosch Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array('currentapp' => 'cart');

	include('../header.inc.php');

	/*
	I just pulled this page off the site I used the class on the first time.
	All the variables were passed to this script via a form submission (that
	checked all the fields, etc).
	*/

	// In the following call, the last parameter passed to the constructor
	// is to indicate to use the test server (0), or the live server (1)
	$GLOBALS['phpgw_info']['server']['cart_payment_type'] = 'pfp';

	$pfp = CreateObject('cart.payment',$verisign_login,$verisign_password,0);
	$pfp->CustomerDetails($name,$address,$city,$state,$zip,$phone,$email);
	$result = $pfp->UseCreditCard($acct,$price,'0105','S');

	if(!$result)
	{
?>
<table cellpadding="0" border="0" cellspacing="0" width="400" align="center">
  <tr>
    <td>This transaction was not accepted. The server responded with the message:
<br><br><font color="#0000ff"><b><?php echo $pfp->respmesg ?></b></font><br><br>
Please user your browser's back button to try the transaction again.
At that point you can correct any information that you may have
inadvertently entered.
    </td>
  </tr>
</table>
<?php
	}
	else
	{
		echo 'Success';
		// success code goes here
	}

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
