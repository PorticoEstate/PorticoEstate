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

	/**
	 * WBXML parser.
	 */
	class syncml_wbxml_parser
	{
		var $tag_code_page = 0;
		var $attribute_code_page = 0;
		var $code_space;

		var $raw_data;
		var $mapper;
		var $closing_nodes = array();

		var $string_table = array();
		var $charset;
		var $publicid, $publicid_ref;
		var $version;

		/**
		 * Parse a WBXML document to XML array.
		 *
		 * @param $raw_data Array with one byte per element.
		 * @param $mapper   Object receiving parser events.
		 * @return array    XML array of WBXML document.
		 */
		function parse($raw_data, $mapper)
		{
			$this->raw_data = $raw_data;
			$this->mapper = &$mapper;

			$this->parse_start();

			$this->parse_body();

			return $mapper->structure;
		}

		function parse_start()
		{
			$this->version = $this->read_uint8();

			$this->publicid = $this->read_mb_uint32();

			if(!$this->publicid)
			{
				$this->publicid_ref = $this->read_mb_uint32();
			}

			$this->charset = $this->read_mb_uint32();

			// string table parsing follows

			if(!current($this->raw_data))
			{
				return;
			}

			$length = $this->read_mb_uint32();

			$buffer = '';
			$start = 0;

			for($i = 0; $i < $length; $i++)
			{
				$byte = $this->read_uint8();

				if($byte === 0)
				{
					$this->string_table[$start] = $buffer;
					$buffer = '';
					$start = $i + 1;
				}
				else
				{
					$buffer .= chr($byte);
				}
			}

			if(strlen($buffer) > 0)
			{
				$this->string_table[$start] = $buffer;
			}

			if(!is_null($this->publicid_ref))
			{
				$this->code_space = $GLOBALS['publicid']
					[$this->string_table[$this->publicid_ref]];
			}
			else
			{
				$this->code_space = $this->publicid;
			}
		}

		function parse_body()
		{
			$tag = $this->read_uint8();

			while($tag !== NULL)
			{
				switch($tag)
				{
					case WBXML_SWITCH:
						$this->tag_code_page = $this->read_uint8();
						break;
					case WBXML_END:
						$this->mapper->end_tag(
							NULL, array_pop($this->closing_nodes));
						break;
					case WBXML_STR_T:
						$this->mapper->data(
							$this->string_table[$this->read_mb_uint32()]);
						break;
					case WBXML_STR_I:
						$this->mapper->data(NULL, $this->read_inline_string());
						break;
					case WBXML_OPAQUE:
						$this->mapper->data(NULL, $this->read_opaque_string());
						break;
					default:
						$attributes = array();
						$tag_code = $tag &
							~(WBXML_ATTRIBUTE_BIT | WBXML_CONTENT_BIT);

						if($tag_code == WBXML_LITERAL)
						{
							$index = $this->read_mb_uint32();
							$tag_name = $this->string_table[$index];
						}
						else
						{
							$tag_name = $GLOBALS['wbxml_code_to_tag']
								[$this->code_space][$this->tag_code_page]
								[$tag_code];
						}

						$this->closing_nodes[] = $tag_name;

						if($tag & WBXML_ATTRIBUTE_BIT)
						{
							$attributes = $this->read_attributes();
						}

						$this->mapper->start_tag(NULL, $tag_name, $attributes);

						if(!($tag & WBXML_CONTENT_BIT))
						{
							$this->mapper->end_tag(NULL, $tag_name);
							array_pop($this->closing_nodes);
						}
				}

				$tag = $this->read_uint8();
			}
		}

		function read_inline_string()
		{
			$buffer = '';

			do
			{
				$byte = $this->read_uint8();
				$buffer .= chr($byte);
			} while($byte > 0);

			return substr($buffer, 0, -1);
		}

		function read_opaque_string()
		{
			$length = $this->read_mb_uint32();

			$buffer = '';

			for(; $length > 0; $length--)
			{
				$buffer .= chr($this->read_uint8());
			}

			return $buffer;
		}

		/**
		 * Read attributes.
		 *
		 * @return array Attribute name as key, attributes value as value.
		 */
		function read_attributes()
		{
			$attributes = array();
			$buffer_name = '';
			$buffer_value = '';

			$byte = $this->read_uint8();

			while($byte != WBXML_END);
			{
				switch($byte)
				{
					case WBXML_SWITCH:
						$this->attribute_code_page = $this->read_uint8();
						break;
					case WBXML_STR_T:
						$buffer_value .=
							$this->string_table[$this->read_mb_uint32()];
						break;
					case WBXML_STR_I:
						$buffer_value .= $this->read_inline_string();
						break;
					case WBXML_OPAQUE:
						$buffer_value .= $this->read_opaque_string();
						break;
					case WBXML_END:
						$attributes[$buffer_name] = $buffer_value;
						$buffer_value = '';
						break;
					case WBXML_LITERAL:
						$attributes[$buffer_name] = $buffer_value;
						$buffer_name =
							$this->string_table[$this->read_mb_uint32()];
						$buffer_value = '';
						break;
					default:
						if($byte & WBXML_ATTRIBUTE_BIT)
						{
							// todo: attribute value space
							$buffer_value .= NULL;
						}
						else
						{
							// todo: attribute name space
							$attributes[$buffer_name] = $buffer_value;
							$buffer_name = NULL;
							$buffer_value = '';
						}
						break;
				}

				$byte = $this->read_uint8();
			}

			$attributes[$buffer_name] = $buffer_value;

			return $attributes;
		}

		function read_uint8()
		{
			$h = each($this->raw_data);

			return $h === FALSE ? NULL : $h['value'];
		}

		function read_mb_uint32()
		{
			$y = 0;

			do
			{
				list(, $x) = each($this->raw_data);
				$y <<= 7;
				$y |= $x & 0x7F;
			} while($x & 0x80);

			return $y;
		}
	}
?>
