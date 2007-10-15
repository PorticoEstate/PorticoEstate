<html>
<head><title>xmlrpc</title></head>
<body>
<?php
include("xmlrpc.inc");

if ($HTTP_POST_VARS["server"]) {
	if ($server=="Userland") {
		$XP="/RPC2"; $XS="206.204.24.2";
	} else {
		$XP="/xmlrpc/server.php"; $XS="pingu.heddley.com";
	}
	$f=new xmlrpcmsg('mail.send');
	$f->addParam(new xmlrpcval($mailto));
	$f->addParam(new xmlrpcval($mailsub));
	$f->addParam(new xmlrpcval($mailmsg));
	$f->addParam(new xmlrpcval($mailfrom));
	$f->addParam(new xmlrpcval($mailcc));
	$f->addParam(new xmlrpcval($mailbcc));
	$f->addParam(new xmlrpcval("text/plain"));
	
	$c=new xmlrpc_client($XP, $XS, 80);
	$c->setDebug(1);
	$r=$c->send($f);
	if (!$r) { die("send to  ${XS}${XP} port 80 failed: network OK?"); }
	$v=$r->value();
	if (!$r->faultCode()) {
		print "Mail sent OK<BR>\n";
	} else {
		print "<FONT COLOR=\"red\">";
		print "Mail send failed<BR>\n";
		print "Fault: ";
		print "Code: " . $r->faultCode() . 
	  " Reason '" .$r->faultString()."'<BR>";
		print "</FONT>";
	}
}
?>
<h2>Mail demo</h2>
<P>This form enables you to send mail via an XML-RPC server. For public use
only the "Userland" server will work (see <a href="http://www.xmlrpc.com/discuss/msgReader$598">Dave Winer's message</a>). When you press send this page will reload showing you the XML-RPC message received from the host server, and the internal evaluation done by the PHP implementation.</P>
<P>You can find the source to this page here: <A href="mail.phps">mail.php</A><BR>
And the source to the UsefulInc mail-by-XML-RPC server (look for the 'mail_send' method): <a href="server.phps">server.php</A></P>
<FORM METHOD="POST">
Server <SELECT NAME="server"><OPTION VALUE="Userland">Userland
<OPTION VALUE="UsefulInc">UsefulInc private server</SELECT><BR>
Subject <INPUT SIZE=60 NAME="mailsub" VALUE="A message from xmlrpc"><BR>
To <INPUT  SIZE=60 NAME="mailto"><BR>
Cc <INPUT SIZE=60 NAME="mailcc"><BR>
Bcc <INPUT SIZE=60 NAME="mailbcc"><BR>
<HR>
From <INPUT SIZE=60 NAME="mailfrom" VALUE=""><BR>
<HR>
Body <TEXTAREA ROWS=7 COLS=60 NAME="mailmsg">Your message here</TEXTAREA><BR>
<INPUT TYPE="Submit" VALUE="Send">
</FORM>
</body>
</html>
