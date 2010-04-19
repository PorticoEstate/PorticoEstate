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
	include_once(PHPGW_INCLUDE_ROOT . SEP . 'communik8r' . SEP . 'inc' . SEP . 'class.bobase.inc.php');
	
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
			$xml = domxml_new_doc('1.0');
			
			$xsl = $xml->create_processing_instruction('xml-stylesheet', 'type="text/xsl" href="' . $GLOBALS['phpgw']->link('/communik8r/xsl/contacts_lookup') . '"');
			$xml->append_child($xsl);

			$phpgw = $xml->create_element_ns('http://dtds.phpgroupware.org/phpgw.dtd', 'response', 'phpgw');
			$phpgw->add_namespace('http://dtds.phpgroupware.org/phpgwapi.dtd', 'phpgwapi');
			$phpgw->add_namespace('http://dtds.phpgroupware.org/communik8r.dtd', 'communik8r');

			$elm = $xml->create_element('communik8r:response');

			$contacts = $xml->create_element('communik8r:contacts');

			foreach($results as $id => $data)
			{
				$info = "{$data['first_name']} {$data['last_name']} <{$data[$comm_type]}>";
				$contact = $xml->create_element('communik8r:contact');
				$contact->set_attribute('id', $id);
				$contact->set_attribute('type', $comm_type);
				$contact->append_child( $xml->create_text_node($info) );
				$contacts->append_child($contact);
			}
			$elm->append_child($contacts);
			$phpgw->append_child($elm);

			$xml->append_child($phpgw);

			echo $xml->dump_mem(true);
		}
	}
?>
