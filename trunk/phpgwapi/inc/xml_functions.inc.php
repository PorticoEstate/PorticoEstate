<?php
	/**
	* XML RPC
	* @author Edd Dumbill <edd@usefulinc.com>
	* @copyright Copyright (C) 1999-2001 Edd Dumbill
	* @copyright Portions Copyright (C) 2004 - 2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

	/**
	* Include XMLRPC Response class
	*/
	require_once PHPGW_API_INC . '/class.xmlrpcresp.inc.php';
	/**
	* Include XMLRPC values class
	*/
	require_once PHPGW_API_INC . '/class.xmlrpcval.inc.php';
	/**
	* Include XMLRPC message class
	*/
	require_once PHPGW_API_INC . '/class.xmlrpcmsg.inc.php';

// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions
// are met:
//
//    * Redistributions of source code must retain the above copyright
//      notice, this list of conditions and the following disclaimer.
//
//    * Redistributions in binary form must reproduce the above
//      copyright notice, this list of conditions and the following
//      disclaimer in the documentation and/or other materials provided
//      with the distribution.
//
//    * Neither the name of the "XML-RPC for PHP" nor the names of its
//      contributors may be used to endorse or promote products derived
//      from this software without specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
// "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
// LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
// FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
// REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
// INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
// (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
// SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
// HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
// STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
// ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
// OF THE POSSIBILITY OF SUCH DAMAGE.

	if(!function_exists('xml_parser_create'))
	{
		// For PHP 4 onward, XML functionality is always compiled-in on windows:
		// no more need to dl-open it. It might have been compiled out on *nix...
		if(strtoupper(substr(PHP_OS, 0, 3) != 'WIN'))
		{
			dl('xml.so');
		}
	}


	// G. Giunta 2005/01/29: declare global these variables,
	// so that xmlrpc.inc will work even if included from within a function
	// Milosch: 2005/08/07 - explicitly request these via $GLOBALS where used.
	$GLOBALS['xmlrpcI4']='i4';
	$GLOBALS['xmlrpcInt']='int';
	$GLOBALS['xmlrpcBoolean']='boolean';
	$GLOBALS['xmlrpcDouble']='double';
	$GLOBALS['xmlrpcString']='string';
	$GLOBALS['xmlrpcDateTime']='dateTime.iso8601';
	$GLOBALS['xmlrpcBase64']='base64';
	$GLOBALS['xmlrpcArray']='array';
	$GLOBALS['xmlrpcStruct']='struct';
	$GLOBALS['xmlrpcValue']='undefined';

	$GLOBALS['xmlrpcTypes']=array(
		$GLOBALS['xmlrpcI4']       => 1,
		$GLOBALS['xmlrpcInt']      => 1,
		$GLOBALS['xmlrpcBoolean']  => 1,
		$GLOBALS['xmlrpcString']   => 1,
		$GLOBALS['xmlrpcDouble']   => 1,
		$GLOBALS['xmlrpcDateTime'] => 1,
		$GLOBALS['xmlrpcBase64']   => 1,
		$GLOBALS['xmlrpcArray']    => 2,
		$GLOBALS['xmlrpcStruct']   => 3
	);

	$GLOBALS['xmlrpc_valid_parents'] = array(
		'VALUE' => array('MEMBER', 'DATA', 'PARAM', 'FAULT'),
		'BOOLEAN' => array('VALUE'),
		'I4' => array('VALUE'),
		'INT' => array('VALUE'),
		'STRING' => array('VALUE'),
		'DOUBLE' => array('VALUE'),
		'DATETIME.ISO8601' => array('VALUE'),
		'BASE64' => array('VALUE'),
		'MEMBER' => array('STRUCT'),
		'NAME' => array('MEMBER'),
		'DATA' => array('ARRAY'),
		'ARRAY' => array('VALUE'),
		'STRUCT' => array('VALUE'),
		'PARAM' => array('PARAMS'),
		'METHODNAME' => array('METHODCALL'),
		'PARAMS' => array('METHODCALL', 'METHODRESPONSE'),
		'FAULT' => array('METHODRESPONSE'),
		'NIL' => array('VALUE') // only used when extension activated
	);

	// define extra types for supporting NULL (useful for json or <NIL/>)
	$GLOBALS['xmlrpcNull']='null';
	$GLOBALS['xmlrpcTypes']['null']=1;

	// Not in use anymore since 2.0. Shall we remove it?
	/// @deprecated
	$GLOBALS['xmlEntities']=array(
		'amp'  => '&',
		'quot' => '"',
		'lt'   => '<',
		'gt'   => '>',
		'apos' => "'"
	);

	// tables used for transcoding different charsets into us-ascii xml

	$GLOBALS['xml_iso88591_Entities']=array();
	$GLOBALS['xml_iso88591_Entities']['in'] = array();
	$GLOBALS['xml_iso88591_Entities']['out'] = array();
	for ($i = 0; $i < 32; $i++)
	{
		$GLOBALS['xml_iso88591_Entities']['in'][] = chr($i);
		$GLOBALS['xml_iso88591_Entities']['out'][] = '&#'.$i.';';
	}
	for ($i = 160; $i < 256; $i++)
	{
		$GLOBALS['xml_iso88591_Entities']['in'][] = chr($i);
		$GLOBALS['xml_iso88591_Entities']['out'][] = '&#'.$i.';';
	}

	/// @todo add to iso table the characters from cp_1252 range, i.e. 128 to 159.
	/// These will NOT be present in true ISO-8859-1, but will save the unwary
	/// windows user from sending junk.
/*
$cp1252_to_xmlent =
  array(
   '\x80'=>'&#x20AC;', '\x81'=>'?', '\x82'=>'&#x201A;', '\x83'=>'&#x0192;',
   '\x84'=>'&#x201E;', '\x85'=>'&#x2026;', '\x86'=>'&#x2020;', \x87'=>'&#x2021;',
   '\x88'=>'&#x02C6;', '\x89'=>'&#x2030;', '\x8A'=>'&#x0160;', '\x8B'=>'&#x2039;',
   '\x8C'=>'&#x0152;', '\x8D'=>'?', '\x8E'=>'&#x017D;', '\x8F'=>'?',
   '\x90'=>'?', '\x91'=>'&#x2018;', '\x92'=>'&#x2019;', '\x93'=>'&#x201C;',
   '\x94'=>'&#x201D;', '\x95'=>'&#x2022;', '\x96'=>'&#x2013;', '\x97'=>'&#x2014;',
   '\x98'=>'&#x02DC;', '\x99'=>'&#x2122;', '\x9A'=>'&#x0161;', '\x9B'=>'&#x203A;',
   '\x9C'=>'&#x0153;', '\x9D'=>'?', '\x9E'=>'&#x017E;', '\x9F'=>'&#x0178;'
  );
*/

	$GLOBALS['xmlrpcerr']['unknown_method']=1;
	$GLOBALS['xmlrpcstr']['unknown_method']='Unknown method';
	$GLOBALS['xmlrpcerr']['invalid_return']=2;
	$GLOBALS['xmlrpcstr']['invalid_return']='Invalid return payload: enable debugging to examine incoming payload';
	$GLOBALS['xmlrpcerr']['incorrect_params']=3;
	$GLOBALS['xmlrpcstr']['incorrect_params']='Incorrect parameters passed to method';
	$GLOBALS['xmlrpcerr']['introspect_unknown']=4;
	$GLOBALS['xmlrpcstr']['introspect_unknown']="Can't introspect: method unknown";
	$GLOBALS['xmlrpcerr']['http_error']=5;
	$GLOBALS['xmlrpcstr']['http_error']="Didn't receive 200 OK from remote server.";
	$GLOBALS['xmlrpcerr']['no_data']=6;
	$GLOBALS['xmlrpcstr']['no_data']='No data received from server.';
	$GLOBALS['xmlrpcerr']['no_ssl']=7;
	$GLOBALS['xmlrpcstr']['no_ssl']='No SSL support compiled in.';
	$GLOBALS['xmlrpcerr']['curl_fail']=8;
	$GLOBALS['xmlrpcstr']['curl_fail']='CURL error';
	$GLOBALS['xmlrpcerr']['invalid_request']=15;
	$GLOBALS['xmlrpcstr']['invalid_request']='Invalid request payload';
	$GLOBALS['xmlrpcerr']['no_curl']=16;
	$GLOBALS['xmlrpcstr']['no_curl']='No CURL support compiled in.';
	$GLOBALS['xmlrpcerr']['server_error']=17;
	$GLOBALS['xmlrpcstr']['server_error']='Internal server error';
	$GLOBALS['xmlrpcerr']['multicall_error']=18;
	$GLOBALS['xmlrpcstr']['multicall_error']='Received from server invalid multicall response';

	$GLOBALS['xmlrpcerr']['multicall_notstruct'] = 9;
	$GLOBALS['xmlrpcstr']['multicall_notstruct'] = 'system.multicall expected struct';
	$GLOBALS['xmlrpcerr']['multicall_nomethod']  = 10;
	$GLOBALS['xmlrpcstr']['multicall_nomethod']  = 'missing methodName';
	$GLOBALS['xmlrpcerr']['multicall_notstring'] = 11;
	$GLOBALS['xmlrpcstr']['multicall_notstring'] = 'methodName is not a string';
	$GLOBALS['xmlrpcerr']['multicall_recursion'] = 12;
	$GLOBALS['xmlrpcstr']['multicall_recursion'] = 'recursive system.multicall forbidden';
	$GLOBALS['xmlrpcerr']['multicall_noparams']  = 13;
	$GLOBALS['xmlrpcstr']['multicall_noparams']  = 'missing params';
	$GLOBALS['xmlrpcerr']['multicall_notarray']  = 14;
	$GLOBALS['xmlrpcstr']['multicall_notarray']  = 'params is not an array';

	$GLOBALS['xmlrpcerr']['cannot_decompress']=103;
	$GLOBALS['xmlrpcstr']['cannot_decompress']='Received from server compressed HTTP and cannot decompress';
	$GLOBALS['xmlrpcerr']['decompress_fail']=104;
	$GLOBALS['xmlrpcstr']['decompress_fail']='Received from server invalid compressed HTTP';
	$GLOBALS['xmlrpcerr']['dechunk_fail']=105;
	$GLOBALS['xmlrpcstr']['dechunk_fail']='Received from server invalid chunked HTTP';
	$GLOBALS['xmlrpcerr']['server_cannot_decompress']=106;
	$GLOBALS['xmlrpcstr']['server_cannot_decompress']='Received from client compressed HTTP request and cannot decompress';
	$GLOBALS['xmlrpcerr']['server_decompress_fail']=107;
	$GLOBALS['xmlrpcstr']['server_decompress_fail']='Received from client invalid compressed HTTP request';

	// The charset encoding used by the server for received messages and
	// by the client for received responses when received charset cannot be determined
	// or is not supported
	$GLOBALS['xmlrpc_defencoding']='UTF-8';

	// The encoding used internally by PHP.
	// String values received as xml will be converted to this, and php strings will be converted to xml
	// as if having been coded with this
	$GLOBALS['xmlrpc_internalencoding']='ISO-8859-1';

	$GLOBALS['xmlrpcName']='XML-RPC for PHP';
	$GLOBALS['xmlrpcVersion']='2.1';

	// let user errors start at 800
	$GLOBALS['xmlrpcerruser']=800;
	// let XML parse errors start at 100
	$GLOBALS['xmlrpcerrxml']=100;

	// formulate backslashes for escaping regexp
	// Not in use anymore since 2.0. Shall we remove it?
	/// @deprecated
	$GLOBALS['xmlrpc_backslash']=chr(92).chr(92);

	// used to store state during parsing
	// quick explanation of components:
	//   ac - used to accumulate values
	//   isf - used to indicate a parsing fault (2) or xmlrpcresp fault (1)
	//   isf_reason - used for storing xmlrpcresp fault string
	//   lv - used to indicate "looking for a value": implements
	//        the logic to allow values with no types to be strings
	//   params - used to store parameters in method calls
	//   method - used to store method name
	//   stack - array with genealogy of xml elements names:
	//           used to validate nesting of xmlrpc elements
	$GLOBALS['_xh']=null;

	/**
	* Convert a string to the correct XML representation in a target charset
	* To help correct communication of non-ascii chars inside strings, regardless
	* of the charset used when sending requests, parsing them, sending responses
	* and parsing responses, an option is to convert all non-ascii chars present in the message
	* into their equivalent 'charset entity'. Charset entities enumerated this way
	* are independent of the charset encoding used to transmit them, and all XML
	* parsers are bound to understand them.
	* Note that in the std case we are not sending a charset encoding mime type
	* along with http headers, so we are bound by RFC 3023 to emit strict us-ascii.
	*
	* @todo do a bit of basic benchmarking (strtr vs. str_replace)
	* @todo	make usage of iconv() or recode_string() or mb_string() where available
	*/
	function xmlrpc_encode_entitites($data, $src_encoding='', $dest_encoding='')
	{
		if ($src_encoding == '')
		{
			// lame, but we know no better...
			$src_encoding = $GLOBALS['xmlrpc_internalencoding'];
		}

		switch(strtoupper($src_encoding.'_'.$dest_encoding))
		{
			case 'ISO-8859-1_':
			case 'ISO-8859-1_US-ASCII':
				$escaped_data = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $data);
				$escaped_data = str_replace($GLOBALS['xml_iso88591_Entities']['in'], $GLOBALS['xml_iso88591_Entities']['out'], $escaped_data);
				break;
			case 'ISO-8859-1_UTF-8':
				$escaped_data = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $data);
				$escaped_data = utf8_encode($escaped_data);
				break;
			case 'ISO-8859-1_ISO-8859-1':
			case 'US-ASCII_US-ASCII':
			case 'US-ASCII_UTF-8':
			case 'US-ASCII_':
			case 'US-ASCII_ISO-8859-1':
			case 'UTF-8_UTF-8':
				$escaped_data = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $data);
				break;
			case 'UTF-8_':
			case 'UTF-8_US-ASCII':
			case 'UTF-8_ISO-8859-1':
	// NB: this will choke on invalid UTF-8, going most likely beyond EOF
	$escaped_data = '';
	// be kind to users creating string xmlrpcvals out of different php types
	$data = (string) $data;
	$ns = strlen ($data);
	for ($nn = 0; $nn < $ns; $nn++)
	{
		$ch = $data[$nn];
		$ii = ord($ch);
		//1 7 0bbbbbbb (127)
		if ($ii < 128)
		{
			/// @todo shall we replace this with a (supposedly) faster str_replace?
			switch($ii){
				case 34:
					$escaped_data .= '&quot;';
					break;
				case 38:
					$escaped_data .= '&amp;';
					break;
				case 39:
					$escaped_data .= '&apos;';
					break;
				case 60:
					$escaped_data .= '&lt;';
					break;
				case 62:
					$escaped_data .= '&gt;';
					break;
				default:
					$escaped_data .= $ch;
			} // switch
		}
		//2 11 110bbbbb 10bbbbbb (2047)
		else if ($ii>>5 == 6)
		{
			$b1 = ($ii & 31);
			$ii = ord($data[$nn+1]);
			$b2 = ($ii & 63);
			$ii = ($b1 * 64) + $b2;
			$ent = sprintf ('&#%d;', $ii);
			$escaped_data .= $ent;
		}
		//3 16 1110bbbb 10bbbbbb 10bbbbbb
		else if ($ii>>4 == 14)
		{
			$b1 = ($ii & 31);
			$ii = ord($data[$nn+1]);
			$b2 = ($ii & 63);
			$ii = ord($data[$nn+2]);
			$b3 = ($ii & 63);
			$ii = ((($b1 * 64) + $b2) * 64) + $b3;
			$ent = sprintf ('&#%d;', $ii);
			$escaped_data .= $ent;
		}
		//4 21 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
		else if ($ii>>3 == 30)
		{
			$b1 = ($ii & 31);
			$ii = ord($data[$nn+1]);
			$b2 = ($ii & 63);
			$ii = ord($data[$nn+2]);
			$b3 = ($ii & 63);
			$ii = ord($data[$nn+3]);
			$b4 = ($ii & 63);
			$ii = ((((($b1 * 64) + $b2) * 64) + $b3) * 64) + $b4;
			$ent = sprintf ('&#%d;', $ii);
			$escaped_data .= $ent;
		}
	}
				break;
			default:
				$escaped_data = '';
				error_log("Converting from $src_encoding to $dest_encoding: not supported...");
		}
		return $escaped_data;
	}

	/// xml parser handler function for opening element tags
	function xmlrpc_se($parser, $name, $attrs, $accept_single_vals=false)
	{
		// if invalid xmlrpc already detected, skip all processing
		if ($GLOBALS['_xh']['isf'] < 2)
		{
			// check for correct element nesting
			// top level element can only be of 2 types
			/// @todo optimization creep: save this check into a bool variable, instead of using count() every time:
			///       there is only a single top level element in xml anyway
			if (count($GLOBALS['_xh']['stack']) == 0)
			{
				if ($name != 'METHODRESPONSE' && $name != 'METHODCALL' && (
					$name != 'VALUE' && !$accept_single_vals))
				{
					$GLOBALS['_xh']['isf'] = 2;
					$GLOBALS['_xh']['isf_reason'] = 'missing top level xmlrpc element';
					return;
				}
				else
				{
					$GLOBALS['_xh']['rt'] = strtolower($name);
				}
			}
			else
			{
				// not top level element: see if parent is OK
				$parent = end($GLOBALS['_xh']['stack']);
				if (!array_key_exists($name, $GLOBALS['xmlrpc_valid_parents']) || !in_array($parent, $GLOBALS['xmlrpc_valid_parents'][$name]))
				{
					$GLOBALS['_xh']['isf'] = 2;
					$GLOBALS['_xh']['isf_reason'] = "xmlrpc element $name cannot be child of $parent";
					return;
				}
			}

			switch($name)
			{
				// optimize for speed switch cases: most common cases first
				case 'VALUE':
					/// @todo we could check for 2 VALUE elements inside a MEMBER or PARAM element
					$GLOBALS['_xh']['vt']='value'; // indicator: no value found yet
					$GLOBALS['_xh']['ac']='';
					$GLOBALS['_xh']['lv']=1;
					$GLOBALS['_xh']['php_class']=null;
					break;
				case 'I4':
				case 'INT':
				case 'STRING':
				case 'BOOLEAN':
				case 'DOUBLE':
				case 'DATETIME.ISO8601':
				case 'BASE64':
					if ($GLOBALS['_xh']['vt']!='value')
					{
						//two data elements inside a value: an error occurred!
						$GLOBALS['_xh']['isf'] = 2;
						$GLOBALS['_xh']['isf_reason'] = "$name element following a {$GLOBALS['_xh']['vt']} element inside a single value";
						return;
					}
					$GLOBALS['_xh']['ac']=''; // reset the accumulator
					break;
				case 'STRUCT':
				case 'ARRAY':
					if ($GLOBALS['_xh']['vt']!='value')
					{
						//two data elements inside a value: an error occurred!
						$GLOBALS['_xh']['isf'] = 2;
						$GLOBALS['_xh']['isf_reason'] = "$name element following a {$GLOBALS['_xh']['vt']} element inside a single value";
						return;
					}
					// create an empty array to hold child values, and push it onto appropriate stack
					$cur_val = array();
					$cur_val['values'] = array();
					$cur_val['type'] = $name;
					// check for out-of-band information to rebuild php objs
					// and in case it is found, save it
					if (@isset($attrs['PHP_CLASS']))
					{
						$cur_val['php_class'] = $attrs['PHP_CLASS'];
					}
					$GLOBALS['_xh']['valuestack'][] = $cur_val;
					$GLOBALS['_xh']['vt']='data'; // be prepared for a data element next
					break;
				case 'DATA':
					if ($GLOBALS['_xh']['vt']!='data')
					{
						//two data elements inside a value: an error occurred!
						$GLOBALS['_xh']['isf'] = 2;
						$GLOBALS['_xh']['isf_reason'] = "found two data elements inside an array element";
						return;
					}
				case 'METHODCALL':
				case 'METHODRESPONSE':
				case 'PARAMS':
					// valid elements that add little to processing
					break;
				case 'METHODNAME':
				case 'NAME':
					/// @todo we could check for 2 NAME elements inside a MEMBER element
					$GLOBALS['_xh']['ac']='';
					break;
				case 'FAULT':
					$GLOBALS['_xh']['isf']=1;
					break;
				case 'MEMBER':
					$GLOBALS['_xh']['valuestack'][count($GLOBALS['_xh']['valuestack'])-1]['name']=''; // set member name to null, in case we do not find in the xml later on
					//$GLOBALS['_xh']['ac']='';
					// Drop trough intentionally
				case 'PARAM':
					// clear value type, so we can check later if no value has been passed for this param/member
					$GLOBALS['_xh']['vt']=null;
					break;
				case 'NIL':
					// we do not support the <NIL/> extension yet, so
					// drop through intentionally
				default:
					/// INVALID ELEMENT: RAISE ISF so that it is later recognized!!!
					$GLOBALS['_xh']['isf'] = 2;
					$GLOBALS['_xh']['isf_reason'] = "found not-xmlrpc xml element $name";
					break;
			}

			// Save current element name to stack, to validate nesting
			$GLOBALS['_xh']['stack'][] = $name;

			/// @todo optimization creep: move this inside the big switch() above
			if($name!='VALUE')
			{
				$GLOBALS['_xh']['lv']=0;
			}
		}
	}

	/// Used in decoding xml chunks that might represent single xmlrpc values
	function xmlrpc_se_any($parser, $name, $attrs)
	{
		xmlrpc_se($parser, $name, $attrs, true);
	}

	/// xml parser handler function for close element tags
	function xmlrpc_ee($parser, $name, $rebuild_xmlrpcvals = true)
	{
		if ($GLOBALS['_xh']['isf'] < 2)
		{
			// push this element name from stack
			// NB: if XML validates, correct opening/closing is guaranteed and
			// we do not have to check for $name == $curr_elem.
			// we also checked for proper nesting at start of elements...
			$curr_elem = array_pop($GLOBALS['_xh']['stack']);

			switch($name)
			{
				case 'VALUE':
					// This if() detects if no scalar was inside <VALUE></VALUE>
					if ($GLOBALS['_xh']['vt']=='value')
					{
						$GLOBALS['_xh']['value']=$GLOBALS['_xh']['ac'];
						$GLOBALS['_xh']['vt']=$GLOBALS['xmlrpcString'];
					}

					if ($rebuild_xmlrpcvals)
					{
						// build the xmlrpc val out of the data received, and substitute it
						$temp =& new xmlrpcval($GLOBALS['_xh']['value'], $GLOBALS['_xh']['vt']);
						// in case we got info about underlying php class, save it
						// in the object we're rebuilding
						if (isset($GLOBALS['_xh']['php_class']))
							$temp->_php_class = $GLOBALS['_xh']['php_class'];
						// check if we are inside an array or struct:
						// if value just built is inside an array, let's move it into array on the stack
						$vscount = count($GLOBALS['_xh']['valuestack']);
						if ($vscount && $GLOBALS['_xh']['valuestack'][$vscount-1]['type']=='ARRAY')
						{
							$GLOBALS['_xh']['valuestack'][$vscount-1]['values'][] = $temp;
						}
						else
						{
							$GLOBALS['_xh']['value'] = $temp;
						}
					}
					else
					{
						/// @todo this needs to treat correctly php-serialized objects,
						/// since std deserializing is done by php_xmlrpc_decode,
						/// which we will not be calling...
						if (isset($GLOBALS['_xh']['php_class']))
						{
						}

						// check if we are inside an array or struct:
						// if value just built is inside an array, let's move it into array on the stack
						$vscount = count($GLOBALS['_xh']['valuestack']);
						if ($vscount && $GLOBALS['_xh']['valuestack'][$vscount-1]['type']=='ARRAY')
						{
							$GLOBALS['_xh']['valuestack'][$vscount-1]['values'][] = $GLOBALS['_xh']['value'];
						}
					}
					break;
				case 'BOOLEAN':
				case 'I4':
				case 'INT':
				case 'STRING':
				case 'DOUBLE':
				case 'DATETIME.ISO8601':
				case 'BASE64':
					$GLOBALS['_xh']['vt']=strtolower($name);
        			/// @todo: optimization creep - remove the if/elseif cycle below
                    /// since the case() in which we are already did that
					if ($name=='STRING')
					{
						$GLOBALS['_xh']['value']=$GLOBALS['_xh']['ac'];
					}
					elseif ($name=='DATETIME.ISO8601')
					{
						if (!preg_match('/^[0-9]{8}T[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $GLOBALS['_xh']['ac']))
						{
							error_log('XML-RPC: invalid value received in DATETIME: '.$GLOBALS['_xh']['ac']);
						}
						$GLOBALS['_xh']['vt']=$GLOBALS['xmlrpcDateTime'];
						$GLOBALS['_xh']['value']=$GLOBALS['_xh']['ac'];
					}
					elseif ($name=='BASE64')
					{
						/// @todo check for failure of base64 decoding / catch warnings
						$GLOBALS['_xh']['value']=base64_decode($GLOBALS['_xh']['ac']);
					}
					elseif ($name=='BOOLEAN')
					{
						// special case here: we translate boolean 1 or 0 into PHP
						// constants true or false.
						// Strings 'true' and 'false' are accepted, even though the
						// spec never mentions them (see eg. Blogger api docs)
						// NB: this simple checks helps a lot sanitizing input, ie no
						// security problems around here
						if ($GLOBALS['_xh']['ac']=='1' || strcasecmp($GLOBALS['_xh']['ac'], 'true') == 0)
						{
							$GLOBALS['_xh']['value']=true;
						}
						else
						{
							// log if receiveing something strange, even though we set the value to false anyway
							if ($GLOBALS['_xh']['ac']!='0' && strcasecmp($_xh[$parser]['ac'], 'false') != 0)
								error_log('XML-RPC: invalid value received in BOOLEAN: '.$GLOBALS['_xh']['ac']);
							$GLOBALS['_xh']['value']=false;
						}
					}
					elseif ($name=='DOUBLE')
					{
						// we have a DOUBLE
						// we must check that only 0123456789-.<space> are characters here
						if (!preg_match('/^[+-]?[eE0123456789 \t.]+$/', $GLOBALS['_xh']['ac']))
						{
							/// @todo: find a better way of throwing an error
							// than this!
							error_log('XML-RPC: non numeric value received in DOUBLE: '.$GLOBALS['_xh']['ac']);
							$GLOBALS['_xh']['value']='ERROR_NON_NUMERIC_FOUND';
						}
						else
						{
							// it's ok, add it on
							$GLOBALS['_xh']['value']=(double)$GLOBALS['_xh']['ac'];
						}
					}
					else
					{
						// we have an I4/INT
						// we must check that only 0123456789-<space> are characters here
						if (!preg_match('/^[+-]?[0123456789 \t]+$/', $GLOBALS['_xh']['ac']))
						{
							/// @todo find a better way of throwing an error
							// than this!
							error_log('XML-RPC: non numeric value received in INT: '.$GLOBALS['_xh']['ac']);
							$GLOBALS['_xh']['value']='ERROR_NON_NUMERIC_FOUND';
						}
						else
						{
							// it's ok, add it on
							$GLOBALS['_xh']['value']=(int)$GLOBALS['_xh']['ac'];
						}
					}
					//$GLOBALS['_xh']['ac']=''; // is this necessary?
					$GLOBALS['_xh']['lv']=3; // indicate we've found a value
					break;
				case 'NAME':
					$GLOBALS['_xh']['valuestack'][count($GLOBALS['_xh']['valuestack'])-1]['name'] = $GLOBALS['_xh']['ac'];
					break;
				case 'MEMBER':
					//$GLOBALS['_xh']['ac']=''; // is this necessary?
					// add to array in the stack the last element built,
					// unless no VALUE was found
					if ($GLOBALS['_xh']['vt'])
					{
						$vscount = count($GLOBALS['_xh']['valuestack']);
						$GLOBALS['_xh']['valuestack'][$vscount-1]['values'][$GLOBALS['_xh']['valuestack'][$vscount-1]['name']] = $GLOBALS['_xh']['value'];
					} else
						error_log('XML-RPC: missing VALUE inside STRUCT in received xml');
					break;
				case 'DATA':
					//$GLOBALS['_xh']['ac']=''; // is this necessary?
					$GLOBALS['_xh']['vt']=null; // reset this to check for 2 data elements in a row - even if they're empty
					break;
				case 'STRUCT':
				case 'ARRAY':
					// fetch out of stack array of values, and promote it to current value
					$curr_val = array_pop($GLOBALS['_xh']['valuestack']);
					$GLOBALS['_xh']['value'] = $curr_val['values'];
					$GLOBALS['_xh']['vt']=strtolower($name);
					if (isset($curr_val['php_class']))
					{
						$GLOBALS['_xh']['php_class'] = $curr_val['php_class'];
					}
					break;
				case 'PARAM':
					// add to array of params the current value,
					// unless no VALUE was found
					if ($GLOBALS['_xh']['vt'])
					{
						$GLOBALS['_xh']['params'][]=$GLOBALS['_xh']['value'];
						$GLOBALS['_xh']['pt'][]=$GLOBALS['_xh']['vt'];
					}
					else
						error_log('XML-RPC: missing VALUE inside PARAM in received xml');
					break;
				case 'METHODNAME':
					$GLOBALS['_xh']['method']=preg_replace('/^[\n\r\t ]+/', '', $GLOBALS['_xh']['ac']);
					break;
				case 'PARAMS':
				case 'FAULT':
				case 'METHODCALL':
				case 'METHORESPONSE':
					break;
				default:
					// End of INVALID ELEMENT!
					// shall we add an assert here for unreachable code???
					break;
			}
		}
	}

	/// Used in decoding xmlrpc requests/responses without rebuilding xmlrpc values
	function xmlrpc_ee_fast($parser, $name)
	{
		xmlrpc_ee($parser, $name, false);
	}

	/// xml parser handler function for character data
	function xmlrpc_cd($parser, $data)
	{
		// skip processing if xml fault already detected
		if ($GLOBALS['_xh']['isf'] < 2)
		{
			// "lookforvalue==3" means that we've found an entire value
			// and should discard any further character data
			if($GLOBALS['_xh']['lv']!=3)
			{
				// G. Giunta 2006-08-23: useless change of 'lv' from 1 to 2
				//if($GLOBALS['_xh']['lv']==1)
				//{
					// if we've found text and we're just in a <value> then
					// say we've found a value
					//$GLOBALS['_xh']['lv']=2;
				//}
				// we always initialize the accumulator before starting parsing, anyway...
				//if(!@isset($GLOBALS['_xh']['ac']))
				//{
				//	$GLOBALS['_xh']['ac'] = '';
				//}
				$GLOBALS['_xh']['ac'].=$data;
			}
		}
	}

	/// xml parser handler function for 'other stuff', ie. not char data or
	/// element start/end tag. In fact it only gets called on unknown entities...
	function xmlrpc_dh($parser, $data)
	{
		// skip processing if xml fault already detected
		if ($GLOBALS['_xh']['isf'] < 2)
		{
			if(substr($data, 0, 1) == '&' && substr($data, -1, 1) == ';')
			{
				// G. Giunta 2006-08-25: useless change of 'lv' from 1 to 2
				//if($GLOBALS['_xh']['lv']==1)
				//{
				//	$GLOBALS['_xh']['lv']=2;
				//}
				$GLOBALS['_xh']['ac'].=$data;
			}
		}
		return true;
	}

	// date helpers

	/**
	* Given a timestamp, return the corresponding ISO8601 encoded string.
	*
	* Really, timezones ought to be supported
	* but the XML-RPC spec says:
	*
	* "Don't assume a timezone. It should be specified by the server in its
	* documentation what assumptions it makes about timezones."
	*
	* These routines always assume localtime unless
	* $utc is set to 1, in which case UTC is assumed
	* and an adjustment for locale is made when encoding
	*
	* @param int $timet (timestamp)
	* @param int $utc (0 or 1)
	* @return string
	*/
	function iso8601_encode($timet, $utc=0)
	{
		if(!$utc)
		{
			$t=strftime("%Y%m%dT%H:%M:%S", $timet);
		}
		else
		{
			if(function_exists('gmstrftime'))
			{
				// gmstrftime doesn't exist in some versions
				// of PHP
				$t=gmstrftime("%Y%m%dT%H:%M:%S", $timet);
			}
			else
			{
				$t=strftime("%Y%m%dT%H:%M:%S", $timet-date('Z'));
			}
		}
		return $t;
	}

	/**
	* Given an ISO8601 date string, return a timet in the localtime, or UTC
	* @param string $idate
	* @param int $utc either 0 or 1
	* @return int (datetime)
	*/
	function iso8601_decode($idate, $utc=0)
	{
		$t=0;
		if(preg_match('/([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})/', $idate, $regs))
		{
			if($utc)
			{
				$t=gmmktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
			}
			else
			{
				$t=mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
			}
		}
		return $t;
	}

	/**
	* Takes an xmlrpc value in PHP xmlrpcval object format and translates it into native PHP types.
	*
	* Works with xmlrpc message objects as input, too.
	*
	* Given proper options parameter, can rebuild generic php object instances
	* (provided those have been encoded to xmlrpc format using a corresponding
	* option in php_xmlrpc_encode())
	* PLEASE NOTE that rebuilding php objects involves calling their constructor function.
	* This means that the remote communication end can decide which php code will
	* get executed on your server, leaving the door possibly open to 'php-injection'
	* style of attacks (provided you have some classes defined on your server that
	* might wreak havoc if instances are built outside an appropriate context).
	* Make sure you trust the remote server/client before eanbling this!
	*
	* @author Dan Libby (dan@libby.com)
	*
	* @param xmlrpcval $xmlrpc_val
	* @param array $options if 'decode_php_objs' is set in the options array, xmlrpc structs can be decoded into php objects
	* @return mixed
	*/
	function php_xmlrpc_decode($xmlrpc_val, $options=array())
	{
		switch($xmlrpc_val->kindOf())
		{
			case 'scalar':
				if (in_array('extension_api', $options))
				{
					reset($xmlrpc_val->me);
					list($typ,$val) = each($xmlrpc_val->me);
					switch ($typ)
					{
						case 'dateTime.iso8601':
							$xmlrpc_val->scalar = $val;
							$xmlrpc_val->xmlrpc_type = 'datetime';
							$xmlrpc_val->timestamp = iso8601_decode($val);
							return $xmlrpc_val;
						case 'base64':
							$xmlrpc_val->scalar = $val;
							$xmlrpc_val->type = $typ;
							return $xmlrpc_val;
						default:
							return $xmlrpc_val->scalarval();
					}
				}
				return $xmlrpc_val->scalarval();
			case 'array':
				$size = $xmlrpc_val->arraysize();
				$arr = array();
				for($i = 0; $i < $size; $i++)
				{
					$arr[] = php_xmlrpc_decode($xmlrpc_val->arraymem($i), $options);
				}
				return $arr;
			case 'struct':
				$xmlrpc_val->structreset();
				// If user said so, try to rebuild php objects for specific struct vals.
				/// @todo should we raise a warning for class not found?
				// shall we check for proper subclass of xmlrpcval instead of
				// presence of _php_class to detect what we can do?
				if (in_array('decode_php_objs', $options) && $xmlrpc_val->_php_class != ''
					&& class_exists($xmlrpc_val->_php_class))
				{
					$obj = @new $xmlrpc_val->_php_class;
					while(list($key,$value)=$xmlrpc_val->structeach())
					{
						$obj->$key = php_xmlrpc_decode($value, $options);
					}
					return $obj;
				}
				else
				{
					$arr = array();
					while(list($key,$value)=$xmlrpc_val->structeach())
					{
						$arr[$key] = php_xmlrpc_decode($value, $options);
					}
					return $arr;
				}
			case 'msg':
				$paramcount = $xmlrpc_val->getNumParams();
				$arr = array();
				for($i = 0; $i < $paramcount; $i++)
				{
					$arr[] = php_xmlrpc_decode($xmlrpc_val->getParam($i));
				}
				return $arr;
			}
	}

	// This constant left here only for historical reasons...
	// it was used to decide if we have to define xmlrpc_encode on our own, but
	// we do not do it anymore
	if(function_exists('xmlrpc_decode'))
	{
		define('XMLRPC_EPI_ENABLED','1');
	}
	else
	{
		define('XMLRPC_EPI_ENABLED','0');
	}

	/**
	* Takes native php types and encodes them into xmlrpc PHP object format.
	* It will not re-encode xmlrpcval objects.
	*
	* Feature creep -- could support more types via optional type argument
	* (string => datetime support has been added, ??? => base64 not yet)
	*
	* If given a proper options parameter, php object instances will be encoded
	* into 'special' xmlrpc values, that can later be decoded into php objects
	* by calling php_xmlrpc_decode() with a corresponding option
	*
	* @author Dan Libby (dan@libby.com)
	*
	* @param mixed $php_val the value to be converted into an xmlrpcval object
	* @param array $options	can include 'encode_php_objs', 'auto_dates', 'null_extension' or 'extension_api'
	* @return xmlrpcval
	*/
	function &php_xmlrpc_encode($php_val, $options=array())
	{
		$type = gettype($php_val);
		switch($type)
		{
			case 'string':
				if (in_array('auto_dates', $options) && preg_match('/^[0-9]{8}T[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $php_val))
					$xmlrpc_val =& new xmlrpcval($php_val, $GLOBALS['xmlrpcDateTime']);
				else
					$xmlrpc_val =& new xmlrpcval($php_val, $GLOBALS['xmlrpcString']);
				break;
			case 'integer':
				$xmlrpc_val =& new xmlrpcval($php_val, $GLOBALS['xmlrpcInt']);
				break;
			case 'double':
				$xmlrpc_val =& new xmlrpcval($php_val, $GLOBALS['xmlrpcDouble']);
				break;
				// <G_Giunta_2001-02-29>
				// Add support for encoding/decoding of booleans, since they are supported in PHP
			case 'boolean':
				$xmlrpc_val =& new xmlrpcval($php_val, $GLOBALS['xmlrpcBoolean']);
				break;
				// </G_Giunta_2001-02-29>
			case 'array':
				// PHP arrays can be encoded to either xmlrpc structs or arrays,
				// depending on wheter they are hashes or plain 0..n integer indexed
				// A shorter one-liner would be
				// $tmp = array_diff(array_keys($php_val), range(0, count($php_val)-1));
				// but execution time skyrockets!
				$j = 0;
				$arr = array();
				$ko = false;
				foreach($php_val as $key => $val)
				{
					$arr[$key] =& php_xmlrpc_encode($val, $options);
					if(!$ko && $key !== $j)
					{
						$ko = true;
					}
					$j++;
				}
				if($ko)
				{
					$xmlrpc_val =& new xmlrpcval($arr, $GLOBALS['xmlrpcStruct']);
				}
				else
				{
					$xmlrpc_val =& new xmlrpcval($arr, $GLOBALS['xmlrpcArray']);
				}
				break;
			case 'object':
				if(is_a($php_val, 'xmlrpcval'))
				{
					$xmlrpc_val = $php_val;
				}
				else
				{
					$arr = array();
					while(list($k,$v) = each($php_val))
					{
						$arr[$k] = php_xmlrpc_encode($v, $options);
					}
					$xmlrpc_val =& new xmlrpcval($arr, $GLOBALS['xmlrpcStruct']);
					if (in_array('encode_php_objs', $options))
					{
						// let's save original class name into xmlrpcval:
						// might be useful later on...
						$xmlrpc_val->_php_class = get_class($php_val);
					}
				}
				break;
			case 'NULL':
				if (in_array('extension_api', $options))
				{
					$xmlrpc_val =& new xmlrpcval('', $GLOBALS['xmlrpcString']);
				}
				if (in_array('null_extension', $options))
				{
					$xmlrpc_val =& new xmlrpcval('', $GLOBALS['xmlrpcNull']);
				}
				else
				{
					$xmlrpc_val =& new xmlrpcval();
				}
				break;
			case 'resource':
				if (in_array('extension_api', $options))
				{
					$xmlrpc_val =& new xmlrpcval((int)$php_val, $GLOBALS['xmlrpcInt']);
				}
				else
				{
					$xmlrpc_val =& new xmlrpcval();
				}
			// catch "user function", "unknown type"
			default:
				// giancarlo pinerolo <ping@alt.it>
				// it has to return
				// an empty object in case, not a boolean.
				$xmlrpc_val =& new xmlrpcval();
				break;
			}
			return $xmlrpc_val;
	}

	/**
	* Convert the xml representation of a method call, method request or single
	* xmlrpc value into the appropriate object (a.k.a. deserialize)
	* @param string $xml_val
	* @param array $options
	* @return mixed false on error, or an instance of either xmlrpcval, xmlrpcmsg or xmlrpcresp
	*/
	function php_xmlrpc_decode_xml($xml_val, $options=array())
	{
		$GLOBALS['_xh'] = array();
		$GLOBALS['_xh']['ac'] = '';
		$GLOBALS['_xh']['stack'] = array();
		$GLOBALS['_xh']['valuestack'] = array();
		$GLOBALS['_xh']['params'] = array();
		$GLOBALS['_xh']['pt'] = array();
		$GLOBALS['_xh']['isf'] = 0;
		$GLOBALS['_xh']['isf_reason'] = '';
		$GLOBALS['_xh']['method'] = false;
		$GLOBALS['_xh']['rt'] = '';
		/// @todo 'guestimate' encoding
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $GLOBALS['xmlrpc_internalencoding']);
		xml_set_element_handler($parser, 'xmlrpc_se_any', 'xmlrpc_ee');
		xml_set_character_data_handler($parser, 'xmlrpc_cd');
		xml_set_default_handler($parser, 'xmlrpc_dh');
		if(!xml_parse($parser, $xml_val, 1))
		{
			$errstr = sprintf('XML error: %s at line %d, column %d',
						xml_error_string(xml_get_error_code($parser)),
						xml_get_current_line_number($parser), xml_get_current_column_number($parser));
			error_log($errstr);
			xml_parser_free($parser);
			return false;
		}
		xml_parser_free($parser);
		if ($GLOBALS['_xh']['isf'] > 1) // test that $GLOBALS['_xh']['value'] is an obj, too???
		{
			error_log($GLOBALS['_xh']['isf_reason']);
			return false;
		}
		switch ($GLOBALS['_xh']['rt'])
		{
			case 'methodresponse':
				$v =& $GLOBALS['_xh']['value'];
				if ($GLOBALS['_xh']['isf'] == 1)
				{
					$vc = $v->structmem('faultCode');
					$vs = $v->structmem('faultString');
					$r =& new xmlrpcresp(0, $vc->scalarval(), $vs->scalarval());
				}
				else
				{
					$r =& new xmlrpcresp($v);
				}
				return $r;
			case 'methodcall':
				$m =& new xmlrpcmsg($GLOBALS['_xh']['method']);
				for($i=0; $i < count($GLOBALS['_xh']['params']); $i++)
				{
					$m->addParam($GLOBALS['_xh']['params'][$i]);
				}
				return $m;
			case 'value':
				return $GLOBALS['_xh']['value'];
			default:
				return false;
		}
	}

	/**
	* decode a string that is encoded w/ "chunked" transfer encoding
	* as defined in rfc2068 par. 19.4.6
	* code shamelessly stolen from nusoap library by Dietrich Ayala
	*
	* @param string $buffer the string to be decoded
	* @return string
	*/
	function decode_chunked($buffer)
	{
		// length := 0
		$length = 0;
		$new = '';

		// read chunk-size, chunk-extension (if any) and crlf
		// get the position of the linebreak
		$chunkend = strpos($buffer,"\r\n") + 2;
		$temp = substr($buffer,0,$chunkend);
		$chunk_size = hexdec( trim($temp) );
		$chunkstart = $chunkend;
		while($chunk_size > 0)
		{
			$chunkend = strpos($buffer, "\r\n", $chunkstart + $chunk_size);

			// just in case we got a broken connection
			if($chunkend == false)
			{
				$chunk = substr($buffer,$chunkstart);
				// append chunk-data to entity-body
				$new .= $chunk;
				$length += strlen($chunk);
				break;
			}

			// read chunk-data and crlf
			$chunk = substr($buffer,$chunkstart,$chunkend-$chunkstart);
			// append chunk-data to entity-body
			$new .= $chunk;
			// length := length + chunk-size
			$length += strlen($chunk);
			// read chunk-size and crlf
			$chunkstart = $chunkend + 2;

			$chunkend = strpos($buffer,"\r\n",$chunkstart)+2;
			if($chunkend == false)
			{
				break; //just in case we got a broken connection
			}
			$temp = substr($buffer,$chunkstart,$chunkend-$chunkstart);
			$chunk_size = hexdec( trim($temp) );
			$chunkstart = $chunkend;
		}
		return $new;
	}

	/**
	* xml charset encoding guessing helper function.
	* Tries to determine the charset encoding of an XML chunk
	* received over HTTP.
	* NB: according to the spec (RFC 3023, if text/xml content-type is received over HTTP without a content-type,
	* we SHOULD assume it is strictly US-ASCII. But we try to be more tolerant of unconforming (legacy?) clients/servers,
	* which will be most probably using UTF-8 anyway...
	*
	* @param string $httpheaders the http Content-type header
	* @param string $xmlchunk xml content buffer
	* @param string $encoding_prefs comma separated list of character encodings to be used as default (when mb extension is enabled)
	*
	* @todo explore usage of mb_http_input(): does it detect http headers + post data? if so, use it instead of hand-detection!!!
	*/
	function guess_encoding($httpheader='', $xmlchunk='', $encoding_prefs=null)
	{
		// discussion: see http://www.yale.edu/pclt/encoding/
		// 1 - test if encoding is specified in HTTP HEADERS

		//Details:
		// LWS:           (\13\10)?( |\t)+
		// token:         (any char but excluded stuff)+
		// header:        Content-type = ...; charset=value(; ...)*
		//   where value is of type token, no LWS allowed between 'charset' and value
		// Note: we do not check for invalid chars in VALUE:
		//   this had better be done using pure ereg as below

		/// @todo this test will pass if ANY header has charset specification, not only Content-Type. Fix it?
		$matches = array();
		if(preg_match('/;\s*charset=([^;]+)/i', $httpheader, $matches))
		{
			return strtoupper(trim($matches[1]));
		}

		// 2 - scan the first bytes of the data for a UTF-16 (or other) BOM pattern
		//     (source: http://www.w3.org/TR/2000/REC-xml-20001006)
		//     NOTE: actually, according to the spec, even if we find the BOM and determine
		//     an encoding, we should check if there is an encoding specified
		//     in the xml declaration, and verify if they match.
		/// @todo implement check as described above?
		/// @todo implement check for first bytes of string even without a BOM? (It sure looks harder than for cases WITH a BOM)
		if(preg_match('/^(\x00\x00\xFE\xFF|\xFF\xFE\x00\x00|\x00\x00\xFF\xFE|\xFE\xFF\x00\x00)/', $xmlchunk))
		{
			return 'UCS-4';
		}
		elseif(preg_match('/^(\xFE\xFF|\xFF\xFE)/', $xmlchunk))
		{
			return 'UTF-16';
		}
		elseif(preg_match('/^(\xEF\xBB\xBF)/', $xmlchunk))
		{
			return 'UTF-8';
		}

		// 3 - test if encoding is specified in the xml declaration
		// Details:
		// SPACE:         (#x20 | #x9 | #xD | #xA)+ === [ \x9\xD\xA]+
		// EQ:            SPACE?=SPACE? === [ \x9\xD\xA]*=[ \x9\xD\xA]*
		if (preg_match('/^<\?xml\s+version\s*=\s*'. "((?:\"[a-zA-Z0-9_.:-]+\")|(?:'[a-zA-Z0-9_.:-]+'))".
			'\s+encoding\s*=\s*' . "((?:\"[A-Za-z][A-Za-z0-9._-]*\")|(?:'[A-Za-z][A-Za-z0-9._-]*'))/",
			$xmlchunk, $matches))
		{
			return strtoupper(substr($matches[2], 1, -1));
		}

		// 4 - if mbstring is available, let it do the guesswork
		// NB: we favour finding an encoding that is compatible with what we can process
		if(extension_loaded('mbstring'))
		{
			if($encoding_prefs)
			{
				$enc = mb_detect_encoding($xmlchunk, $encoding_prefs);
			}
			else
			{
				$enc = mb_detect_encoding($xmlchunk);
			}
			// NB: mb_detect likes to call it ascii, xml parser likes to call it US_ASCII...
			// IANA also likes better US-ASCII, so go with it
			if($enc == 'ASCII')
			{
				$enc = 'US-'.$enc;
			}
			return $enc;
		}
		else
		{
			// no encoding specified: as per HTTP1.1 assume it is iso-8859-1?
			// Both RFC 2616 (HTTP 1.1) and 1945(http 1.0) clearly state that for text/xxx content types
			// this should be the standard. And we should be getting text/xml as request and response.
			// BUT we have to be backward compatible with the lib, which always used UTF-8 as default...
			return $GLOBALS['xmlrpc_defencoding'];
		}
	}

	/**
	* Checks if a given charset encoding is present in a list of encodings or
	* if it is a valid subset of any encoding in the list
	* @param string $encoding charset to be tested
	* @param mixed $validlist comma separated list of valid charsets (or array of charsets)
	*/
	function is_valid_charset($encoding, $validlist)
	{
		$charset_supersets = array(
			'US-ASCII' => array ('ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4',
				'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8',
				'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-11', 'ISO-8859-12',
				'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'UTF-8',
				'EUC-JP', 'EUC-', 'EUC-KR', 'EUC-CN')
		);
		if (is_string($validlist))
			$validlist = explode(',', $validlist);
		if (@in_array(strtoupper($encoding), $validlist))
			return true;
		else
		{
			if (array_key_exists($encoding, $charset_supersets))
				foreach ($validlist as $allowed)
					if (in_array($allowed, $charset_supersets[$encoding]))
						return true;
				return false;
		}
	}

	// listMethods: either a string, or nothing
	$GLOBALS['_xmlrpcs_listMethods_sig'] = array(array($GLOBALS['xmlrpcArray'], $GLOBALS['xmlrpcString']), array($GLOBALS['xmlrpcArray']));
	$GLOBALS['_xmlrpcs_listMethods_doc'] = 'This method lists all the methods that the XML-RPC server knows how to dispatch';
	function _xmlrpcs_listMethods($server, $m)
	{
		$v     =  CreateObject('phpgwapi.xmlrpcval');
		$dmap  = $server->dmap;
		$outAr = array();
		for(reset($dmap); list($key, $val) = each($dmap); )
		{
			$outAr[] = createObject('phpgwapi.xmlrpcval',$key, 'string');
		}
		$dmap = $GLOBALS['_xmlrpcs_dmap'];
		for(reset($dmap); list($key, $val) = each($dmap); )
		{
			$outAr[] = createObject('phpgwapi.xmlrpcval',$key, 'string');
		}
		$v->addArray($outAr);
		return CreateObject('phpgwapi.xmlrpcresp',$v);
	}

	$GLOBALS['_xmlrpcs_methodSignature_sig']=array(array($GLOBALS['xmlrpcArray'], $GLOBALS['xmlrpcString']));
	$GLOBALS['_xmlrpcs_methodSignature_doc']='Returns an array of known signatures (an array of arrays) for the method name passed. If no signatures are known, returns a none-array (test for type != array to detect missing signature)';
	function _xmlrpcs_methodSignature($server, $m)
	{
		$methName = $m->getParam(0);
		$methName = $methName->scalarval();
		if (ereg("^system\.", $methName))
		{
			$dmap = $GLOBALS['_xmlrpcs_dmap'];
			$sysCall = 1;
		}
		else
		{
			$dmap = $server->dmap;
			$sysCall = 0;
		}
		//	print "<!-- ${methName} -->\n";
		if (isset($dmap[$methName]))
		{
			if ($dmap[$methName]['signature'])
			{
				$sigs = array();
				$thesigs=$dmap[$methName]['signature'];
				for($i=0; $i<sizeof($thesigs); $i++)
				{
					$cursig = array();
					$inSig  = $thesigs[$i];
					for($j=0; $j<sizeof($inSig); $j++)
					{
						$cursig[] = createObject('phpgwapi.xmlrpcval',$inSig[$j], 'string');
					}
					$sigs[] = createObject('phpgwapi.xmlrpcval',$cursig, 'array');
				}
				$r = createObject('phpgwapi.xmlrpcresp',CreateObject('phpgwapi.xmlrpcval',$sigs, 'array'));
			}
			else
			{
				$r = createObject('phpgwapi.xmlrpcresp', CreateObject('phpgwapi.xmlrpcval','undef', 'string'));
			}
		}
		else
		{
			$r = createObject('phpgwapi.xmlrpcresp',0,$GLOBALS['xmlrpcerr']['introspect_unknown'],$GLOBALS['xmlrpcstr']['introspect_unknown']);
		}
		return $r;
	}

	$GLOBALS['_xmlrpcs_methodHelp_sig'] = array(array($GLOBALS['xmlrpcString'], $GLOBALS['xmlrpcString']));
	$GLOBALS['_xmlrpcs_methodHelp_doc'] = 'Returns help text if defined for the method passed, otherwise returns an empty string';
	function _xmlrpcs_methodHelp($server, $m)
	{
		$methName = $m->getParam(0);
		$methName = $methName->scalarval();
		if (ereg("^system\.", $methName))
		{
			$dmap = $GLOBALS['_xmlrpcs_dmap'];
			$sysCall=1;
		}
		else
		{
			$dmap = $server->dmap;
			$sysCall=0;
		}
		//	print "<!-- ${methName} -->\n";
		if (isset($dmap[$methName]))
		{
			if ($dmap[$methName]['docstring'])
			{
				$r = createObject('phpgwapi.xmlrpcresp', CreateObject('phpgwapi.xmlrpcval',$dmap[$methName]['docstring']),'string');
			}
			else
			{
				$r = createObject('phpgwapi.xmlrpcresp', CreateObject('phpgwapi.xmlrpcval'), 'string');
			}
		}
		else
		{
			$r = createObject('phpgwapi.xmlrpcresp',0,$GLOBALS['xmlrpcerr']['introspect_unknown'],$GLOBALS['xmlrpcstr']['introspect_unknown']);
		}
		return $r;
	}

	/*
	$GLOBALS['_xmlrpcs_listApps_sig'] = array(array(xmlrpcStruct,xmlrpcString));
	$GLOBALS['_xmlrpcs_listApps_doc'] = 'Returns a list of installed phpgw apps';
	function _xmlrpcs_listApps($server,$m)
	{
		$m->getParam(0);
		$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_applications WHERE app_enabled<3",__LINE__,__FILE__);
		if($GLOBALS['phpgw']->db->num_rows())
		{
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$name   = $GLOBALS['phpgw']->db->f('app_name');
				$title  = $GLOBALS['phpgw']->db->f('app_title');
				$status = $GLOBALS['phpgw']->db->f('app_enabled');
				$version= $GLOBALS['phpgw']->db->f('app_version');
				$apps[$name] = createObject('phpgwapi.xmlrpcval',
					array(
						'title'  => CreateObject('phpgwapi.xmlrpcval',$title,'string'),
						'name'   => CreateObject('phpgwapi.xmlrpcval',$name,'string'),
						'status' => CreateObject('phpgwapi.xmlrpcval',$status,'string'),
						'version'=> CreateObject('phpgwapi.xmlrpcval',$version,'string')
					),
					'struct'
				);
			}
		}
		return CreateObject('phpgwapi.xmlrpcresp',CreateObject('phpgwapi.xmlrpcval',$apps, 'struct'));
	}
	*/

	$GLOBALS['_xmlrpcs_login_sig'] = array(array($GLOBALS['xmlrpcStruct'], $GLOBALS['xmlrpcStruct']));
	$GLOBALS['_xmlrpcs_login_doc'] = 'phpGroupWare client or server login via XML-RPC';
	function _xmlrpcs_login($server,$m)
	{
		$rdata = $m->getParam(0);
		$data = $rdata->scalarval();

		if($data['server_name'])
		{
			$server_name = $data['server_name']->scalarval();
		}
		if($data['domain'])
		{
			$domain = $data['domain']->scalarval();
		}
		$username    = $data['username']->scalarval();
		$password    = $data['password']->scalarval();

		if($server_name)
		{
			list($sessionid,$kp3) = $GLOBALS['phpgw']->session->create_server($username.'@'.$server_name,$password,"text");
		}
		else
		{
			if($domain)
			{
				$user = $username.'@'.$domain;
			}
			else
			{
				$user = $username;
			}
			$sessionid = $GLOBALS['phpgw']->session->create($user, $password);
			$kp3 = $GLOBALS['phpgw']->session->kp3;
			$domain = $GLOBALS['phpgw']->session->account_domain;
		}

		if($sessionid && $kp3)
		{
			$rtrn['domain'] = createObject('phpgwapi.xmlrpcval',$domain,'string');
			$rtrn['sessionid'] = createObject('phpgwapi.xmlrpcval',$sessionid,'string');
			$rtrn['kp3'] = createObject('phpgwapi.xmlrpcval',$kp3,'string');
		}
		else
		{
			$rtrn['GOAWAY'] = createObject('phpgwapi.xmlrpcval','XOXO','string');
		}
		return CreateObject('phpgwapi.xmlrpcresp',CreateObject('phpgwapi.xmlrpcval',$rtrn,'struct'));
	}

	$GLOBALS['_xmlrpcs_phpgw_api_version_sig'] = array(array($GLOBALS['xmlrpcString'], $GLOBALS['xmlrpcString']));
	$GLOBALS['_xmlrpcs_phpgw_api_version_doc'] = 'Returns the phpGroupWare API version';
	function _xmlrpcs_phpgw_api_version($server,$m)
	{
		$version = $GLOBALS['phpgw_info']['server']['versions']['phpgwapi'];

		return CreateObject('phpgwapi.xmlrpcresp',CreateObject('phpgwapi.xmlrpcval',$version,'string'));
	}


	$GLOBALS['_xmlrpcs_logout_sig'] = array(array($GLOBALS['xmlrpcStruct'], $GLOBALS['xmlrpcStruct']));
	$GLOBALS['_xmlrpcs_logout_doc'] = 'phpGroupWare client or server logout via XML-RPC';
	function _xmlrpcs_logout($server,$m)
	{
		$rdata = $m->getParam(0);
		$data = $rdata->scalarval();

		$sessionid = $data['sessionid']->scalarval();
		$kp3       = $data['kp3']->scalarval();

		$later = $GLOBALS['phpgw']->session->destroy($sessionid,$kp3);

		if ($later)
		{
			$rtrn['GOODBYE'] = createObject('phpgwapi.xmlrpcval','XOXO','string');
		}
		else
		{
			/* This never happens, yet */
			$rtrn['OOPS'] = createObject('phpgwapi.xmlrpcval','WHAT?','string');
		}
		return CreateObject('phpgwapi.xmlrpcresp',CreateObject('phpgwapi.xmlrpcval',$rtrn,'struct'));
	}

	$GLOBALS['_xmlrpcs_dmap'] = array(
		'system.listMethods' => array(
			'function'  => '_xmlrpcs_listMethods',
			'signature' => $GLOBALS['_xmlrpcs_listMethods_sig'],
			'docstring' => $GLOBALS['_xmlrpcs_listMethods_doc']
		),
		'system.methodHelp' => array(
			'function'  => '_xmlrpcs_methodHelp',
			'signature' => $GLOBALS['_xmlrpcs_methodHelp_sig'],
			'docstring' => $GLOBALS['_xmlrpcs_methodHelp_doc']
		),
		'system.methodSignature' => array(
			'function'  => '_xmlrpcs_methodSignature',
			'signature' => $GLOBALS['_xmlrpcs_methodSignature_sig'],
			'docstring' => $GLOBALS['_xmlrpcs_methodSignature_doc']
		),
		/*
		'system.listApps' => array(
			'function'  => '_xmlrpcs_listApps',
			'signature' => $GLOBALS['_xmlrpcs_listApps_sig'],
			'docstring' => $GLOBALS['_xmlrpcs_listApps_doc']
		),
		*/
		'system.login'  => array(
			'function'  => '_xmlrpcs_login',
			'signature' => $GLOBALS['_xmlrpcs_login_sig'],
			'docstring' => $GLOBALS['_xmlrpcs_login_doc']
		),
		'system.logout'  => array(
			'function'  => '_xmlrpcs_logout',
			'signature' => $GLOBALS['_xmlrpcs_logout_sig'],
			'docstring' => $GLOBALS['_xmlrpcs_logout_doc']
		),
		'system.phpgw_api_version' => array(
			'function'  => '_xmlrpcs_phpgw_api_version',
			'signature' => $GLOBALS['_xmlrpcs_phpgw_api_version_sig'],
			'docstring' => $GLOBALS['_xmlrpcs_phpgw_api_version_doc']
		)
	);

	$GLOBALS['_xmlrpc_debuginfo'] = '';
	function xmlrpc_debugmsg($m)
	{
		$GLOBALS['_xmlrpc_debuginfo'] .= $m . "\n";
	}

	/**
	 * Error reporting for XML-RPC
	 *
	 * Author: jengo <br>
	 * Returns XML-RPC fault and stops this execution of the application. <br>
	 * Syntax: void xmlrpcfault(string) <br>
	 * Example1: xmlrpcfault('Session could not be verifed'); <br>
	 * @param $string Error message to be returned.
	 */
	function xmlrpcfault($string)
	{
		$r = createObject('phpgwapi.xmlrpcresp',
			CreateObject('phpgwapi.xmlrpcval'),
			$GLOBALS['xmlrpcerr']['unknown_method'],
			$string
		);
		$payload = '<?xml version="1.0"?>' . "\n" . $r->serialize();
		Header('Content-type: text/xml');
		Header('Content-length: ' . strlen($payload));
		print $payload;
		$GLOBALS['phpgw']->common->phpgw_exit(False);
	}
?>
