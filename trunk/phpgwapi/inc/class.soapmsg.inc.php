<?php
	/**
	* SOAPx4 message
	* @author Edd Dumbill <edd@usefulinc.com>
	* @author Victor Zou <victor@gigaideas.com.cn>
	* @author Dietrich Ayala <dietrich@ganx4.com>
	* @copyright Copyright (C) 1999-2000 Edd Dumbill
	* @copyright Copyright (C) 2000-2001 Victor Zou
	* @copyright Copyright (C) 2001 Dietrich Ayala
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @package phpgwapi
	* @subpackage communication
	* @version $Id: class.soapmsg.inc.php 17062 2006-09-03 06:15:27Z skwashd $
	* @internal This project began based on code from the 2 projects below,
	* @internal and still contains some original code. The licenses of both must be respected.
	* @internal XML-RPC for PHP; SOAP for PHP
	*/

	/**
	* SOAPx4 client
	*
	* @package phpgwapi
	* @subpackage communication
	*/
	class soapmsg
	{
		// params is an array of soapval objects
		function soapmsg($method,$params,$method_namespace='http://testuri.org',$new_namespaces=False)
		{
			// globalize method namespace
			$GLOBALS['methodNamespace'] = $method_namespace;
			$namespaces = $GLOBALS['namespaces'];

			// make method struct
			$this->value = createObject('phpgwapi.soapval',$method,"struct",$params,$method_namespace);
			if(is_array($new_namespaces))
			{
				$i = count($namespaces);
				@reset($new_namespaces);
				while(list($null,$v) = @each($new_namespaces))
				/* foreach($new_namespaces as $v) */
				{
					$namespaces[$v] = 'ns' . $i++;
				}
				$this->namespaces = $namespaces;
			}
			$this->payload = '';
			$this->debug_flag = True;
			$this->debug_str = "entering soapmsg() with soapval ".$this->value->name."\n";
		}

		function make_envelope($payload)
		{
			$namespaces = $GLOBALS['namespaces'];
			@reset($namespaces);
			while(list($k,$v) = @each($namespaces))
			/* foreach($namespaces as $k => $v) */
			{
				$ns_string .= " xmlns:$v=\"$k\"";
			}
			return "<SOAP-ENV:Envelope $ns_string SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">\n"
				. $payload . "</SOAP-ENV:Envelope>\n";
		}

		function make_body($payload)
		{
			return "<SOAP-ENV:Body>\n" . $payload . "</SOAP-ENV:Body>\n";
		}

		function createPayload()
		{
			$value = $this->value;
			$payload = $this->make_envelope($this->make_body($value->serialize()));
			$this->debug($value->debug_str);
			$payload = "<?xml version=\"1.0\"?>\n".$payload;
			if($this->debug_flag)
			{
				$payload .= $this->serializeDebug();
			}
			$this->payload = str_replace("\n","\r\n", $payload);
		}

		function serialize()
		{
			if($this->payload == '')
			{
				$this->createPayload();
				return $this->payload;
			}
			else
			{
				return $this->payload;
			}
		}

		// returns a soapval object
		function parseResponse($data)
		{
			$this->debug("Entering parseResponse()");
			//$this->debug(" w/ data $data");
			// strip headers here
			//$clean_data = ereg_replace("\r\n","\n", $data);
			if(ereg("^.*\r\n\r\n<",$data))
			{
				$this->debug("found proper seperation of headers and document");
				$this->debug("getting rid of headers, stringlen: ".strlen($data));
				$clean_data = ereg_replace("^.*\r\n\r\n<","<", $data);
				$this->debug("cleaned data, stringlen: ".strlen($clean_data));
			}
			else
			{
				// return fault
				return CreateObject('phpgwapi.soapval',
					'fault',
					'SOAPStruct',
					Array(
						CreateObject('phpgwapi.soapval','faultcode','string','SOAP-MSG'),
						CreateObject('phpgwapi.soapval','faultstring','string','HTTP Error'),
						CreateObject('phpgwapi.soapval','faultdetail','string','HTTP headers were not immediately followed by \'\r\n\r\n\'')
					)
				);
			}
	/*
			// if response is a proper http response, and is not a 200
			if(ereg("^HTTP",$clean_data) && !ereg("200$", $clean_data))
			{
				// get error data
				$errstr = substr($clean_data, 0, strpos($clean_data, "\n")-1);
				// return fault
				return CreateObject('phpgwapi.soapval',
					"fault",
					"SOAPStruct",
					array(
						CreateObject('phpgwapi.soapval',"faultcode","string","SOAP-MSG"),
						CreateObject('phpgwapi.soapval',"faultstring","string","HTTP error")
					)
				);
			}
	*/
			$this->debug("about to create parser instance w/ data: $clean_data");
			// parse response
			$response = createObject('phpgwapi.soap_parser',$clean_data);
			// return array of parameters
			$ret = $response->get_response();
			$this->debug($response->debug_str);
			return $ret;
	 	}

		// dbg
		function debug($string)
		{
			if($this->debug_flag)
			{
				$this->debug_str .= "$string\n";
			}
		}

		// preps debug data for encoding into soapmsg
		function serializeDebug()
		{
			if($this->debug_flag)
			{
				return "<!-- DEBUG INFO:\n".$this->debug_str."-->\n";
			}
			else
			{
				return '';
			}
		}
	}
?>
