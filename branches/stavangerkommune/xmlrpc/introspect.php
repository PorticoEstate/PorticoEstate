<?php
  /**************************************************************************\
  * phpGroupWare - Interserver XML-RPC/SOAP Test app                         *
  * http://www.phpgroupware.org                                              *
  * This file written by Miles Lott <milosch@phpgroupware.org                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'xmlrpc'
	);
	include('../header.inc.php');

	function rpc_call($client, $msg)
	{
		$r=$client->send($msg);
		if (!$r)
		{
			print "<PRE>ERROR: couldn't send message</PRE>\n";
			return 0;
		}
		else
		{
			if (!$r->faultCode())
			{
				return $r->value();
			}
			else
			{
				print "Fault: ";
				print "Code: " . $r->faultCode() . 
				" Reason '" .$r->faultString()."'<BR>";
				return 0;
			}
		}
	}

	$f = CreateObject('phpgwapi.xmlrpcmsg','system.listMethods');
	$c = CreateObject('phpgwapi.xmlrpc_client','/phpgroupware/xmlrpc.php', $HTTP_HOST, 80);
	$c->setDebug(0);

	$v = rpc_call($c, $f);
	print '<h2>methods available at http://' . $c->server . ':' . $c->port . $c->path . "</h2>\n";
	if ($v)
	{
		for($i=0; $i<$v->arraysize(); $i++)
		{
			$mname=$v->arraymem($i);
			print '<H3>' . $mname->scalarval() . "</H3>\n";
			$f= CreateObject('phpgwapi.xmlrpcmsg','system.methodHelp');
			$f->addParam(CreateObject('phpgwapi.xmlrpcval',$mname->scalarval(), 'string'));
			$w=rpc_call($c, $f);
			if ($w)
			{
				$txt=$w->scalarval();
				if ($txt!='')
				{
					print "<H4>Documentation</H4><P>${txt}</P>\n";
				}
				else
				{
					print "<P>No documentation available.</P>\n";
				}
			}
			$f= CreateObject('phpgwapi.xmlrpcmsg','system.methodSignature');
			$f->addParam(CreateObject('phpgwapi.xmlrpcval',$mname->scalarval(), 'string'));
			$w=rpc_call($c, $f);
			if ($w)
			{
				print "<H4>Signature</H4><P>\n";
				if ($w->kindOf()=="array")
				{
					for($j=0; $j<$w->arraysize(); $j++)
					{
						$x=$w->arraymem($j);
						$ret=$x->arraymem(0);
						print '<CODE>' . $ret->scalarval() . ' ' . $mname->scalarval() .'(';
						if ($x->arraysize()>1)
						{
							for($k=1; $k<$x->arraysize(); $k++)
							{
								$y = $x->arraymem($k);
								print $y->scalarval();
								if ($k<$x->arraysize()-1)
								{
									print ', ';
								}
							}
						}
						print ")</CODE><BR>\n";
					}
				}
				else
				{
					print "Signature unknown\n";
				}
				print "</P>\n";
			}
		}
	}

	$GLOBALS['phpgw']->common->phpgw_footer();
