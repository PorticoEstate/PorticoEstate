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
		'noheader' => True,
		'noappheader' => True
	);

	include('../header.inc.php');
?>

<script language="JavaScript">
	<!--
	function quickjump(dropdown) {
		var i = dropdown.selectedIndex
		location = dropdown.options[i].value
	}
	//-->
	</script>
<?php

// method test params
$method_params["echoString"]["inputString"] = "test string";
$method_params["echoStringArray"]["inputStringArray"] = array("good","bad");
$method_params["echoInteger"]["inputInteger"] = 34345;
//$method_params["echoIntegerArray"]["inputIntegerArray"] = array(-1,342,325325);
$method_params["echoIntegerArray"]["inputIntegerArray"] = array(1,234324324,2);
$method_params["echoFloat"]["inputFloat"] = 342.23;
//$method_params["echoFloatArray"]["inputFloatArray"] = array(1.3223,34.2,325.325);
	$method_params["echoFloatArray"]["inputFloatArray"] = array(
		CreateObject('phpgwapi.soapval',
			"nan",
			"float",
			32432.234
		),
		CreateObject('phpgwapi.soapval',
			"inf",
			"float",
			-23423.23
		),
		CreateObject('phpgwapi.soapval',
			"neginf",
			"float",
			-INF
		)
	);
	$method_params["echoStruct"]["inputStruct"] = CreateObject('phpgwapi.soapval',
		"inputStruct",
		"SOAPStruct",array(
			"varString"=>"arg",
			"varInt"=>34,
			"varFloat"=>325.325
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

$servers["SOAPx4  - interop test suite (dev)"] = array(
	"soapaction" => "urn:soapinterop",
	"endpoint" => "http://dietrich.ganx4.com/soapx4/testbed/test_server.php",
	"methodNamespace" => "http://soapinterop.org",
	"soapactionNeedsMethod" => 0,
	"name" => "SOAPx4  - interop test suite (dev)");

// get list of all endpoints from xmethods
	$soapclient = CreateObject('phpgwapi.soap_client',"http://www.xmethods.net/perl/soaplite.cgi");
	if($endpointArray = $soapclient->call("getAllEndpoints",array(),"http://soapinterop.org/ilab","http://soapinterop.org/ilab#getAllEndpoints"))
	{
		foreach($endpointArray as $k => $v)
		{
			$servers[str_replace("+","",$v["name"])] = $v;
		}
	}
	else
	{
		print "ERROR: couldn't get remote server list.<br>DEBUG:<br>".$soapclient->debug_str;
	}
?>

<form action='test_methods_wsdl.php' method='post'>
<select name='nserver'>
<option>Choose Server...
	<?php
	print isset($nserver) ? "<option value='$nserver'>$nserver" : "";
	foreach($servers as $k => $v){
		if($v["wsdl"] != ""){
			print "<option value='$k'>$k\n";
		}
	}
	?>
</select>
<select name='method' onChange='submit(this.form)'>
	<option>method:
	<?php
	foreach(array_keys($method_params) as $func){
		print "<option value='$func'>$func\n ";
	}
	?>
</select>
</form>

<?php

if($method && $nserver){
	$server = $servers[$nserver];
	// print server info
	foreach($server as $k => $v){
		print "<strong>$k:</strong> $v<br>";
	}
	print "<br>";
	print "<b>METHOD: $method</b><br>";
	$soapclient = CreateObject('phpgwapi.soap_client',$server["wsdl"],"wsdl");
	//$soapclient->debug_flag = true;
	$return_val = $soapclient->call($method,$method_params[$method],$server["methodNamespace"]);
	
	// print results
	$sent_val = array_shift($method_params[$method]);
	print "sent: ".$sent_val."<br>";
	if(is_array($sent_val)){
		foreach($sent_val as $k => $v){
			print "$k = $v, ";
		}
		print "<br>";
	}
	print "recieved: ".$return_val."<br>";
	if(is_array($return_val)){
		foreach($return_val as $k => $v){
			print "$k = $v, ";
		}
		print "<br>";
	}
	print "<br>";
	
	print "<strong>Request:</strong><br><xmp>$soapclient->request</xmp><br>";
	print "<strong>Response:</strong><br><xmp>$soapclient->response</xmp>";
	unset($soapclient);
}
?>
