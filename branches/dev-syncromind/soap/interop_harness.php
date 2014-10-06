<?php
/**************************************************************************\
* phpGroupWare - addressbook                                               *
* http://www.phpgroupware.org                                              *
* Written by Joseph Engo <jengo@phpgroupware.org>                          *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'disable_Template_class' => True,
		'currentapp' => 'soap'
	);
	include('../header.inc.php');
?>
<!-- function for quickjump -->
<script language="JavaScript">
<!--
function quickjump(dropdown) {
	var i = dropdown.selectedIndex
	location = dropdown.options[i].value
}
//-->
</script>
<?php
	// get list of all endpoints from xmethods
	$soapclient = CreateObject('phpgwapi.phpgw_soapclient','http://www.xmethods.net/perl/soaplite.cgi');
	$endpointArray = $soapclient->call('getAllEndpoints',array(),'http://soapinterop.org/ilab','http://soapinterop.org/ilab#getAllEndpoints');

	@reset($endpointArray);
	while(list($k,$v) = @each($endpointArray))
	{
		$servers[str_replace("+","",$v["name"])] = $v;
	}

	// method test params
	$method_params['echoString']['inputString'] = 'test string';
	$method_params['echoStringArray']['inputStringArray'] = array('good','bad');
	$method_params['echoInteger']['inputInteger'] = 34345;
	//$method_params["echoIntegerArray"]["inputIntegerArray"] = array(-1,342,325325);
	$method_params['echoIntegerArray']['inputIntegerArray'] = array(1,234324324,2);
	$method_params['echoFloat']['inputFloat'] = 342.23;
	//$method_params["echoFloatArray"]["inputFloatArray"] = array(1.3223,34.2,325.325);
	$method_params['echoFloatArray']['inputFloatArray'] = array(
		CreateObject('phpgwapi.soapval',
			'nan',
			'float',
			32432.234
		),
		CreateObject('phpgwapi.soapval',
			'inf',
			'float',
			-23423.23
		),
		CreateObject('phpgwapi.soapval',
			'neginf',
			'float',
			-INF
		)
	);
	$method_params['echoStruct']['inputStruct'] = CreateObject('phpgwapi.soapval',
		'inputStruct',
		'SOAPStruct',
		array(
			'varString'=>'arg',
			'varInt'=>34,
			'varFloat'=>325.325
		)
	);
	$method_params["echoStructArray"]["inputStructArray"] = array(
		CreateObject('phpgwapi.soapval',
			"item",
			"SOAPStruct",
			array(
				"varString"=>"arg",
				"varInt"=>34,
				"varFloat"=>325.325
			)
		),
		CreateObject('phpgwapi.soapval',
			"item",
			"SOAPStruct",
			array(
				"varString"=>"arg",
				"varInt"=>34,
				"varFloat"=>325.325
			)
		),
		CreateObject('phpgwapi.soapval',
			"item",
			"SOAPStruct",
			array(
				"varString"=>"arg",
				"varInt"=>34,
				"varFloat"=>325.325
			)
		)
	);
	$method_params["echoVoid"] = "";
	$method_params["echoBase64"]["inputBase64"] = CreateObject('phpgwapi.soapval',
		"inputBase64",
		"base64",
		"TmVicmFza2E="
	);
	$method_params["echoDate"]["inputDate"] = CreateObject('phpgwapi.soapval',
		"inputDate",
		"timeInstant",
		"2001-04-25T09:31:41-07:00"
	);

	$servers['phpGroupWare'] = array(
		'soapaction' => 'urn:soapinterop',
		'endpoint'   => 'http://www.phpgroupware.org/cvsdemo/soap.php',
		'methodNamespace' => 'http://soapinterop.org',
		'soapactionNeedsMethod' => 0,
		'name'       => 'SOAPx4  - interop test suite (dev)'
	);
?>

<form>
<select onChange='quickjump(this)'>
<option>Choose Server...
	<?php
	print isset($nserver) ? '<option value="' . $GLOBALS['phpgw']->link('/soap/interop_harness.php','nserver=' . $nserver) . '">' . $nserver : "";
	@reset($servers);
	while(list($k,$v) = @each($servers))
	{
		echo '<option value="' . $GLOBALS['phpgw']->link('/soap/interop_harness.php','nserver=' . $k) . '">' . $k . "\n";
	}
	?>
</select>
</form>

<?php
	if($nserver)
	{
		$server = $servers[$nserver];
		// print server info
		@reset($server);
		while(list($k,$v) = @each($server))
		{
			print "<strong>$k:</strong> $v<br>";
		}
		print "<br><br>";
		// loop thru test suite
		$method_keys = array_keys($method_params);
		while(list(,$method) = @each($method_keys))
		{
			// create soap message
			if(!$soapmsg = CreateObject('phpgwapi.soapmsg',
				$method,
				$method_params[$method],
				$server["methodNamespace"]
			))
			{
				die("couldn't create soap message for method: $method!");
			}
			else
			{
				// invoke the client
				$client = CreateObject('phpgwapi.soap_client',$server["endpoint"]);
				//$client->debug_flag = true;
				// methodname required?
				if($server["soapactionNeedsMethod"] == 1)
				{
					$soapaction = $server["soapaction"].$method;
				}
				else
				{
					$soapaction = $server["soapaction"];
				}
				// send message and get response
				if($return = $client->send($soapmsg,$soapaction))
				{
					// check for valid response
					if(get_class($return) == "soapval")
					{
						// fault?
						if(eregi("fault",$return->name))
						{
							$status = "failed - got fault";
						}
						else
						{
							$status = "passed";
						}
					}
					else
					{
						$status = "failed - return was not a soapval object";
					}
				}
				else
				{
					$status = "failed - send/receive failed somewhere, go test in test_methods.php to see dumps";
				}
				// print results
				print "<b>METHOD: $method... ";
				print "<font color='";
				print ($status == "passed") ? "green" : "red";
				print "'>$status</font>";

				// log the transaction, and print link to dumps
				if($harness)
				{
					if($status == "failed")
					{
						$client->incoming_payload .= "\n\n<!-- SOAPx4 CLIENT DEBUG\n$client->debug_str\n\nRETURN VAL DEBUG: $return->debug_str-->";
					}
					$id = harness($nserver,$method,$client->outgoing_payload,$client->incoming_payload,$status);
					print "&nbsp;&nbsp;<a href='../client_dumps.php?id=$id'>view dumps...</a>";
				}
				print "<br>";
			}
		}
	}

	$GLOBALS['phpgw']->common->phpgw_footer();
	unset($soapmsg);
	unset($client);
?>
