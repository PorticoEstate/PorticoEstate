<?php

	/**
	 * XmlToArray
	 * @author Rasmus Andersson {@link http://rasmusandersson.se/}
	 * @author Eric Rosebrock http://www.phpfreaks.com
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2002,2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgwapi
	 * @subpackage xml
	 * @version $Id$
	 * @internal This class originated from: kris@h3x.com AT: http://www.devdump.com/phpxml.php
	 */

	/**
	 * Parse XML to an array.
	 *
	 * Works about the same way as {@link XmlToArray} but returns a slightly more complex/detailed structure.
	 * Also, take a look at the ArrayToXml class - wich is the opposite to this class.
	 *
	 * Part of parse() and _getChildren() originates from kris[at]h3x.com, http://www.devdump.com/phpxml.php
	 *
	 * <b>Example</b><code>
	 * 	  require_once('XmlToArray2.php');
	 * 	  $xa = new XmlToArray2();
	 * 	  $a = $xa->parseFile('test.xml');
	 * 	  print_r( $a );</code>
	 *
	 * <b>Example</b><code>
	 * 	  require_once('XmlToArray2.php');
	 * 	  $xa = new XmlToArray2('utf-8');
	 * 	  $a = $xa->parse('<root><child name="test"><hello>Yes</hello><hello>'
	 * 					 .'Yes again</hello></child></root>');
	 * 	  print_r( $a );</code>
	 *
	 *
	 * <b>Changelog</b>
	 * 	- v0.1 - Implemented Kris's methods into the predecessor class XmlToArray and cleaned up a bit.
	 * 	- v0.2 - Added option for value-modifier callback function.
	 * 			 Added automatic utf8 decoding.
	 * 			 Whitespaces is now passed through core parser and handled explicitly.
	 * 			 Added option for stripping off linebreaks.
	 * 			 Added build-in error logging and reporting.
	 * 			 Changed attribute and value keys to comform to generic standard. (@ and #)
	 * 	- v0.3 - Added option to include or not include empty values (#) - default is not to include them.
	 * 			 ie: <tag foo="bar" />, <tag /> or <tag></tag> But not: <tag> </tag>
	 * 			 Changed setValueModifier() to accept array(object, function) type parameter or string.
	 * 	- v0.4 - Improved error reporting and handling.
	 * 	- v0.5 - Added the option to make all tags and attributes lower case or upper case.
	 * 	- v0.6 - Added the option to skip attributes - and simplify the output (Sigurd Nes)
	 *
	 * <b>Known issues</b>
	 * 	- None at the moment
	 * 	- Please send bug reports to rasmus[at]flajm.se
	 *
	 *
	 * @version 0.6 / 2007-01-10
	 * @package phpgwapi
	 * @subpackage xml
	 */
	class XmlToArray
	{

		/**
		 * @var string
		 * @access private
		 */
		var $_encoding = 'ISO-8859-1';

		/**
		 * @var bool
		 * @access private
		 */
		var $_strip_linebreaks = false;

		/**
		 * @var bool
		 * @access private
		 */
		var $_includesRoot = false;

		/**
		 * @var string|null
		 * @access private
		 */
		var $_valueModifier = NULL;

		/**
		 * Is set automaticaly by parse() if the data matches _seems_utf8()
		 * then runs utf8_decode() on all values.
		 *
		 * @var bool
		 * @access private
		 */
		var $_decodeUtf8 = false;

		/**
		 * @var bool
		 * @access private
		 */
		var $_automaticUtf8Decoding = true;

		/**
		 * Contains the error trace
		 *
		 * @var array
		 * @access private
		 */
		var $_error_trace = array();

		/**
		 * @var float
		 * @access private
		 */
		var $_error_start_timer = 0.0;

		/**
		 * @var bool
		 * @access private
		 */
		var $_include_empty_values = false;

		/** @access private */
		var $_lower_case_tags = false;

		/**
		 * @var bool
		 */
		var $get_attributes = false;

		/**
		 * Create an instance of this class as an object and set some options.
		 *
		 * @param   string  $encoding (optional) Defaults to ISO-8859-1
		 * @param   bool	$stripLinebreaks (optional) Defaults to no/false
		 * @param   bool	$includeRootElement (optional) Defaults to no/false
		 * @param   bool	$includeEmptyValues (optional) Defaults to no/false
		 * @return  object  XmlToArray instance
		 */
		function XmlToArray( $encoding = NULL, $stripLinebreaks = NULL,
					   $includeRootElement = NULL, $automaticUtf8Decoding = NULL,
					   $includeEmptyValues = NULL )
		{
			if ( is_string( $encoding ) )
			{
				$this->setEncoding( $encoding );
			}
			if ( is_bool( $stripLinebreaks ) )
			{
				$this->setStripsLinebreaks( $stripLinebreaks );
			}
			if ( is_bool( $includeRootElement ) )
			{
				$this->setIncludesRoot( $includeRootElement );
			}
			if ( is_bool( $automaticUtf8Decoding ) )
			{
				$this->setDecodesUTF8Automaticly( $automaticUtf8Decoding );
			}
			if ( is_bool( $includeEmptyValues ) )
			{
				$this->setIncludesEmptyValues( $includeEmptyValues );
			}

			list($usec, $sec) = explode( " ", microtime() );
			$this->_error_start_timer = (float) $usec + (float) $sec;
		}

		/**
		 * Supported encodings are "ISO-8859-1", which is also the default
		 * if no encoding is specified, "UTF-8" and "US-ASCII". Can take any encoding
		 * xml_parser_create(string encoding) can.
		 *
		 * @param  string  $enc
		 */
		function setEncoding( $enc )
		{
			$enc = strtoupper( $enc );
			if ( $enc != 'ISO-8859-1' && $enc != 'UTF-8' && $enc != 'US-ASCII' )
			{
				$this->_logError( 'setEncoding',
					  'Unsupported encoding specified. Using default/current.' );
				return;
			}
			$this->_encoding = $enc;
		}

		/**
		 * @return string
		 */
		function encoding()
		{
			return $this->_encoding;
		}

		/**
		 * @param bool $b
		 */
		function setStripsLinebreaks( $b )
		{
			$this->_strip_linebreaks = $b;
		}

		/**
		 * @return bool
		 */
		function stripsLinebreaks()
		{
			return $this->_strip_linebreaks;
		}

		/**
		 * @param int $i  CASE_LOWER or CASE_UPPER
		 */
		function setTagCase( $i )
		{
			$this->_lower_case_tags = ($i == CASE_LOWER);
		}

		/**
		 * Has the side effect to only include the first root element if set to false.
		 * This shouldn't be any problem, since well-formed xml only has one root element.
		 *
		 * @param bool $b
		 */
		function setIncludesRoot( $b )
		{
			$this->_includesRoot = $b;
		}

		/**
		 * @return bool
		 */
		function includesRoot()
		{
			return $this->_includesRoot;
		}

		/**
		 * Enable on or disable automatic utf8 decoding. Uses seems_utf8() to guess if the
		 * document contains any utf8 encoded chars. Decoding will only be done on values.
		 *
		 * @param bool $b
		 */
		function setDecodesUTF8Automaticly( $b )
		{
			$this->_automaticUtf8Decoding = $b;
		}

		/**
		 * @return bool
		 */
		function decodesUTF8Automaticly()
		{
			return $this->_automaticUtf8Decoding;
		}

		/**
		 * Enable on or disable automatic utf8 decoding. Uses seems_utf8() to guess if the
		 * document contains any utf8 encoded chars. Decoding will only be done on values.
		 *
		 * @param bool $b
		 */
		function setIncludesEmptyValues( $b )
		{
			$this->_include_empty_values = $b;
		}

		/**
		 * @return bool
		 */
		function includesEmptyValues()
		{
			return $this->_include_empty_values;
		}

		/**
		 * Register a function wich will be called with one argument (string $value) for
		 * each value parsed. This way, you can manipulate the values in a quick way.
		 * Do uppercase conversion, trim off tabs, or whatever. Set to NULL to disable.
		 * Disabled by default.
		 *
		 * <b>Example</b><code>
		 * function myValueModifier( $value )
		 * {
		 * 	return strtoupper($value);
		 * }
		 * $xa = new XmlToArray2('utf-8');
		 * $xa->setValueModifier('myValueModifier');
		 * print_r( $xa->parseFile('test.xml') );</code>
		 *
		 * Must be set before calling any parse method.
		 *
		 * @param  string|array  $function_name  String, array($object, 'function'), array('object_name', 'function')
		 * 									   or array(&$object, 'function')
		 * @return  bool  Success?
		 */
		function setValueModifier( $function )
		{
			if ( is_string( $function ) )
			{
				if ( function_exists( $function ) )
				{
					$this->_valueModifier = $function;
					return true;
				}
				else
				{
					$this->_logError( 'setValueModifier',
					   'Registered value modifier function can not be found.' );
					return false;
				}
			}
			else if ( is_array( $function ) )
			{
				$this->_valueModifier = $function;
			}
			else
			{
				$this->_logError( 'setValueModifier',
					  'Parameter of unsupported type. Should be string or array.' );
				return false;
			}
			return true;
		}

		/**
		 * @return string
		 */
		function valueModifier()
		{
			return $this->_valueModifier;
		}

		/**
		 * Parse a file and return the structure
		 *
		 * @param string $file
		 * @return array
		 */
		function parseFile( $file )
		{
			if ( !file_exists( $file ) )
			{
				$this->_logError( 'parseFile', 'The file "' . $file . '" can not be found!' );
				return array();
			}
			return $this->parse( file_get_contents( $file ) );
		}

		/**
		 * @access private
		 */
		function _logError( $function, $msg )
		{
			list($usec, $sec) = explode( " ", microtime() );
			$time					 = ((float) $usec + (float) $sec) - $this->_error_start_timer;
			$this->_error_trace[]	 = array($function, $msg, $time);
		}

		/**
		 * Get the current error traceback
		 *
		 * @return string|NULL  NULL is returned if no errors.
		 */
		function errors()
		{
			if ( count( $this->_error_trace ) == 0 )
				return NULL;
			$s	 = '';
			$len = count( $this->_error_trace ) - 1;
			for ( $i = $len; $i > -1; $i-- )
			{
				$s .= '[' . round( $this->_error_trace[$i][2] * 1000, 2 ) . ' ms] <b>XmlToArray2->' . $this->_error_trace[$i][0] . '()</b> ' . $this->_error_trace[$i][1] . "<br/>";
			}
			return $s;
		}

		/**
		 * Calls a user-set value-modifier function if it exists.
		 * Also strips linebreaks if that option is turned on.
		 *
		 * @access private
		 */
		function _onValue( $value )
		{
			if ( $this->_strip_linebreaks )
			{
				$value = preg_replace( '/[\r\n]+/', ' ', $value );
			}
			if ( $this->_decodeUtf8 )
			{
				$value = utf8_decode( $value );
			}
			if ( $this->_valueModifier != NULL )
			{
				$value = @call_user_func( $this->_valueModifier, $value );
			}
			return $value;
		}

		/**
		 * Calls _onValue() on all attribute values
		 *
		 * @access private
		 */
		function _onAttributes( $attr )
		{
			foreach ( $attr as $k => $v )
			{
				$attr[$k] = $this->_onValue( $v );
			}
			return $attr;
		}

		/**
		 * Parse a string containing xml and return the structure
		 *
		 * @param string $data
		 * @return array
		 */
		function parse( $data )
		{
			$data	 = trim( $data );
			$err	 = false;

			if ( $data == '' )
			{
				$this->_logError( 'parse', 'Empty data' );
				return array();
			}

			if ( $this->_automaticUtf8Decoding )
			{
				if ( $this->_seems_utf8( $data ) )
				{
					$this->_decodeUtf8 = true;
				}
			}

			$parser	 = xml_parser_create( $this->_encoding );
			xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
			xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 0 );
			xml_parse_into_struct( $parser, $data, $vals, $index ) or $err	 = true;

			if ( $err )
			{
				$this->_logError( 'parse',
					  'XML parser failed: '
					. ucfirst( xml_error_string( xml_get_error_code( $parser ) ) ) );
				xml_parser_free( $parser );
				return;
			}
			xml_parser_free( $parser );

			$tree = array();
			$i = 0;

			$tagname = ( $this->_lower_case_tags ) ? strtolower( $vals[$i]['tag'] ) : $vals[$i]['tag'];
			if ( isset( $vals[$i]['attributes'] ) )
			{
				if ( $this->get_attributes )
				{
					$tree[$tagname][]['@']	 = $vals[$i]['attributes'];
					$index					 = count( $tree[$tagname] ) - 1;
					$tree[$tagname][$index]	 = array_merge( $tree[$tagname][$index],
											 $this->_getChildren( $vals, $i ) );
				}
				else
				{
					$tree[$tagname][] = $this->_getChildren( $vals, $i );
				}
			}
			else
			{
				$tree[$tagname][] = $this->_getChildren( $vals, $i );
			}

			if ( !$this->_includesRoot )
			{
				$keys	 = array_keys( $tree );
				$tree	 = $tree[$keys[0]][0];
			}
			return $tree;
		}

		/**
		 * @access private
		 * @return mixed
		 */
		function _getChildren( $vals, &$i )
		{
			$children = array(); // Contains node data
			if ( isset( $vals[$i]['tag'] ) )
			{
				if ( isset( $vals[$i]['value'] ) && trim( $vals[$i]['value'] ) != '' )
				{
					$children = $this->_onValue( $vals[$i]['value'] );
				}
				while ( ++$i < count( $vals ) )
				{
					switch ( $vals[$i]['type'] )
					{
						case 'cdata':
							if ( isset( $children['#'] ) )
							{
								if ( trim( $vals[$i]['value'] ) != '' )
								{
									$children['#'] .= $vals[$i]['value'];
								}
							}
							else
							{
								if ( trim( $vals[$i]['value'] ) != '' )
								{
									$children['#'] = $vals[$i]['value'];
								}
							}
							break;

						case 'complete':
							$tagname = ( $this->_lower_case_tags ) ? strtolower( $vals[$i]['tag'] ) : $vals[$i]['tag'];
							if ( isset( $vals[$i]['attributes'] ) )
							{
								if ( $this->get_attributes )
								{
									$children[$tagname][]['@']	 = $vals[$i]['attributes'];
									$index						 = count( $children[$tagname] ) - 1;
								}

								if ( isset( $vals[$i]['value'] ) )
								{
									if ( $this->get_attributes )
									{
										$children[$tagname][] = $this->_onValue( $vals[$i]['value'] );
									}
									else
									{
										$children[$tagname] = $this->_onValue( $vals[$i]['value'] );
									}
								}
								else if ( $this->_include_empty_values )
								{
									$children[$tagname] = '';
								}
							}
							else
							{
								if ( isset( $vals[$i]['value'] ) )
								{
									$children[$tagname] = $this->_onValue( $vals[$i]['value'] );
								}
								else if ( $this->_include_empty_values )
								{
									$children[$tagname] = '';
								}
							}
							break;

						case 'open':
							$tagname = ( $this->_lower_case_tags ) ? strtolower( $vals[$i]['tag'] ) : $vals[$i]['tag'];
							if ( isset( $vals[$i]['attributes'] ) )
							{
								if ( $this->get_attributes )
								{
									$children[$tagname][]['@']	 = $this->_onAttributes( $vals[$i]['attributes'] );
									//	$index = count($children[$tagname])-1;
									$index						 = count( $children[$vals[$i]['tag']] ) - 1;
									$children[$tagname][$index]	 = array_merge( $children[$tagname][$index],
													 $this->_getChildren( $vals, $i ) );
								}
								else
								{
									$children[$tagname][] = $this->_getChildren( $vals, $i );
								}
							}
							else
							{
								$children[$tagname][] = $this->_getChildren( $vals, $i );
							}
							break;

						case 'close':
							return $children;
					}//switch
				}//while
			}
		}

		/**
		 * @access private
		 */
		function _seems_utf8( $Str )
		{
			for ( $i = 0; $i < strlen( $Str ); $i++ )
			{
				if ( ord( $Str[$i] ) < 0x80 )
					continue;# 0bbbbbbb
				elseif ( (ord( $Str[$i] ) & 0xE0) == 0xC0 )
					$n	 = 1;# 110bbbbb
				elseif ( (ord( $Str[$i] ) & 0xF0) == 0xE0 )
					$n	 = 2;# 1110bbbb
				elseif ( (ord( $Str[$i] ) & 0xF8) == 0xF0 )
					$n	 = 3;# 11110bbb
				elseif ( (ord( $Str[$i] ) & 0xFC) == 0xF8 )
					$n	 = 4;# 111110bb
				elseif ( (ord( $Str[$i] ) & 0xFE) == 0xFC )
					$n	 = 5;# 1111110b
				else
					return false;# Does not match any model
				for ( $j = 0; $j < $n; $j++ ) # n bytes matching 10bbbbbb follow ?
				{
					if ( (++$i == strlen( $Str )) || ((ord( $Str[$i] ) & 0xC0) != 0x80) )
					{
						return false;
					}
				}
			}
			return true;
		}

	}
