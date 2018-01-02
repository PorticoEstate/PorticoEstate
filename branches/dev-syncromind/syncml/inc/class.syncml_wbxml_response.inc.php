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

	require_once 'class.syncml_response.inc.php';
	require_once 'class.syncml_wbxml_encoder.inc.php';

	require_once 'class.xml_parser.inc.php';

	class syncml_wbxml_response extends syncml_response
	{
		var $xml_parser;

		function syncml_wbxml_response()
		{
			$this->root_namespace = '-//SYNCML//DTD SyncML 1.1//EN';

			$this->xml_parser = new xml_parser();
		}

		function print_response()
		{
			$encoder = new syncml_wbxml_encoder();

			$encoder->header(
				0x02, $this->root_namespace, 0x04);

			$encoder->start_tag(NULL, 'SyncML', array());
			$encoder->data(NULL, NULL);
			$encoder->raw($this->filter(
				'<SyncHdr>' . $this->header . $this->header_cred .
				'</SyncHdr>'));
			$encoder->start_tag(NULL, 'SyncBody', array());
			$encoder->data(NULL, NULL);
			$encoder->raw(implode('', $this->commands));
			$encoder->start_tag(NULL, 'Final', array());
			$encoder->end_tag(NULL, 'Final');
			$encoder->end_tag(NULL, 'SyncBody');
			$encoder->end_tag(NULL, 'SyncML');

			echo $encoder->structure;
		}

		function filter($data)
		{
			return $this->xml_parser->parse($data, new syncml_wbxml_encoder());
		}

		function set_syncml_namespace_version($version)
		{
			$this->root_namespace = sprintf(
				'-//SYNCML//DTD SyncML %s//EN', $version);
		}
	}
?>
