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

  /* $Id: class.payment_pfp.inc.php 9800 2002-03-20 12:49:41Z milosch $ */

/*      Last Modified: 6/19/01 8:55PM by Jusitn Koivisto [Koivi Media]

	This is my implementation of a class for using PayflowPro transactions.
	I programmed this so that I had an easy way of integrating the
	Verisign stuff into some existing sites.

	All information used to base this class on was taken directly out of
	the Payflow Pro Developer?s Guide (Release Date: November 9, 2000).

	Tender types that are supported by this class are:
		Credit Cards (Purchase, Business, Corporate)

	Tender types to have support added soon:
		Electronic Checks
		ACH Checks

	Functions that you will use include:
		pfp_class($user,$pwd,$test=1,$port=443,$time='45',$prox='',$proxp='',$proxl='',$proxw='')
			The class constructor. This is called whenever you need to instantiate a
			class object.

			$user   your Verisign merchant ID
			$pwd    your Verisign password
			$test   set this to 1 if you are only testing
			$port   the port of the processing server
			$time   the timeout for the transaction
			$prox   your proxy server (if needed)
			$proxp  your proxy port
			$proxl  your proxy login
			$proxw  your proxy password

		CustomerDetails($name,$street,$city,$state,$zip,$phone='',$email='',$drvlic='')
			Use this to set information about the purchasing party. The parameters
			should not need more explanation than saying $drvlic is for the
			driver's license number of the customer.

		UseCreditCard($acct,$amt,$expdate,$trxtype,$commcard='P')
			This is the function to call to make a credit card transaction.

			$acct	   Credit card number
			$amt	    Amount of transaction
			$expdate	Expiration date of the card (MMYY)
			$trxtype	Transaction type (See TRXTYPE in Payflow Pro Developer's Guide)
			$commcard       Card type (See COMMCARD in Payflow Pro Developer's Guide)

			sets:
				$respmesg       The response string given from the transaction (error
						messages, feedback, etc.)
				$authcode       The authorization code issued from the bank
				$pnref	  Payment Network Reference ID for transaction
				$avsaddr	String telling of matching state of customer's street
						address from the Address Verification Service
				$avszip	 String telling of matching state of customer's zip code
						address from the Address Verification Service

			returns:
				1 on accepted transaction
				0 on declined transaction
				-1 on communication error
*/

/*
	if(!$PFP_CLASS_INC)
	{
		$PFP_CLASS_INC=True;
	}
	else
	{
		return(0);
	}
*/
	class payment
	{
		var $host;      // the server that will handle the transaction
		var $port;      // the port of the above server to use
		var $phost;     // proxy host
		var $pport;     // proxy port
		var $plog;      // proxy login
		var $ppwd;      // proxy password
		var $user;      // the Verisign account id
		var $pwd;       // the Verisign account passwd
		var $timeout;   // default timeout is 45 secs, less than 30 not recommended
		var $trxtype;   // the type of transaction
		var $tender;    // card, check, etc.
		var $amt;       // amount of transaction
		var $acct;      // credit card number
		var $expdate;   // card expire date
		var $micr;      // magenetic ink check reader (e-checks)
		var $chknum;    // number of e-check
		var $aba;       // ABA routing number (ACH)
		var $accttype;  // account type - 'S'avings or 'C'hecking - (ACH)
		var $name;      // client name
		var $street;    // client address
		var $city;      // client city
		var $state;     // client state
		var $zip;       // client postal code
		var $drvlic;    // client driver license number
		var $email;     // client email address
		var $phone;     // client phone #
		var $pnref;     // Payment Network Reference ID for transaction
		var $authcode;  // issued by bank for accepted trx
		var $comment1;  // reporting and auditing purposes
		var $comment2;  // reporting and auditing purposes
		var $origid;    // returned from transaction - used when referencing previous trx
		var $taxamt;    // amount of tax (commercial cards)
		var $ponum;     // purchase order number (commercial cards)
		var $commcard;  // used to specify type of card (default is purchase card)
		var $respmesg;  // message string returned from transaction attempt
		var $avsaddr;   // AVS response on street address
		var $avszip;    // AVS response on zip code

		// Class constructor
		//      user and pwd refer to the Verisign account
		function cart_pfp($user,$pwd,$test=0,$port=443,$time='45',$prox='',$proxp='',$proxl='',$proxw='')
		{
			if($test)
			{
				$this->host='test.signio.com';
			}
			else
			{
				$this->host='connect.signio.com';
			}
			$this->port    = $port;
			$this->timeout = $time;
			$this->user    = $user;
			$this->pwd     = $pwd;
			$this->phost   = $prox;
			$this->pport   = $proxp;
			$this->plog    = $proxl;
			$this->ppwd    = $proxw;
			$this->amt     = 0;
			$this->tender  = '';
		}

		// Set customer details
		function CustomerDetails($name,$street,$city,$state,$zip,$phone='',$email='',$drvlic='')
		{
			$this->name   = $name;
			$this->street = $street;
			$this->city   = $city;
			$this->state  = $state;
			$this->zip    = $zip;
			$this->drvlic = $drvlic;
			$this->email  = $email;
			$this->phone  = $phone;
		}

		// this trx function is for using credit cards.
		// return values: 1 = accepted, 0 = rejected, -1 no attempt (communication error)
		function UseCreditCard($acct,$amt,$expdate,$trxtype,$commcard='P')
		{
			// commcard is used to commercial cards. Value passed should
			// be one of the following:
			//      P Purchase Card (normal, default)
			//      C Corporate Card
			//      B Business Card

			// the date sent to this function should always be in the
			// format of MMYY
			$now=date('ym');
			$tmp=substr($expdate,2,2).substr($expdate,0,2);
			if($tmp<=$now)
			{
				PFP_ERROR('Expiration date is not in the future.');
				return 0;
			}
			$result=$this->SetTender('C');
			if(!$result)
			{
				PFP_ERROR('Invalid or unsupported tender type');
			}
			unset($result);
			$result=$this->SetTrxType($trxtype);
			if(!$result)
			{
				PFP_ERROR('Invalid or unsupported transfer type');
			}
			unset($result);
			$this->acct=$acct;
			$this->amt=$amt;
			$this->expdate=$expdate;
			$this->commcard=$commcard;

			// create the transaction array
			$parameters = array(
				'USER'     => "$this->user",
				'PWD'      => "$this->pwd",
				'TRXTYPE'  => "$this->trxtype",
				'TENDER'   => "$this->tender",
				'ACCT'     => "$this->acct",
				'EXPDATE'  => "$this->expdate",
				'AMT'      => "$this->amt",
				'COMMENT1' => "$this->comment1",
				'ORIGID'   => "$this->origid",
				'STREET'   => "$this->street",
				'ZIP'      => "$this->zip",
				'COMMCARD' => "$this->commcard"
			);
			if($this->phost && $this->pport && $this->plog & $this->ppwd)
			{
				$result=pfpro_process($parameters,$this->host,$this->port,$this->timeout,$this->phost,$this->pport,$this->plog,$this->ppwd);
			}
			else
			{
				$result=pfpro_process($parameters,$this->host,$this->port,$this->timeout);
			}
			$this->ParseResponse($result);
			if($result['RESULT']==0)
			{
				return 1;
			}
			elseif($result['RESULT']>0)
			{
				return 0;
			}
			else
			{
				return -1;
			}
		}

		// function to find out what the response was from the transaction
		// returns a string based on result of transaction
		function ParseResponse($result)
		{
			@reset($result);
			while(list($key,$val) = @each($result))
			{
				switch ($key)
				{
					case 'PNREF':
						$this->pnref = $val;
						break;
					case 'AUTHCODE':
						$this->authcode = $val;
						break;
					case 'RESPMSG':
						$this->respmesg = $val;
						break;
					case 'AVSADDR':
						switch($val)
						{
							case 'X': $this->avsaddr = 'Service Unavailable'; break;
							case 'N': $this->avsaddr = 'No Match'; break;
							case 'Y': $this->avsaddr = 'Match'; break;
						}
						break;
					case 'AVSZIP':
						switch($val)
						{
							case 'X': $this->avszip = 'Service Unavailable'; break;
							case 'N': $this->avszip = 'No Match'; break;
							case 'Y': $this->avszip = 'Match'; break;
						}
						break;
					default:
						break;
				}
			}
		}

		// this is called by trx functions
		function SetTender($tender)
		{
			if(!ereg('[CKA]',$tender))
			{
				return 0;
			}
			$this->tender = $tender;
			return 1;
		}

		// this is called by trx functions
		function SetTrxType($trxtype)
		{
			if(!ereg('[SCADVF]',$trxtype))
			{
				return 0;
			}
			$this->trxtype = $trxtype;
			return 1;
		}
	}

	function PFP_ERROR($msg)
	{
		echo "<font size=+2 color=#ff0000>$msg</font><br>\n";
	}
?>
