<html>
<head><title>zope test</title></head>
<body>
<?php
include("xmlrpc.inc");

  $f=new xmlrpcmsg('document_src', array());
  print "<PRE>\n" . htmlspecialchars($f->serialize()) . "</PRE>";
  $c=new xmlrpc_client("/index_html", "pingu.heddley.com", 9080);
  $c->setCredentials("username", "password");
  $c->setDebug(1);
  $r=$c->send($f);
  if (!$r) { die("send failed"); }
  $v=$r->value();
  if (!$r->faultCode()) {
	print "I received:" .  $v->scalarval() . "<BR>";
	print "<HR>I got this value back<BR><PRE>" .
		htmlentities($r->serialize()). "</PRE><HR>\n";
  } else {
	print "Fault: ";
	print "Code: " . $r->faultCode() . 
	  " Reason '" .$r->faultString()."'<BR>";
  }


?>
</body>
</html>
