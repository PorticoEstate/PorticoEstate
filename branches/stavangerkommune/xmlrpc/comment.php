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
		'noheader'    => True,
		'noappheader' => True,
		'nonavbar'    => True 
	);

	include('../header.inc.php');

	$mydir = '/phpgroupware/xmlrpc';

	// define some utility functions
	function bomb()
	{
		$GLOBALS['phpgw']->common->phpgw_footer();
	}

	function dispatch($client, $method, $args)
	{
		$msg  = CreateObject('phpgwapi.xmlrpcmsg',$method, $args);
		$client->debug = True;
		$resp = $client->send($msg);
		if (!$resp)
		{
			print "<p>IO error: ".$client->errstring."</p>"; bomb();
		}
//		_debug_array($msg);
		if ($resp->faultCode())
		{
			print "<p>There was an error: " . $resp->faultCode() . " " .
				$resp->faultString() . "</p>";
			bomb();
		} 
		return xmlrpc_decode($resp->value());	
	}

	// create client for discussion server
	$dclient = CreateObject('phpgwapi.xmlrpc_client',"${mydir}/discuss.php",$HTTP_HOST, 80);

	// check if we're posting a comment, and send it if so
//	$storyid = $HTTP_POST_VARS['storyid'];

	if ($storyid)
	{
		//	print "Returning to " . $HTTP_POST_VARS["returnto"];

		$res = dispatch($dclient, 'discuss.addComment', array(
				CreateObject('phpgwapi.xmlrpcval',$storyid),
				CreateObject('phpgwapi.xmlrpcval',stripslashes($name)),
				CreateObject('phpgwapi.xmlrpcval',stripslashes($commenttext))
		));

		// send the browser back to the originating page
		Header('Location: ' . $GLOBALS['phpgw']->link($mydir . '/comment.php',
			  'catid='   . $catid
			. '&chanid=' . $chanid
			. '&oc='     . $catid
		));
	}

	// now we've got here, we're exploring the story store
	$GLOBALS['phpgw']->common->phpgw_header();
	echo parse_navbar();
?>
<h2>Meerkat integration</h2>
<?php
	if ($oc==$catid)
	{
		$chanid = $chanid;
	}
	else
	{
		$chanid=0;
	}

	$client = CreateObject('phpgwapi.xmlrpc_client',"/meerkat/xml-rpc/server.php","www.oreillynet.com", 80);

	if ($comment && !$cdone)
	{
		// we're making a comment on a story,
		// so display a comment form
?>
<h3>Make a comment on the story</h3>
<form method="post" action="<?php echo $GLOBALS['phpgw']->link('/xmlrpc/comment.php') ?>">
<p>Your name:<br /><input type="text" size="30" name="name" /></p>
<p>Your comment:<br /><textarea rows="5" cols="60"
   name="commenttext"></textarea></p>
<input type="submit" value="Send comment" />
<input type="hidden" name="storyid" 
   value="<?php echo $comment;?>" />
<input type="hidden" name="chanid" 
   value="<?php echo $chanid; ?>" />
<input type="hidden" name="catid" 
   value="<?php echo $catid; ?>" />

</form>
<?php
	}
	else
	{
		$categories = dispatch($client, 'meerkat.getCategories', array());
		if ($catid)
		{
			$sources = dispatch($client, 'meerkat.getChannelsByCategory', array(CreateObject('phpgwapi.xmlrpcval',$catid, 'int')));
		}
		if ($chanid)
		{
			$stories = dispatch($client, 'meerkat.getItems', array(
				CreateObject('phpgwapi.xmlrpcval',array(
					'channel'      => CreateObject('phpgwapi.xmlrpcval',$chanid, 'int'),
					'ids'          => CreateObject('phpgwapi.xmlrpcval',1, 'int'),
					'descriptions' => CreateObject('phpgwapi.xmlrpcval',200, 'int'),
					'num_items'    => CreateObject('phpgwapi.xmlrpcval',5, 'int'),
					'dates'        => CreateObject('phpgwapi.xmlrpcval',0, 'int')
					),
					'struct'
				)
			));
		}
?>
<form method="post" action="<?php echo $GLOBALS['phpgw']->link('/xmlrpc/comment.php') ?>">
<p>Subject area:<br />
<select name="catid">
<?php
		if (!$catid)
		{
			print "<option value=\"0\">Choose a category</option>\n";
		}
		while(list($k,$v) = each($categories))
		{
			print "<option value=\"" . $v['id'] ."\"";
			if ($v['id']==$catid)
			{
				print ' selected';
			}
			print ">". $v['title'] . "</option>\n"; 
		}
?>
</select></p>
<?php 
		if ($catid)
		{
?>
<p>News source:<br />
<select name="chanid">
<?php
			if (!$chanid)
			{
				print "<option value=\"0\">Choose a source</option>\n";
			}
			while(list($k,$v) = each($sources))
			{
				print "<option value=\"" . $v['id'] ."\"";
				if ($v['id']==$chanid)
				{
					print "\" selected=\"selected\"";
				}
				print ">". $v['title'] . "</option>\n"; 
			}
?>
</select>
</p>

<?php 
		} // end if ($catid)
?>

<p><input type="submit" value="Update" /></p>
<input type="hidden" name="oc" value="<?php echo $catid; ?>" />
</form>

<?php 
		if ($chanid)
		{
?>

<h2>Stories available</h2>
<table>
<?php
			while(list($k,$v) = each($stories))
			{
				print "<tr>";
				print "<td><b>" . $v['title'] . "</b><br />";
				print $v['description'] . "<br />";
				print "<em><a target=\"_blank\" href=\"" . 
					 $v['link'] . "\">Read full story</a> ";
				print "<a href=\"" . $GLOBALS['phpgw']->link('/xmlrpc/comment.php',"catid=${catid}&chanid=${chanid}&" .
					 "oc=${oc}&comment=" . $v['id']) . "\">Comment on this story</a>";
				print "</em>";
				print "</td>";
				print "</tr>\n";
				// now look for existing comments
				$res = dispatch($dclient, "discuss.getComments", array(CreateObject('phpgwapi.xmlrpcval',$v['id'])));
				if (sizeof($res)>0)
				{
					 print "<tr><td bgcolor=\"#dddddd\"><p><b><i>" . "Comments on this story:</i></b></p>";
					 for($i=0; $i<sizeof($res); $i++)
					 {
						 $s=$res[$i];
						 print "<p><b>From:</b> " . htmlentities($s['name']) . "<br />";
						 print "<b>Comment:</b> " . htmlentities($s['comment']) . "</p>";
					 }
					 print "</td></tr>\n";
				 }
				 print "<tr><td><hr /></td></tr>\n";
			 }
?>
</table>

<?php 
		} // end if ($chanid) 
	} // end if comment
?>
<hr />
<p>
<a href="http://meerkat.oreillynet.com"><img align="right" 
	src="http://meerkat.oreillynet.com/icons/meerkat-powered.jpg"
	height="31" width="88" alt="Meerkat powered, yeah!"
	border="0" hspace="8" /></a>
<em>$Id$</em></p>

<?php
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
