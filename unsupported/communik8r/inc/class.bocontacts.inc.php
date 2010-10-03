<?php
	/**
	* Communik8r contacts logic class
	*
	* @author Dave Hall skwashd@phpgroupware.org
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package communik8r
	* @subpackage contacts
	* @version $Id: class.bocontacts.inc.php,v 1.1.1.1 2005/08/23 05:04:02 skwashd Exp $
	*/

	/**
	* @see bobase
	*/
	phpgw::import_class('communik8r.bobase');	

	/**
	* Communik8r contacts logic class
	*/
	class bocontacts extends bobase
	{
		/**
		* REST URL handler
		*/
		function rest($uri_parts)
		{
			switch ( strtoupper($_SERVER['REQUEST_METHOD']) )
			{
				case 'GET':
					switch ( count($uri_parts) )
					{
						case 3: //requesting a look up of a contact
							$this->lookup();
							break;

						case 4: //requesting mailbox summary
							$this->get_summary($uri_parts);
							break;

						case 5: //requesting message
							$this->get_msg($uri_parts);
							break;

						case 6: //requesting attachment
							break;

						default:
							die('<error>invalid request</error>');
							//invalid request
					}
				break;
			}
		}

		function lookup()
		{
			$comm_type = '';
			$results = array();

			if ( strlen( $search_key = get_var('search', array('GET') ) ) > 2 )
			{
				$contacts = createObject('phpgwapi.contacts');

				$comm_type = get_var('comm_type', array('GET') );

				//error_log("Initial Input: search: {$search_key} comm_type: {$comm_type}");
				
				$results = $contacts->get_persons_comm_lookup($search_key, $comm_type);

				unset($contacts);
			}
			else
			{
				exit;//empty response is handled on client side ... i think
			}

			Header('Content-Type: text/xml');
			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;
			
			$xsl = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . $GLOBALS['phpgw']->link('/communik8r/templates/base/contacts_lookup.xsl') . '"');
			$xml->appendChild($xsl);

			$phpgw = $xml->createElement('phpgw:response', 'phpgw');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgw', 'http://dtds.phpgroupware.org/phpgw.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgwapi', 'http://dtds.phpgroupware.org/phpgwapi.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:communik8r', 'http://dtds.phpgroupware.org/communik8r.dtd');

			$elm = $xml->createElement('communik8r:response');

			$contacts = $xml->createElement('communik8r:contacts');

			foreach($results as $id => $data)
			{
				$info = "{$data['first_name']} {$data['last_name']} <{$data[$comm_type]}>";
				$contact = $xml->createElement('communik8r:contact');
				$contact->setAttribute('id', $id);
				$contact->setAttribute('type', $comm_type);
				$contact->appendChild( $xml->createTextNode($info) );
				$contacts->appendChild($contact);
			}
			$elm->appendChild($contacts);
			$phpgw->appendChild($elm);

			$xml->appendChild($phpgw);

			echo $xml->saveXML();
		}
	}
