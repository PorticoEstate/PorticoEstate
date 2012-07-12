<?php
	/**
	* EMail
	*
	* @author Mark C3ushman <mark@cushman.net>
	* @copyright Copyright (C) xxxx Mark C3ushman
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal Based on Aeromail http://the.cushman.net/
	*/

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'email',
		'enable_network_class' => True, 
		'noheader' => True,
		'nonavbar' => True
	);
	
	/**
	* Include phpgroupware header
	*/
	include('../header.inc.php');
	
	
	/*
	if (isset($GLOBALS['phpgw_info']['flags']['newsmode']) && $GLOBALS['phpgw_info']['flags']['newsmode'])
	{
		$GLOBALS['phpgw']->common->read_preferences('nntp');
	}
	@set_time_limit(0);

	echo 'Mailbox = '.$mailbox.'<br />'."\n";
	echo 'Mailbox = '.$GLOBALS['phpgw']->msg->mailsvr_stream.'<br />'."\n";
	echo 'Msgnum = '.$m.'<br />'."\n";
	echo 'Part Number = '.$p.'<br />'."\n";
	echo 'Subtype = '.$s.'<br />'."\n";
	*/
	//$data = $GLOBALS['phpgw']->dcom->fetchbody($GLOBALS['phpgw']->msg->mailsvr_stream, $m, $p);
	$data = $GLOBALS['phpgw']->msg->phpgw_fetchbody($p);
	//$picture = $GLOBALS['phpgw']->dcom->base64($data);
	$picture = $GLOBALS['phpgw']->msg->de_base64($data);

	//  echo strlen($picture)."<br />\n";
	//  echo $data;

	Header('Content-length: '.strlen($picture));
	Header('Content-type: image/'.$s);
	Header('Content-disposition: attachment; filename="'.urldecode($n).'"');
	echo $picture;
	flush();

	// IS THIS FILE EVER USED ANYMORE?
	if (is_object($GLOBALS['phpgw']->msg))
	{
		$terminate = True;
	}
	else
	{
		$terminate = False;
	}
	
	if ($terminate == True)
	{
		// close down ALL mailserver streams
		$GLOBALS['phpgw']->msg->end_request();
		// destroy the object
		$GLOBALS['phpgw']->msg = '';
		unset($GLOBALS['phpgw']->msg);
	}
	// shut down this transaction
	$GLOBALS['phpgw']->common->phpgw_exit(False);
?>
