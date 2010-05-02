<?php
	/**
	* Communik8r attachment logic class
	*
	* @author Dave Hall skwashd@phpgroupware.org
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package communik8r
	* @subpackage attachments
	* @version $Id: class.boattachments.inc.php,v 1.1.1.1 2005/08/23 05:03:53 skwashd Exp $
	*/

	/**
	* @see bobase
	*/
	phpgw::import_class('communik8r.bobase');
	
	/**
	* Communik8r attachment logic class
	*/
	class boattachments extends bobase
	{
		/**
		* @var string $msg_id the unique string for the current message
		*/
		var $msg_id;

		/**
		* @var string path where attachments for this message are stored
		*/
		var $msg_path;

		/**
		* @var obj $sess Sessions class
		*/
		var $sess;
		
		function boattachments()
		{
			$this->sess =& $GLOBALS['phpgw']->session;
		}

		/**
		* REST URL handler
		*/
		function rest($data)
		{
			$this->_validate_msg_id($data['msg_id']);
			//error_log("boattachments called with as {$_SERVER['REQUEST_METHOD']} with " . print_r($uri_parts, True) . " uri parts");
			switch ( strtoupper($_SERVER['REQUEST_METHOD']) )
			{
				case 'GET':
					switch ( $data['action'] )
					{
						case 'full': //requesting list of attachments for a message
							$this->get_attachment_list($data);
							break;

						case 4: //requesting mailbox summary
							$this->get_attachment($uri_parts[3]);//to be implemented
							break;

						default:
							die('<error>invalid request</error>');
							//invalid request
					}
				break;

				case 'POST':
					if ( $data['action'] == 'full' )//new attachment
					{
						$this->store_file();
						$this->get_attachment_list($data);
						exit;
					}
					die('<error>invalid request</error>');
				break;

				case 'DELETE':
					if ( count($uri_parts) == 4 )
					{
						$this->remove_attachment( $uri_parts[3], array() );// remove attachment
					}
					else
					{
						die('<error>invalid request</error>');
					}
				break;
			}
		}

		/**
		* Check to see if a path exists, and optionally create it
		*
		* @param string $path the path to check - default is message path
		* @param bool $auto_create
		* @returns bool does path exist ?
		*/
		function check_path($path = '', $auto_create = True)
		{
			if ( !strlen($this->msg_path) )
			{
				return false;
			}
			
			if ( !$path )
			{
				$path = $this->msg_path;
			}

			if ( is_dir ( $path ) )
			{
				return True;
			}
			else
			{
				if ( $auto_create )
				{
					$this->make_path($path);
					return $this->check_path($path, False);
				}
			}
			return False;
		}

		function get_attachment_list($data)
		{
			$inc_langs = ( isset($data['action']) && $data['action'] == 'full');
			
			if ( $inc_langs )
			{
				$lang_strs = array
				(
					 'actions'	=> lang('actions'),
					 'attach'	=> lang('attach'),
					 'close'	=> lang('close'),
					 'filename'	=> lang('filename'),
					 'help'		=> lang('help'),
					 'remove'	=> lang('remove'),
					 'size'		=> lang('size')
				);
			}
			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;
			
			$xsl = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . "{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/templates/base/attach_popup.xsl" . '"');
			$xml->appendChild($xsl);
			
			$phpgw = $xml->createElement('phpgw:response', 'phpgw');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgw', 'http://dtds.phpgroupware.org/phpgw.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgwapi', 'http://dtds.phpgroupware.org/phpgwapi.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:communik8r', 'http://dtds.phpgroupware.org/communik8r.dtd');

			$info = $xml->createElement('phpgwapi:info');
			
			$base_url = $xml->createElement('phpgwapi:base_url');
			$base_url->appendChild( $xml->createTextNode( $GLOBALS['phpgw']->link('index.php') ) );
			$info->appendChild($base_url);
			unset($base_url);
			$app_url = $xml->createElement('phpgwapi:app_url');
			$app_url->appendChild( $xml->createTextNode("{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r") );
			$info->appendChild($app_url);
			unset($app_url);
			$api_url = $xml->createElement('phpgwapi:api_url');
			$api_url->appendChild( $xml->createTextNode("{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi") );
			$info->appendChild($api_url);
			unset($api_url);

			$msg_id = $xml->createElement('phpgwapi:msg_id');
			$msg_id->appendChild( $xml->createTextNode($data['msg_id']) );
			$info->appendChild($msg_id);
			unset($msg_id);

			$skin = $xml->createElement('phpgwapi:skin');
			$skin->appendChild( $xml->createTextNode('base') );
			$info->appendChild($skin);
			unset($skin);

			if ( $inc_langs )
			{
				$langs = $xml->createElement('phpgwapi:langs');
				foreach ( $lang_strs as $lkey => $lval )
				{
					$lang = $xml->createElement('phpgwapi:lang');
					$lang->setAttribute('id', $lkey);
					$lang->appendChild($xml->createTextNode($lval) );
					$langs->appendChild($lang);
				}
				$info->appendChild($langs);
			}
			
			$phpgw->appendChild($info);

			$attachments = $xml->createElement('communik8r:attachments');
			
			foreach( $this->get_raw_list() as $key => $vals )
			{
				$attachment = $xml->createElement('communik8r:attachment');
				$attachment->setAttribute('id', $key);
				$attachment->setAttribute('icon', $this->mime2icon($vals['type']) );
				$attachment->setAttribute('size', $this->_int2human($vals['size']) );
				$attachment->appendChild($xml->createTextNode($vals['name']));
				$attachments->appendChild($attachment);
			}

			$phpgw->appendChild($attachments);
			
			$xml->appendChild($phpgw);
			
			Header('Content-Type: text/xml');
			echo $xml->saveXML();
		}

		/**
		* Get a list of attachments for the current message
		*
		* @return array list of attachments - see $_FILES structure
		*/
		function get_raw_list()
		{
			$msgs = $this->sess->appsession('composing');
			//error_log($this->msg_id . "  " . print_r($msgs[$this->msg_id]['attachments'], True) );
			if ( isset($msgs[$this->msg_id]['attachments']) )
			{
				return $msgs[$this->msg_id]['attachments'];
			}
			return array();
		}
		
		/**
		* Remove an attachment from a message
		*
		* @param string $attach_id attachment id
		*/
		function remove_attachment($attach_id)
		{
			$msgs = $this->sess->appsession('composing');
			if ( isset($msg[$this->msg_id]['attachments'][$attach_id]) 
				&& is_array($msg[$this->msg_id]['attachments'][$attach_id]) )
			{
				if ( unlink($msg[$this->msg_id]['attachments'][$attach_id]['phpgw_file']) )
				{
					$this->_store_attachment_data($attach_id, array() );
					return True;
				}
			}
			trigger_error(serialize(array(500 => 'unable to remove attachment')), E_USER_ERROR);
			return False;
		}

		/**
		* Store a file as a temp file
		*/
		function store_file()
		{
			//error_log("boattachments::store_file() called: \$_FILES == " . print_r($_FILES, True) );
			if ( is_uploaded_file($_FILES['attachment']['tmp_name']) )
			{
				if ( $this->check_path() )
				{
					$id = $this->_get_attach_id($_FILES['attachment']['filename']);
					$_FILES['attachment']['phpgw_file'] = $this->msg_path . '/' . $id;
					//error_log("Attempting to store: " . $_FILES['attachment']['phpgw_file'] );
					$attach_data = $_FILES['attachment'];
					if ( !is_uploaded_file( $_FILES['attachment']['tmp_name'] ) 
						|| !move_uploaded_file($_FILES['attachment']['tmp_name'],  $_FILES['attachment']['phpgw_file']) ) 
					{
						trigger_error(serialize(array(500 => 'unable to save attachment')), E_USER_ERROR);
					}
					$this->_store_attachment_data($id, $attach_data);
				}
			}
		}

		/**
		* Store a string as an attachment
		*/
		function store_string($str, $file_name, $mimetype = 'message/rfc822')
		{
			if( !strlen($str) )
			{
				return false;
			}

			$this->_validate_msg_id($this->msg_id);
			if ( $this->check_path() )
			{
				$phpgw_file = $this->msg_path . '/' . $this->_get_attach_id($file_name);
				$fp = fopen($phpgw_file, 'wb');
				fwrite($fp, $str);
				$data = array
					(
						'name'		=> $filename,
						'type'		=> $mimetype,
						'size'		=> strlen(str),
						'phpgw_file'	=> $phpgw_file
					);
				
				$this->_store_attachment_data($id, $data);
			}
		}

		/**
		* Make a random filename hash
		*/
		function _get_attach_id($filename)
		{
			if( (date('s') % 2) ) //add some more randomness to the madness
			{
				return sha1($filename . time() . md5($this->msg_id));
			}
			else
			{
				return md5($this->msg_id . time() . sha1($filename));
			}
		}

		/**
		* Store information about an attachment
		*
		* @param string $attach_id the unique id for the attachment
		* @param array $attach_data $_FILES info for attachment
		*/
		function _store_attachment_data($attach_id, $attach_data)
		{
			$msgs = $this->sess->appsession('composing');
			
			if ( count($attach_data) && !isset($msgs[$this->msg_id]['attachments'][$attach_id]) )
			{
				$msgs[$this->msg_id]['attachments'][$attach_id] = $attach_data;
				$this->sess->appsession('composing', 'communik8r', $msgs);
			}
			else //remove
			{
				unset($msgs[$this->msg_id]['attachments'][$attach_id]);
				$this->sess->appsession('composing', 'communik8r', $msgs);
			}
		}

		/**
		* Check that a message ID is valid
		*/
		function _validate_msg_id($msg_id)
		{
			$messages = $this->sess->appsession('composing');
			if ( is_array($messages[$msg_id]) )
			{
				$this->msg_id = $msg_id;
				$this->msg_path = $GLOBALS['phpgw_info']['server']['temp_dir'] 
						. '/c8' 
						. '/' . $this->sess->sessionid 
						. '/' . $msg_id;
			}
			else
			{
				trigger_error(serialize(array(403 => 'invalid message id')), E_USER_WARNING);
			}
		}
	}
