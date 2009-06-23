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

	$phpgw_info['flags'] = array(
		'currentapp' => 'soap',
		'noheader' => True
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
	$soapclient = CreateObject('phpgwapi.phpgw_soapclient',array("http://www.xmethods.net/perl/soaplite.cgi"));
	$endpointArray = $soapclient->call("getAllEndpoints",array(),"http://soapinterop.org/ilab","http://soapinterop.org/ilab#getAllEndpoints");
	foreach($endpointArray as $k => $v)
	{
		$servers[str_replace("+","",$v["name"])] = $v;
	}

	// method test params
	$method_params["echoString"]["inputString"] = "test string";
	$method_params["echoStringArray"]["inputStringArray"] = array("good","bad");
	$method_params["echoInteger"]["inputInteger"] = 34345;
	//$method_params["echoIntegerArray"]["inputIntegerArray"] = array(-1,342,325325);
	$method_params["echoIntegerArray"]["inputIntegerArray"] = array(1,234324324,2);
	$method_params["echoFloat"]["inputFloat"] = 342.23;
	//$method_params["echoFloatArray"]["inputFloatArray"] = array(1.3223,34.2,325.325);
	$method_params["echoFloatArray"]["inputFloatArray"] = array(
		CreateObject('soap.soapval',array("nan","float",32432.234)),
		CreateObject('soap.soapval',array("inf","float",-23423.23)),
		CreateObject('soap.soapval',array("neginf","float",-INF))
	);

	$method_params["echoStruct"]["inputStruct"] = CreateObject('soap.soapval',
		array(
			"inputStruct",
			"SOAPStruct",
			array(
				"varString"=>"arg",
				"varInt"=>34,
				"varFloat"=>325.325
			)
		)
	);
	$method_params["echoStructArray"]["inputStructArray"] = array(
		CreateObject('soap.soapval',array(
			"item",
			"SOAPStruct",
			array(
				"varString"=>"arg",
				"varInt"=>34,
				"varFloat"=>325.325
			)
		)),
		CreateObject('soap.soapval',array(
			"item",
			"SOAPStruct",
			array(
				"varString"=>"arg",
				"varInt"=>34,
				"varFloat"=>325.325
			)
		)),
		CreateObject('soap.soapval',array(
			"item",
			"SOAPStruct",
			array(
				"varString"=>"arg",
				"varInt"=>34,
				"varFloat"=>325.325
			)
	)));
	$method_params["echoVoid"] = "";
	$method_params["echoBase64"]["inputBase64"] = CreateObject('soap.soapval',array("inputBase64","base64","TmVicmFza2E="));
	$method_params["echoDate"]["inputDate"] = CreateObject('soap.soapval',array("inputDate","timeInstant","2001-04-25T09:31:41-07:00"));
?>

<form>
<select onChange='quickjump(this)'>
<option>Choose Server...
	<?php
	print isset($nserver) ? "<option value='$PHP_SELF?nserver=$nserver'>$nserver" : "";
	foreach($servers as $k => $v){
		if($v["wsdl"] != ""){
			print "<option value='$PHP_SELF?nserver=$k'>$k\n";
		}
	}
	?>
</select>
</form>

<?php
	if($nserver)
	{
		$server = $servers[$nserver];
		// print server info
		foreach($server as $k => $v)
		{
			print "<strong>$k:</strong> $v<br>";
		}
		print "<br><br>";

		// loop thru test suite
		foreach(array_keys($method_params) as $method)
		{
			$soapclient = CreateObject('phpgwapi.phpgw_soapclient',array($server['wsdl'],'wsdl'));
			//$soapclient->debug_flag = true;
			$return_val = $soapclient->call($method,$method_params[$method],$server['methodNamespace']);
			unset($soapclient);

			// print results
			print "<b>METHOD: $method</b><br>";
			// print results
			$sent_val = array_shift($method_params[$method]);
			print 'sent: '.$sent_val.'<br>';
			if(is_array($sent_val))
			{
				foreach($sent_val as $k => $v)
				{
					print "$k = $v, ";
				}
				print '<br>';
			}
			print 'recieved: '.$return_val.'<br>';
			if(is_array($return_val))
			{
				foreach($return_val as $k => $v)
				{
					print "$k = $v, ";
				}
				print '<br>';
			}
		}
	}
?>
