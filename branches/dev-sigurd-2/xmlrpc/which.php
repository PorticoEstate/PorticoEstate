<html>
<head><title>xmlrpc</title></head>
<body>
<?php
include("xmlrpc.inc");

  $f=new xmlrpcmsg('interopEchoTests.whichToolkit',
				   array());
  $c=new xmlrpc_client("/server.php", "xmlrpc.heddley.com", 80);
  $c->setDebug(0);
  $r=$c->send($f);
  if (!$r) { die("send failed"); }
  $v=xmlrpc_decode($r->value());
  if (!$r->faultCode()) {
	print "<pre>";
	print "name: " . $v["toolkitName"] . "\n";
	print "version: " . $v["toolkitVersion"] . "\n";
	print "docs: " . $v["toolkitDocsUrl"] . "\n";
	print "os: " . $v["toolkitOperatingSystem"] . "\n";
	print "</pre>";
  } else {
	print "Fault: ";
	print "Code: " . $r->faultCode() . 
	  " Reason '" .$r->faultString()."'<BR>";
  }

?>
</body>
</html>
