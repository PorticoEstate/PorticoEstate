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

	require_once 'inc/wbxml_code_spaces.inc.php';
	require_once 'inc/constants.inc.php';

	/**
	 * TODO:
	 *   1. No support for attributes *at all* (SyncML doesn't use attributes)
	 *   2. LITERAL/LITERAL_A/LITERAL_C/LITERAL_AC (SyncML doesn't need to use
	 *      literals).
	 *   3. Having code spaces and code pages in global variables is not a
	 *      good idea.
	 */

	/**
	 * Encode XML events to WBXML.
	 */
	class syncml_wbxml_encoder
	{
		var $structure = '';

		var $namespaces = array('syncml');

		var $last_tag_code = NULL;
		var $last_attributes = NULL;
		var $last_payload = NULL;

		var $current_code_page = 0x00;

		function _print_last_tag()
		{
			if(!is_null($this->last_tag_code))
			{
				if(is_array($this->last_attributes) &&
					count($this->last_attributes) > 0)
				{
					$this->structure .=
						chr($this->last_tag_code /* | WBXML_ATTRIBUTE_BIT */) .
						$this->last_payload /* .
						chr(WBXML_ATTRSTART) */;

					/*
					foreach($this->last_attributes as $a)
					{
						// todo: print attribute
					}
					*/
				}
				else
				{
					$this->structure .=
						chr($this->last_tag_code) . $this->last_payload;
				}
			}

			$this->last_tag_code = $this->last_attributes =
				$this->last_payload = NULL;
		}

		function header($version, $dtd_string, $charset)
		{
			$this->structure .= chr($version);
			$this->structure .= chr(0) . chr(0); // ref to strtbl

			$this->structure .= chr($charset);

			// strtbl
			$this->structure .= chr(strlen($dtd_string)) . $dtd_string;
		}

		function end_tag($parser, $tag)
		{
			$code = $this->last_tag_code;

			$this->_print_last_tag();

			if($code === NULL || $code & WBXML_CONTENT_BIT)
			{
				$this->structure .= chr(WBXML_END);
			}

			$poped_ns = array_pop($this->namespaces);
			$end_ns = end($this->namespaces);

			if($poped_ns != $end_ns)
			{
				list($code_space, $code_page) =
					isset($GLOBALS['namespaces'][$end_ns]) ?
					$GLOBALS['namespaces'][$end_ns] : array(0xFD1, 0);

				$this->structure .= chr(WBXML_SWITCH) . chr($code_page);
			}
		}

		function start_tag($parser, $tag, $attrs)
		{
			$last_ns = end($this->namespaces);

			if(isset($attrs['XMLNS']) && $attrs['XMLNS'])
			{
				$this->namespaces[] = $attrs['XMLNS'];
			}
			else
			{
				$this->namespaces[] = end($this->namespaces);
			}

			$current_ns = end($this->namespaces);

			list($code_space, $code_page) =
				isset($GLOBALS['namespaces'][$current_ns]) ?
				$GLOBALS['namespaces'][$current_ns] : array(0xFD1, 0);

			if($this->last_tag_code)
			{
				$this->last_tag_code |= WBXML_CONTENT_BIT;
				$this->_print_last_tag();
			}

			if($last_ns != $current_ns)
			{
				$this->structure .= chr(WBXML_SWITCH) . chr($code_page);
			}

			if(isset($GLOBALS['wbxml_tag_to_code'][$code_space][$code_page]
				[strtoupper($tag)]) && $GLOBALS['wbxml_tag_to_code']
				[$code_space][$code_page][strtoupper($tag)])
			{
				$this->last_tag_code = $GLOBALS['wbxml_tag_to_code']
					[$code_space][$code_page][strtoupper($tag)];
				$this->last_attributes = $attrs;
			}
			else
			{
				// todo: set literal in last_tag_code, tag name in payload
			}
		}

		function raw($data)
		{
			$this->structure .= $data;
		}

		function data($parser, $data)
		{
			if($this->last_tag_code)
			{
				$this->last_tag_code |= WBXML_CONTENT_BIT;
				$this->_print_last_tag();
			}

			if(!is_null($data))
			{
				$this->structure .= chr(WBXML_OPAQUE) .
					$this->_build_mb_uint32(strlen($data)) . $data;
			}
		}

		/**
		 * Build multi-byte int from int.
		 *
		 * @param $number Integer to encode to multi-byte convention.
		 */
		function _build_mb_uint32($number)
		{
			$last_seven_bits = $number & 0x7F;
			$number >>= 7;

			$mb_uint32 = chr($last_seven_bits);

			while($number > 0)
			{
				// 0x7F = 01111111
				$last_seven_bits = $number & 0x7F;
				$number >>= 7;

				// 0x80 = 10000000
				$mb_uint32 = chr(0x80 | $last_seven_bits) . $mb_uint32;
			}

			return $mb_uint32;
		}
	}
?>
