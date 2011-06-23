<html>
<head><title>xmlrpc</title></head>
<body>
<?php

$inAr=array("Dave" => 24, "Edd" => 45, "Joe" => 37, "Fred" => 27);

include("xmlrpc.inc");
reset($inAr);
print "<BR>This was the input data<BR><PRE>";
while (list($key, $val)=each($inAr)) {
print $key . ", " . $val . "\n";
}
// create parameters from the input array
$p=array();
reset($inAr);
while (list($key, $val)=each($inAr)) {
  $p[]=new xmlrpcval(array("name" => new xmlrpcval($key), 
						   "age" => new xmlrpcval($val, "int")), "struct");
}
$v=new xmlrpcval($p, "array");
// print "Output values look like this: <PRE>\n" .  htmlentities($v->serialize()). "</PRE>\n";
$f=new xmlrpcmsg('examples.sortByAge',  array($v));

$c=new xmlrpc_client("/server.php", "xmlrpc.heddley.com", 80);
$c->setDebug(1);
print "\nAnd I sent\n\n";
print htmlspecialchars($f->serialize());
print "</PRE>";
print "Sending request...<BR>\n";
$r=$c->send($f);
if (!$r) { die("send failed"); }
$v=$r->value();
if (!$r->faultCode()) {
  print "The server gave me these results:<PRE>";
  $max=$v->arraysize(); 
  for($i=0; $i<$max; $i++) {
	$rec=$v->arraymem($i);
	$n=$rec->structmem("name");
	$a=$rec->structmem("age");
	print $n->scalarval() . ", " . $a->scalarval() . "\n";
  }

  print "<PRE><HR>For nerds: I got this value back<BR><PRE>" .
	htmlentities($r->serialize()). "</PRE><HR>\n";
} else {
  print "Fault: ";
  print "Code: " . $r->faultCode() . 
	" Reason '" .$r->faultString()."'<BR>";
}

?>
</body>
</html>
