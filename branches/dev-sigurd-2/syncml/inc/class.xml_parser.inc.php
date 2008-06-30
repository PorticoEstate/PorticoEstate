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

	require_once 'inc/functions.inc.php';

	class xml_parser
	{
		function parse($data, $mapper)
		{
			$encoding = syncml_parse_encoding($data);

			if($encoding)
			{
				$parser = xml_parser_create($encoding);
			}
			else
			{
				$parser = xml_parser_create();
			}

			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 1);

			xml_set_object($parser, $mapper);

			xml_set_element_handler($parser, 'start_tag', 'end_tag');
			xml_set_character_data_handler($parser, 'data');

			xml_parse($parser, $data);
			xml_parser_free($parser);

			return $mapper->structure;
		}
	}
?>
