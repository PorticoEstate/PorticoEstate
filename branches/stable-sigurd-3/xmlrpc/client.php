<?php
/**************************************************************************\
* phpGroupWare - XML-RPC Test App                                          *
* http://www.phpgroupware.org                                              *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id$ */

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'  => 'xmlrpc',
		'noheader'    => False,
		'noappheader' => False,
		'nonavbar'    => False
	);

	include('../header.inc.php');

	phpgw::import_class('phpgwapi.xmlrpc_client');
	if ($_POST['stateno'] != '')
	{
		$username = 'anonymous';
		$password = 'anonymous1';
		$phpgw_domain = 'default';

		$stateno = phpgw::get_var('stateno', 'int', 'POST', 0);
		$c = new xmlrpc_client("{$GLOBALS['phpgw_info']['server']['webserver_url']}/xmlrpc.php?domain={$phpgw_domain}", $_SERVER['HTTP_HOST'], 80);
		$c->setCredentials($username, $password);	 
		$f=new xmlrpcmsg('xmlrpc.examples.findstate',array(php_xmlrpc_encode($stateno))	);

//		print "<pre>" . htmlentities($f->serialize('UTF-8')) . "</pre>\n";
//		$c->setDebug(1);
		$r=&$c->send($f);

//		$cookies = $r->cookies();

		if(!$r->faultCode())
		{
			$v=$r->value();
			print "</pre><br/>State number " . $stateno . " is "
				. htmlspecialchars($v->scalarval()) . "<br/>";
			// print "<HR>I got this value back<BR><PRE>" .
			//  htmlentities($r->serialize()). "</PRE><HR>\n";
		}
		else
		{
			print "An error occurred: ";
			print "Code: " . htmlspecialchars($r->faultCode())
				. " Reason: '" . htmlspecialchars($r->faultString()) . "'</pre><br/>";
		}
	}
	else
	{
		$stateno = "";
	}

	echo '
<form action="' . $GLOBALS['phpgw']->link('/xmlrpc/client.php') . '" method="post">
<input name="stateno" VALUE="' . $stateno . '">
<input type="submit" value="go" name="submit">
</form>
<p>
enter a US state number to query its name';

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
