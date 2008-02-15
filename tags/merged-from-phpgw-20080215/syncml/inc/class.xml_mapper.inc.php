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

	require_once 'inc/constants.inc.php';

	/**
	 * Recieves parser events and builds an array structure of them.
	 */
	class xml_mapper
	{
		/**
		 * The resulting array structure.
		 */
		var $structure = array();

		/**
		 * Path to the current element. Used internally.
		 */
		var $path = array();

		/**
		 * Buffered character data. Used internally.
		 */
		var $data = "";

		function xml_mapper()
		{
			$this->path = array(&$this->structure);
		}

		function start_tag($parser, $tag, $attrs)
		{
			$end = &$this->get_last_path_element_by_ref();

			$child_arr = array
			(
				SYNCML_XML_ATTRIBUTES => count($attrs) > 0 ? $attrs : NULL,
				SYNCML_XML_ORIGINAL_ORDER => array(),
				SYNCML_XML_TAG_NAME => $tag
			);

			$end[$tag][] = &$child_arr;

			$end[SYNCML_XML_ORIGINAL_ORDER][] = &$child_arr;

			$this->data = "";
			$this->path[] = &$child_arr;
		}

		function end_tag($parser, $tag)
		{
			$end = &$this->get_last_path_element_by_ref();

			$end[SYNCML_XML_DATA] =
				trim($this->data) == '' ? NULL : $this->data;

			$this->data = "";
			array_pop($this->path);
		}

		function data($parser, $data)
		{
			$this->data .= $data;
		}

		function &get_last_path_element_by_ref()
		{
			end($this->path);
			return $this->path[key($this->path)];
		}
	}
?>
