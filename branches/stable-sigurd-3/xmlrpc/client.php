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

	if ($HTTP_POST_VARS['stateno'] != '')
	{
		$c = CreateObject('phpgwapi.xmlrpc_client',"{$GLOBALS['phpgw_info']['server']['webserver_url']}/xmlrpc.php", $HTTP_SERVER_VARS['HTTP_HOST'], 80);
		$f = CreateObject('phpgwapi.xmlrpcmsg','examples.getStateName',array(CreateObject('phpgwapi.xmlrpcval',$HTTP_POST_VARS['stateno'], 'int')));
		print "<pre>" . htmlentities($f->serialize('UTF-8')) . "</pre>\n";

		$c->setDebug(1);
		$r = $c->send($f);
		if (!$r)
		{
			die('send failed');
		}
		$v = $r->value();
		if (!$r->faultCode())
		{
			print 'State number ' . $HTTP_POST_VARS['stateno'] . ' is ' . $v->scalarval() . '<br>';
			// print "<HR>I got this value back<BR><PRE>" .
			//  htmlentities($r->serialize()). "</PRE><HR>\n";
		}
		else
		{
			print 'Fault: ';
			print 'Code: ' . $r->faultCode() . " Reason '" .$r->faultString()."'<br>";
		}
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
