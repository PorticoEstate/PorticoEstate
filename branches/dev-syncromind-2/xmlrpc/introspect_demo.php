<html>
<head><title>xmlrpc</title></head>
<body>
<?php
include("xmlrpc.inc");

function rpc_call($client, $msg) {
	$r=$client->send($msg);
	if (!$r) {
		print "<PRE>ERROR: couldn't send message</PRE>\n";
		return 0;
	} else {
		if (!$r->faultCode()) {
			return $r->value();
		} else {
			print "Fault: ";
			print "Code: " . $r->faultCode() . 
				" Reason '" .$r->faultString()."'<BR>";
			return 0;
		}
	}
}

$f=new xmlrpcmsg('system.listMethods');
$c=new xmlrpc_client("/demo/server.php", "xmlrpc.usefulinc.com", 80);
$c->setDebug(0);

$v=rpc_call($c, $f);
print "<h2>methods available at http://" . $c->server . ":" . 
$c->port . $c->path .  "</h2>\n";
if ($v) {
	
	for($i=0; $i<$v->arraysize(); $i++) {
		$mname=$v->arraymem($i);
		print "<H3>" . $mname->scalarval() . "</H3>\n";
		$f=new xmlrpcmsg('system.methodHelp');
		$f->addParam(new xmlrpcval($mname->scalarval(), "string"));
		$w=rpc_call($c, $f);
		if ($w) {
			$txt=$w->scalarval();
			if ($txt!="") {
				print "<H4>Documentation</H4><P>${txt}</P>\n";
			} else {
				print "<P>No documentation available.</P>\n";
			}
		}
		$f=new xmlrpcmsg('system.methodSignature');
		$f->addParam(new xmlrpcval($mname->scalarval(), "string"));
		$w=rpc_call($c, $f);
		if ($w) {
			print "<H4>Signature</H4><P>\n";
			if ($w->kindOf()=="array") {
				for($j=0; $j<$w->arraysize(); $j++) {
					$x=$w->arraymem($j);
					$ret=$x->arraymem(0);
					print "<CODE>" . $ret->scalarval() . " " . 
						$mname->scalarval() ."(";
					if ($x->arraysize()>1) {
						for($k=1; $k<$x->arraysize(); $k++) {
							$y=$x->arraymem($k);
							print $y->scalarval();
							if ($k<$x->arraysize()-1) {
								print ", ";
							}
						}
					}
					print ")</CODE><BR>\n";
				}
			} else {
				print "Signature unknown\n";
			}
			print "</P>\n";
		}
	}

}


?>
</body>
</html>
