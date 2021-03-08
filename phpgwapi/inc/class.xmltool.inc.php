<?php
	/**
	* XML tools
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Ralf Becker <ralfbecker@outdoortraining.de>
	* @copyright Copyright (C) 2002-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage xml
	* @version $Id$
	*/

	/**
	* Convert variable to XML
	*
	* @param string $name
	* @param string $data
	*
	* @return string XML string
	*/
	function var2xml($name, $data)
	{
		$doc = new xmltool();
		return $doc->import_var($name, $data, true, true);
	}

	/**
	* XML tools
	*
	* @package phpgwapi
	* @subpackage xml
	*/
	class xmltool
	{
		/**
		 * @var string
		 * @access private
		 */
		var $_encoding = 'UTF-8';

		/* for root nodes */
		var $xmlversion = '1.0';
		var $doctype = array();

		/* shared */
		var $node_type = '';
		var $name = '';
		var $data_type;
		var $data;

		/* for nodes */
		var $attributes = array();
		var $comments = array();
		var $indentstring = "\t";

		/* start the class as either a root or a node */
		public function __construct($node_type = 'root', $name='', $indentstring="\t")
		{
			$this->node_type = $node_type;
			$this->indentstring = $indentstring;
			if ($this->node_type == 'node')
			{
				if($name !== '')
				{
					$this->name = $name;
				}
				else
				{
					echo 'You must name node type objects<br>';
					exit;
				}
			}
		}

		/**
		 * Supported encodings are "ISO-8859-1", which is also the default
		 * if no encoding is specified, "UTF-8" and "US-ASCII". Can take any encoding
		 * xml_parser_create(string encoding) can.
		 *
		 * @param  string  $enc
		 */
		function set_encoding( $enc )
		{
			$enc = strtoupper($enc);
			if ($enc != 'ISO-8859-1' && $enc != 'UTF-8' && $enc != 'US-ASCII')
			{
				echo 'Unsupported encoding specified. Using default/current.<br>';
				exit;
			}
			$this->_encoding = $enc;
		}

		public function set_version($version = '1.0')
		{
			$this->xmlversion = $version;
			return true;
		}

		public function set_doctype($name, $uri = '')
		{
			if ( $this->node_type == 'root' )
			{
				$this->doctype[$name] = $uri;
				return true;
			}

			return false;
		}

		public function add_node($node_object, $name = '')
		{
			switch ($this->node_type)
			{
				case 'root':
					if (is_object($node_object))
					{
						$this->data = $node_object;
					}
					else
					{
						$this->data = $this->import_var($name, $node_object);
					}
					break;

				case 'node':
					if(!is_array($this->data))
					{
						$this->data = Array();
						$this->data_type = 'node';
					}
					if (is_object($node_object))
					{
						if ($name != '')
						{
							$this->data[$name] = $node_object;
						}
						else
						{
							$this->data[] = $node_object;
						}
					}
					else
					{
						$this->data[$name] = $this->import_var($name, $node_object);
					}
					return true;
			}
		}

		public function get_node($name = '')	// what is that public function doing: NOTHING !!!
		{
			switch	($this->data_type)
			{
				case 'root':
					break;
				case 'node':
					break;
				case 'object':
					break;
			}

		}

		public function set_value($string)
		{
			$this->data = $string;
			$this->data_type = 'value';
			return true;
		}

		public function get_value()
		{
			if ( $this->data_type == 'value' )
			{
				return $this->data;
			}

			return false;
		}

		public function set_attribute($name, $value = '')
		{
			$this->attributes[$name] = $value;
			return true;
		}

		public function get_attribute($name)
		{
			return $this->attributes[$name];
		}

		public function get_attributes()
		{
			return $this->attributes;
		}

		public function add_comment($comment)
		{
			$this->comments[] = $comment;
			return true;
		}

		public function import_var($name, $value, $is_root = false, $export_xml = false)
		{
			$node = new xmltool('node', $name, $this->indentstring);
			switch ( gettype($value) )
			{
				case 'string':
				case 'integer':
				case 'double':
				case 'NULL':
					$node->set_value($value);
					break;

				case 'boolean':
					if($value == true)
					{
						$node->set_value('1');
					}
					else
					{
						$node->set_value('0');
					}
					break;

				case 'array':
					$new_index = false;
					foreach ( $value as $val )
					{
						if ( is_array($val) && count($val) )
						{
							list($first_key) = array_keys($val);
							if ( is_int($first_key) )
							{
								$new_index = true;
								break;
							}
						}
					}
					foreach ( $value as $key => $val )
					{
						if($new_index)
						{
							$keyname = $name;
							$nextkey = $key;
						}
						else
						{
							$keyname = $key;
							$nextkey = $key;
						}
						switch ( strtolower(gettype($val)) )
						{
							case 'string':
							case 'integer':
							case 'double':
							case 'null':
								$subnode = new xmltool('node', $nextkey,$this->indentstring);
								$subnode->set_value($val);
								$node->add_node($subnode);
								break;

							case 'boolean':
								$subnode = new xmltool('node', $nextkey,$this->indentstring);
								$subnode->set_value((int) $val);
								$node->add_node($subnode);
								break;

							case 'array':
								//list($first_key) = each($val);
								$first_key = key($val);
								if($new_index && is_int($first_key))
								{
									foreach ( $val as $subval )
									{
										$node->add_node($this->import_var($nextkey, $subval));
									}
								}
								else
								{
									$subnode = $this->import_var($nextkey, $val);
									$node->add_node($subnode);
								}
								break;
							case 'object':
								$subnode = new xmltool('node', $nextkey,$this->indentstring);
								$subnode->set_value((string) $val);
								$node->add_node($subnode);
								break;

							case 'resource':
								trigger_error('Cannot package PHP resource pointers into XML', E_USER_ERROR);

							default:
								trigger_error('Invalid or unknown data type', E_USER_ERROR);
						}

					}
					break;

				case 'object':
					$subnode->set_value((string) $value);
					break;

				case 'resource':
					trigger_error('Cannot package PHP resource pointers into XML', E_USER_ERROR);

				default:
					trigger_error('Invalid or unknown data type', E_USER_ERROR);
			}

			if($is_root)
			{
				$this->add_node($node);
				if($export_xml)
				{
					$xml = $this->export_xml();
					return $xml;
				}

				return true;
			}

			$this->add_node($node);
			return $node;
		}

		public function export_var()
		{
			if($this->node_type == 'root')
			{
				return $this->data->export_var();
			}

			if($this->data_type != 'node')
			{
				if ( preg_match('/PHP_SERIALIZED_OBJECT&:/', $this->data) )
				{
					return unserialize(preg_replace('/PHP_SERIALIZED_OBJECT&:/', '', $this->data));
				}

				return $this->data;
			}
			else
			{
				$new_index = false;
				foreach ( $this->data as $key => $val )
				{
					if(!isset($found_keys[$val->name]))
					{
						$found_keys[$val->name] = true;
					}
					else
					{
						$new_index = true;
					}
				}

				if($new_index)
				{
					foreach ( $this->data as $val )
					{
						$return_array[$val->name][] = $val->export_var();
					}
				}
				else
				{
					foreach ( $this->data as $val )
					{
						$return_array[$val->name] = $val->export_var();
					}
				}

				return $return_array;
			}
		}

		public function export_struct()
		{
			if($this->node_type == 'root')
			{
				return $this->data->export_struct();
			}

			$retval['tag'] = $this->name;
			$retval['attributes'] = $this->attributes;
			if($this->data_type != 'node')
			{
				if ( preg_match('/PHP_SERIALIZED_OBJECT&:/', $this->data) )
				{
					$retval['value'] = unserialize(preg_replace('/PHP_SERIALIZED_OBJECT&:/', '', $this->data));
				}
				else
				{
					$retval['value'] = $this->data;
				}

				return $retval;
			}

			foreach ( $this->data as $val )
			{
				$retval['children'][] = $val->export_struct();
			}

			return $retval;
		}


		public function import_xml_children($data, &$i, $parent_node)
		{
			while (++$i < count($data))
			{
				switch ($data[$i]['type'])
				{
					case 'cdata':
					case 'complete':
						$node = new xmltool('node',$data[$i]['tag'],$this->indentstring);
						if(is_array($data[$i]['attributes']) && count($data[$i]['attributes']) > 0)
						{
							foreach ( $data[$i]['attributes'] as $k => $v )
							{
								$node->set_attribute($k, $v);
							}
						}
						$node->set_value($data[$i]['value']);
						$parent_node->add_node($node);
						break;

					case 'open':
						$node = new xmltool('node',$data[$i]['tag'],$this->indentstring);
						if(is_array($data[$i]['attributes']) && count($data[$i]['attributes']) > 0)
						{
							foreach ( $data[$i]['attributes'] as $k => $v )
							{
								$node->set_attribute($k, $v);
							}
						}

						$node = $this->import_xml_children($data, $i, $node);
						$parent_node->add_node($node);
						break;

					case 'close':
						return $parent_node;
				}
			}
		}

		public function import_xml($xmldata)
		{
			$parser = xml_parser_create();
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE,   1);
			xml_parse_into_struct($parser, $xmldata, $vals, $index);
			xml_parser_free($parser);
			unset($index);
			$node = new xmltool('node',$vals[0]['tag'],$this->indentstring);
			if ( isset($vals[0]['attributes']) )
			{
				foreach ( $vals[0]['attributes'] as $key => $value )
				{
					$node->set_attribute($key, $value);
				}
			}

			switch ( $vals[0]['type'] )
			{
				case 'complete':
					$node->set_value($vals[0]['value']);
					break;

				case 'cdata':
					$node->set_value($vals[0]['value']);
					break;

				case 'open':
					$node = $this->import_xml_children($vals, 0, $node);
					break;

				case 'closed':
					exit;
			}
			$this->add_node($node);
		}

		public function export_xml($indent = 1)
		{
			$type_error = false;
			if ($this->node_type == 'root')
			{
				$result = "<?xml version=\"{$this->xmlversion}\" encoding=\"{$this->_encoding}\"?>\n";

				if ( count($this->doctype) == 1 )
				{
					//list($doctype_name, $doctype_uri) = each($this->doctype);
					$doctype_name = key($this->doctype);
					$doctype_uri = current($this->doctype);
					$result .= "<!DOCTYPE {$doctype_name} SYSTEM \"{$doctype_uri}\">\n";
				}

				if ( is_array($this->comments) )
				{
					foreach ( $this->comments as $key => $val )
					{
						$result .= "<!-- {$val} -->\n";
					}
				}
				if(is_object($this->data))
				{
					$indent = 0;
					$result .= $this->data->export_xml($indent);
				}

				return $result;
			}
			else /* For node objects */
			{
				$indentstring = '';
				str_pad($indentstring, $indent, $this->indentstring);

				$result = $indentstring.'<'.$this->name;
				if ( is_array($this->attributes) )
				{
					foreach ( $this->attributes as $key => $val )
					{
						$val = htmlspecialchars($val, ENT_QUOTES, $this->_encoding);
						$result .= " {$key}=\"{$val}\"";
					}
				}

				$endtag_indent = $indentstring;
				if ( empty($this->data_type) )
				{
					$result .= " />\n";
				}
				else
				{
					$result .= '>';

					switch ($this->data_type)
					{
						case 'value':
							if(is_array($this->data))
							{
								$type_error = true;
								break;
							}

							//TODO Work out how to solve this properly - this is done this way to stop W3C compliant links breaking
							$this->data = html_entity_decode($this->data);
							if ( strlen($this->data) > 30 && !empty($this->indentstring) )
							{
								$this->data = htmlspecialchars($this->data, ENT_QUOTES, $this->_encoding);
								$result .= "{$this->data}";
								//XXX Caeies : OUCH Is see no way to add data INTO the note for indenting ... WTF ?
								//XXX Yes this kill me because I got more than 30 chars in URL for images ... I let you test what a long_url_to_img%0A do in that case ...
								//$result .= "\n{$indentstring}{$this->indentstring}{$this->data}\n";
								$endtag_indent = $indentstring;
							}
							else
							{
								$result .= htmlspecialchars($this->data, ENT_QUOTES, $this->_encoding);
								$endtag_indent = '';
							}
							break;

						case 'node':
							$result .= "\n";
							if(!is_array($this->data))
							{
								$type_error = true;
								break;
							}

							$subindent = $indent + 1;

							foreach ( $this->data as $key => $val )
							{
								if(is_object($val))
								{
									$result .= $val->export_xml($subindent);
								}
							}
							break;

						default:
						if($this->data != '')
						{
							echo "Invalid or unset data type '{$this->data_type}'. This should not be possible if using the class as intended<br>\n";
						}
					}

					if ($type_error)
					{
						echo "Invalid data type. Tagged as '{$this->data_type}' but data is '" . gettype($this->data) . "'<br>\n";
					}

					$result .= "{$endtag_indent}</{$this->name}>";
					if($indent != 0)
					{
						$result .= "\n";
					}
				}
				if ( is_array($this->comments) )
				{
					foreach ( $this->comments as $key => $val )
					{
						$result .= "{$endtag_indent}<!-- {$val} -->\n";
					}
				}
				return $result;
			}
		}
	}


	/**
	* XML node
	*
	* @package phpgwapi
	* @subpackage xml
	*/
	class xmlnode extends xmltool
	{
		public function xmlnode($name)
		{
			$this->xmltool('node',$name);
		}
	}


	/**
	* XML doc
	*
	* @package phpgwapi
	* @subpackage xml
	*/
	class xmldoc extends xmltool
	{
		public function xmldoc($version = '1.0')
		{
			$this->xmltool('root');
			$this->set_version($version);
		}

		public function add_root($root_node)
		{
			return $this->add_node($root_node);
		}
	}
