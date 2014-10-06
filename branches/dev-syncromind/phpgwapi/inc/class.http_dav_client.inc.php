<?php
	/**
	* WebDAV - Provides methods for manipulating an RFC 2518 DAV repository
	* @author Jonathon Sim (for Zeald Ltd) <jsim@free.net.nz>
	* @copyright Copyright (C) 2002 Zeald Ltd
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage network
	* @version $Id$
	* @internal At the moment much of this is simply a wrapper around the NET_HTTP_Client class, with some other methods for parsing the returned XML etc Ideally this will eventually use groupware's inbuilt HTTP class
	*/

	/**
	* Debug flag for dav client
	*/
  define ('DEBUG_DAV_CLIENT', 0);
	/**
	* Debug flag for dav xml
	*/
  define ('DEBUG_DAV_XML', 0);
	/**
	* Debug flag for cache operations
	*/
  define ('DEBUG_CACHE', 0);
	/**
	* Debug flag for propfind cache
	* This cache avoid to do 2 request when there is a 301 or a 404
	*/
  define ('DEBUG_CACHEPROP', 0);

	// FIXME - code should be updated to the new PHP5/DOM to parse the XML.
	if (!function_exists('domxml_open_mem'))
	{
		if(is_file(PHPGW_API_INC.'/domxml-php4-to-php5.php'))
		{
			require_once PHPGW_API_INC.'/domxml-php4-to-php5.php'; //Load a PHP5 converter
		}
		else
		{
			throw new Exception(lang('ERROR: you need the DOM XML Functions or a converter'));
		}
	}

	/**
	* DAV parser
	*
	* @package phpgwapi
	* @subpackage network
	* @access private
	*/
	class dav_parser {

		function davtree($branch)
		{
			$object = array();
			if(!is_object($branch))
			{
				return $object;
			}
			$branch = $branch->first_child();
			
			while ($branch)
			{
				if (!($branch->is_blank_node()))
				{
					if ( $branch->node_type() == XML_ELEMENT_NODE )
					{
						if ( $branch->node_name () == 'response' )
						{
							//process the properties
							$tmp = dav_parser::davproperties($branch);
							$object[$tmp['full_name']] = $tmp;
						}
						else
						{
							$tmp = dav_parser::davtree($branch);
							$object = $tmp;
						}
					}
				}
				$branch = $branch->next_sibling();
			}
			return $object;
		}

		function davproperties(&$branch)
		{
			$properties = array();
			$b = $branch->first_child();
			while($b)
			{
				if(!($b->is_blank_node()) && $b->node_type() == XML_ELEMENT_NODE)
				{
					$c = $b->first_child();
					switch ($attribute = $b->node_name())
					{
						case 'href':
							$string = urldecode(trim($c->node_value()));
							$idx = strrpos($string, '/');
							if( $idx && ($idx == strlen( $string ) - 1 ) )
							{
								$string = substr($string, 0, $idx);
							}
							$properties['name'] = basename($string);
							//$properties['directory'] = dirname($string);
							$properties['full_name'] = $string;
							$this->url_name = $string;
							break;
						case 'propstat':
							$tmp = dav_parser::davproperties($b);
							preg_match('#HTTP/1.[01] ([0-9]+)#',$tmp['status'],$match);
							if ( is_array($match) )
							{
								if ( intval($match[1]) == 200 )
								{
									$properties += $tmp;
								}
								else
								{
									$properties[$match[0]] = $tmp;
								}
							}
							break;
						case 'prop':
							$properties = dav_parser::davproperties($b);
							break;
						case 'supportedlock':
							$properties[$attribute] = dav_parser::davproperties($b);
							break;
						case 'lockdiscovery':
							$properties[$attribute] = dav_parser::davproperties($b);
							break;
						case 'lockentry':
							$properties[$attribute][] = dav_parser::davproperties($b);
							break;
						case 'lockinfo':
							$properties[$attribute][] = dav_parser::davproperties($b);
							break;
						case 'activelock':
							$properties[$attribute][] = dav_parser::davproperties($b);
							break;
						case 'owner':
							$properties[$attribute] = dav_parser::davproperties($b);
							break;
						case 'locktoken':
							$properties[$attribute][] = dav_parser::davproperties($b);
							break;
						case 'locktype':
							$properties[$attribute] = dav_parser::davproperties($b);
							break;
						case 'lockscope':
							$properties[$attribute] = dav_parser::davproperties($b);
							break;
						case 'creationdate':
							$tmp = explode('T',$c->node_value());
							$properties[$attribute] = date('Y-m-d',strtotime($tmp[0]));
							break;
						case 'getlastmodified':
							$properties[$attribute] = date('Y-m-d',strtotime($c->node_value()));
							break;
						default:
							if ( !empty($attribute) )
							{
								$properties[$attribute] = array();
							}
							if ( is_object($c) )
							{
								while($c)
								{
									switch ($c->node_type())
									{
									case XML_TEXT_NODE:
										if ( $str = trim($c->node_value()) )
										{
											$properties[$attribute] = $str;
										}
										break;
									case XML_ELEMENT_NODE:
											$properties[$attribute][$c->node_name()] = dav_parser::davproperties($c);
									
									}
									$c = $c->next_sibling();
								}
							}
							break;
					}
				}
				$b = $b->next_sibling();
			}
			return $properties;
		}

	}

	/**
	* DAV processor
	* 
	* This class uses a bunch of recursive functions to process the DAV XML tree
	* digging out the relevent information and putting it into an array
	* @package phpgwapi
	* @subpackage network
	* @access private
	*/
	class dav_processor
	{
		var $xml = '';
		var $tree = array();

		function parse_tree($xml_string)
		{

			$this->xml = $xml_string;

			if(empty($this->xml))
			{
				_debug_array('xml string is empty !');
				return;
			}

			if(is_array($this->tree) && count($this->tree))
			{
				unset($this->tree);
				$this->tree = array();
			}

			// So, we fake the DAV: uri into a more acceptable one
			$xml_string = preg_replace('/"DAV:"/','"http://webdav.org/dav/"',$this->xml);
			// Build it
			$domobj = domxml_open_mem($xml_string);
			if(!is_object($domobj))
			{
				_debug_array($xml_string);
				return;
			}
//			$time1 = getmicrotime();
			$this->tree = dav_parser::davtree($domobj);
//			echo getmicrotime() - $time1."\n";
			unset ($xml_string);
			$domobj->free();
		}

	}	


	/**
	* DAV client
	* 
	* @package phpgwapi
	* @subpackage network
	* @access public
	*/
	class http_dav_client
	{
		var $attributes=array();
		var $vfs_property_map = array();
		var $cached_props =  array();
		var $dav_processor = NULL;
		var $cached_propfind = array();
		var $str_dav_error = '';

		function http_dav_client()
		{
			$this->http_client = createObject('phpgwapi.net_http_client');
			$this->set_debug(0);
		}
		
		// TODO:  Get rid of this
		// A quick, temporary debug output function
		function debug($info) {

			if (DEBUG_DAV_CLIENT)
			{
				echo '<b> http_dav_client debug:<em> ';
				if (is_array($info))
				{
					print_r($info);
				}
				else
				{
					echo $info;
				}
				echo '</em></b><br>';
			}
		}
	
		/**
		* Glues a parsed url (ie parsed using PHP's parse_url) back together
		*
		* @param array $url The parsed url
		* @return boolean|string URI string or false
		*/	
		function glue_url ($url)
		{
			if (!is_array($url))
			{
				return false;
			}
			// scheme
			$uri = (!empty($url['scheme'])) ? $url['scheme'].'://' : '';
			// user & pass
			if (!empty($url['user']))
			{
				$uri .= $url['user'];
				if (!empty($url['pass']))
				{
					$uri .=':'.$url['pass'];
				}
				$uri .='@'; 
			}
			// host 
			$uri .= $url['host'];
			// port 
			$port = (!empty($url['port'])) ? ':'.$url['port'] : '';
			$uri .= $port; 
			// path 
			$uri .= $url['path'];
			// fragment or query
			if (isset($url['fragment']))
			{
				$uri .= '#'.$url['fragment'];
			} elseif (isset($url['query']))
			{
				$uri .= '?'.$url['query'];
			}
			return $uri;
		}	
		
		/**
		* Encodes a url from its "display name" to something the dav server will accept
		*
		* @param string $uri The unencoded URI
		* @return string Encoded URI
		* @internal Deals with "url"s which may contain spaces and other unsavoury characters, by using appropriate %20s
		*/			
		function encodeurl($uri)
		{
			$parsed_uri =  parse_url($uri);
			if (empty($parsed_uri['scheme']))
			{
				$path = $uri;
			}
			else
			{
				$path = $parsed_uri['path'];
			}
			$fixed_array = array();
			foreach (explode('/', $path) as $name)
			{
				$fixed_array[] = rawurlencode($name);
			}
			$fixed_path = implode('/', $fixed_array);
			if (!empty($parsed_uri['scheme']))
			{
				$parsed_uri['path'] = $fixed_path;
				$newuri = $this->glue_url($parsed_uri);
			}
			else
			{
				$newuri = $fixed_path;
			}			
			return $newuri;
			
		}

		/**
		* Decodes a url to its "display name"
		*
		* @param string $uri Encoded URI
		* @return string Decoded URI
		* @internal Deals with "url"s which may contain spaces and other unsavoury characters, by using appropriate %20s
		*/		
		function decodeurl($uri)
		{
			$parsed_uri =  parse_url($uri);
			if (empty($parsed_uri['scheme']))
			{
				$path = $uri;
			}
			else
			{
				$path = $parsed_uri['path'];
			}
			$fixed_array = array();
			foreach (explode('/', $path) as $name)
			{
				$fixed_array[]  = rawurldecode($name);
			}
			$fixed_path = implode('/', $fixed_array);
			if (!empty($parsed_uri['scheme']))
			{
				$parsed_uri['path'] = $fixed_path;
				$newuri = $this->glue_url($parsed_uri);
			}
			else
			{
				$newuri = $fixed_path;
			}			
			return $newuri;
			
		}

		/**
		* Sets the "attribute map"
		* @param array $attributes Attributes to extract "as-is" from the DAV properties
		* @param array $dav_map A mapping of dav_property_name => attribute_name for attributes with different names in DAV and the desired name space.
		* @internal This is mainly for use by VFS, where the VFS attributes (eg size) differ from the corresponding DAV ones ("getcontentlength")
		*/
		function set_attributes($attributes, $dav_map)
		{
			$this->vfs_property_map = $dav_map;
			$this->attributes = $attributes;
		}
		
		/**
		* Sets authentication credentials for HTTP AUTH
		*
		* @param string $username The username to connect with
		* @param string $password The password to connect with
		* @internal The only supported authentication type is "basic"
		*/
		function set_credentials( $username, $password )
		{
			$this->http_client->setCredentials($username, $password );
		}

		/**
		* Connects to the server
		*
		* @param string $dav_host The host to connect to
		* @param integer $dav_port The port to connect to
		* @internal If the server requires authentication you will need to set credentials with set_credentials first
		*/
		function connect($dav_host,$dav_port,$ssl=False)
		{
			$this->dav_host = $dav_host;
			$this->dav_port = $dav_port;
			$this->http_client->addHeader('Host',$this->dav_host);
			$this->http_client->addHeader('Connection','close');
			//$this->http_client->addHeader('transfer-encoding','identity');
	//		$this->http_client->addHeader('Connection','keep-alive');
	//		$this->http_client->addHeader('Keep-Alive','timeout=20, state="Accept,Accept-Language"');
			$this->http_client->addHeader('Accept-Encoding','chunked');
			$this->http_client->setProtocolVersion( '1.1' );
			$this->http_client->addHeader( 'user-agent', 'Mozilla/5.0 (compatible; PHPGroupware dav_client/1; Linux)');
			$this->propfind_restore_session();
if(DEBUG_CACHEPROP)	unset($this->cached_propfind);
			$ret = $this->http_client->Connect($dav_host,$dav_port,$ssl);
			$this->server = $this->http_client->url['scheme'] . '://'.$this->dav_host;
			$this->server .= (empty($this->dav_port)) ? '' : ':'.$this->dav_port;
			return $ret;
		}

		function set_debug($debug)
		{
			$this->http_client->setDebug($debug);
		}

		/**
		* Disconnect from the server
		*
		* @internal When doing HTTP 1.1 we frequently close/reopen the connection anyway, so this function needs to be called after any other DAV calls (since if they find the connection closed, they just reopen it)
		*/
		function disconnect()
		{
			$this->propfind_save_session();
			$this->http_client->Disconnect();
		}
		
		/**
		* A high-level method of getting DAV properties
		*
		* @param array $props
		* @param string $url The URL to get properties for
		* @param integer $scope the 'depth' to recuse subdirectories (default 1)
		* @param boolean $is_dir
		* @param boolean $sorted Whether we should sort the rsulting array (default True)
		* @return array of file->property arra
		* @internal This function performs all the necessary XML parsing etc to convert DAV properties (ie XML nodes) into associative arrays of properties - including doing mappings from DAV property names to any desired property name format (eg the VFS one) This is controlled by the attribute arrays set in the set_attributes function.
		*/
		function get_properties(&$props, $url, $scope=1, $is_dir =False, $sorted=true){
			$request_id = $url.'//'.$scope.'//'.$sorted; //A unique id for this request (for caching)
			if (isset($this->cached_props[$request_id]) && $this->cached_props[$request_id])
			{
if (DEBUG_CACHE) echo'Get properties: Cache hit : cache id:'.$request_id.'<br />';
				$props = $this->cached_props[$request_id]['data'];
				return $this->cached_props[$request_id]['status'] != 401;
			}
			else if (! $sorted && isset($this->cached_props[$url.'//'.$scope.'//1']) && $this->cached_props[$url.'//'.$scope.'//1'])
			{
if (DEBUG_CACHE) echo 'Get propetries: Cache hit : cache id: '.$request_id.'<br />';
				$props = $this->cached_props[$url.'//'.$scope.'//1']['data'];
				return $this->cached_props[$request_id]['status'] != 401;
			}
if (DEBUG_CACHE) 
{
	echo ' <b> get_properties: Cache miss </b>: cache id: '.$request_id. '<br />';
/*	echo " cache:<pre>";
	print_r($this->cached_props);
	echo '</pre>';*/
}
	

			if(($ret = $this->propfind($url,$scope,$is_dir)) != 207)
			{
				$this->cached_props[$request_id]['status'] = $ret;
				//_debug_array('You are not supposed to see this message');
				//_debug_array('Ret : '.$ret.'<br />');
				//The following condition should never occurs
				if($ret == 404 || $ret == 401 || ($ret = $this->propfind($url.'/',$scope)) != 207)
				{
					//_debug_array('Returning empty array :/ for '.$url);
					$this->cached_props[$request_id]['status'] = $ret;
					$this->cached_props[$request_id]['data'] = false;
					$props = array();
					return $ret != 401;
				}
			}
			$this->cached_props[$request_id]['status'] = $ret;
			$xml_result=$this->http_client->getBody();
			if ( !is_object($this->dav_processor) )
			{
				$this->dav_processor = new dav_processor();
			}
			$dav_processor = $this->dav_processor;
			$dav_processor->parse_tree($xml_result);
			$result_array = $dav_processor->tree;
			$result = array();
			foreach($result_array as $name=>$item) {
/*				_debug_array("**".$name."**");*/
				$name = $this->decodeurl($name);
				$newitem = array();
				//Get any extra properties that may share the vfs name
				foreach ($this->attributes as $num=>$vfs_name)
				{
					if (isset($item[$vfs_name]) && $item[$vfs_name])
					{
						if(is_string($item[$vfs_name]))
						{
							$newitem[$vfs_name] = utf8_decode($item[$vfs_name]);
						}
						else
						{
							$newitem[$vfs_name] = $item[$vfs_name];
						}
					}
				}

				//Map some DAV properties onto VFS ones.
				foreach ($this->vfs_property_map as $dav_name=>$vfs_name)
				{
					if (isset($item[$dav_name]) && $item[$dav_name])
					{
						if(is_string($item[$dav_name]))
						{
							$newitem[$vfs_name] = utf8_decode($item[$dav_name]);
						}
						else
						{
							$newitem[$vfs_name] = $item[$dav_name];
						}
					}
				}

				$newitem['name'] = $item['name'];
				if ((isset($item['getcontenttype']) && $item['getcontenttype'] == 'httpd/unix-directory') || (isset($item['resourcetype']) && isset($item['resourcetype']['collection']) && is_array($item['resourcetype']['collection'])) )
				{
					$newitem['is_dir'] = 1;
					$newitem['mime_type']='Directory';
					$newitem['size'] = 4096;
					$this->cached_props[$name.'//0//1']['data'] = array($name=>$newitem);
					$this->cached_props[$name.'//0//1']['status'] = 207;
				}
				else
				{
					if ( !isset($item['getcontenttype']) && ! isset($newitem['mime_type']) )
					{
						$newitem['mime_type'] = 'application/octet-stream';
					}
					$this->cached_props[$name.'//1//1']['data'] = array($name => $newitem);
					$this->cached_props[$name.'//0//1']['status'] = 207;
					$this->cached_props[$name.'//0//1']['data'] = array($name => $newitem);
					$this->cached_props[$name.'//1//1']['status'] = 207;
				}
				//for manage version
				if(isset($item['version-name']))
				{
					$newitem['version']=$item['version-name'];
				}
				$this->debug('<br><br>properties:<br>');
				$this->debug($newitem);
				$result[$name]=$newitem;
			}
			if ($sorted && is_array($result))
			{
				ksort($result);
			}
			$this->cached_props[$request_id]['data'] = $result;
			$props = $result;
			return $this->cached_props[$request_id]['status'] != 401;
		}
		
		function get($uri)
		{
			$uri = $this->encodeurl($uri);
			return $this->http_client->Get($uri);
		}

		/**
		* Get body content
		*
		* @return string
		* @internal Invoke it after a Get() call for instance, to retrieve the response
		*/
		function get_body()
		{
			return $this->http_client->getBody();
		}

		/**
		* Return the response headers
		*
		* @return array Headers received from server in the form headername => value
		* @internal To be called after a Get() or Head() call
		*/
		function get_headers()
		{
			return $this->http_client->getHeaders();
		}

		/**
		* Put is the method to sending a file on the server.
		*
		* @param string $uri The location of the file on the server. dont forget the heading "/"
		* @param string $data The content of the file. Binary content accepted
		* @param string $token
		* @return string Response status code 201 (Created) if ok
		*/
		function put($uri, $data, $token='')
		{
		$uri = $this->encodeurl($uri);
if (DEBUG_CACHE) echo '<b>cache cleared</b>';
		$this->cached_props = array();
if (DEBUG_CACHEPROP) echo '<b>uncached propfind ('.$uri.')</b>';
		unset($this->cached_propfind[$uri]);
		if (strlen($token)) 
		{
			$this->http_client->addHeader('If', '<'.$uri.'>'.' (<'.$token.'>)');
		}

		$result = $this->http_client->Put($uri, $data);
		$this->http_client->removeHeader('If');
		return $result;
		}
		
		/**
		* Copy a file -allready on the server- into a new location
		*
		* @param string $srcUri the current file location on the server. dont forget the heading "/"
		* @param string $destUri the destination location on the server. this is *not* a full URL
		* @param boolean $overwrite boolean - true to overwrite an existing destination - overwrite by default
		* @param integer $scope
		* @param string $token
		* @return Returns the HTTP status code
		* @internal Returns response status code 204 (Unchanged) if ok
		*/
		function copy( $srcUri, $destUri, $overwrite=true, $scope=0, $token='')
		{
			$srcUri = $this->encodeurl($srcUri);
			$destUri = $this->encodeurl($destUri);
if (DEBUG_CACHE) echo '<b>cp cache cleared</b>';
			$this->cached_props = array();
if ( DEBUG_CACHEPROP) echo '<b>cp propfind unseted</b><br />';
			$this->delete_uri_in_cache($destUri);

			if (strlen($token)) 
			{
				$this->http_client->addHeader('If', '<'.$srcUri.'>'.' (<'.$token.'>)');
			}
			$result = $this->http_client->Copy( $srcUri, $destUri, $overwrite, $scope);
			$this->http_client->removeHeader('If');
			if ( $result == 204 )
			{
if ( DEBUG_CACHEPROP) echo '<b>cp propfind setted</b><br />';
				$this->cached_propfind[$destUri] = 207;
			}
			return $result;
		}

		/**
		* Moves a WEBDAV resource on the server
		*
		* @param string $srcUri The current file location on the server. Dont forget the heading "/"
		* @param string $destUri The destination location on the server. This is *not* a full URL
		* @param boolean $overwrite True to overwrite an existing destination (default is yes)
		* @param integer $scope
		* @param string $token
		* @return string HTTP status code - Response status code 204 (Unchanged) if ok
		*/
		function move( $srcUri, $destUri, $overwrite=true, $scope=0, $token='' )
		{
			$srcUri = $this->encodeurl($srcUri);
			$destUri = $this->encodeurl($destUri);
if (DEBUG_CACHE) echo '<b>cache cleared</b>';
			$this->cached_props = array();
if (DEBUG_CACHEPROP) _debug_array('cache prop cleared');
			$this->delete_uri_in_cache($srcUri);
			$this->delete_uri_in_cache($destUri);
			if (strlen($token)) 
			{
				$this->http_client->addHeader('If', '<'.$srcUri.'>'.' (<'.$token.'>)');
			}
			$result = $this->http_client->Move( $srcUri, $destUri, $overwrite, $scope);
			$this->http_client->removeHeader('If');
			return $result;
		}

		/**
		* Deletes a WEBDAV resource
		*
		* @param string $uri The URI we are deleting
		* @param integer $scope
		* @param string $token
		* @return string HTTP status code - response status code 204 (Unchanged) if ok
		*/
		function delete( $uri, $scope=0, $token='')
		{
			$uri = $this->encodeurl($uri);
if (DEBUG_CACHE) echo '<b>cache cleared</b>';
			$this->cached_props = array();
if (DEBUG_CACHEPROP) _debug_array('cache prop cleared');
			$this->delete_uri_in_cache($uri);
			if (strlen($token)) 
			{
				$this->http_client->addHeader('If', '<'.$uri.'>'.' (<'.$token.'>)');
			}
			
			$result = $this->http_client->Delete( $uri, $scope);
			$this->http_client->removeHeader('If');
			return $result;
		}
		
		/**
		* Creates a WEBDAV collection (AKA a directory)
		*
		* @param string $uri The URI to create
		* @param string $token
		* @return HTTP status code
		*/
		function mkcol( $uri, $token='' )
		{
			$uri = $this->encodeurl($uri);
if (DEBUG_CACHE) echo '<b>mkcol : cache cleared</b><br />';
			$this->cached_props = array();
if (DEBUG_CACHEPROP) _debug_array('mkcol : cache prop setted <br />');
			$this->delete_uri_in_cache($uri);
			if (strlen($token)) 
			{
				$this->http_client->addHeader('If', '<'.$uri.'>'.' (<'.$token.'>)');
			}
			$ret = $this->http_client->MkCol( $uri );
			if ( $ret == 201 )
			{
				$this->cached_propfind[$uri.'/'] = 207;
			}
			$this->http_client->removeHeader('If');
			return $ret;
		}

		/**
		* Queries WEBDAV properties
		*
		* @param string $uri URI of resource whose properties we are changing
		* @param integer $scope Specifies how "deep" to search (0=just this file/dir 1=subfiles/dirs etc)
		* @param boolean $is_dir
		* @return Returns the HTTP status code
		* @internal To get the result XML call get_body()
		*/
		function propfind( $uri, $scope=0, $is_dir = False )
		{
			$uri = $this->encodeurl($uri);
//if ( DEBUG_CACHEPROP ) { _debug_array($this->cached_propfind); }
			if ( isset($this->cached_propfind[$uri]) )
			{
if ( DEBUG_CACHEPROP ) _debug_array("Cache hit File/Dir! : $uri");
				if ( ($ret = $this->cached_propfind[$uri]) == 404 || $ret == 401)
					return $ret;
				return $this->http_client->PropFind( $uri, $scope);
			}
			elseif ( isset($this->cached_propfind[$uri.'/']) )
			{
if ( DEBUG_CACHEPROP ) _debug_array("Cache hit Dir ! : $uri/");
				if ( ($ret = $this->cached_propfind[$uri.'/']) == 404 || $ret == 401)
					return $ret;
				return $this->http_client->PropFind( $uri.'/', $scope);
			}
			else
			{
if ( DEBUG_CACHEPROP ) _debug_array("Cache Miss! : $uri");
				if ( $is_dir && !ereg('#/$#',$uri) )
				{
if ( DEBUG_CACHEPROP ) _debug_array("Cache dir (is_dir)! : $uri/");
					$this->cached_propfind[$uri.'/'] = $this->http_client->PropFind( $uri.'/', $scope);
					return $this->cached_propfind[$uri.'/'];
				}
				else
				{
					if ( 301 == 
						($ret = $this->http_client->PropFind( $uri, $scope) ) )
						{
if ( DEBUG_CACHEPROP ) _debug_array("Cache dir! : $uri/");
							$this->cached_propfind[$uri.'/'] = $this->http_client->PropFind( $uri.'/',$scope);
							return $this->cached_propfind[$uri.'/'];
						}
if ( DEBUG_CACHEPROP ) _debug_array("Cache File/Dir! : $uri");
						$this->cached_propfind[$uri] = $ret;
						return $this->cached_propfind[$uri];
				}
			}
		}

		/**
		* Sets DAV properties
		*
		* @param string $uri URI of resource whose properties we are changing
		* @param array $attributes Attribute,value pairs
		* @param string $namespaces Extra namespace definitions that apply to the properties
		* @param string $token
		* @param boolean $is_dir
		* @return string HTTP status code
		* @internal To make DAV properties useful it helps to use a well established XML dialect such as the "Dublin Core"
		*/
		function proppatch($uri, $attributes,  $namespaces='', $token='',$is_dir = False)
		{
			$uri = $this->encodeurl($uri);
if (DEBUG_CACHE) echo '<b>proppatch: cache cleared</b><br />';
			$this->cached_props = array();
			if ( isset($this->cached_propfind[$uri.'/']) )
			{
				$is_dir = true;
			}
			if (strlen($token)) 
			{
				//XXX if $uri is without an ending / and is a directory ... what to do ???
				$this->http_client->addHeader('If', '<'.$uri.'>'.' (<'.$token.'>)');
			}
			// Begin evil nastiness
			$davxml = '<?xml version="1.0" encoding="utf-8" ?>
<D:propertyupdate xmlns:D="DAV:"';

			if ($namespaces)
			{
				$davxml .= ' ' . $namespaces;
			}
			$davxml .= ' >';
			foreach ($attributes as $name => $value)
			{
				$davxml .= '
  <D:set>
	<D:prop>
	   <'.$name.'>'.utf8_encode(htmlspecialchars($value)).'</'.$name.'>
	</D:prop>
  </D:set>
';
			}
			$davxml .= '
</D:propertyupdate>';

			if (DEBUG_DAV_XML) {
				echo '<b>proppatch: send</b><pre>'.htmlentities($davxml).'</pre>';
			}
			$this->http_client->requestBody = $davxml;
			//Ok if we know that this is a dir add a / if needed
			if ( $is_dir && !ereg('#./$#',$uri) )
			{
				if( $this->http_client->sendCommand( 'PROPPATCH '.$uri.'/ HTTP/1.1' ) )
				{
					$this->http_client->processReply();
				}
			}
			else
			{
				//Ok the uri should be ok
				if ( $this->http_client->sendCommand( 'PROPPATCH '.$uri.' HTTP/1.1' ) )
				{
					$this->http_client->processReply();
					if ( $this->http_client->reply == '301' )
					{
						//In fact the file is a directory !
						$this->http_client->requestBody = $davxml;
						if ( $this->http_client->sendCommand( 'PROPPATCH '.$uri.'/ HTTP/1.1' ) )
						{
							$this->http_client->processReply();
						}
					}
				}
			}
			if (DEBUG_DAV_XML) {
				echo '<b>proppatch: Recieve</b><pre>'.htmlentities($this->http_client->getBody()).'</pre>';
			}
			$this->http_client->removeHeader('If');
			return $this->http_client->reply;
		}
		
		/**
		* Unlocks a locked resource on the DAV server
		*
		* @param string $uri URI of the resource we are unlocking
		* @param string $token A 'token' for the lock (to get the token, do a propfind)
		* @return boolean True if successful
		* @internal Not all DAV servers support locking (its in the RFC, but many common. DAV servers only implement "DAV class 1" (no locking)
		*/
		function unlock($uri, $token)
		{
			if ( empty($token) )
			{
				return False;
			}

			$uri = $this->encodeurl($uri);

if (DEBUG_CACHE) echo '<b>cache cleared</b>';
			$this->cached_props = array();
			
			//Use the propfind cache to know where to look at
			if ( isset($this->cached_propfind[$uri]) )
			{
				if ( ($ret = $this->cached_propfind[$uri]) == 207 )
				{
					$ret = $this->http_client->Unlock($uri,$token);
				}
			}
			elseif ( isset($this->cached_propfind["{$uri}/"]) )
			{
				if ( ($ret = $this->cached_propfind["{$uri}/"]) == 207 )
				{
					$ret = $this->http_client->Unlock("{$uri}/",$token);
				}
			}
			else
			{
				if ( ($ret = $this->http_client->Unlock($uri,$token)) == 204 )
				{
					$this->cached_propfind[$uri] = 207;
				}
				elseif( $ret == 301 && ($ret = $this->http_client->Unlock("{$uri}/",$token)) == 204 )
				{
					$this->cached_propfind["{$uri}/"] = 207;
				}
			}

			switch($ret)
			{
				case 204:
					return True;
				default:
					$this->str_dav_error = $this->http_client->getBody();
					return False;
			}
		}

		/**
		* Locks a resource on the DAV server
		*
		* @param string $uri URI of the resource we are locking
		* @param string $owner The 'owner' information for the lock (purely informative)
		* @param integer $depth The depth to which we lock collections
		* @param string $timeout
		* @return true If successfull
		* @internal Not all DAV servers support locking (its in the RFC, but many common DAV servers only implement "DAV class 1" (no locking)
		*/	
		function lock($uri, $owner, $depth=0, $timeout='Infinite')
		{
			$uri = $this->encodeurl($uri);
if (DEBUG_CACHE) echo '<b>cache cleared</b>';
			$this->cached_props = array();

			$this->http_client->addHeader('Depth', $depth);
			if ( (strtolower(trim($timeout)) != 'infinite'))
			{
				$timeout = 'Second-'.intval($timeout);
			}
			$this->http_client->addHeader('Timeout', $timeout);
			//Try to use the cache
			if ( isset($this->cached_propfind[$uri]) )
			{
				if ( ($ret = $this->cached_propfind[$uri]) == 207 )
				{
					$ret = $this->http_client->Lock($uri, 'exclusive', 'write', $owner);
				}
			}
			elseif ( isset($this->cached_propfind["{$uri}/"]) )
			{
				if ( ($ret = $this->cached_propfind["{$uri}/"]) == 207 )
				{
					$ret = $this->http_client->Lock("{$uri}/", 'exclusive', 'write', $owner);
				}
			}
			else
			{
				//Should never occurs, but just in case !
				if ( ($ret = $this->http_client->Lock($uri, 'exclusive', 'write', $owner)) == 200)
				{
					$this->cached_propfind[$uri] = 207;
				}
				elseif ( $ret == 301 && ($ret = $this->http_client->Lock("{$uri}/", 'exclusive', 'write', $owner)) == 200)
				{
					$this->cached_propfind["{$uri}/"] = 207;
				}
			}

			switch ($ret)
			{
				case 200:
					//we could process the answer to get the lock token but ...
					return True;
				case 412:
				case 423:
					$this->str_dav_error = $this->http_client->getBody();
					return False;
				case 404:
				case 401:
				default:
					$this->str_dav_error = $this->http_client->getBody();
					return False;
			}
		}

		/**
		* Ddetermines the optional HTTP features supported by a server
		*
		* @param string $uri URI of the resource we are seeking options for (or * for the whole server)
		* @return array|boolean Option values or false
		* @internal Interesting options include "ACCESS" (whether you can read a file) and DAV (DAV features)
		*/
		function options($uri)
		{
			$uri = $this->encodeurl($uri);
			if( $this->http_client->sendCommand( 'OPTIONS '.$uri.' HTTP/1.1' ) == '200' )
			{
				$this->http_client->processReply();
				$headers = $this->http_client->getHeaders();
				return $headers;
			}
			else
			{
				return False;
			}
		}

		/**
		* Determines the features of a DAV server
		*
		* @param string $uri URI of resource whose properties we are changing
		* @return array Option values or NULL
		* @internal Likely return codes include NULL (this isnt a dav server!), 1 (This is a dav server, supporting all standard DAV features except locking) 2, (additionally supports locking (should also return 1)) and  'version-control' (this server supports versioning extensions for this resource)
		*/		
		function dav_features($uri)
		{
			$uri = $this->encodeurl($uri);
			$options = $this->options($uri);
			$dav_options = $options['DAV'];
			if ($dav_options)
			{
				$features=explode(',', $dav_options);
			}
			else
			{
				$features = NULL;
			}
			return $features;
		}
/* RFC 3253 DeltaV versioning extensions 
   These are 100% untested, and almost certainly dont work yet...
   eventually they will be made to work with subversion...
*/
	
		/**
		* Report is a kind of extended PROPFIND - it queries properties accros versions etc.
		*
		* @param string $uri URI of resource whose properties we are changing
		* @param string $report The type of report desired eg DAV:version-tree, DAV:expand-property etc (see http://greenbytes.de/tech/webdav/rfc3253.html#METHOD_REPORT)
		* @param array $properties
		* @param string $namespaces Any extra XML namespaces needed for the specified properties
		* @return array Option values
		* @internal From the relevent RFC: 	"A REPORT request is an extensible mechanism for obtaining information about a resource. Unlike a resource property, which has a single value, the value of a report can depend on additional information specified in the REPORT request body and in the REPORT request headers."
		*/		
		function report($uri, $report, $properties,  $namespaces='')
		{
			$uri = $this->encodeurl($uri);
			$davxml = '<?xml version="1.0" encoding="utf-8" ?>
<D:'.$report . 'xmlns:D="DAV:"';
			if ($namespaces)
			{
				$davxml .= ' ' . $namespaces;
			}
			$davxml .= ' >
	<D:prop>';
			foreach($properties as $property) 
			{
				$davxml .= '<'.$property.'/>\n';
			}
			$davxml .= '\t<D:/prop>\n<D:/'.$report.'>';		
			if (DEBUG_DAV_XML) {
				echo '<b>send</b><pre>'.htmlentities($davxml).'</pre>';
			}
			$this->http_client->requestBody = $davxml;
			if( $this->http_client->sendCommand( 'REPORT '.$uri.' HTTP/1.1' ) )
			{
				$this->http_client->processReply();
			}

			if (DEBUG_DAV_XML) {
				echo '<b>Recieve</b><pre>'.htmlentities($this->http_client->getBody()).'</pre>';
			}
			return $this->http_client->reply;		
		}
		
		/**
		* Save the override_locks array in session file
		*/	
		function propfind_save_session()
		{
			// Save the overrided locks in the session
			$app = 'phpgwapi' ; //$GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->session = $GLOBALS['phpgw']->session->appsession ('dav_props',$app, base64_encode(serialize($this->cached_propfind)));
		}	

		/*
		* Restore the override_locks array from session, use only in vfs_shared
		*/
		function propfind_restore_session()
		{
			//Reload the overriden_locks
			$app = 'phpgwapi' ; //$GLOBALS['phpgw_info']['flags']['currentapp'];
			$session_data = base64_decode($GLOBALS['phpgw']->session->appsession ('dav_props',$app));
			if ($session_data)
			{
				$this->cached_propfind = unserialize($session_data);
			}
			else
			{
				$this->cached_propfind = array();
			}
		}
		
		/**
		* Some cache deletion
		*
		* @private
		*/
		function delete_uri_in_cache($uri)
		{
			unset($this->cached_propfind[$uri]);
			unset($this->cached_propfind[$uri.'/']);
			unset($this->cached_propfind[$this->server.$uri]);
			unset($this->cached_propfind[$this->server.$uri.'/']);
			$suri = substr($uri,strlen($this->server));
			unset($this->cached_propfind[$suri]);
			unset($this->cached_propfind[$suri.'/']);
		}
	}

