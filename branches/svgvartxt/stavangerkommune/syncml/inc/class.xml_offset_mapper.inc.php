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

	require 'inc/class.xml_mapper.inc.php';

	/**
	 * Same as xml_mapper, but discards events untill a given path is reached.
	 */
	class xml_offset_mapper extends xml_mapper
	{
		/**
		 * How many matching elements reached.
		 */
		var $offset_matching_depth = 0;

		/**
		 * Elements to match.
		 */
		var $offset_elements = array();

		/**
		 * Number of elements to match.
		 */
		var $offset_count = 0;

		/**
		 * Current depth.
		 */
		var $depth = 0;

		/**
		 * Build a offset mapper.
		 *
		 * @param $offset Element offset. Tag names in order.
		 */
		function xml_offset_mapper($offset = array())
		{
			parent::xml_mapper();

			$this->offset_elements = array_change_key_case(
				$offset, CASE_UPPER);
			$this->offset_count = count($offset);
		}

		function start_tag($parser, $tag, $attrs)
		{
			switch($this->offset_matching_depth)
			{
				// the offset is OK
				case $this->offset_count:
					parent::start_tag($parser, $tag, $attrs);
					break;
				// this depth is OK
				case $this->depth:
					// this tag is OK
					if($this->offset_elements[$this->depth] == $tag)
					{
						$this->offset_matching_depth++;
					}
					break;
			}

			$this->depth++;
		}

		function end_tag($parser, $tag)
		{
			$this->depth--;

			if($this->offset_matching_depth > $this->depth)
			{
				$this->offset_matching_depth = $this->depth;
			}

			if($this->offset_matching_depth == $this->offset_count)
			{
				parent::end_tag($parser, $tag);
			}
		}

		function data($parser, $data)
		{
			if($this->offset_matching_depth == $this->offset_count &&
			   $this->offset_matching_depth < $this->depth)
			{
				parent::data($parser, $data);
			}
		}
	}
?>
