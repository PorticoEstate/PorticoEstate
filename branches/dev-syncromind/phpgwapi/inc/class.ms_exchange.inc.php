<?php
	/**
	* phpGroupWare MS Exchange integration
	*
	* @author Troy Wolf <troy@troywolf.com>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License Version 2 or later
	* @package phpgroupware
	* @subpackage phpgwapi
	* @version $Id$
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.

		You should have received a copy of the GNU Lesser General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* phpGroupWare caching system
	*
	* Implementing Webdav protocoll for communicate with Microsoft Exchange
	* The code is mostly written / inspired by Troy Wolf (troy@troywolf.com)
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category communication
	*/

	require_once PHPGW_API_INC . '/exchange/class_http.php';
	require_once PHPGW_API_INC . '/exchange/class_xml.php';

	class phpgwapi_ms_exchange
	{
		public $exchange_server = "http://NameOfYourExchangeServer";
	//	public $exchange_domain = "domain";
		public $exchange_username = "YourExchangeUsername";
		public $exchange_password = "YourExchangePassword";

		public function __construct()
		{
			$this->http = new http();
			$this->http->headers["Content-Type"] = 'text/xml; charset="UTF-8"'; 

			$this->xml = new xml();
		}

		/**
		 * Iterate the folders in a user's inbox
		 *
		 * @return object that is an array of objects and arrays that makes it easy to access the parts you need
		 */
		public function list_folders()
		{
			// http://msdn.microsoft.com/library/default.asp?url=/library/en-us/e2k3/e2k3/_webdav_depth_header.asp
			$this->http->headers["Depth"] = "0";

			$this->http->headers["Translate"] = "f";

			// The trickiest part is forming your WebDAV query. This example shows how to
			// find all the folders in the inbox for a user name.
			$this->http->xmlrequest = '<?xml version="1.0"?>';
			$this->http->xmlrequest .= <<<END
			<a:searchrequest xmlns:a="DAV:" xmlns:s="http://schemas.microsoft.com/exchange/security/">
			   <a:sql>
			       SELECT "DAV:displayname"
			       FROM SCOPE('hierarchical traversal of "{$this->exchange_server}/Exchange/{$this->exchange_username}/inbox"')
			   </a:sql>
			</a:searchrequest>
END;
			// IMPORTANT -- The END line above must be completely left-aligned. No white-space.

			// The 'fetch' method does the work of sending and receiving the request.
			// NOTICE the last parameter passed--'SEARCH' in this example. That is the
			// HTTP verb that you must correctly set according to the type of WebDAV request
			// you are making.  The examples on this page use either 'PROPFIND' or 'SEARCH'.
			if (!$this->http->fetch("{$this->exchange_server}/Exchange/{$this->exchange_username}/inbox", 0, null, $this->exchange_username, $this->exchange_password, "SEARCH")) 
			{
			  echo "<h2>There is a problem with the http request!</h2>";
			  echo $this->http->log;
			  exit();
			}

			// Note: The following lines can be uncommented to aid in debugging.
			#echo "<pre>".$this->http->log."</pre><hr />\n";
			#echo "<pre>".$this->http->header."</pre><hr />\n";
			#echo "<pre>".$this->http->body."</pre><hr />\n";
			#exit();
			// Or, these next lines will display the result as an XML doc in the browser.
			#header('Content-type: text/xml');
			#echo $this->http->body;
			#exit();

			// The assumption now is that we've got an XML result back from the Exchange
			// Server, so let's parse the XML into an object we can more easily access.

			if (!$this->xml->fetch($this->http->body))
			{
			    echo "<h2>There was a problem parsing your XML!</h2>";
			    echo "<pre>".$this->http->log."</pre><hr />\n";
			    echo "<pre>".$this->http->header."</pre><hr />\n";
			    echo "<pre>".$this->http->body."</pre><hr />\n";
			    echo "<pre>".$this->xml->log."</pre><hr />\n";
			    exit();
			}

			// You should now have an object that is an array of objects and arrays that
			// makes it easy to access the parts you need. These next lines can be
			// uncommented to make a raw display of the data object.
			#echo "<pre>\n";
			#print_r($this->xml->data);
			#echo "</pre>\n";
			#exit();

			// And finally, an example of iterating the inbox folder names and url's to
			// display in the browser. I also show you 2 methods to link to the folders.
			// One uses the href provided in the response which opens the folder using OWA.
			// The other is an Outlook style link to open the folder in the Outlook desktop
			// client.
			echo '<table border="1">';
			foreach($this->xml->data->A_MULTISTATUS[0]->A_RESPONSE as $idx=>$item)
			{
			    echo '<tr>'
			        .'<td>'.$item->A_PROPSTAT[0]->A_PROP[0]->A_DISPLAYNAME[0]->_text.'</td>'
			        .'<td><a href="'.$item->A_HREF[0]->_text.'">Click to open via OWA</a></td>'
			        .'<td><a href="Outlook:Inbox/'.$item->A_PROPSTAT[0]->A_PROP[0]->A_DISPLAYNAME[0]->_text.'">Click to open via Outlook</a></td>'
			        ."</tr>\n";
			}
			echo "<table>\n"; 

		}

		/**
		 * Iterate the email items in a user's inbox
		 *
		 * @return object that is an array of objects and arrays that makes it easy to access the parts you need
		 */
		public function list_email()
		{
			$this->http->headers["Depth"] = "0";
			$this->http->headers["Translate"] = "f";

			// Find all the email items in the inbox for a user name.
			$this->http->xmlrequest = '<?xml version="1.0"?>';
			$this->http->xmlrequest .= <<<END
			<a:searchrequest xmlns:a="DAV:" xmlns:s="http://schemas.microsoft.com/exchange/security/">
			   <a:sql>
			       SELECT "DAV:displayname"
			       ,"urn:schemas:httpmail:subject"
			       FROM "{$this->exchange_server}/Exchange/{$this->exchange_username}/inbox"
			   </a:sql>
			</a:searchrequest>
END;
			// IMPORTANT -- The END line above must be completely left-aligned. No white-space.

			// The 'fetch' method does the work of sending and receiving the request.
			// NOTICE the last parameter passed--'SEARCH' in this example. That is the
			// HTTP verb that you must correctly set according to the type of WebDAV request
			// you are making.  The examples on this page use either 'PROPFIND' or 'SEARCH'.
			if (!$this->http->fetch("{$this->exchange_server}/Exchange/{$this->exchange_username}/inbox", 0, null, $this->exchange_username, $this->exchange_password, "SEARCH"))
			{
			  echo "<h2>There is a problem with the http request!</h2>";
			  echo $this->http->log;
			  exit();
			}

			// The assumption now is that we've got an XML result back from the Exchange
			// Server, so let's parse the XML into an object we can more easily access.
			// For this task, we'll use Troy's xml class object.
			$x = new xml();
			if (!$this->xml->fetch($this->http->body))
			{
			    echo "<h2>There was a problem parsing your XML!</h2>";
			    echo "<pre>".$this->http->log."</pre><hr />\n";
			    echo "<pre>".$this->http->header."</pre><hr />\n";
			    echo "<pre>".$this->http->body."</pre><hr />\n";
			    echo "<pre>".$this->xml->log."</pre><hr />\n";
			    exit();
			}

			// And finally, an example of iterating the email items to display in the
			// browser. I also show you 2 methods to link to the items. One uses the href
			// provided in the response which opens the folder using OWA. The other is an
			// Outlook style link to open the folder in the Outlook desktop client.
			echo '<table border="1">';
			foreach($this->xml->data->A_MULTISTATUS[0]->A_RESPONSE as $idx=>$item)
			{
			    echo '<tr>'
			        .'<td>'.$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text.'</td>'
			        .'<td><a href="'.$item->A_HREF[0]->_text.'">Click to open via OWA</a></td>'
			        .'<td><a href="Outlook:Inbox/~'.$item->A_PROPSTAT[0]->A_PROP[0]->D_SUBJECT[0]->_text.'">Click to open via Outlook</a></td>'
			        ."</tr>\n";
			}
			echo "<table>\n";

		}

		/**
		 * Search for contacts that match a certain criteria
		 *
		 * @return object that is an array of objects and arrays that makes it easy to access the parts you need
		 */
		public function list_contacts()
		{
			$this->http->headers["Depth"] = "0";
			$this->http->headers["Translate"] = "f";

			// Find all the contacts for a specific company in a specific folder.
			// http://msdn.microsoft.com/library/default.asp?url=/library/en-us/e2k3/e2k3/_exch2k_urn_content-classes_person.asp
			$this->http->xmlrequest = '<?xml version="1.0"?>';
			$this->http->xmlrequest .= <<<END
			<a:searchrequest xmlns:a="DAV:">
			    <a:sql>
			        SELECT "a:href"
			        ,"urn:schemas:contacts:o"
			        ,"urn:schemas:contacts:cn"
			        ,"urn:schemas:contacts:fileas"
			        ,"urn:schemas:contacts:title"
			        ,"urn:schemas:contacts:email1"
			        ,"urn:schemas:contacts:telephoneNumber"
			        FROM "{$this->exchange_server}/public/Customer%20Contacts/"
			        WHERE "urn:schemas:contacts:o" = 'XYZ Industries, Inc.'
			        ORDER BY "urn:schemas:contacts:cn"
			    </a:sql>
			</a:searchrequest>
END;
			// IMPORTANT -- The END line above must be completely left-aligned. No white-space.

			// The 'fetch' method does the work of sending and receiving the request.
			// NOTICE the last parameter passed--'SEARCH' in this example. That is the
			// HTTP verb that you must correctly set according to the type of WebDAV request
			// you are making.  The examples on this page use either 'PROPFIND' or 'SEARCH'.
			if (!$this->http->fetch("{$this->exchange_server}/public/Customer%20Contacts", 0, null, $this->exchange_username, $this->exchange_password, "SEARCH"))
			{
			  echo "<h2>There is a problem with the http request!</h2>";
			  echo $this->http->log;
			  exit();
			}

			// The assumption now is that we've got an XML result back from the Exchange
			// Server, so let's parse the XML into an object we can more easily access.
			// For this task, we'll use Troy's xml class object.
			$x = new xml();
			if (!$this->xml->fetch($this->http->body))
			{
			    echo "<h2>There was a problem parsing your XML!</h2>";
			    echo "<pre>".$this->http->log."</pre><hr />\n";
			    echo "<pre>".$this->http->header."</pre><hr />\n";
			    echo "<pre>".$this->http->body."</pre><hr />\n";
			    echo "<pre>".$this->xml->log."</pre><hr />\n";
			    exit();
			}

			// And finally, an example of iterating the contact items to display in the browser.
			echo '<table border="1">';
			echo "<tr><th>Company</th><th>Name</th><th>Title</th><th>Email</th><th>Phone</th></tr>\n";
			foreach($this->xml->data->A_MULTISTATUS[0]->A_RESPONSE as $idx=>$contact)
			{
			    echo '<tr>'
			        .'<td>'.$contact->A_PROPSTAT[0]->A_PROP[0]->E_O[0]->_text.'</td>'
			        .'<td><a href="outlook://Public%20Folders/All%20Public%20Folders/Account_Contacts/~'.str_replace(" ","%20",$contact->A_PROPSTAT[0]->A_PROP[0]->E_CN[0]->_text).'">'
			        .$contact->A_PROPSTAT[0]->A_PROP[0]->E_CN[0]->_text.'</a></td>'
			        .'<td>'.$contact->A_PROPSTAT[0]->A_PROP[0]->E_TITLE[0]->_text.'</td>'
			        .'<td>'.$contact->A_PROPSTAT[0]->A_PROP[0]->E_EMAIL1[0]->_text.'</td>'
			        .'<td>'.$contact->A_PROPSTAT[0]->A_PROP[0]->E_TELEPHONENUMBER[0]->_text.'</td>'
			        ."</tr>\n";
			}
			echo "<table>\n";

					}

		/**
		 * Grab all properties for an item
		 *
		 * @return object that is an array of objects and arrays that makes it easy to access the parts you need
		 */
		public function allprop()
		{
			$this->http->headers["Depth"] = "0";
			$this->http->headers["Translate"] = "f";

			// Find all the properties for a specific item.
			$this->http->xmlrequest = '<?xml version="1.0"?>';
			$this->http->xmlrequest .= <<<END
			<a:propfind xmlns:a="DAV:">
			    <a:allprop/>
			</a:propfind>
END;
			// IMPORTANT -- The END line above must be completely left-aligned. No white-space.

			// The 'fetch' method does the work of sending and receiving the request.
			// NOTICE the last parameter passed--'PROPFIND' in this example. That is the
			// HTTP verb that you must correctly set according to the type of WebDAV request
			// you are making.  The examples on this page use either 'PROPFIND' or 'SEARCH'.
			if (!$this->http->fetch("{$this->exchange_server}/public/Email%20Log/Some%20Message-668992879.EML", 0, null, $this->exchange_username, $this->exchange_password, "PROPFIND"))
			{
			  echo "<h2>There is a problem with the http request!</h2>";
			  echo $this->http->log;
			  exit();
			}

			// The assumption now is that we've got an XML result back from the Exchange
			// Server, so let's parse the XML into an object we can more easily access.
			// For this task, we'll use Troy's xml class object.
			$x = new xml();
			if (!$this->xml->fetch($this->http->body))
			{
			    echo "<h2>There was a problem parsing your XML!</h2>";
			    echo "<pre>".$this->http->log."</pre><hr />\n";
			    echo "<pre>".$this->http->header."</pre><hr />\n";
			    echo "<pre>".$this->http->body."</pre><hr />\n";
			    echo "<pre>".$this->xml->log."</pre><hr />\n";
			    exit();
			}

			echo "<pre>";
			print_r($this->xml->data);
			echo "</pre>";


		}

		/**
		 * Create a new contact if the URL does not exist or update an existing contact
		 * Notice the last 2 properties. Those are custom fields that must be pre-defined for the folder.
		 *
		 * @return object that is an array of objects and arrays that makes it easy to access the parts you need
		 */
		public function set_contact()
		{
			$this->http->headers["Depth"] = "0";
			$this->http->headers["Translate"] = "f";

			// Build the XML request.
			// This section must be against the left margin.
			$this->http->xmlrequest = '<?xml version="1.0"?>';
			$this->http->xmlrequest .= <<<END
			<g:propertyupdate   xmlns:g="DAV:"
			                    xmlns:b="urn:uuid:c2f41010-65b3-11d1-a29f-00aa00c14882/"
			                    xmlns:c="urn:schemas:contacts:"
			                    xmlns:e="http://schemas.microsoft.com/exchange/"
			                    xmlns:mapi="http://schemas.microsoft.com/mapi/"
			                    xmlns:o="urn:schemas-microsoft-com:office:office"
			                    xmlns:cust="urn:schemas:customproperty"
			                    xmlns:ed="urn:schemas-microsoft-com:exch-data:"
			                    xmlns:repl="http://schemas.microsoft.com/repl/"
			                    xmlns:x="xml:"
			                    xmlns:cal="urn:schemas:calendar:"
			                    xmlns:mail="urn:schemas:httpmail:"
			                    xmlns:ec="urn:schemas-microsoft-com:exch-data:expected-content-class"
			                    xmlns:j="urn:content-classes:propertydef"
			                    xmlns:mailheader="urn:schemas:mailheader:">
			    <g:set>
			        <g:prop>
			            <g:contentclass>urn:content-classes:person</g:contentclass>
			            <e:outlookmessageclass>IPM.Contact</e:outlookmessageclass>
			            <e:keywords-utf8>
			            <x:v>Buddies</x:v><x:v>Engineers</x:v>
			            </e:keywords-utf8>
			            <c:language>US English</c:language>
			            <c:o>Innotech, Inc.</c:o>
			            <c:givenName>John</c:givenName>
			            <c:sn>Doe</c:sn>
			            <c:cn>John Doe</c:cn>
			            <c:fileas>Doe, John</c:fileas>
			            <c:street>100 N. Main</c:street>
			            <c:postofficebox>PO Box 555</c:postofficebox>
			            <c:l>Kansas City</c:l>
			            <c:st>MO</c:st>
			            <c:postalcode>64118</c:postalcode>
			            <c:co>USA</c:co>
			            <c:telephoneNumber>425-555-1110</c:telephoneNumber>
			            <c:facsimiletelephonenumber>425-555-1112</c:facsimiletelephonenumber>
			            <c:homePhone>425-555-1113</c:homePhone>
			            <c:mobile>425-555-1117</c:mobile>
			            <mapi:email1addrtype>SMTP</mapi:email1addrtype>
			            <mapi:email1emailaddress>john.doe@hotmail.com</mapi:email1emailaddress>
			            <mapi:email1originaldisplayname>John Doe</mapi:email1originaldisplayname>
			            <favoritecolor>Blue</favoritecolor>
			            <newsletter b:dt="boolean">1</newsletter>
			        </g:prop>
			    </g:set>
			</g:propertyupdate>
END;
			// IMPORTANT -- The END line above must be completely left-aligned. No white-space.

			// The http object's 'fetch' method does the work of sending and receiving the
			// request. We use the WebDAV PROPPATCH method to create or update Exchange items.
			$url = "{$this->exchange_server}/public/Company%20Contacts/john%20doe.EML";
			if (!$this->http->fetch($url, 0, null, $this->exchange_username, $this->exchange_password, "PROPPATCH"))
			{
			  echo "<h2>There is a problem with the http request!</h2>";
			  echo $this->http->log;
			  exit();
			}

			// You can print out the response to help troubleshoot.
			echo "<pre>".$this->http->header."</pre><hr />\n";
			echo "<pre>".$this->http->body."</pre><hr />\n";

		}

		/**
		 * Create a new email message
		 *
		 * @return object that is an array of objects and arrays that makes it easy to access the parts you need
		 */
		public function new_email()
		{
			$this->http->headers["Depth"] = "0";
			$this->http->headers["Translate"] = "f";

			$subject = "Some subject";
			$htmldescription = "This is spam. Please delete this email.";
			// Build the XML request.
			// This section must be against the left margin.
			$this->http->xmlrequest = '<?xml version="1.0"?>';
			$this->http->xmlrequest .= <<<END
			<a:propertyupdate   xmlns:a="DAV:"
			                    xmlns:b="urn:uuid:c2f41010-65b3-11d1-a29f-00aa00c14882/"
			                    xmlns:g="http://schemas.microsoft.com/mapi/"
			                    xmlns:e="urn:schemas:httpmail:"
			                    xmlns:d="urn:schemas:mailheader:"
			                    xmlns:c="xml:"
			                    xmlns:f="http://schemas.microsoft.com/mapi/proptag/"
			                    xmlns:h="http://schemas.microsoft.com/exchange/"
			                    xmlns:i="urn:schemas-microsoft-com:office:office"
			                    xmlns:k="http://schemas.microsoft.com/repl/"
			                    xmlns:j="urn:schemas:calendar:"
			                    xmlns:l="urn:schemas-microsoft-com:exch-data:">
			    <a:set>
			        <a:prop>
			            <a:contentclass>urn:content-classes:message</a:contentclass>
			            <h:outlookmessageclass>IPM.Note</h:outlookmessageclass>
			            <d:to>foo@foobar.com</d:to>
			            <d:cc>bar@foobar.com</d:cc>
			            <d:bcc>bob@aol.com</d:bcc>
			            <g:subject>$subject</g:subject>
			            <e:htmldescription>$htmldescription</e:htmldescription>
			        </a:prop>
			    </a:set>
			</a:propertyupdate>
END;
			// IMPORTANT -- The END line above must be completely left-aligned. No white-space.

			// The http object's 'fetch' method does the work of sending and receiving the
			// request. We use the WebDAV PROPPATCH method to create or update Exchange items.
			$url = "{$this->exchange_server}/Exchange/{$this->exchange_username}/Drafts/".urlencode($subject).".EML";
			if (!$this->http->fetch($url, 0, null, $this->exchange_username, $this->exchange_password, "PROPPATCH"))
			{
			  echo "<h2>There is a problem with the http request!</h2>";
			  echo $this->http->log;
			  exit();
			}

			// You can print out the response to help troubleshoot.
			echo "<pre>".$this->http->header."</pre><hr />\n";
			echo "<pre>".$this->http->body."</pre><hr />\n";

			// Bonus tip! You can automatically open this new draft message for your user by
			// formulating an outlook URL. Then either redirect to the URL by uncommenting the
			// header line below, or pop the URL in client-side javascript using window.open.
			#header("Location: outlook:drafts/~".urlencode($subject));

		}
	}
