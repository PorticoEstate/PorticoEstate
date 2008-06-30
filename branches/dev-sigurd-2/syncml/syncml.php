<?php
	/**
	 * phpGroupWare (http://phpgroupware.org/)
	 * SyncML interface
	 *
	 * @author    Johan Gunnarsson <johang@phpgroupware.org>
	 * @copyright Copyright (c) 2007 Free Software Foundation, Inc.
	 * @license   GNU General Public License 3 or later
	 * @package   syncml
	 * @version   $Id$
	 */

	ob_start();

	error_reporting(E_ALL);
	// error_reporting(0);

	$phpgw_info = array();

	$GLOBALS['phpgw_info']['flags'] = array(
		'disable_template_class' => True,
		'currentapp'             => 'login',
		'noheader'               => True,
		'nocachecontrol'         => True
	);

	require('../header.inc.php');

	require_once 'inc/class.syncml_logger.inc.php';

	require_once 'inc/class.xml_parser.inc.php';
	require_once 'inc/class.xml_offset_mapper.inc.php';

	require_once 'inc/class.syncml_wbxml_parser.inc.php';
	require_once 'inc/class.syncml_wbxml_response.inc.php';

	require_once 'inc/class.syncml_response.inc.php';
	require_once 'inc/class.syncml_message.inc.php';

	$file_date = gettimeofday(true);
	// this is a ugly, ugly hack
	//$GLOBALS['phpgw']->db->query('TRUNCATE phpgw_access_log');

	if(!isset($_SERVER['CONTENT_TYPE']) ||
		$_SERVER['REQUEST_METHOD'] != 'POST')
	{
		exit('I\'m a SyncML server (1)');
	}

	$post_input = implode("\r\n", file('php://input'));

	file_put_contents('/tmp/'.$file_date.'-a.xml', $post_input);
	switch($_SERVER['CONTENT_TYPE'])
	{
		case 'application/vnd.syncml+wbxml':
			$post_input = wbxml_decode($post_input);
		case 'application/vnd.syncml+xml':
			$parser = new xml_parser();
			$response = new syncml_response();
			break;
		default:
			exit('I\'m a SyncML server (2)');
	}

	$message = new syncml_message();

	// the header

	$header = $parser->parse($post_input,
		new xml_offset_mapper(array('SYNCML', 'SYNCHDR')));
	$message->process_header($header);

	unset($header);

	// the body

	$body = $parser->parse($post_input,
		new xml_offset_mapper(array('SYNCML', 'SYNCBODY')));
	$message->process_body($body);

	unset($body, $GLOBALS['HTTP_RAW_POST_DATA']);

	// execute and print everything

	$message->execute($response);

	$response->print_response();
	file_put_contents('/tmp/'.$file_date.'-b.xml', ob_get_contents()."\n");

	if($_SERVER['CONTENT_TYPE'] == 'application/vnd.syncml+wbxml')
	{
		// remove the xml declaration tag
		$xml = substr(ob_get_clean(), 38);
		ob_start();
		// replace some bogus FPI values
		echo str_replace(
			array(
				chr(0x02) . chr(0xA4) . chr(0x01) . chr(0x6A),
				chr(0x02) . chr(0xA4) . chr(0x02) . chr(0x6A)),
			array(
				chr(0x02) . chr(0x9F) . chr(0x51) . chr(0x6A),
				chr(0x02) . chr(0x9F) . chr(0x52) . chr(0x6A)),
			wbxml_encode($xml, 0x02, FALSE, FALSE)
		);
	}

	header('Content-Type: ' . $_SERVER['CONTENT_TYPE']);
	header('Content-Length: ' . ob_get_length());

	ob_end_flush();
