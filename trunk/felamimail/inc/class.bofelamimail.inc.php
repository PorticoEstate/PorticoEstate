<?php
	/***************************************************************************\
	* eGroupWare - FeLaMiMail                                                   *
	* http://www.linux-at-work.de                                               *
	* http://www.phpgw.de                                                       *
	* http://www.egroupware.org                                                 *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/

	/* $Id$ */

	/**
	* the core logic of FeLaMiMail
	*
	* This class contains all logic of FeLaMiMail.
	* @package FeLaMiMail
	* @author Lars Kneschke
	* @version 1.35
	* @copyright Lars Kneschke 2002,2003,2004
	* @license http://opensource.org/licenses/gpl-license.php GPL
	*/
	class bofelamimail
	{
		var $public_functions = array
		(
			'flagMessages'		=> True,
		);

		var $mbox;		// the mailbox identifier any function should use
		static $debug = false; //true; // sometimes debuging is quite handy, to see things. check with the error log to see results
		// define some constants
		// message types
		var $type = array("text", "multipart", "message", "application", "audio", "image", "video", "other");
		
		// message encodings
		var $encoding = array("7bit", "8bit", "binary", "base64", "quoted-printable", "other");
		static $displayCharset;		
		// set to true, if php is compiled with multi byte string support
		var $mbAvailable = FALSE;

		// what type of mimeTypes do we want from the body(text/html, text/plain)
		var $htmlOptions;

		var $sessionData;
		
		// the current selected user profile
		var $profileID = 0;
		
		/**
		 * Folders that get automatic created AND get translated to the users language
		 *
		 * @var array
		 */
		var $autoFolders = array('Drafts', 'Junk', 'Sent', 'Trash', 'Templates');
		
		function __construct($_displayCharset='utf-8')
		{
			$this->restoreSessionData();

			// FIXME: this->foldername seems to be unused
			//$this->foldername	= $this->sessionData['mailbox'];
			$this->accountid	= $GLOBALS['phpgw_info']['user']['account_id'];
			
			$this->bopreferences	=& CreateObject('felamimail.bopreferences');
			$this->sofelamimail	=& CreateObject('felamimail.sofelamimail');
			$this->botranslation	=& CreateObject('felamimail.translation');

			$this->mailPreferences	= $this->bopreferences->getPreferences();

			if(is_object($this->mailPreferences))
			{
				$this->icServer = $this->mailPreferences->getIncomingServer(0);
				$this->ogServer = $this->mailPreferences->getOutgoingServer(0);
			}

			#_debug_array($this->mailPreferences);
			$this->imapBaseDir	= '';

			self::$displayCharset	= $_displayCharset;
			if(function_exists(mb_decode_mimeheader)) {
				mb_internal_encoding(self::$displayCharset);
			}

			// set some defaults
			if(empty($this->sessionData)) {
				// this should be under user preferences
				// sessionData empty
				// no filter active
				$this->sessionData['activeFilter']	= "-1";
				// default mailbox INBOX
				$this->sessionData['mailbox']		= "INBOX";
				// default start message
				$this->sessionData['startMessage']	= 1;
				// default mailbox for preferences pages
				$this->sessionData['preferences']['mailbox']	= "INBOX";
				
				$this->sessionData['messageFilter'] = array(
					'string'	=> '',
					'type'		=> 'quick',
					'status'	=> 'any',
				);
				
				// default sorting
				switch($GLOBALS['phpgw_info']['user']['preferences']['felamimail']['sortOrder']) {
					case 1:
						$this->sessionData['sort'] = SORTDATE;
						$this->sessionData['sortReverse'] = false;
						break;
					case 2:
						$this->sessionData['sort'] = SORTFROM;
						$this->sessionData['sortReverse'] = true;
						break;
					case 3:
						$this->sessionData['sort'] = SORTFROM;
						$this->sessionData['sortReverse'] = false;
						break;
					case 4:
						$this->sessionData['sort'] = SORTSUBJECT;
						$this->sessionData['sortReverse'] = true;
						break;
					case 5:
						$this->sessionData['sort'] = SORTSUBJECT;
						$this->sessionData['sortReverse'] = false;
						break;
					case 6:
						$this->sessionData['sort'] = SORTSIZE;
						$this->sessionData['sortReverse'] = true;
						break;
					case 7:
						$this->sessionData['sort'] = SORTSIZE;
						$this->sessionData['sortReverse'] = false;
						break;
					default:
						$this->sessionData['sort'] = SORTDATE;
						$this->sessionData['sortReverse'] = true;
						break;
				}
				$this->saveSessionData();
			}
			
			if (function_exists('mb_convert_encoding')) {
				$this->mbAvailable = TRUE;
			}

			$this->htmlOptions	= $this->mailPreferences->preferences['htmlOptions'];
		}
		
		function setACL($_folderName, $_accountName, $_acl)
		{
			if ( PEAR::isError($this->icServer->setACL($_folderName, $_accountName, $_acl)) ) {
				return false;
			}
			
			return TRUE;
		}
		
		function deleteACL($_folderName, $_accountName)
		{
			if ( PEAR::isError($this->icServer->deleteACL($_folderName, $_accountName)) ) {
				return false;
			}
			
			return TRUE;
		}
		
		/**
		* hook to add account
		*
		* this function is a wrapper function for emailadmin
		*
		* @param _hookValues contains the hook values as array
		* @returns nothing
		*/
		function addAccount($_hookValues) 
		{
			if(!$this->mailPreferences)
			{
				phpgwapi_cache::message_set('bofelamimail::mailPreferences not set', 'error');
				return;
			}
			$icServer = $this->mailPreferences->getIncomingServer(0);
			if(is_a($icServer,'defaultimap')) {
				$icServer->addAccount($_hookValues);
			}

			$ogServer = $this->mailPreferences->getOutgoingServer(0);
			if(is_a($ogServer,'defaultsmtp')) {
				$ogServer->addAccount($_hookValues);
			}
		}
		
		function adminMenu()
		{
 			if ($GLOBALS['phpgw_info']['server']['account_repository'] == "ldap")
			{
									$data = Array
							(
					'description'   => 'email settings',
					'url'           => '/index.php',
					'extradata'     => 'menuaction=emailadmin.uiuserdata.editUserData'
				);
			
				//Do not modify below this line
				global $menuData;
			
				$menuData[] = $data;
			}
		}
		
		/**
		* save a message in folder
		*
		* @todo set flags again
		*
		* @param string _folderName the foldername 
		* @param string _header the header of the message
		* @param string _body the body of the message
		* @param string _flags the imap flags to set for the saved message
		*
		* @returns the id of the message appended or false
		*/
		function appendMessage($_folderName, $_header, $_body, $_flags)
		{
			$header = ltrim(str_replace("\n","\r\n",$_header));
			$body   = str_replace("\n","\r\n",$_body);
			$messageid = $this->icServer->appendMessage("$header"."$body", $_folderName, $_flags);
			if ( PEAR::isError($messageid)) {
				return false;
			}
			return $messageid;
		}
		
		function closeConnection() {
			$this->icServer->disconnect();
		}
		
		/**
		* remove any messages which are marked as deleted or
		* remove any messages from the trashfolder
		*
		* @param string _folderName the foldername 
		* @returns nothing
		*/
		function compressFolder($_folderName = false)
		{
			$folderName	= ($_folderName ? $_folderName : $this->sessionData['mailbox']);
			$deleteOptions	= $GLOBALS['phpgw_info']['user']['preferences']['felamimail']['deleteOptions'];
			$trashFolder	= $GLOBALS['phpgw_info']['user']['preferences']['felamimail']['trashFolder'];
			
			$this->icServer->selectMailbox($folderName);

			if($folderName == $trashFolder && $deleteOptions == "move_to_trash") {
				$this->icServer->deleteMessages('1:*');
				$this->icServer->expunge();
			} else {
				$this->icServer->expunge();
			}
		}
		
		/**
		* create a new folder under given parent folder
		*
		* @param string _parent the parent foldername
		* @param string _folderName the new foldername 
		* @param bool _subscribe subscribe to the new folder
		*
		* @returns mixed name of the newly created folder or false on error
		*/
		function createFolder($_parent, $_folderName, $_subscribe=false)
		{
			$parent		= $this->_encodeFolderName($_parent);
			$folderName	= $this->_encodeFolderName($_folderName);
			
			if(empty($parent)) {
				$newFolderName = $folderName;
			} else {
				$HierarchyDelimiter = $this->icServer->getHierarchyDelimiter();
				if (PEAR::isError($HierarchyDelimiter)) $HierarchyDelimiter = '/';
				$newFolderName = $parent . $HierarchyDelimiter . $folderName;
			}
			
			if ( PEAR::isError($this->icServer->createMailbox($newFolderName) ) ) {
				return false;
			}

			if ( PEAR::isError($this->icServer->subscribeMailbox($newFolderName) ) ) {
				return false;
			}
			
			return $newFolderName;

		}
		
		function createIMAPFilter($_folder, $_criterias) 
		{
			if(!is_array($_criterias)) {
				return 'ALL';
			}
		#	error_log(print_r($_criterias, true));
			$imapFilter = '';
			
			#foreach($_criterias as $criteria => $parameter) {
			if(!empty($_criterias['string'])) {
				$criteria = strtoupper($_criterias['type']);
				switch ($criteria) {
					case 'QUICK':
						if($this->isSentFolder($_folder)) {
							$imapFilter .= 'OR SUBJECT "'. $_criterias['string'] .'" TO "'. $_criterias['string'] .'" ';
						} else {
							$imapFilter .= 'OR SUBJECT "'. $_criterias['string'] .'" FROM "'. $_criterias['string'] .'" ';
						}
						break;
					case 'BCC':
					case 'BODY':
					case 'CC':
					case 'FROM':
					case 'KEYWORD':
					case 'SUBJECT':
					case 'TEXT':
					case 'TO':
						$imapFilter .= $criteria .' "'. $_criterias['string'] .'" ';
						break;
				}
			}

			#foreach($_criterias as $criteria => $parameter) {
				$criteria = strtoupper($_criterias['status']);
				switch ($criteria) {
					case 'ANSWERED':
					case 'DELETED':
					case 'FLAGGED':
					case 'NEW':
					case 'OLD':
					case 'RECENT':
					case 'SEEN':
					case 'UNANSWERED':
					case 'UNDELETED':
					case 'UNFLAGGED':
					case 'UNSEEN':
						$imapFilter .= $criteria .' ';
						break;
					
					case 'BEFORE':
					case 'ON':
					case 'SINCE':
						$imapFilter .= $criteria .' "'. date() .'" ';
						break;
				}
			#}
		#	error_log("Filter: $imapFilter");
			if($imapFilter == '') {
				return 'ALL';
			} else {
				return trim($imapFilter);
				#return 'CHARSET '. strtoupper(self::$displayCharset) .' '. trim($imapFilter);
			}
		}
		
		/**
		* convert a mailboxname from displaycharset to urf7-imap
		*
		* @param string _folderName the foldername 
		*
		* @returns string the converted foldername
		*/
		function decodeFolderName($_folderName)
		{
			return $this->botranslation->convert($_folderName, self::$displayCharset, 'UTF7-IMAP');
		}

		function decodeMimePart($_mimeMessage, $_encoding, $_charset = '') 
		{
			// decode the part
			if (self::$debug) error_log("bofelamimail::decodeMimePart: ".print_r($_mimeMessage,true));
			switch ($_encoding) 
			{
				case 'BASE64':
					// use imap_base64 to decode
					return imap_base64($_mimeMessage);
					break;
				case 'QUOTED-PRINTABLE':
					// use imap_qprint to decode
					return quoted_printable_decode($_mimeMessage);
					break;
				default:
					// it is either not encoded or we don't know about it
					return $_mimeMessage;
					break;
			}
		}

		function decode_header($_string)
		{
			if(function_exists(mb_decode_mimeheader)) {
				$string = $_string;
				if(preg_match_all('/=\?.*\?Q\?.*\?=/iU', $string, $matches)) {
					foreach($matches[0] as $match) {
						$fixedMatch = str_replace('_', ' ', $match);
						$string = str_replace($match, $fixedMatch, $string);
					}
					$string = str_replace('=?ISO8859-','=?ISO-8859-',$string);
				}
				$string = mb_decode_mimeheader($string);
				return preg_replace('/([\000-\012\015\016\020-\037\075])/','',$string);
			} elseif(function_exists(iconv_mime_decode)) {
				// continue decoding also if an error occurs
				$string = @iconv_mime_decode($_string, 2, self::$displayCharset);
				return preg_replace('/([\000-\012\015\016\020-\037\075])/','',$string);
			} elseif(function_exists(imap_mime_header_decode)) {
				$newString = '';

				$string = preg_replace('/\?=\s+=\?/', '?= =?', $_string);

				$elements=imap_mime_header_decode($string);

				foreach((array)$elements as $element) {
					if ($element->charset == 'default')
						$element->charset = 'iso-8859-1';
					$tempString = $this->botranslation->convert($element->text,$element->charset);
					$newString .= $tempString;
				}
				
				return preg_replace('/([\000-\012\015\016\020-\037\075])/','',$newString);
			}
			
			// no decoding function available
			return preg_replace('/([\000-\012\015\016\020-\037\075])/','',$_string);
		}

		function decode_subject($_string)
		{
			#$string = $_string;
			$_string = self::decode_header($_string);
			if($_string=='NIL')
			{
				$_string = 'No Subject';
			}
			return $_string;

		}

		/**
		 * decodes winmail.dat attachments
		 *
		 * @param int $_uid
		 * @param string $_partID
		 * @param int $_filenumber
		 * @return array
		 */
		function decode_winmail( $_uid, $_partID, $_filenumber=0 ) 
		{
			$attachment = $this->getAttachment( $_uid, $_partID );

			$dir = $GLOBALS['phpgw_info']['server']['temp_dir']."/fmail_winmail/$_uid";
			$mime = CreateObject('phpgwapi.mime_magic');
			if ( $attachment['type'] == 'APPLICATION/MS-TNEF' && $attachment['filename'] == 'winmail.dat' ) 
			{
				// decode winmail.dat
				if ( !file_exists( "$dir/winmail.dat" ) ) 
				{
					mkdir( $dir, 0700, true );
					file_put_contents( "$dir/winmail.dat", $attachment['attachment'] );
					exec( "cd $dir && ytnef -f . winmail.dat" );
				}

				// list contents
				$files = scandir( $dir );
				foreach ( $files as $num => $file ) 
				{
					if ( filetype( "$dir/$file" ) != 'file' || $file == 'winmail.dat' ) continue;
					if ( $_filenumber > 0 && $_filenumber != $num ) continue;
					$type = $mime->filename2mime($file);
					$attachments[] = array(
						'is_winmail' => $num,
						'name' => $file,
						'size' => filesize( "$dir/$file"),
						'partID' => $_partID,
						'mimeType' => $type,
						'type' => $type,
						'attachment' => $_filenumber > 0 ? file_get_contents("$dir/$file") : '',
					);
					unlink($dir."/".$file);
				}
				if (file_exists($dir."/winmail.dat")) unlink($dir."/winmail.dat");
				if (file_exists($dir)) rmdir($dir);
				return $_filenumber > 0 ? $attachments[0] : $attachments;
			}
			return false;
		}
	
		function deleteAccount($_hookValues)
		{
			$icServer = $this->mailPreferences->getIncomingServer(0);
			if(is_a($icServer,'defaultimap')) {
				$icServer->deleteAccount($_hookValues);
			}

			$ogServer = $this->mailPreferences->getOutgoingServer(0);
			if(is_a($ogServer,'defaultsmtp')) {
				$ogServer->deleteAccount($_hookValues);
			}
		}
		
		/**
		* delete a existing folder
		*
		* @param string _folderName the name of the folder to be deleted
		*
		* @returns bool true on success, false on failure
		*/
		function deleteFolder($_folderName) 
		{
			$folderName = $this->_encodeFolderName($_folderName);
			
			$this->icServer->unsubscribeMailbox($folderName);
			if ( PEAR::isError($this->icServer->deleteMailbox($folderName)) ) {
				return false;
			}
			
			return true;
		}

		function deleteMessages($_messageUID, $_folder=NULL) 
		{
			$msglist = '';
			$oldMailbox = '';
			if (is_null($_folder) || empty($_folder)) $_folder = $this->sessionData['mailbox'];	
			if(!is_array($_messageUID) || count($_messageUID) === 0) {
				return false;
			}

			$deleteOptions  = $this->mailPreferences->preferences['deleteOptions'];
			$trashFolder    = $this->mailPreferences->preferences['trashFolder'];
			$draftFolder	= $GLOBALS['phpgw_info']['user']['preferences']['felamimail']['draftFolder'];
			$templateFolder    = $GLOBALS['phpgw_info']['user']['preferences']['felamimail']['templateFolder'];

			if(($this->sessionData['mailbox'] == $trashFolder && $deleteOptions == "move_to_trash") ||
			   ($this->sessionData['mailbox'] == $draftFolder)) {
				$deleteOptions = "remove_immediately";
			}
			if($this->icServer->getCurrentMailbox() != $_folder) {
				$oldMailbox = $this->icServer->getCurrentMailbox();
				$this->icServer->selectMailbox($_folder);
			}
			
			switch($deleteOptions) {
				case "move_to_trash":
					if(!empty($trashFolder)) {
						#error_log(implode(' : ', $_messageUID));
						#error_log("$trashFolder <= ". $this->sessionData['mailbox']);
						// copy messages
						if ( PEAR::isError($this->icServer->copyMessages($trashFolder, $_messageUID, $_folder, true)) ) {
							return false;
						}
						// mark messages as deleted
						if ( PEAR::isError($this->icServer->deleteMessages($_messageUID, true))) {
							return false;
						}
						// delete the messages finaly
						$this->icServer->expunge();
					}
					break;

				case "mark_as_deleted":
					// mark messages as deleted
					if ( PEAR::isError($this->icServer->deleteMessages($_messageUID, true))) {
						return false;
					}
					break;

				case "remove_immediately":
					// mark messages as deleted
					if ( PEAR::isError($this->icServer->deleteMessages($_messageUID, true))) {
						return false;
					}
					// delete the messages finaly
					$this->icServer->expunge();
					break;
			}
			
			if($oldMailbox != '') {
				$this->icServer->selectMailbox($oldMailbox);
			}
			
			return true;
		}
		
		/**
		* convert a mailboxname from utf7-imap to displaycharset 
		*
		* @param string _folderName the foldername 
		*
		* @returns string the converted string
		*/
		function encodeFolderName($_folderName)
		{
			return $this->botranslation->convert($_folderName, 'UTF7-IMAP', self::$displayCharset);
		}

#		function encodeHeader($_string, $_encoding='q')
#		{
#			switch($_encoding) {
#				case "q":
#					if(!preg_match("/[\x80-\xFF]/",$_string)) {
#						// nothing to quote, only 7 bit ascii
#						return $_string;
#					}
#					
#					$string = imap_8bit($_string);
#					$stringParts = explode("=\r\n",$string);
#					while(list($key,$value) = each($stringParts)) {
#						if(!empty($retString)) $retString .= " ";
#						$value = str_replace(" ","_",$value);
#						// imap_8bit does not convert "?"
#						// it does not need, but it should
#						$value = str_replace("?","=3F",$value);
#						$retString .= "=?".strtoupper(self::$displayCharset). "?Q?". $value. "?=";
#					}
#					#exit;
#					return $retString;
#					break;
#				default:
#					return $_string;
#			}
#		}

		function getNotifyFlags ($_messageUID) {
			$flags =  $this->icServer->getFlags($_messageUID, true);
			if (PEAR::isError($flags)) {
				return null;
				}
			if ( in_array('MDNSent',$flags[0]) )
				return true;

			if ( in_array('MDNnotSent',$flags[0]) )
				return false;

			return null;
		}

		function flagMessages($_flag, $_messageUID)
		{
			#error_log("felamimail::bocompose::flagMessages");
			if(!is_array($_messageUID)) {
				#return false;
				$_messageUID=array($_messageUID);
			}
			
			$this->icServer->selectMailbox($this->sessionData['mailbox']);
			
			switch($_flag) {
				case "flagged":
					$this->icServer->setFlags($_messageUID, '\\Flagged', 'add', true);
					break;
				case "read":
					$this->icServer->setFlags($_messageUID, '\\Seen', 'add', true);
					break;
				case "forwarded":
					$this->icServer->setFlags($_messageUID, '$Forwarded', 'add', true);
				case "answered":
					$this->icServer->setFlags($_messageUID, '\\Answered', 'add', true);
					break;
				case "unflagged":
					$this->icServer->setFlags($_messageUID, '\\Flagged', 'remove', true);
					break;
				case "unread":
					$this->icServer->setFlags($_messageUID, '\\Seen', 'remove', true);
					$this->icServer->setFlags($_messageUID, '\\Answered', 'remove', true);
					$this->icServer->setFlags($_messageUID, '$Forwarded', 'remove', true);
					break;
				case "mdnsent":
					$this->icServer->setFlags($_messageUID, 'MDNSent', 'add', true);
					break;
				case "mdnnotsent":
					$this->icServer->setFlags($_messageUID, 'MDNnotSent', 'add', true);
					break;
			}
			
			$this->sessionData['folderStatus'][$this->profileID][$this->sessionData['mailbox']]['uidValidity'] = 0;
			$this->saveSessionData();
		}
		
		function _getSubStructure($_structure, $_partID)
		{
			$tempID = '';
			$structure = $_structure;
			$imapPartIDs = explode('.',$_partID);

			if($_partID != 1) {
				foreach($imapPartIDs as $imapPartID) {
					if(!empty($tempID)) {
						$tempID .= '.';
					}
					$tempID .= $imapPartID;
					//print "TEMPID: $tempID<br>";
					//_debug_array($structure);
					if($structure->subParts[$tempID]->type == 'MESSAGE' && $structure->subParts[$tempID]->subType == 'RFC822' &&
					   count($structure->subParts[$tempID]->subParts) == 1 &&
					   $structure->subParts[$tempID]->subParts[$tempID]->type == 'MULTIPART' &&
					   ($structure->subParts[$tempID]->subParts[$tempID]->subType == 'MIXED' ||
					    $structure->subParts[$tempID]->subParts[$tempID]->subType == 'ALTERNATIVE' ||
					    $structure->subParts[$tempID]->subParts[$tempID]->subType == 'RELATED' ||
					    $structure->subParts[$tempID]->subParts[$tempID]->subType == 'REPORT')) 
					{
						$structure = $structure->subParts[$tempID]->subParts[$tempID];
					} else {
						$structure = $structure->subParts[$tempID];
					}
				}
			}

			if($structure->partID != $_partID) {
				foreach($imapPartIDs as $imapPartID) {
					if(!empty($tempID)) {
						$tempID .= '.';
					}
					$tempID .= $imapPartID;
					//print "TEMPID: $tempID<br>";
					//_debug_array($structure);
					if($structure->subParts[$tempID]->type == 'MESSAGE' && $structure->subParts[$tempID]->subType == 'RFC822' &&
					   count($structure->subParts[$tempID]->subParts) == 1 &&
					   $structure->subParts[$tempID]->subParts[$tempID]->type == 'MULTIPART' &&
					   ($structure->subParts[$tempID]->subParts[$tempID]->subType == 'MIXED' || 
					    $structure->subParts[$tempID]->subParts[$tempID]->subType == 'ALTERNATIVE' ||
					    $structure->subParts[$tempID]->subParts[$tempID]->subType == 'RELATED' ||
					    $structure->subParts[$tempID]->subParts[$tempID]->subType == 'REPORT')) {
						$structure = $structure->subParts[$tempID]->subParts[$tempID];
					} else {
						$structure = $structure->subParts[$tempID];
					}
				}
				if($structure->partID != $_partID) {
					error_log("bofelamimail::_getSubStructure(". __LINE__ .") partID's don't match");
					return false;
				}
			}
			
			return $structure;
		}

		/*
		* strip tags out of the message completely with their content
		* param $_body is the text to be processed
		* param $tag is the tagname which is to be removed. Note, that only the name of the tag is to be passed to the function
		*            without the enclosing brackets
		* param $endtag can be different from tag  but should be used only, if begin and endtag are known to be different e.g.: <!-- -->
		*/
		static function replaceTagsCompletley(&$_body,$tag,$endtag='')
		{
			if ($tag) $tag = strtolower($tag);
			if ($endtag == '' || empty($endtag) || !isset($endtag)) 
			{
			        $endtag = $tag;
			} else {
			        $endtag = strtolower($endtag);
			}
			// strip tags out of the message completely with their content
			$taglen=strlen($tag);
			$endtaglen=strlen($endtag);
			if ($_body) {
				$begin_tag = strpos(strtolower($_body),'<'.$tag);
				while ($begin_tag !== FALSE) {
					$bodylength = strlen($_body);
					//since there is a begin tag there should be an end tag, starting somewhere at least the length of the tag down chars further down
					$end_tag=strpos(strtolower($_body),$endtag.'>',$begin_tag+$taglen+1);
					if ($end_tag !== FALSE && $end_tag > $begin_tag) {
						if (self::$debug) error_log("bofelamimail:replaceTagsCompletley: substitution of (<)$tag to $endtag(>) from position $begin_tag:". substr($_body,$begin_tag,$taglen+10));
						if (self::$debug) error_log("bofelamimail:replaceTagsCompletley: substitute to $endtag(>) at position ".($end_tag+$endtaglen+1).":".substr($_body,$end_tag+$endtaglen+1-$endtaglen-1,$endtaglen+10));
						$_body = substr($_body,0,$begin_tag-1).substr($_body,$end_tag+$endtaglen+1);
					} else {
						//somehow there is a begin tag of a tag but no end tag. throw it away
						// we will take care of this later on/somewhere else: now
						if (self::$debug) error_log("bofelamimail:replaceTagsCompletley: substitution of (<)$tag(>), since there is no end tag");
						$end_tag=strpos(strtolower($_body),'>',$begin_tag+$taglen+1);
						$_body = substr($_body,0,$begin_tag-1).substr($_body,$end_tag+1);
						//break;
					}
					$new_start = strpos(strtolower($_body),'<'."$tag");
					if ($new_start == $begin_tag && $bodylength == strlen($_body)) {
						// sometimes the substitution does not take place, so if the position does not change: break
						break;
					} else {
						$begin_tag = $new_start;
					}
					if (strlen($_body)<$begin_tag) break;
				}
			}
		}
		
		static function getCleanHTML(&$_html)
		{
			$kses	= CreateObject('felamimail.kses');
			$kses->AddProtocol('cid');
			// since check protocoll is called for every value associated to an attribute we have to add color and background-color to the valid protocolls
			$kses->AddProtocol('color');
			$kses->AddProtocol('background-color');
			$kses->AddHTML(
				'p', array(
					'align'	=> array('minlen' =>   1, 'maxlen' =>  10)
				)
			);
			$kses->AddHTML("tbody");
			$kses->AddHTML("thead");
			$kses->AddHTML("tt");
			$kses->AddHTML("br");
			$kses->AddHTML("b");
			$kses->AddHTML("u");
			$kses->AddHTML("s");
			$kses->AddHTML("i");
			$kses->AddHTML('em');
			$kses->AddHTML("strong");
			$kses->AddHTML("strike");
			$kses->AddHTML("center");
			$kses->AddHTML(
				"font",array(
					"color"	=> array('maxlen' => 20),
					"size"=>array('maxlen'=>2)
				)
			);
			$kses->AddHTML(
				"hr",array(
					"class"		=> array('maxlen' => 20),
					"style"		=> array('minlen' => 1),
				)
			);
			$kses->AddHTML(
				"div",array(
					'align' => array('maxlen' => 10)
				)
			);
			$kses->AddHTML("ul");
			$kses->AddHTML(
				"ol",array(
					"type"	=> array('maxlen' => 20)
				)
			);
			$kses->AddHTML("li");
			$kses->AddHTML("h1");
			$kses->AddHTML("h2");
			$kses->AddHTML("h3");
			$kses->AddHTML(
				"style",array(
					"type"	=> array('maxlen' => 20),
					"color"	=> array('maxlen' => 20),
					"background-color" => array('maxlen' => 20)
				)
			);

			$kses->AddHTML("select");
			$kses->AddHTML(
				"option",array(
					"value" => array('maxlen' => 45),
					"selected" => array()
				)
			);

			$kses->AddHTML(
				"a", array(
					"href" 		=> array('maxlen' => 145, 'minlen' => 10),
					"name" 		=> array('minlen' => 2),
					'target'	=> array('maxlen' => 10)
				)
			);

			$kses->AddHTML(
				"pre", array(
					"wrap" => array('maxlen' => 10)
				)
			);
			
			//      Allows 'td' tag with colspan|rowspan|class|style|width|nowrap attributes,
			//              colspan has minval of   2       and maxval of 5
			//              rowspan has minval of   3       and maxval of 6
			//              class   has minlen of   1 char  and maxlen of   10 chars
			//              style   has minlen of  10 chars and maxlen of 100 chars
			//              width   has maxval of 100
			//              nowrap  is valueless
			$kses->AddHTML(
				"table",array(
					"class"   => array("minlen" =>   1, 'maxlen' =>  20),
					"border"   => array("minlen" =>   1, 'maxlen' =>  10),
					"cellpadding"   => array("minlen" =>   0, 'maxlen' =>  10),
					"cellspacing"   => array("minlen" =>   0, 'maxlen' =>  10),
					"width"   => array("maxlen" => 5),
					"style"   => array('minlen' =>  10, 'maxlen' => 100),
					"bgcolor"   => array('maxlen' =>  10),
					"align"   => array('maxlen' =>  10),
					"valign"   => array('maxlen' =>  10),
					"bordercolor"   => array('maxlen' =>  10)
				)
			);
			$kses->AddHTML(
				"tr",array(
					"colspan"	=> array('minval' =>   2, 'maxval' =>   5),
					"rowspan"	=> array('minval' =>   3, 'maxval' =>   6),
					"class"		=> array("minlen" =>   1, 'maxlen' =>  20),
					"width"		=> array("maxlen" => 5),
					"style"		=> array('minlen' =>  10, 'maxlen' => 100),
					"align"		=> array('maxlen' =>  10),
					'bgcolor'	=> array('maxlen' => 10),
					"valign"	=> array('maxlen' =>  10),
					"nowrap"	=> array('valueless' => 'y')
				)
			);
			$kses->AddHTML(
				"td",array(
					"colspan" => array('minval' =>   2, 'maxval' =>   5),
					"rowspan" => array('minval' =>   3, 'maxval' =>   6),
					"class"   => array("minlen" =>   1, 'maxlen' =>  20),
					"width"   => array("maxlen" => 5),
					"style"   => array('minlen' =>  10, 'maxlen' => 100),
					"align"   => array('maxlen' =>  10),
					'bgcolor' => array('maxlen' => 10),
					"valign"   => array('maxlen' =>  10),
					"nowrap"  => array('valueless' => 'y')
				)
			);
			$kses->AddHTML(
				"th",array(
					"colspan" => array('minval' =>   2, 'maxval' =>   5),
					"rowspan" => array('minval' =>   3, 'maxval' =>   6),
					"class"   => array("minlen" =>   1, 'maxlen' =>  20),
					"width"   => array("maxlen" => 5),
					"style"   => array('minlen' =>  10, 'maxlen' => 100),
					"align"   => array('maxlen' =>  10),
					"valign"   => array('maxlen' =>  10),
					"nowrap"  => array('valueless' => 'y')
				)
			);
			$kses->AddHTML(
				"span",array(
					"class"   => array("minlen" =>   1, 'maxlen' =>  20),
					"style"	  => array('minlen' =>  5, 'maxlen' => 100) 
				)
			);
			$kses->AddHTML(
				"blockquote",array(
					"class"	=> array("minlen" =>   1, 'maxlen' =>  20),
					"style"	=> array("minlen" =>   1),
					"cite"	=> array('maxlen' => 30),
					"type"	=> array('maxlen' => 10),
					"dir"	=> array("minlen" =>   1, 'maxlen' =>  10)
				)
			);

			$kses->AddHTML(
				'img',array(
					"src"		=> array("minlen" =>   4, 'maxlen' =>  200, $GLOBALS['phpgw_info']['user']['preferences']['felamimail']['allowExternalIMGs'] ? '' : 'match' => '/^cid:.*/'),
					"align"		=> array("minlen" =>   1),
					"border"	=> array('maxlen' => 30),
				)
			);
			// clean out empty or pagewide style definitions
			self::replaceTagsCompletley($_html,'style>','</style');
			// no scripts allowed
			self::replaceTagsCompletley($_html,'script', '</script');
			// clean ot comments
			self::replaceTagsCompletley($_html,'!--','--');

			$_html = $kses->Parse($_html);
			// there may be leftovers clean out empty or pagewide style definitions
			self::replaceTagsCompletley($_html,'style>','</style');
			$_html = preg_replace('/([\000-\012])/','',$_html);
		}

		/**
		* retrieve a attachment
		*
		* @param int _uid the uid of the message
		* @param string _partID the id of the part, which holds the attachment
		* @param int _winmail_nr winmail.dat attachment nr.
		*
		* @returns array
		*/
		function getAttachment($_uid, $_partID, $_winmail_nr=0)
		{
			// parse message structure
			$structure = $this->icServer->getStructure($_uid, true);
			if($_partID != '') {
				$structure = $this->_getSubStructure($structure, $_partID);
			}

			if(isset($structure->parameters['NAME'])) {
				$filename	= $this->decode_header($structure->parameters['NAME']);
			} elseif(isset($structure->dparameters['FILENAME'])) {
				$filename	= $this->decode_header($structure->dparameters['FILENAME']);
                        } elseif(isset($structure->dparameters['FILENAME*'])) {
                                $filename       = $this->decode_header($structure->dparameters['FILENAME*']);
			} else {
				$filename	= lang("unknown");
			}
			
			$attachment = $this->icServer->getBodyPart($_uid, $_partID, true);
			
			switch ($structure->encoding) {
				case 'BASE64':
					// use imap_base64 to decode
					$attachment = imap_base64($attachment);
					break;
				case 'QUOTED-PRINTABLE':
					// use imap_qprint to decode
					#$attachment = imap_qprint($attachment);
					$attachment = quoted_printable_decode($attachment);
					break;
				default:
					// it is either not encoded or we don't know about it
			}
			
			$attachmentData = array(
				'type'		=> $structure->type .'/'. $structure->subType,
				'filename'	=> $filename, 
				'attachment'	=> $attachment
				);
			# if the attachment holds a winmail number and is a winmail.dat then we have to handle that.
			if ( $filename == 'winmail.dat' && $_winmail_nr > 0 &&
				( $wmattach = $this->decode_winmail( $_uid, $_partID, $_winmail_nr ) ) ) 
			{
				$attachmentData = array(
					'type'       => $wmattach['type'],
					'filename'   => $wmattach['name'], 
					'attachment' => $wmattach['attachment'],
 				);
			}
			return $attachmentData;
		}
		
		// this function is based on a on "Building A PHP-Based Mail Client"
		// http://www.devshed.com
		// fetch a specific attachment from a message
		function getAttachmentByCID($_uid, $_cid, $_part)
		{
			$partID = false;
			
			$attachments = $this->getMessageAttachments($_uid, $_part);
			foreach($attachments as $attachment) {
				if(strpos($attachment['cid'], $_cid) !== false) {
					$partID = $attachment['partID'];
					break;
				}
			}

			#print "PARTID: $partID<bR>"; exit;

			if($partID == false) {
				return false;
			}

			// parse message structure
			$structure = $this->icServer->getStructure($_uid, true);
			$structure = $this->_getSubStructure($structure, $partID);

			if(isset($structure->parameters['NAME'])) {
				$filename	= $this->decode_header($structure->parameters['NAME']);
			} elseif(isset($structure->dparameters['FILENAME'])) {
				$filename	= $this->decode_header($structure->dparameters['FILENAME']);
                        } elseif(isset($structure->dparameters['FILENAME*'])) {
                                $filename       = $this->decode_header($structure->dparameters['FILENAME*']);
			} else {
				$filename	= lang("unknown");
			}
			
			$attachment = $this->icServer->getBodyPart($_uid, $partID, true);
			
			switch ($structure->encoding) {
				case 'BASE64':
					// use imap_base64 to decode
					$attachment = imap_base64($attachment);
					break;
				case 'QUOTED-PRINTABLE':
					// use imap_qprint to decode
					#$attachment = imap_qprint($attachment);
					$attachment = quoted_printable_decode($attachment);
					break;
				default:
					// it is either not encoded or we don't know about it
			}
			
			$attachmentData = array(
				'type'		=> $structure->type .'/'. $structure->subType,
				'filename'	=> $filename, 
				'attachment'	=> $attachment
			);
				
			return $attachmentData;
		}
		
		function getEMailProfile()
		{
			$config =& CreateObject('phpgwapi.config','felamimail');
			$config->read();
			$felamimailConfig = $config->config_data;
			
			#_debug_array($felamimailConfig);
			
			if(!isset($felamimailConfig['profileID'])){
				return -1;
			} else {
				return intval($felamimailConfig['profileID']);
			}
		}
		
		function getErrorMessage()
		{
			return $this->icServer->_connectionErrorObject->message;
		}		

		/**
		* get IMAP folder status
		*
		* returns an array information about the imap folder
		*
		* @param _folderName string the foldername
		*
		* @returns array
		*/
		function getFolderStatus($_folderName)
		{
			$retValue = array();
			$retValue['subscribed'] = false;
			if(!$icServer = $this->mailPreferences->getIncomingServer(0)) {
				return false;
			}

			// does the folder exist???
			$folderInfo = $this->icServer->getMailboxes('', $_folderName, true);
			if(is_a($folderInfo, 'PEAR_Error') || !is_array($folderInfo[0])) {
				return false;
			}
			#if(!is_array($folderInfo[0])) {
			#	return false;
			#}
			
			$subscribedFolders = $this->icServer->listsubscribedMailboxes('', $_folderName);
			if(is_array($subscribedFolders) && count($subscribedFolders) == 1) {
				$retValue['subscribed'] = true;
			}

			$retValue['delimiter']		= $folderInfo[0]['HIERACHY_DELIMITER'];
			$retValue['attributes']		= $folderInfo[0]['ATTRIBUTES'];
			$shortNameParts			= explode($retValue['delimiter'], $_folderName);
			$retValue['shortName']		= array_pop($shortNameParts);
			$retValue['displayName']	= $this->encodeFolderName($_folderName);
			$retValue['shortDisplayName']	= $this->encodeFolderName($retValue['shortName']);
			if(strtoupper($retValue['shortName']) == 'INBOX') {
				$retValue['displayName']	= lang('INBOX');
				$retValue['shortDisplayName']	= lang('INBOX');
			}
			// translate the automatic Folders (Sent, Drafts, ...) like the INBOX
			elseif (in_array($retValue['shortName'],$this->autoFolders))
			{
				$retValue['displayName'] = $retValue['shortDisplayName'] = lang($retValue['shortName']);
			}
			
			if ( PEAR::isError($folderStatus = $this->icServer->getStatus($_folderName)) ) {
				//_debug_array($folderStatus);
			} else {
				$retValue['messages']		= $folderStatus['MESSAGES'];
				$retValue['recent']		= $folderStatus['RECENT'];
				$retValue['uidnext']		= $folderStatus['UIDNEXT'];
				$retValue['uidvalidity']	= $folderStatus['UIDVALIDITY'];
				$retValue['unseen']		= $folderStatus['UNSEEN'];
			}

			return $retValue;
		}
		
		/**
		* get IMAP folder objects
		*
		* returns an array of IMAP folder objects. Put INBOX folder in first
		* position. Preserves the folder seperator for later use. The returned
		* array is indexed using the foldername.
		*
		* @param _subscribedOnly boolean get subscribed or all folders
		* @param _getCounters    boolean get get messages counters
		*
		* @returns array with folder objects. eg.: INBOX => {inbox object}
		*/		
		function getFolderObjects($_subscribedOnly=false, $_getCounters=false) 
		{
			$isUWIMAP = false;
			
			$delimiter = $this->icServer->getHierarchyDelimiter();
			if( PEAR::isError($delimiter)) $delimiter = '/';
			
			$inboxData = new stdClass;
			$inboxData->name 		= 'INBOX';
			$inboxData->folderName		= 'INBOX';
			$inboxData->displayName		= lang('INBOX');
			$inboxData->delimiter 		= $delimiter;
			$inboxData->shortFolderName	= 'INBOX';
			$inboxData->shortDisplayName	= lang('INBOX');
			$inboxData->subscribed = true;
			if($_getCounters == true) {
				$folderStatus = $this->icServer->getStatus('INBOX');
				
				$status =  new stdClass;
				$status->messages	= $folderStatus['MESSAGES'];
				$status->unseen		= $folderStatus['UNSEEN'];
				$status->recent		= $folderStatus['RECENT'];

				$inboxData->counter	= $status;
			}
			#$inboxData->attributes = 64;
			$folders = array('INBOX' => $inboxData);
			#_debug_array($folders);

			$nameSpace = $this->icServer->getNameSpaces();
			#_debug_array($nameSpace);

			if(isset($nameSpace['#mh/'])) {
				// removed the uwimap code
				// but we need to reintroduce him later
				// uw imap does not return the attribute of a folder, when requesting subscribed folders only
				// dovecot has the same problem too
			} else {
				if (is_array($nameSpace)) { 
				  foreach($nameSpace as $type => $singleNameSpace) {
					if($type == 'personal' && ($singleNameSpace[2]['name'] == '#mh/' || count($nameSpace) == 1) && $this->icServer->mailboxExist('Mail')) {
						// uw-imap server with mailbox prefix or dovecot maybe
						$foldersNameSpace[$type]['prefix'] = 'Mail';
					} elseif($type == 'personal' && ($singleNameSpace[2]['name'] == '#mh/' || count($nameSpace) == 1) && $this->icServer->mailboxExist('mail')) {
						// uw-imap server with mailbox prefix or dovecot maybe
						$foldersNameSpace[$type]['prefix'] = 'mail';
					} else {
						$foldersNameSpace[$type]['prefix'] = $singleNameSpace[0]['name'];
					}

					$foldersNameSpace[$type]['delimiter'] = $delimiter;

					if(is_array($singleNameSpace[0])) {
						// fetch and sort the subscribed folders
						$subscribedMailboxes = $this->icServer->listsubscribedMailboxes($foldersNameSpace[$type]['prefix']);
						if( PEAR::isError($subscribedMailboxes) ) {
							continue;
						}
						$foldersNameSpace[$type]['subscribed'] = $subscribedMailboxes;
						sort($foldersNameSpace[$type]['subscribed']);
						// fetch and sort all folders
						$foldersNameSpace[$type]['all'] = $this->icServer->getMailboxes($foldersNameSpace[$type]['prefix']);
						sort($foldersNameSpace[$type]['all']);
					}
				  }
				}

				// check for autocreated folders
				if(isset($foldersNameSpace['personal']['prefix'])) {
					$personalPrefix = $foldersNameSpace['personal']['prefix'];
					$personalDelimiter = $foldersNameSpace['personal']['delimiter'];
					if(!empty($personalPrefix)) {
						if(substr($personalPrefix, -1) != $personalDelimiter) {
							$folderPrefix = $personalPrefix . $personalDelimiter;
						} else {
							$folderPrefix = $personalPrefix;
						}
					}
					foreach($this->autoFolders as $personalFolderName) {
						$folderName = (!empty($personalPrefix)) ? $folderPrefix.$personalFolderName : $personalFolderName;
						if(!is_array($foldersNameSpace['personal']['all']) || !in_array($folderName, $foldersNameSpace['personal']['all'])) {
							if($this->createFolder('', $folderName, true)) {
								$foldersNameSpace['personal']['all'][] = $folderName;
								$foldersNameSpace['personal']['subscribed'][] = $folderName;
							} else {
							#	print "FOLDERNAME failed: $folderName<br>";
							}
						}
					}
				}
			}

			foreach( array('personal', 'others', 'shared') as $type) {
				if(isset($foldersNameSpace[$type])) {
					if($_subscribedOnly) {
						$listOfFolders = $foldersNameSpace[$type]['subscribed'];
					} else {
						$listOfFolders = $foldersNameSpace[$type]['all'];
					}
					foreach((array)$listOfFolders as $folderName) {
						if($_subscribedOnly && !in_array($folderName, $foldersNameSpace[$type]['all'])) {
							continue;
						}
						$folderParts = explode($delimiter, $folderName);
						$shortName = array_pop($folderParts);
						
						$folderObject = new stdClass;
						$folderObject->delimiter	= $delimiter;
						$folderObject->folderName	= $folderName;
						$folderObject->shortFolderName	= $shortName;
						if(!$_subscribedOnly) {
							$folderObject->subscribed = in_array($folderName, $foldersNameSpace[$type]['subscribed']);
						}
						
						if($_getCounters == true) {
							$folderStatus = $this->icServer->getStatus($folderName);

							if(is_array($folderStatus)) {
								$status =  new stdClass;
								$status->messages	= $folderStatus['MESSAGES'];
								$status->unseen		= $folderStatus['UNSEEN'];
								$status->recent 	= $folderStatus['RECENT'];
								
								$folderObject->counter = $status;
							}
						}

						if(strtoupper($folderName) == 'INBOX') {
							$folderName = 'INBOX';
							$folderObject->folderName	= 'INBOX';
							$folderObject->shortFolderName	= 'INBOX';
							$folderObject->displayName	= lang('INBOX');
							$folderObject->shortDisplayName = lang('INBOX');
							$folderObject->subscribed	= true;
						// translate the automatic Folders (Sent, Drafts, ...) like the INBOX
						} elseif (in_array($shortName,$this->autoFolders)) {
							$folderObject->displayName = $folderObject->shortDisplayName = lang($shortName);
						} else {
							$folderObject->displayName = $this->encodeFolderName($folderObject->folderName);
							$folderObject->shortDisplayName = $this->encodeFolderName($shortName);
						}
						$folderName = $folderName;
						$folders[$folderName] = $folderObject;
					}
				}
			}
			
			#_debug_array($folders); exit;
			
			return $folders;
		}
		
		function getMimePartCharset($_mimePartObject) 
		{
			$charSet = 'iso-8859-1';

			if(is_array($_mimePartObject->parameters)) {
				if(isset($_mimePartObject->parameters['CHARSET'])) {
					$charSet = $_mimePartObject->parameters['CHARSET'];
				}
			}
			
			return $charSet;
		}
		
		function getMultipartAlternative($_uid, $_structure, $_htmlMode) 
		{
			// a multipart/alternative has exactly 2 parts (text and html  OR  text and something else)

			$partText = false;
			$partHTML = false;

			foreach($_structure as $mimePart) {
				if($mimePart->type == 'TEXT' && $mimePart->subType == 'PLAIN' && $mimePart->bytes > 0) {
					$partText = $mimePart;
				} elseif($mimePart->type == 'TEXT' && $mimePart->subType == 'HTML' && $mimePart->bytes > 0) {
					$partHTML = $mimePart;
				} elseif ($mimePart->type == 'MULTIPART' && $mimePart->subType == 'RELATED' && is_array($mimePart->subParts)) {
					// in a multipart alternative we treat the multipart/related as html part
					$partHTML = array($mimePart);
				}
			}

			switch($_htmlMode) {
				case 'always_display':
					if(is_object($partHTML)) {
						if($partHTML->subType == 'RELATED') {
							return $this->getMultipartRelated($_uid, $partHTML, 'always_display');
						} else {
							return $this->getTextPart($_uid, $partHTML, 'always_display');
						}
					} elseif(is_object($partText)) {
						return $this->getTextPart($_uid, $partText);
					}

					break;
				case 'only_if_no_text':
					if(is_object($partText)) {
						return $this->getTextPart($_uid, $partText);
					} elseif(is_object($partHTML)) {
						if($partHTML->type) {
							return $this->getMultipartRelated($_uid, $partHTML, $_htmlMode);
						} else {
							return $this->getTextPart($_uid, $partHTML, 'always_display');
						}
					}

					break;
						
				default:
					if(is_object($partText)) {
						return $this->getTextPart($_uid, $partText);
					} else {
						$bodyPart = array(
							'body'		=> lang("no plain text part found"),
							'mimeType'	=> 'text/plain',
							'charSet'	=> self::$displayCharset,
						);
					}

					break;
			}

			return $bodyPart;
		}
		
		function getMultipartMixed($_uid, $_structure, $_htmlMode) 
		{
			$bodyPart = array();

			foreach($_structure as $part) {
				switch($part->type) {
					case 'MULTIPART':
						switch($part->subType) {
							case 'ALTERNATIVE':
								$bodyPart[] = $this->getMultipartAlternative($_uid, $part->subParts, $_htmlMode);
								break;
							
							case 'MIXED':
							case 'SIGNED':
								$bodyPart = array_merge($bodyPart, $this->getMultipartMixed($_uid, $part->subParts, $_htmlMode));
								break;
							
							case 'RELATED':
								$bodyPart = array_merge($bodyPart, $this->getMultipartRelated($_uid, $part->subParts, $_htmlMode));
								break;
						}
						break;
					
					case 'TEXT':
						switch($part->subType) {
							case 'PLAIN':
							case 'HTML':
								if($part->disposition != 'ATTACHMENT') {
									$bodyPart[] = $this->getTextPart($_uid, $part, $_htmlMode);
								}
								break;
						}
						break;
			
					case 'MESSAGE':
						if($part->subType == 'delivery-status') {
							$bodyPart[] = $this->getTextPart($_uid, $part);
						}
						break;
						
					default:
						// do nothing
						// the part is a attachment
				}
			}

			return $bodyPart;
		}
		
		function getMultipartRelated($_uid, $_structure, $_htmlMode) 
		{
			return $this->getMultipartMixed($_uid, $_structure, $_htmlMode);
		}
		
		function getTextPart($_uid, $_structure, $_htmlMode = '') 
		{
			$bodyPart = array();
			
			$partID = $_structure->partID;
			$mimePartBody = $this->icServer->getBodyPart($_uid, $partID, true);
			#_debug_array(preg_replace('/PropertyFile___$/','',$this->decodeMimePart($mimePartBody, $_structure->encoding)));
			if($_structure->subType == 'HTML' && $_htmlMode != 'always_display'  && $_htmlMode != 'only_if_no_text') {
				$bodyPart = array(
					'body'		=> lang("displaying html messages is disabled"),
					'mimeType'	=> 'text/html',
					'charSet'	=> self::$displayCharset,
				);
			} else {
				// some Servers append PropertyFile___ ; strip that here for display
				$bodyPart = array(
					'body'		=> preg_replace('/PropertyFile___$/','',$this->decodeMimePart($mimePartBody, $_structure->encoding, $this->getMimePartCharset($_structure))),
					'mimeType'	=> ($_structure->type == 'TEXT' && $_structure->subType == 'HTML') ? 'text/html' : 'text/plain',
					'charSet'	=> $this->getMimePartCharset($_structure),
				);
			}
			return $bodyPart;
		}
		
		function getNameSpace($_icServer) 
		{
			$this->icServer->getNameSpaces();
		}

		/**
		* fetches a sorted list of messages from the imap server
		* private function
		*
		* @todo implement sort based on Net_IMAP
		* @param string $_folderName the name of the folder in which the messages get searched
		* @param integer $_sort the primary sort key
		* @param bool $_reverse sort the messages ascending or descending
		* @param array $_filter the search filter
		* @return bool
		*/
		function getSortedList($_folderName, $_sort, $_reverse, $_filter) 
		{
			if(PEAR::isError($folderStatus = $this->icServer->examineMailbox($_folderName))) {
				return false;
			}
			
			if(is_array($this->sessionData['folderStatus'][0][$_folderName]) &&
				$this->sessionData['folderStatus'][0][$_folderName]['uidValidity']	=== $folderStatus['UIDVALIDITY'] &&
				$this->sessionData['folderStatus'][0][$_folderName]['messages']	=== $folderStatus['EXISTS'] &&
				$this->sessionData['folderStatus'][0][$_folderName]['uidnext']	=== $folderStatus['UIDNEXT'] &&
				$this->sessionData['folderStatus'][0][$_folderName]['filter']	=== $_filter &&
				$this->sessionData['folderStatus'][0][$_folderName]['sort']	=== $_sort
			) {
				#error_log("USE CACHE");
				$sortResult = $this->sessionData['folderStatus'][0][$_folderName]['sortResult'];
			} else {
				#error_log("USE NO CACHE");
				$filter = $this->createIMAPFilter($_folderName, $_filter);
				if($this->icServer->hasCapability('SORT')) {
					$sortOrder = $this->_getSortString($_sort);
					if (!empty(self::$displayCharset)) {
						$sortResult = $this->icServer->sort($sortOrder, strtoupper( self::$displayCharset ), $filter, true);
					}
					if (PEAR::isError($sortResult) || empty(self::$displayCharset)) {
						$sortResult = $this->icServer->sort($sortOrder, 'US-ASCII', $filter, true);
					}
				} else {
					$advFilter = 'CHARSET '. strtoupper(self::$displayCharset) .' '.$filter;
					$sortResult = $this->icServer->search($advFilter, true);
					if (PEAR::isError($sortResult)) $sortResult = $this->icServer->search($filter, true); 
					if(is_array($sortResult)) {
							sort($sortResult, SORT_NUMERIC);
					}
				}

				$this->sessionData['folderStatus'][0][$_folderName]['uidValidity'] = $folderStatus['UIDVALIDITY'];
				$this->sessionData['folderStatus'][0][$_folderName]['messages']	= $folderStatus['EXISTS'];
				$this->sessionData['folderStatus'][0][$_folderName]['uidnext']	= $folderStatus['UIDNEXT'];
				$this->sessionData['folderStatus'][0][$_folderName]['filter']	= $_filter;
				$this->sessionData['folderStatus'][0][$_folderName]['sortResult'] = $sortResult;
				$this->sessionData['folderStatus'][0][$_folderName]['sort']	= $_sort;
			}
			$this->sessionData['folderStatus'][0][$_folderName]['reverse'] 	= $_reverse;
			$this->saveSessionData();
			
			return $sortResult;
		}
		
		function getMessageEnvelope($_uid, $_partID = '')
		{
			if($_partID == '') {
				if( PEAR::isError($envelope = $this->icServer->getEnvelope('', $_uid, true)) ) {
					return false;
				}
			
				return $envelope[0];
			} else {
				if( PEAR::isError($headers = $this->icServer->getParsedHeaders($_uid, true, $_partID, true)) ) {
					return false;
				}
				
				#_debug_array($headers);
				$newData = array(
					'DATE'		=> $headers['DATE'],
					'SUBJECT'	=> $headers['SUBJECT'],
					'MESSAGE_ID'	=> $headers['MESSAGE-ID']
				);

				$recepientList = array('FROM', 'TO', 'CC', 'BCC', 'SENDER', 'REPLY_TO');
				foreach($recepientList as $recepientType) {
					if(isset($headers[$recepientType])) {
						$addresses = imap_rfc822_parse_adrlist($headers[$recepientType], '');
						foreach($addresses as $singleAddress) {
							$addressData = array(
								'PERSONAL_NAME'		=> $singleAddress->personal ? $singleAddress->personal : 'NIL',
								'AT_DOMAIN_LIST'	=> $singleAddress->adl ? $singleAddress->adl : 'NIL',
								'MAILBOX_NAME'		=> $singleAddress->mailbox ? $singleAddress->mailbox : 'NIL',
								'HOST_NAME'		=> $singleAddress->host ? $singleAddress->host : 'NIL',
								'EMAIL'			=> $singleAddress->host ? $singleAddress->mailbox.'@'.$singleAddress->host : $singleAddress->mailbox,
							);
							if($addressData['PERSONAL_NAME'] != 'NIL') {
								$addressData['RFC822_EMAIL'] = imap_rfc822_write_address($singleAddress->mailbox, $singleAddress->host, $singleAddress->personal);
							} else {
								$addressData['RFC822_EMAIL'] = 'NIL';
							}
							$newData[$recepientType][] = $addressData;
						}
					} else {
						if($recepientType == 'SENDER' || $recepientType == 'REPLY_TO') {
							$newData[$recepientType] = $newData['FROM'];
						} else {
							$newData[$recepientType] = array();
						}
					}
				}
				#_debug_array($newData);

				return $newData;
			}
		}
		
		function getHeaders($_folderName, $_startMessage, $_numberOfMessages, $_sort, $_reverse, $_filter)
		{
			$reverse = (bool)$_reverse;
			// get the list of messages to fetch
			$this->reopen($_folderName);
			//$this->icServer->selectMailbox($_folderName);
			
			#print "<pre>";
			#$this->icServer->setDebug(true);
			
			$sortResult = $this->getSortedList($_folderName, $_sort, $_reverse, $_filter);

			#$this->icServer->setDebug(false);
			#print "</pre>";
			// nothing found
			if(!is_array($sortResult) || empty($sortResult)) {
				$retValue = array();
				$retValue['info']['total']	= 0;
				$retValue['info']['first']	= 0;
				$retValue['info']['last']	= 0;
				return $retValue;
			}
			
			$total = count($sortResult);
			#_debug_array($sortResult);
			#_debug_array(array_slice($sortResult, -5, -2));
			#error_log("REVERSE: $reverse");
			if($reverse === true) {
				$startMessage = $_startMessage-1;
				if($startMessage > 0) {
					$sortResult = array_slice($sortResult, -($_numberOfMessages+$startMessage), -$startMessage);
				} else {
					$sortResult = array_slice($sortResult, -($_numberOfMessages+($_startMessage-1)));
				}
				$sortResult = array_reverse($sortResult);
			} else {
				$sortResult = array_slice($sortResult, $_startMessage-1, $_numberOfMessages);
			}

			$queryString = implode(',', $sortResult);

			// fetch the data for the selected messages
			$headersNew = $this->icServer->getSummary($queryString, true);

			$count = 0;

			foreach((array)$sortResult as $uid) {
				$sortOrder[$uid] = $count++;
			}

			$count = 0;
			if (is_array($headersNew)) {	
				foreach((array)$headersNew as $headerObject) {
					#if($count == 0) _debug_array($headerObject);
					$uid = $headerObject['UID'];
					
					// make dates like "Mon, 23 Apr 2007 10:11:06 UT" working with strtotime
					if(substr($headerObject['DATE'],-2) === 'UT') {
						$headerObject['DATE'] .= 'C';
					}

					$retValue['header'][$sortOrder[$uid]]['subject']	= $this->decode_subject($headerObject['SUBJECT']);
					$retValue['header'][$sortOrder[$uid]]['size'] 		= $headerObject['SIZE'];
					$retValue['header'][$sortOrder[$uid]]['date']		= strtotime($headerObject['DATE']);
					$retValue['header'][$sortOrder[$uid]]['mimetype']	= $headerObject['MIMETYPE'];
					$retValue['header'][$sortOrder[$uid]]['id']		= $headerObject['MSG_NUM'];
					$retValue['header'][$sortOrder[$uid]]['uid']		= $headerObject['UID'];
					$retValue['header'][$sortOrder[$uid]]['priority']		= ($headerObject['PRIORITY']?$headerObject['PRIORITY']:3);
					if (is_array($headerObject['FLAGS'])) {
						$retValue['header'][$sortOrder[$uid]]['recent']		= in_array('\\Recent', $headerObject['FLAGS']);
						$retValue['header'][$sortOrder[$uid]]['flagged']	= in_array('\\Flagged', $headerObject['FLAGS']);
						$retValue['header'][$sortOrder[$uid]]['answered']	= in_array('\\Answered', $headerObject['FLAGS']);
						$retValue['header'][$sortOrder[$uid]]['forwarded']   = in_array('$Forwarded', $headerObject['FLAGS']);
						$retValue['header'][$sortOrder[$uid]]['deleted']	= in_array('\\Deleted', $headerObject['FLAGS']);
						$retValue['header'][$sortOrder[$uid]]['seen']		= in_array('\\Seen', $headerObject['FLAGS']);
						$retValue['header'][$sortOrder[$uid]]['draft']		= in_array('\\Draft', $headerObject['FLAGS']);
						$retValue['header'][$sortOrder[$uid]]['mdnsent']	= in_array('MDNSent', $headerObject['FLAGS']);
						$retValue['header'][$sortOrder[$uid]]['mdnnotsent']	= in_array('MDNnotSent', $headerObject['FLAGS']);
					}
					if(is_array($headerObject['FROM']) && is_array($headerObject['FROM'][0])) {
						if($headerObject['FROM'][0]['HOST_NAME'] != 'NIL') {
							$retValue['header'][$sortOrder[$uid]]['sender_address'] = $headerObject['FROM'][0]['EMAIL'];
						} else {
							$retValue['header'][$sortOrder[$uid]]['sender_address'] = $headerObject['FROM'][0]['MAILBOX_NAME'];
						}
						if($headerObject['FROM'][0]['PERSONAL_NAME'] != 'NIL') {
							$retValue['header'][$sortOrder[$uid]]['sender_name'] = $this->decode_header($headerObject['FROM'][0]['PERSONAL_NAME']);
						}
						
					}

					if(is_array($headerObject['TO']) && is_array($headerObject['TO'][0])) {
						if($headerObject['TO'][0]['HOST_NAME'] != 'NIL') {
							$retValue['header'][$sortOrder[$uid]]['to_address'] = $headerObject['TO'][0]['EMAIL'];
						} else {
							$retValue['header'][$sortOrder[$uid]]['to_address'] = $headerObject['TO'][0]['MAILBOX_NAME'];
						}
						if($headerObject['TO'][0]['PERSONAL_NAME'] != 'NIL') {
							$retValue['header'][$sortOrder[$uid]]['to_name'] = $this->decode_header($headerObject['TO'][0]['PERSONAL_NAME']);
						}
						
					}

					$count++;
				}
				
				// sort the messages to the requested displayorder
				if(is_array($retValue['header'])) {
					ksort($retValue['header']);
					$retValue['info']['total']	= $total;
					$retValue['info']['first']	= $_startMessage;
					$retValue['info']['last']	= $_startMessage + $count - 1 ;
					return $retValue;
				} else {
					$retValue = array();
					$retValue['info']['total']	= 0;
					$retValue['info']['first']	= 0;
					$retValue['info']['last']	= 0;
					return $retValue;
				}
			} else {
				error_log("bofelamimail::getHeaders -> retrieval of Message Details failed: ".print_r($headersNew,TRUE));
				$retValue = array();
				$retValue['info']['total']  = 0;
				$retValue['info']['first']  = 0;
				$retValue['info']['last']   = 0;
				return $retValue;
			}
		}

		function getNextMessage($_foldername, $_id) 
		{
			#_debug_array($this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult']);
			#_debug_array($this->sessionData['folderStatus'][$this->profileID]);
			#print "ID: $_id<br>";
			$position=false;
			if (is_array($this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult'])) {
				$position = array_search($_id, $this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult']);
			}
			#print "POS: $position<br>";

			if($position !== false) {
				$retValue = array();
				
				if($this->sessionData['folderStatus'][$this->profileID][$_foldername]['reverse'] == true) {
					#print "is reverse<br>";
					if(isset($this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult'][$position-1])) {
						$retValue['next'] = $this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult'][$position-1];
					}
					if(isset($this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult'][$position+1])) {
						$retValue['previous'] = $this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult'][$position+1];
					}
				} else {
					#print "is not reverse";
					if(isset($this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult'][$position-1])) {
						$retValue['previous'] = $this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult'][$position-1];
					}
					if(isset($this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult'][$position+1])) {
						$retValue['next'] = $this->sessionData['folderStatus'][$this->profileID][$_foldername]['sortResult'][$position+1];
					}
				}
				
				return $retValue;
			}
			
			return false;
		}
		
		function getIMAPACL($_folderName, $user='')
		{
			if(($this->hasCapability('ACL'))) {
				if ( PEAR::isError($acl = $this->icServer->getACL($_folderName)) ) {
					return false;
				}
				
				if ($user=='') {
					return $acl;
				}
				
				foreach ($acl as $i => $userACL) {
					if ($userACL['USER'] == $user) {
						return $userACL['RIGHTS'];
					}
				}

				return '';
			}
			
			return false;
		}
		
		/**
		* checks if the imap server supports a given capability
		*
		* @param string $_capability the name of the capability to check for
		* @return bool
		*/
		function hasCapability($_capability)
		{
			return $this->icServer->hasCapability(strtoupper($_capability));
		}
		
		function getMailPreferences() 
		{
			return $this->mailPreferences;
		}
		
		function getMessageAttachments($_uid, $_partID='', $_structure='') 
		{
			#if($_structure!='') {
			#	if(is_object($_structure)) {
			#		print "buh<br>";
			#	}
			#	_debug_array($_structure); exit;
			#}

			if(is_object($_structure)) {
				$structure = $_structure;
			} else {
				$structure = $this->icServer->getStructure($_uid, true);
				
				if($_partID != '') {
					$structure = $this->_getSubStructure($structure, $_partID);
				}
			}
			
			#print "<hr>";
			#_debug_array($structure);
			
			// this kind of messages contain only the attachment and no body
			if($structure->type == 'APPLICATION' || $structure->type == 'AUDIO' || $structure->type == 'IMAGE') {
				$attachments = array();
			
				$newAttachment = array();
				$newAttachment['size']		= $structure->bytes;
				$newAttachment['mimeType']	= $structure->type .'/'. $structure->subType;
				$newAttachment['partID']	= $structure->partID;
				$newAttachment['encoding']      = $structure->encoding;
				if(isset($structure->cid)) {
					$newAttachment['cid']	= $structure->cid;
				}
				if(isset($structure->parameters['NAME'])) {
					$newAttachment['name']	= $this->decode_header($structure->parameters['NAME']);
				} elseif(isset($structure->dparameters['FILENAME'])) {
					$newAttachment['name']	= $this->decode_header($structure->dparameters['FILENAME']);
				} elseif(isset($structure->dparameters['FILENAME*'])) {
                                        $newAttachment['name']  = $this->decode_header($structure->dparameters['FILENAME*']);
				} else {
					$newAttachment['name']	= lang("unknown");
				}
				# if the new attachment is a winmail.dat, we have to decode that first
				if ( $newAttachment['name'] == 'winmail.dat' && 
					( $wmattachments = $this->decode_winmail( $_uid, $newAttachment['partID'] ) ) ) 
				{
					$attachments = array_merge( $attachments, $wmattachments );
				} else {
					$attachments[] = $newAttachment;
				}
				//$attachments[] = $newAttachment;
				
				return $attachments;
			}
			
			// this kind of message can have no attachments
			if($structure->type == 'TEXT' || 
			   ($structure->type == 'MULTIPART' && $structure->subType == 'ALTERNATIVE' && !is_array($structure->subParts)) ||
			   !is_array($structure->subParts)) {
				return array();
			}

			$attachments = array();

			foreach($structure->subParts as $subPart) {
				// skip all non attachment parts
				if(($subPart->type == 'TEXT' && ($subPart->subType == 'PLAIN' || $subPart->subType == 'HTML') && $subPart->disposition != 'ATTACHMENT') ||
				   ($subPart->type == 'MULTIPART' && $subPart->subType == 'ALTERNATIVE') ||
				   ($subPart->type == 'MULTIPART' && $subPart->subType == 'APPLEFILE') ||
				   ($subPart->type == 'MESSAGE' && $subPart->subType == 'delivery-status')) 
				{
					if ($subPart->type == 'MULTIPART' && $subPart->subType == 'ALTERNATIVE')
					{
						$attachments = $this->getMessageAttachments($_uid, '', $subPart);
					}
					continue;
				}
				
			   	// fetch the subparts for this part
				if($subPart->type == 'MULTIPART' && 
				   ($subPart->subType == 'RELATED' || 
					$subPart->subType == 'MIXED' || 
					$subPart->subType == 'SIGNED' || 
					$subPart->subType == 'APPLEDOUBLE')) {
				   	$attachments = array_merge($this->getMessageAttachments($_uid, '', $subPart), $attachments);
				} else {
					$newAttachment = array();
					$newAttachment['size']		= $subPart->bytes;
					$newAttachment['mimeType']	= $subPart->type .'/'. $subPart->subType;
					$newAttachment['partID']	= $subPart->partID;
					$newAttachment['encoding']	= $subPart->encoding;
					if(isset($subPart->cid)) {
						$newAttachment['cid']	= $subPart->cid;
					}
					if(isset($subPart->parameters['NAME'])) {
						$newAttachment['name']	= $this->decode_header($subPart->parameters['NAME']);
					} elseif(isset($subPart->dparameters['FILENAME'])) {
						$newAttachment['name']	= $this->decode_header($subPart->dparameters['FILENAME']);
                                        } elseif(isset($subPart->dparameters['FILENAME*'])) {
                                                $newAttachment['name']  = $this->decode_header($subPart->dparameters['FILENAME*']);
					} else {
						$newAttachment['name']	= lang("unknown");
					}
					# if the new attachment is a winmail.dat, we have to decode that first
					if ( $newAttachment['name'] == 'winmail.dat' &&
						( $wmattachments = $this->decode_winmail( $_uid, $newAttachment['partID'] ) ) )
					{
						$attachments = array_merge( $attachments, $wmattachments );
					} else {
						$attachments[] = $newAttachment;
					}
					//$attachments[] = $newAttachment;
				}
			}

		   	//_debug_array($attachments); exit;
			return $attachments;

		}
		
		function getMessageBody($_uid, $_htmlOptions='', $_partID='', $_structure = '') 
		{
			if($_htmlOptions != '') {
				$this->htmlOptions = $_htmlOptions; 
			}
			if(is_object($_structure)) {
				$structure = $_structure;
			} else {
				$structure = $this->icServer->getStructure($_uid, true);
				if($_partID != '') {
					$structure = $this->_getSubStructure($structure, $_partID);
				}
			}
			if (self::$debug) _debug_array($structure);
			switch($structure->type) {
				case 'APPLICATION':
					return array(
						array(
							'body'		=> '',
							'mimeType'	=> 'text/plain',
							'charSet'	=> 'iso-8859-1',
						)
					);
					break;
				case 'MULTIPART':
					switch($structure->subType) {
						case 'ALTERNATIVE':
							return array($this->getMultipartAlternative($_uid, $structure->subParts, $this->htmlOptions));
							
							break;

						case 'MIXED':
						case 'REPORT':
						case 'SIGNED':
							return $this->getMultipartMixed($_uid, $structure->subParts, $this->htmlOptions);
							break;

						case 'RELATED':
							return $this->getMultipartRelated($_uid, $structure->subParts, $this->htmlOptions);
							break;
					}
					
					break;
				case 'AUDIO': // some servers send audiofiles and imagesfiles directly, without any stuff surround it
				case 'IMAGE': // they are displayed as Attachment NOT INLINE
					return array(
						array(
							'body'      => '',
							'mimeType'  => $structure->subType,
						),
					);
					break;
				case 'TEXT':
					$bodyPart = array();
					if (($structure->subType == 'HTML' || $structure->subType == 'PLAIN') && $structure->disposition != 'ATTACHMENT') {
						$bodyPart = array($this->getTextPart($_uid, $structure, $this->htmlOptions));
					}
					return $bodyPart;
					break;
				case 'MESSAGE':
					switch($structure->subType) {
						case 'RFC822':
							$newStructure = array_shift($structure->subParts);
							return $this->getMessageBody($_uid, $_htmlOptions, $newStructure->partID, $newStructure);
							break;
					}
					break;
				default:
					return array(
						array(
							'body'		=> lang('The mimeparser can not parse this message.'),
							'mimeType'	=> 'text/plain',
							'charSet'	=> 'iso-8859-1',
						)
					);
					break;
			}
		}

		function getMessageHeader($_uid, $_partID = '') 
		{
			$retValue = $this->icServer->getParsedHeaders($_uid, true, $_partID, true);
			
			return $retValue;
		}

		function getMessageRawBody($_uid, $_partID = '')
		{
			if($_partID != '') {
				$body = $this->icServer->getBody($_uid, true);
			} else {
				$body = $this->icServer->getBodyPart($_uid, $_partID, true);
			}
			
			return $body;
		}

		function getMessageRawHeader($_uid, $_partID = '') 
		{
			$retValue = $this->icServer->getRawHeaders($_uid, $_partID, true);
			
			return $retValue;
		}

		// return the qouta of the users INBOX
		function getQuotaRoot() 
		{
			if(!$this->icServer->hasCapability('QUOTA')) {
				return false;
			}

			$quota = $this->icServer->getStorageQuotaRoot('INBOX');
			if(is_array($quota)) {
				return array(
					'usage'	=> $quota['USED'],
					'limit'	=> $quota['QMAX'],
				);
			} else {
				return false;
			}
		}
		
	#	function imapGetQuota($_username)
	#	{
	#		$quota_value = @imap_get_quota($this->mbox, "user.".$_username);
	#
	#		if(is_array($quota_value) && count($quota_value) > 0)
	#		{
	#			return array('limit' => $quota_value['limit']/1024);
	#		}
	#		else
	#		{
	#			return false;
	#		}
	#	}		
		
	#	function imap_get_quotaroot($_folderName)
	#	{
	#		return @imap_get_quotaroot($this->mbox, $_folderName);
	#	}
		
	#	function imapSetQuota($_username, $_quotaLimit)
	#	{
	#		if(is_numeric($_quotaLimit) && $_quotaLimit >= 0)
	#		{
	#			// enable quota
	#			$quota_value = @imap_set_quota($this->mbox, "user.".$_username, $_quotaLimit*1024);
	#		}
	#		else
	#		{
	#			// disable quota
	#			$quota_value = @imap_set_quota($this->mbox, "user.".$_username, -1);
	#		}
	#	}
		
		function isSentFolder($_folderName)
		{
			if(empty($this->mailPreferences->preferences['sentFolder'])) {
				return false;
			}
			// does the folder exist???
			if (!self::folderExists($_folderName)) {	
				return false;
			}

			if(false !== stripos($_folderName, $this->mailPreferences->preferences['sentFolder'])) {
				return true;
			} else {
				return false;
			}
		}
		
		function isDraftFolder($_folderName)
		{
			if(empty($this->mailPreferences->preferences['draftFolder'])) {
				return false;
			}
			// does the folder exist???
			if (!self::folderExists($_folderName)) {
				return false;
			}
	
			if(false !== strpos(strtolower($_folderName), strtolower($this->mailPreferences->preferences['draftFolder']))) {
				return true;
			} else {
				return false;
			}
		}

		function isTemplateFolder($_folderName)
		{
			if(empty($this->mailPreferences->preferences['templateFolder'])) {
				return false;
			}
			// does the folder exist???
			if (!self::folderExists($_folderName)) {
				return false;
			}

			if(false !== strpos(strtolower($_folderName), strtolower($this->mailPreferences->preferences['templateFolder']))) {
				return true;
			} else {
				return false;
			}
		}
		
		function folderExists($_folder, $forceCheck=false)
		{
			// does the folder exist???
			#error_log("bofelamimail::folderExists->Connected?".$this->icServer->_connected.", ".$_folder.", ".$forceCheck);
			if ((!($this->icServer->_connected == 1)) && $forceCheck) {
				#error_log("bofelamimail::folderExists->NotConnected and forceCheck");
				return false;
			} 
			$folderInfo = $this->icServer->getMailboxes('', $_folder, true);
			#error_log(print_r($folderInfo,true));
			if(is_a($folderInfo, 'PEAR_Error') || !is_array($folderInfo[0])) {
				return false;
			} else {
				return true;
			} 
		}

		function moveMessages($_foldername, $_messageUID)
		{
			$msglist = '';
			
			$deleteOptions  = $GLOBALS['phpgw_info']["user"]["preferences"]["felamimail"]["deleteOptions"];

			if ( PEAR::isError($this->icServer->copyMessages($_foldername, $_messageUID, $this->sessionData['mailbox'], true)) ) {
				return false;
			}
			// mark messages as deleted
			if ( PEAR::isError($this->icServer->deleteMessages($_messageUID, true))) {
				return false;
			}

			if($deleteOptions != "mark_as_deleted") {
				// delete the messages finaly
				$this->icServer->expunge();
			}
			
			return true;
		}

		function openConnection($_icServerID=0, $_adminConnection=false)
		{
			if(!$this->icServer = $this->mailPreferences->getIncomingServer((int)$_icServerID)) {
				$this->errorMessage = lang('No active IMAP server found!!');
				return false;
			}
			#error_log( "---------------------------open connection <br>");
			#error_log(print_r($this->icServer,true));
			if ($this->icServer->_connected == 1) {
				$tretval = $this->icServer->selectMailbox($this->icServer->currentMailbox);
			} else {
				$tretval = $this->icServer->openConnection($_adminConnection);
			}
			#error_log(print_r($this->icServer->_connected,true));
			return $tretval;
		}		

		/**
		* rename a folder
		*
		* @param string _oldFolderName the old foldername
		* @param string _parent the parent foldername
		* @param string _folderName the new foldername 
		*
		* @returns mixed name of the newly created folder or false on error
		*/
		function renameFolder($_oldFolderName, $_parent, $_folderName)
		{
			$oldFolderName	= $this->_encodeFolderName($_oldFolderName);
			$parent		= $this->_encodeFolderName($_parent);
			$folderName	= $this->_encodeFolderName($_folderName);
			
			if(empty($parent)) {
				$newFolderName = $folderName;
			} else {
				$HierarchyDelimiter = $this->icServer->getHierarchyDelimiter();
				if (PEAR::isError($HierarchyDelimiter)) $HierarchyDelimiter = '/';
				$newFolderName = $parent . $HierarchyDelimiter . $folderName;
			}
			error_log("create folder: $newFolderName");
			
			if ( PEAR::isError($this->icServer->renameMailbox($oldFolderName, $newFolderName) ) ) {
				return false;
			}

			return $newFolderName;

		}
		
		function reopen($_foldername)
		{
			#error_log( "------------------------reopen-<br>");
			#error_log(print_r($this->icServer->_connected,true));
			if ($this->icServer->_connected == 1) {
				$tretval = $this->icServer->selectMailbox($_foldername);
			} else {
				$tretval = $this->icServer->openConnection(false);
				$tretval = $this->icServer->selectMailbox($_foldername);
			}
		}
		
		function restoreSessionData()
		{
			$this->sessionData = $GLOBALS['phpgw']->session->appsession('session_data');
		}
		
		function saveFilter($_formData)
		{
			if(!empty($_formData['from']))
				$data['from']	= $_formData['from'];
			if(!empty($_formData['to']))
				$data['to']	= $_formData['to'];
			if(!empty($_formData['subject']))
				$data['subject']= $_formData['subject'];
			if($_formData['filterActive'] == "true")
			{
				$data['filterActive']= "true";
			}

			$this->sessionData['filter'] = $data;
			$this->saveSessionData();
		}
		
		function saveSessionData()
		{
			$GLOBALS['phpgw']->session->appsession('session_data','',$this->sessionData);
		}
		
		function setEMailProfile($_profileID)
		{
			$config =& CreateObject('phpgwapi.config','felamimail');
			$config->read();
			$config->value('profileID',$_profileID);
			$config->save_repository();
		}
		
		function subscribe($_folderName, $_status)
		{
			if($_status === true) {
				if ( PEAR::isError($this->icServer->subscribeMailbox($_folderName))) {
					return false;
				}
			} else {
				if ( PEAR::isError($this->icServer->unsubscribeMailbox($_folderName))) {
					return false;
				}
			}
			
			return true;
		}
		
		function toggleFilter() 
		{
			if($this->sessionData['filter']['filterActive'] == 'true') {
				$this->sessionData['filter']['filterActive'] = 'false';
			} else {
				$this->sessionData['filter']['filterActive'] = 'true';
			}
			$this->saveSessionData();
		}

		function updateAccount($_hookValues) 
		{
			if(!is_object($this->mailPreferences))
			{
				return;
			}
			$icServer = $this->mailPreferences->getIncomingServer(0);
			if(is_a($icServer,'defaultimap')) {
				$icServer->updateAccount($_hookValues);
			}

			$ogServer = $this->mailPreferences->getOutgoingServer(0);
			if(is_a($ogServer,'defaultsmtp')) {
				$ogServer->updateAccount($_hookValues);
			}
		}
		
		function updateSingleACL($_folderName, $_accountName, $_aclType, $_aclStatus)
		{
			$userACL = $this->getIMAPACL($_folderName, $_accountName);
			
			if($_aclStatus == 'true') {
				if(strpos($userACL, $_aclType) === false) {
					$userACL .= $_aclType;
					$this->setACL($_folderName, $_accountName, $userACL);
				}
			} elseif($_aclStatus == 'false') {
				if(strpos($userACL, $_aclType) !== false) {
					$userACL = str_replace($_aclType,'',$userACL);
					$this->setACL($_folderName, $_accountName, $userACL);
				}
			}
			
			return $userACL;
		}
		
		function wordwrap($str, $cols, $cut)
		{
			$lines = explode('\n', $str);
			$newStr = '';
			foreach($lines as $line)
			{
				// replace tabs by 8 space chars, or any tab only counts one char
				//$line = str_replace("\t","        ",$line);
				//$newStr .= wordwrap($line, $cols, $cut);
				$allowedLength = $cols-strlen($cut);
				if (strlen($line) > $allowedLength) {
					$s=explode(" ", $line);
					$line = "";
					$linecnt = 0;
					foreach ($s as $k=>$v) {
						$cnt = strlen($v);
						// only break long words within the wordboundaries, 
						if($cnt > $allowedLength) {
							$v=wordwrap($v, $allowedLength, $cut, true);
						}
						// the rest should be broken at the start of the new word that exceeds the limit  
						if ($linecnt+$cnt > $allowedLength) {
							$v=$cut.$v;
							$linecnt = 0;
						} else {
							$linecnt += $cnt;
						}
						if (strlen($v)) $line .= (strlen($line) ? " " : "").$v;
					}
				}
				$newStr .= $line;
			}
			return $newStr;
		}
		
		/**
		* convert the foldername from display charset to UTF-7
		*
		* @param string _parent the parent foldername
		* @returns ISO-8859-1 encoded string
		*/
		function _encodeFolderName($_folderName) {
			return $this->botranslation->convert($_folderName, self::$displayCharset, 'ISO-8859-1');
		}

		/**
		* convert the foldername from UTF-7 to display charset
		*
		* @param string _parent the parent foldername
		* @returns ISO-8859-1 encoded string
		*/
		function _decodeFolderName($_folderName) {
			return $this->botranslation->convert($_folderName, self::$displayCharset, 'ISO-8859-1');
		}

		/**
		* convert the sort value from the gui(integer) into a string
		*
		* @param int _sort the integer sort order
		* @returns the ascii sort string
		*/
		function _getSortString($_sort) 
		{
			switch($_sort) {
				case 2:
					$retValue = 'FROM';
					break;
				case 3:
					$retValue = 'SUBJECT';
					break;
				case 6:
					$retValue = 'SIZE';
					break;
				case 0:
				default:
					$retValue = 'DATE';
					break;
			}
			
			return $retValue;
		}

		function sendMDN($uid) {
			$identities = $this->mailPreferences->getIdentity();
			$headers = $this->getMessageHeader($uid);
			$send = & CreateObject('phpgwapi.send');
			$send->ClearAddresses();
			$send->ClearAttachments();
			$send->IsHTML(False);
			$send->IsSMTP();

			$array_to = explode(",",$headers['TO']);
			foreach($identities as  $identity) {
				if ( preg_match('/\b'.$identity->emailAddress.'\b/',$headers['TO']) ) {
					$send->From = $identity->emailAddress;
					$send->FromName = $identity->realName;
					error_log('Not Default '.$from);
					break;
				}
				if($identity->default) {
					$send->From = $identity->emailAddress;
					$send->FromName = $identity->realName;
				}
			}

			if (isset($headers['DISPOSITION-NOTIFICATION-TO'])) {
				$send->AddAddress( $headers['DISPOSITION-NOTIFICATION-TO'] );
			} else if ( isset($headers['RETURN-RECEIPT-TO']) ) {
				$send->AddAddress( $headers['RETURN-RECEIPT-TO']);
			} else if ( isset($headers['X-CONFIRM-READING-TO']) ) {
				$send->AddAddress( $headers['X-CONFIRM-READING-TO']);
			} else return false;

			$send->AddCustomHeader('References: '.$headers['MESSAGE-ID']);
			$send->Subject = $send->encode_subject( lang('Read')." : ".$headers['SUBJECT'] );
			
			$sep = "-----------mdn".$uniq_id = md5(uniqid(time()));
			
			$body = "--".$sep."\r\n".
				"Content-Type: text/plain; charset=ISO-8859-1\r\n".
				"Content-Transfer-Encoding: 7bit\r\n\r\n".
				$send->EncodeString(lang("Your message to %1 was displayed." ,$send->From),"7bit").
				"\r\n";

			$body .= "--".$sep."\r\n".
				"Content-Type: message/disposition-notification; name=\"MDNPart2.txt\"\r\n" .
				"Content-Disposition: inline\r\n".
				"Content-Transfer-Encoding: 7bit\r\n\r\n";
			$body.= $send->EncodeString("Reporting-UA: eGroupWare\r\n" .
						   "Final-Recipient: rfc822;".$send->From."\r\n" .
						   "Original-Message-ID: ".$headers['MESSAGE-ID']."\r\n".
						   "Disposition: manual-action/MDN-sent-manually; displayed",'7bit')."\r\n";

			$body .= "--".$sep."\r\n".
				"Content-Type: text/rfc822-headers; name=\"MDNPart3.txt\"\r\n" .
				"Content-Transfer-Encoding: 7bit\r\n" .
				"Content-Disposition: inline\r\n\r\n";
			$body .= $send->EncodeString($this->getMessageRawHeader($uid),'7bit')."\r\n";
			$body .= "--".$sep."--";


			$header = rtrim($send->CreateHeader())."\r\n"."Content-Type: multipart/report; report-type=disposition-notification;\r\n".
				"\tboundary=\"".$sep."\"\r\n\r\n";
			return $send->SmtpSend($header,$body);
		}

		/**
		 * Tests if string contains 8bit symbols.
		 *
		 * If charset is not set, function defaults to default_charset.
		 * $default_charset global must be set correctly if $charset is
		 * not used.
		 * @param string $string tested string
		 * @param string $charset charset used in a string
		 * @return bool true if 8bit symbols are detected
		 */
		static function is8bit(&$string,$charset='') {

		    if ($charset=='') $charset= self::$displayCharset;

			/**
			* Don't use \240 in ranges. Sometimes RH 7.2 doesn't like it.
			* Don't use \200-\237 for iso-8859-x charsets. This ranges
			* stores control symbols in those charsets.
			* Use preg_match instead of ereg in order to avoid problems
			* with mbstring overloading
			*/
			if (preg_match("/^iso-8859/i",$charset)) {
				$needle='/\240|[\241-\377]/';
			} else {
				$needle='/[\200-\237]|\240|[\241-\377]/';
			}
			return preg_match("$needle",$string);
		}
		static function detect_qp(&$sting) {
			$needle = '/(=[0-9][A-F])|(=[A-F][0-9])|(=[A-F][A-F])|(=[0-9][0-9])/';
			return preg_match("$needle",$string);
		}
	}
