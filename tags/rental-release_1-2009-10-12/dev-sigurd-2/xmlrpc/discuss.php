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

	exit;
	$GLOBALS['phpgw_info'] = array();
	/*
	$login  = 'anonymous';
	$passwd = 'anonymous1';

	$GLOBALS['phpgw_info']['flags'] = array(
		'disable_template_class' => True,
		'login' => True,
		'currentapp' => 'login',
		'noheader'  => True
	);
	*/
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'login',
		'noheader'   => True
	);
	include('../header.inc.php');
	/* $sessionid = $GLOBALS['phpgw']->session->create($login,$passwd); */

	$addcomment_sig = array(array($xmlrpcInt, $xmlrpcString, $xmlrpcString, $xmlrpcString));
	$addcomment_doc = 'Adds a comment to an item. The first parameter
is the item ID, the second the name of the commenter, and the third
is the comment itself. Returns the number of comments against that
ID.';

	function addcomment($m)
	{
		$err = '';
		// get the first param
		$msgID   = xmlrpc_decode($m->getParam(0));
		$name    = xmlrpc_decode($m->getParam(1));
		$comment = xmlrpc_decode($m->getParam(2));
	
		$countID = "${msgID}_count";
		$sql = 'SELECT COUNT(msg_id) FROM phpgw_discuss';
		$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->next_record();
		$count = $GLOBALS['phpgw']->db->f(0);
		
		if(!$count)
		{
			$count = 0;
		}
		// add the new comment in
		$count++;
		$sql = "INSERT INTO phpgw_discuss (msg_id,comment,name,count) VALUES ($msgID,'$comment','$name',$count)";
		$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);

		// if we generated an error, create an error return response
		if ($err)
		{
			return CreateObject('phpgwapi.xmlrpcresp',0, $GLOBALS['xmlrpcerruser'], $err);
		}
		else
		{
			// otherwise, we create the right response
			// with the state name
			return CreateObject('phpgwapi.xmlrpcresp', CreateObject('phpgwapi.xmlrpcval',$count, "int"));
		}
	}

	$getcomments_sig = array(array($xmlrpcArray, $xmlrpcString));
	$getcomments_doc = 'Returns an array of comments for a given ID, which
is the sole argument. Each array item is a struct containing name
and comment text.';

	function getcomments($m)
	{
		$err = '';
		$ra = array();
		// get the first param
		$msgID = xmlrpc_decode($m->getParam(0));

		$countID = "${msgID}_count";
		$sql = 'SELECT * FROM phpgw_discuss WHERE msg_id=' . $msgID;
		$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
		$count = $GLOBALS['phpgw']->db->num_rows();
		while($data = $GLOBALS['phpgw']->db->next_record())
		{
			$name    = $GLOBALS['phpgw']->db->f('name');
			$comment = $GLOBALS['phpgw']->db->f('comment');
			// push a new struct onto the return array
			$ra[] = CreateObject('phpgwapi.xmlrpcval',array(
				'name'    => CreateObject('phpgwapi.xmlrpcval',$name),
				'comment' => CreateObject('phpgwapi.xmlrpcval',$comment)
				),
				'struct'
			);
		}
		// if we generated an error, create an error return response
		if ($err)
		{
			return CreateObject('phpgwapi.xmlrpcresp','', $GLOBALS['xmlrpcerruser'], $err);
		}
		else
		{
			// otherwise, we create the right response
			// with the state name
			return CreateObject('phpgwapi.xmlrpcresp',CreateObject('phpgwapi.xmlrpcval',$ra, 'array'));
		}
	}

	$s = CreateObject('phpgwapi.xmlrpc_server', array(
		'discuss.addComment' => array(
			'function'  => 'addcomment',
			'signature' => $addcomment_sig,
			'docstring' => $addcomment_doc
		),
		'discuss.getComments' => array(
			'function'  => 'getcomments',
			'signature' => $getcomments_sig,
			'docstring' => $getcomments_doc
		)
	));
?>
