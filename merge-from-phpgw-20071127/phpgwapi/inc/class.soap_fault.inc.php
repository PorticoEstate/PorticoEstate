<?php
	/**
	* This file is generated automaticaly from the nusoap library for
	* phpGroupWare, using the nusoap2phpgwapi.php script written for this purpose by 
	* Caeies (caeies@phpgroupware.org)
	* @copyright Portions Copyright (C) 2003,2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @package phpgwapi
	* @subpackage communication
	* Please see original header after this one and class.nusoap_base.inc.php
	* @version $Id: class.soap_fault.inc.php 17829 2006-12-28 18:03:40Z Caeies $
	*/

/* Please see class.base_nusoap.inc.php for more information */

if (@!$GLOBALS['phpgw_info']['flags']['included_classes']['nusoap_base'])
{
	require_once(PHPGW_API_INC."/class.nusoap_base.inc.php");
	$GLOBALS['phpgw_info']['flags']['included_classes']['nusoap_base'] = True;
}



/**
* Contains information for a SOAP fault.
* Mainly used for returning faults from deployed functions
* in a server instance.
* @author   Dietrich Ayala <dietrich@ganx4.com>
* @version  $ I d : nusoap.php,v 1.95 2006/02/02 15:52:34 snichol Exp $
* @access public
*/
class phpgwapi_soap_fault extends phpgwapi_nusoap_base {
	/**
	 * The fault code (client|server)
	 * @var string
	 * @access private
	 */
	var $faultcode;
	/**
	 * The fault actor
	 * @var string
	 * @access private
	 */
	var $faultactor;
	/**
	 * The fault string, a description of the fault
	 * @var string
	 * @access private
	 */
	var $faultstring;
	/**
	 * The fault detail, typically a string or array of string
	 * @var mixed
	 * @access private
	 */
	var $faultdetail;

	/**
	* constructor
    *
    * @param string $faultcode (SOAP-ENV:Client | SOAP-ENV:Server)
    * @param string $faultactor only used when msg routed between multiple actors
    * @param string $faultstring human readable error message
    * @param mixed $faultdetail detail, typically a string or array of string
	*/
	function phpgwapi_soap_fault($faultcode,$faultactor='',$faultstring='',$faultdetail=''){
		parent::phpgwapi_nusoap_base();
		$this->faultcode = $faultcode;
		$this->faultactor = $faultactor;
		$this->faultstring = $faultstring;
		$this->faultdetail = $faultdetail;
	}

	/**
	* serialize a fault
	*
	* @return	string	The serialization of the fault instance.
	* @access   public
	*/
	function serialize(){
		$ns_string = '';
		foreach($this->namespaces as $k => $v){
			$ns_string .= "\n  xmlns:$k=\"$v\"";
		}
		$return_msg =
			'<?xml version="1.0" encoding="'.$this->soap_defencoding.'"?>'.
			'<SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"'.$ns_string.">\n".
				'<SOAP-ENV:Body>'.
				'<SOAP-ENV:Fault>'.
					$this->serialize_val($this->faultcode, 'faultcode').
					$this->serialize_val($this->faultactor, 'faultactor').
					$this->serialize_val($this->faultstring, 'faultstring').
					$this->serialize_val($this->faultdetail, 'detail').
				'</SOAP-ENV:Fault>'.
				'</SOAP-ENV:Body>'.
			'</SOAP-ENV:Envelope>';
		return $return_msg;
	}
}



?>
