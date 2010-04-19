<?php
	/**
	 * Communik8r MIME handler class
	 *
	 * @author Ryo Chijiiwa <Ryo@IlohaMail.org>
	 * @author Dave Hall skwashd@phpgroupware.org
	 * @copyright Copyright (c) 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
	 * @copyright Copyright (c) 2005 Dave Hall skwashd at communik8r.org
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @internal taken from http://ilohamail.org and converted to OOP by skwashd
	 * @package communik8r
	 * @subpackage comm
	 * @version $Id: class.mail_mime.inc.php,v 1.2 2005/08/25 09:25:57 skwashd Exp $
	 */
	
	/**
	 * @def int Invalid mime type
	 */
	define('MIME_INVALID', -1);

	/**
	 * @def int mime type text
	 */
	define('MIME_TEXT', 0);

	/**
	 * @def int mime type multipart
	 */
	define('MIME_MULTIPART', 1);

	/**
	 * @def int mime type message
	 */
	define('MIME_MESSAGE', 2);

	/**
	 * @def int mime type application
	 */
	define('MIME_APPLICATION', 3);

	/**
	 * @def int mime type audio
	 */
	define('MIME_AUDIO', 4);

	/**
	 * @def int mime type image
	 */
	define('MIME_IMAGE', 5);

	/**
	 * @def int mime type video
	 */
	define('MIME_VIDEO', 6);

	/**
	 * @def int mime type other
	 */
	define('MIME_OTHER', 7);

	/**
	 * MIME handler class
	 */

	class mail_mime
	{

		/**
		 * @var array $struct the message structure array
		 */
		var $struct;

		/**
		 * @constructor
		 * @param string $str raw structure string
		 */
		function mail_mime($str)
		{
			$this->struct = $this->parse_raw_string($str);
		}

		/**
		 * Get a message part meta data as an array
		 *
		 * @param string $part the part to retreive
		 * @param array $a message structure
		 * @returns array message part meta data
		 */
		function get_part_array($part, $a = array() )
		{
			if ( !is_array($a) )
			{
				return false;
			}

			if ( !count($a) )
			{
				$a = $this->struct;
			}

			if (strpos($part, ".") > 0)
			{
				$original_part = $part;
				$pos = strpos($part, ".");
				$rest = substr($original_part, $pos+1);
				$part = substr($original_part, 0, $pos);
				if ( strtolower($a[0]) == 'message' && strtolower($a[1]) == 'rfc822' )
				{
					$a = $a[8];
				}
				trigger_error("m - part: $original_part current: $part rest: $rest array: ".implode(" ", $a));
				return $this->get_part_array($rest, $a[$part-1]);
			}
			else if ($part > 0)
			{
				if ( strtolower($a[0]) == 'message' && strtolower($a[1]) == 'rfc822' )
				{
					$a = $a[8];
				}
				trigger_error("s - part: $part rest: $rest array: ".implode(" ", $a));
				if ( is_array($a[$part - 1]) )
				{
					return $a[$part - 1];
				}
				else
				{
					return false;
				}
			}
			else if ( empty($part) || $part == 0 )
			{
				return $a;
			}
		}

		function get_num_parts($part, $a = array() )
		{
			if (is_array($a))
			{
				if ( !count($a) )
				{
					$a = $this->struct;
				}

				$parent = $this->get_part_array($part, $a);

				if ( strtoupper($parent[0]) == 'message' && strtoupper($parent[1]) == 'rfc822')
				{
					$parent = $parent[8];
				}

				$c = 0;
				foreach ( $parent as $key => $val)
				{
					$is_array = is_array($val);
					if ($is_array)
					{
						++$c;
					}
					else
					{
						break;
					}
				}
				return $c;
			}
			return 0;
		}

		/**
		 * Get the mime type for a message part
		 *
		 * @param string $part part number
		 * @param array $a message parts
		 * @returns string the mime type
		 */
		function get_part_type_string($part, $a = array())
		{
			if ( !count($a) )
			{
				$part_a = $this->get_part_array($part);
			}
			else
			{
				$part_a = $this->get_part_array($part, $a);
			}

			if ($part_a)
			{
				if (is_array($part_a[0]))
				{
					//error_log('is_array($part_a[0])');
					$type_str = 'multipart/';
					foreach ( $part_a as $element )
					{
						if ( !is_array( $element ) )
						{
							$type_str .= $element;
							break;
						}
					}
					return $type_str;
				}
				else
				{
					//error_log('!is_array($part_a[0]) so mime is ' . "{$part_a[0]}/{$part_a[1]}");
					return "{$part_a[0]}/{$part_a[1]}";
				}
			}
			return '';
		}

		/**
		 * Find the first text part of a message
		 *
		 * @param string $part the message part number
		 * @param array $structure the message structure
		 * @returns array the first text part of the message
		 */
		function get_first_text_part($part = 0, $structure = array() )
		{
			if ($part == 0)
			{
				$part = '';
			}

			if ( !count($structure) )
			{
				$structure = $this->struct;
			}

			$type_code = -1;
			while ($type_code != 0)
			{
				$type_code = $this->get_part_type_code($part, $structure);
				$disposition = $this->get_part_disposition($part, $structure);
				if ( $type_code == 1 )
				{
					$part .= ( empty($part) ? '' : '.') . '1';
				}
				else if ( $type_code > 0 || strtolower($disposition) == 'attachment')
				{
					$parts_a = explode('.', $part);
					$lastPart = count($parts_a) - 1;
					$parts_a[$lastPart] = intval($parts_a[$lastPart]) + 1;
					$part = implode(".", $parts_a);
				}
				else if ($type_code == -1)
				{
					return '';
				}
			}

			return $part;
		}

		/**
		 * Get the mime type code
		 *
		 * @param string $part part number
		 * @param array $a message structure
		 * @returns int the mime type code
		 */
		function get_part_type_code($part, $a = array() )
		{
			if ( !count($a) )
			{
				$part_a = $this->get_part_array($part);
			}
			else
			{
				$part_a = $this->get_part_array($part, $a);
			}

			$types = array
				(
				 MIME_TEXT	=> 'text',
				 MIME_MULTIPART	=> 'multipart',
				 MIME_MESSAGE	=> 'message',
				 MIME_APPLICATION=> 'application',
				 MIME_AUDIO	=> 'audio',
				 MIME_IMAGE	=> 'image',
				 MIME_VIDEO	=> 'video',
				 MIME_OTHER	=> 'other'
				);

			if ($part_a)
			{
				if (is_array($part_a[0]))
				{
					$str="multipart";
				}
				else
				{
					$str=$part_a[0];
				}

				$code = 7;

				foreach ( $types as $key => $val)
				{
					if ( strtolower($str) == $val )
					{
						$code = $key;
					}
				}
				return $code;
			}
			else
			{
				return -1;
			}
		}

		/**
		 * Get the encoding type code
		 *
		 * @param string $part part number
		 * @param array $a message structure
		 * returns int the type encoding code
		 */
		function get_part_encoding_code($part, $a = array() )
		{
			if ( !count($a) )
			{
				$part_a = $this->get_part_array($part);
			}
			else
			{
				$part_a = $this->get_part_array($part, $a);
			}

			$encodings = array('7BIT', '8BIT', 'BINARY', 'BASE64', 'QUOTED-PRINTABLE', 'OTHER');

			if ($part_a)
			{
				if ( is_array($part_a[0]) )
				{
					return -1;
				}
				else
				{
					$str = $part_a[5];
				}

				$code = 5;

				foreach ($encodings as $encoding )
				{
					if ( strtoupper($str) == $encoding)
					{
						return $key;
					}
				}
			}
			return -1;
		}


		/**
		 * Get the encoding type for the specified part
		 *
		 * @param string $part part number
		 * @param array $a message structure
		 * @returns string the type encoding
		 */
		function get_part_encoding_string($part, $a = array() )
		{
			if ( !count($a) )
			{
				$part_a = $this->get_part_array($part);
			}
			else
			{
				$part_a = $this->get_part_array($part, $a);
			}

			if ($part_a)
			{
				if ( !is_array($part_a[0]) )
				{
					return $part_a[5];
				}
			}
			return '';
		}

		/**
		 * Get the size of the message part
		 *
		 * @param string $part message part
		 * @param array $a message structure
		 * @returns int part size in bytes
		 */
		function get_part_size($part, $a = array() )
		{
			if ( !count($a) )
			{
				$part_a = $this->get_part_array($part);
			}
			else
			{
				$part_a = $this->get_part_array($part, $a);
			}

			if ($part_a)
			{
				if ( !is_array($part_a[0]) )
				{
					return $part_a[6];
				}
			}
			return -1;
		}

		/**
		 * Get the id string for a message part
		 *
		 * @param string $part message part
		 * @param array $a message structure
		 * @returns string part id
		 */
		function get_part_id($part, $a = array() )
		{
			if ( !count($a) )
			{
				$part_a = $this->get_part_array($part);
			}
			else
			{
				$part_a = $this->get_part_array($part, $a);
			}

			if ($part_a)
			{
				if (!is_array($part_a[0]) )
				{
					return $part_a[3];
				}
			}
			return -1;
		}

		/**
		 * Get the mime part disposistion
		 *
		 * @param string $part message part
		 * @param array $a message structure
		 * @returns string message part disposition
		 */
		function get_part_disposition($part, $a = array() )
		{
			if ( !count($a) )
			{
				$part_a = $this->get_part_array($part);
			}
			else
			{
				$part_a = $this->get_part_array($part, $a);
			}

			if ($part_a)
			{
				if ( !is_array($part_a[0]))
				{
					$id = count($part_a) - 2;
					if ( is_array($part_a[$id]) )
					{
						return $part_a[$id][0];
					}
				}
			}
			return '';
		}

		/**
		 * Get the mime part file name
		 *
		 * @param string $part message part
		 * @param array $a message structure
		 * @returns string filename
		 */
		function get_part_name($part, $a = array() )
		{
			if ( !count($a) )
			{
				$part_a = $this->get_part_array($part);
			}
			else
			{
				$part_a = $this->get_part_array($part, $a);
			}

			$name = '';
			if ($part_a)
			{
				if ( !is_array($part_a[0]) )
				{
					if (is_array($part_a[2]))
					{
						//first look in content type
						$name="";
						foreach ($part_a[2] as $key => $val)
						{
							$val = strtoupper($key);
							if ( $val == 'NAME' || $val == 'FILENAME' )
							{
								$name = $part_a[2][$key+1];
							}
						}
					}
					if ( empty($name) )
					{
						//check in content disposition
						$id = count($part_a) - 2;
						if (is_array($part_a[$id]) && is_array($part_a[$id][1]) )
						{
							$array = $part_a[$id][1];
							foreach( $array as $key => $val )
							{
								$val = strtoupper($val);
								if ( $val == 'NAME' || $val == 'FILENAME' ) 
								{
									$name = $array[$key+1];
								}
							}
						}
					}
				}
			}
			return $name;
		}

		/**
		 * Get the mime part charset
		 *
		 * @param string $part message part
		 * @param array $a message structure
		 * @returns string message part charset
		 */
		function get_part_charset($part, $a = array() )
		{
			if ( !count($a) )
			{
				$part_a = $this->get_part_array($part);
			}
			else
			{
				$part_a = $this->get_part_array($part, $a);
			}

			if ($part_a)
			{
				if ( !is_array($part_a[0]) )
				{
					if (is_array($part_a[2]))
					{
						$name = '';
						foreach ( $part_a[2] as $key => $val)
						{
							if ( strtolower($val) == 'charset')
							{
								$name=$part_a[2][$key+1];
							}
						}
						return $name;
					}
				}
			}
			return '';
		}

		/**
		 * Get the list of parts with in a message structure
		 *
		 * @param string $part message part
		 * @param array $a message structure
		 * @returns string message part list
		 */
		function get_part_list($part, $a = array() )
		{
			if ( !count($a) )
			{
				$part_a = $this->get_part_array($part);
			}
			else
			{
				$part_a = $this->get_part_array($part, $a);
			}

			$data = array();
			$num_parts = $this->get_num_parts(0, $part_a);
			if ($num_parts !== false)
			{
				//echo "<!-- ($num_parts parts)//-->\n";
				for ($i = 0; $i < $num_parts; ++$i)
				{
					$part_code = $part . (empty($part) ? '' : "." ) . (++$i) ;
					$part_type = $this->get_part_type_code($part_code);
					$part_disposition = $this->get_part_disposition($part_code);
					trigger_error("part: $part_code type: $part_type");
					if ( strtolower($part_disposition) != 'attachment'
						&& ($part_type == 1 || $part_type == 2) )
					{
						$data = array_merge($data, $this->get_part_list($part_code));
					}
					else
					{
						$data[$part_code] = array
									(
										 'typestring'	=> $this->get_part_type_string($part_code),
										 'disposition'	=> $part_disposition,
										 'size'		=> $this->get_part_size($part_code),
										 'name'		=> $this->get_part_name($part_code),
										 'id'		=> $this->get_part_id($part_code),
										 'charset'	=> $this->get_part_charset($part_code)
									);
					}
				}
			}
			return $data;
		}

		/**
		 * Get the next part number
		 *
		 * @param string $part the part number
		 * @returns string the next part number
		 */
		function get_next_part($part)
		{
			if ( strpos($part, ".") === false)
			{
				return ++$part;
			}
			else
			{
				$parts_a = explode(".", $part);
				$num_levels = count($parts_a);
				$parts_a[$num_levels - 1]++;
				return implode(".", $parts_a);
			}
		}

		/**
		* Convert a raw structure string into a structure array
		*
		* @param string $str raw string
		* @returns array structure array
		*/
		function parse_raw_string($str)
		{
			$line = substr($str, 1, strlen($str) - 2);
			$line = str_replace(')(', ') (', $line);

			$struct = $this->_parse_BS_string($line);
			if ( strtolower($struct[0]) == 'message' 
				&& strtoupper($struct[1]) == 'rfc822' )
			{
				$struct = array($struct);
			}
			return $struct;
		}

		/**
		 * Find where the next closing parenthasis is
		 *
		 * @private
		 * @param string $str the string containing ')'
		 * @param int string position offset
		 * @returns int position of )
		 */
		function _closing_paren_pos($str, $start = 0)
		{
			$level = 0;
			$len = strlen($str);
			$in_quote = 0;
			for ( $i = $start; $i < $len; ++$i )
			{
				if ($str[$i] == '"')
				{
					$in_quote = ($in_quote + 1) % 2;
				}

				if (!$in_quote)
				{
					if ( $str[$i] == '(' )
					{
						++$level;
					}
					else if ( ($level > 0) && ($str[$i] == ')') )
					{
						$level--;
					}
					else if (($level == 0) && ($str[$i] == ')') )
					{
						return $i;
					}
				}
			}
		}

		/**
		 * Does something with brackets :)
		 *
		 * @private
		 * @param string $str
		 * @returns string?
		 */
		function _parse_BS_string($str)
		{
			$id = 0;
			$a = array();
			$len = strlen($str);

			$in_quote = 0;
			for ($i = 0; $i < $len; ++$i)
			{
				if ($str[$i] == '"')
				{
					$in_quote = ($in_quote + 1) % 2;
				}
				else if (!$in_quote)
				{
					if ($str[$i] == ' ')
					{
						++$id; //space means new element
					}
					else if ($str[$i] == '(') //new part
					{
						++$i;
						$endPos = $this->_closing_paren_pos($str, $i);
						$partLen = $endPos - $i;
						$part = substr($str, $i, $partLen);
						$a[$id] = $this->_parse_BS_string($part); //send part string
						$i = $endPos;
					}
					else
					{
						$a[$id].=$str[$i]; //add to current element in array
					}
				}
				else if($in_quote)
				{
					if ($str[$i] == '\\')
					{
						++$i; //escape backslashes
					}
					else
					{
						$a[$id] .= $str[$i]; //add to current element in array
					}
				}
			}
			return $a;
		}
	}
?>
