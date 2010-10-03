<html>
<head><title>xmlrpc</title></head>
<body>
<?php
include("xmlrpc.inc");

if ($HTTP_POST_VARS["stateno"]!="") {
  $f=new xmlrpcmsg('examples.getStateName',
				   array(new xmlrpcval($HTTP_POST_VARS["stateno"], "int")));
  $c=new xmlrpc_client("/RPC2", "betty.userland.com", 80);
  $r=$c->send($f);
  $v=$r->value();
  if (!$r->faultCode()) {
	print "State number ". $HTTP_POST_VARS["stateno"] . " is " .
	  $v->scalarval() . "<BR>";
	print "<HR>I got this value back<BR><PRE>" .
	  htmlentities($r->serialize()). "</PRE><HR>\n";
  } else {
	print "Fault: ";
	print "Code: " . $r->faultCode() . 
	  " Reason '" .$r->faultString()."'<BR>";
  }
}
print "<FORM  METHOD=\"POST\">
<INPUT NAME=\"stateno\" VALUE=\"${stateno}\"><input type=\"submit\" value=\"go\" name=\"submit\"></FORM><P>
enter a state number to query its name";
?>
</body>
</html>
