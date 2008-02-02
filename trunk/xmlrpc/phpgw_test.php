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

	if ($HTTP_POST_VARS['method'])
	{
		$f = CreateObject('phpgwapi.xmlrpcmsg',$HTTP_POST_VARS['method'],array(
			CreateObject('phpgwapi.xmlrpcval',$HTTP_POST_VARS['param'], 'string')
		));
		print "<pre>" . htmlentities($f->serialize()) . "</pre>\n";
		$c = CreateObject('phpgwapi.xmlrpc_client',"/phpgroupware/xmlrpc.php", $HTTP_SERVER_VARS['HTTP_HOST'], 80);
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

	if (!$method)
	{
		$method = 'system.listMethods';
	}
	echo '
<form action="' . $GLOBALS['phpgw']->link('/xmlrpc/phpgw_test.php') . '" method="post">
<input name="method" VALUE="' . $method . '">
<input name="param" VALUE="' . $param . '">
<input type="submit" value="go" name="submit">
</form>
<p>
Enter a method to execute and one parameter';

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
