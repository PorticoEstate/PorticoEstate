<?php
	/**
	* XML helper
	* Original work by 'djdykes' http://snipplr.com/users/djdykes/
	* @author Brent Kelly  - Zeald.com
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage xml
	* @version $Id: class.xmlhelper.inc.php 10127 2012-10-07 17:06:01Z sigurdne $
	*/

	class phpgwapi_xmlhelper
	{
		/**
		 * The main function for converting to an XML document.
		 * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
		 *
		 * @param array $data
		 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
		 * @param SimpleXMLElement $xml - should only be used recursively
		 * @return string XML
		 */
		public static function toXML( $data, $rootNodeName = 'ResultSet', &$xml=null )
		{
			if ( is_null( $xml ) )
			{
				$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");

			}
			// loop through the data passed in.
			foreach( $data as $key => $value )
			{
				// no numeric keys in our xml please!
				$numeric = 0;
				if ( is_numeric( $key ) )
				{
					$numeric = 1;
					$key = $rootNodeName;
				}
				if(is_object($value))
				{
					$methods = get_class_methods(get_class($value));
					if(in_array("toArray", $methods))
					{
						$value = $value->toArray();
					}
					else
					{
						$value = get_object_vars($value);
					}
				}
				// delete any char not allowed in XML element names
				$key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

				// if there is another array found recrusively call this function
				if ( is_array( $value ) )
				{
					$node = self::is_assoc( $value ) || $numeric ? $xml->addChild( $key ) : $xml;

					// recrusive call.
					if ( $numeric )
					{
						$key = 'anon';
					}
					self::toXml( $value, $key, $node );
				}
				else
				{
					// add single node.
					switch ( strtolower(gettype($value)) )
					{
						case 'integer':
						case 'boolean':
							$value = (int) $value;
							break;

						case 'double':
						case 'null':
							$value = $value;
							break;

						case 'string':
							//TODO Work out how to solve this properly - this is done this way to stop W3C compliant links breaking
							$value = html_entity_decode($value);
							$value = htmlspecialchars($value , ENT_QUOTES, 'UTF-8');
							break;

						case 'object':
							$value = htmlspecialchars((string) $value , ENT_QUOTES, 'UTF-8');
							break;

						case 'resource':
							trigger_error('Cannot package PHP resource pointers into XML', E_USER_ERROR);

						default:
							trigger_error('Invalid or unknown data type', E_USER_ERROR);
					}

					$xml->addChild( $key, $value );
				}
			}

			// pass back as XML
			return $xml->asXML();

		// if you want the XML to be formatted, use the below instead to return the XML
			//$doc = new DOMDocument('1.0');
			//$doc->preserveWhiteSpace = false;
			//$doc->loadXML( $xml->asXML() );
			//$doc->formatOutput = true;
			//return $doc->saveXML();
		}


		/**
		 * Convert an XML document to a multi dimensional array
		 * Pass in an XML document (or SimpleXMLElement object) and this recrusively loops through and builds a representative array
		 *
		 * @param string $xml - XML document - can optionally be a SimpleXMLElement object
		 * @return array ARRAY
		 */
		public static function toArray( $xml )
		{
			if ( is_string( $xml ) )
			{
				$xml = new SimpleXMLElement( $xml );
			}

			$children = $xml->children();

			if ( !$children )
			{
				return (string) $xml;
			}
			$arr = array();
			foreach ( $children as $key => $node )
			{
				$node = self::toArray( $node );

				// support for 'anon' non-associative arrays
				if ( $key == 'anon' )
				{
					$key = count( $arr );
				}

				// if the node is already set, put it into an array
				if ( isset( $arr[$key] ) )
				{
					if ( !is_array( $arr[$key] ) || $arr[$key][0] == null )
					{
						$arr[$key] = array( $arr[$key] );
					}

					$arr[$key][] = $node;
				}
				else
				{
					$arr[$key] = $node;
				}
			}
			return $arr;
		}

		/**
		 * determine if a variable is an associative array
		 * Returns true if array has elements with non-numeric keys
		 *
		 * @param array $array
		 * @return bool
		 */

		public static function is_assoc($array)
		{
			$candidates = array();
			//find candidates
			if(is_array($array))
			{
				$candidates = array_diff_key($array, array_keys(array_keys($array)));
			}

			//Final check
			if(($candidates && (0 !== count($candidates) || count($array)==0)))
			{
				foreach ($candidates as $key => $data)
				{
					if(! is_numeric( $key ))
					{
						return true;
					}
				}
			}
		// This one won't work if the start key is <> 0 - and there is holes..
		//	return (is_array($array) && (0 !== count(array_diff_key($array, array_keys(array_keys($array)))) || count($array)==0));
		}

		/**
		* Not for use with big dataset
		*/
		public static function xml2assoc($xmldata)
		{
			$xml = new XMLReader();
			$xml->xml($xmldata);
			$assoc = self::_xml2assoc($xml);
			$xml->close();
			return $assoc;
		}

		protected static function _xml2assoc($xml)
		{
			$tree = null;
			while($xml->read())
			{
				switch ($xml->nodeType)
				{
					case XMLReader::END_ELEMENT:
						return $tree;
					case XMLReader::ELEMENT:
						$node = array('tag' => $xml->name, 'value' => $xml->isEmptyElement ? '' : self::_xml2assoc($xml));
						if($xml->hasAttributes)
						{
							while($xml->moveToNextAttribute())
							{
								$node['attributes'][$xml->name] = $xml->value;
							}
						}
						$tree[] = $node;
					break;
					case XMLReader::TEXT:
					case XMLReader::CDATA:
						$tree .= $xml->value;
				}
			}
			return $tree;
		}
	}
