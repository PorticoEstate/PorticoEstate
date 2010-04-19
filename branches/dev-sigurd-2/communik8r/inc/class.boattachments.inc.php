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
	include_once(PHPGW_INCLUDE_ROOT . SEP . 'communik8r' . SEP . 'inc' . SEP . 'class.bobase.inc.php');
	
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
		function rest($uri_parts)
		{
			$this->_validate_msg_id($uri_parts[2]);
			//error_log("boattachments called with as {$_SERVER['REQUEST_METHOD']} with " . print_r($uri_parts, True) . " uri parts");
			switch ( strtoupper($_SERVER['REQUEST_METHOD']) )
			{
				case 'GET':
					switch ( count($uri_parts) )
					{
						case 3: //requesting list of attachments for a message
							$this->get_attachment_list();
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
					if ( count($uri_parts) == 3 )//new attachment
					{
						$this->store_file();
						$this->get_attachment_list();
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

		function get_attachment_list()
		{
			$inc_langs = ( isset($_GET['mode']) && $_GET['mode'] == 'full');
			
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
			$xml = domxml_new_doc('1.0');
			
			$xsl = $xml->create_processing_instruction('xml-stylesheet', 'type="text/xsl" href="' . "{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/xsl/attach_popup" . '"');
			$xml->append_child($xsl);
			
			$phpgw = $xml->create_element_ns('http://dtds.phpgroupware.org/phpgw.dtd', 'response', 'phpgw');
			$phpgw->add_namespace('http://dtds.phpgroupware.org/phpgwapi.dtd', 'phpgwapi');
			$phpgw->add_namespace('http://dtds.phpgroupware.org/communik8r.dtd', 'communik8r');

			$info = $xml->create_element('phpgwapi:info');
			
			$base_url = $xml->create_element('phpgwapi:base_url');
			$base_url->append_child( $xml->create_text_node("{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r") );
			$info->append_child($base_url);
			unset($base_url);

			$skin = $xml->create_element('phpgwapi:skin');
			$skin->append_child( $xml->create_text_node('base') );
			$info->append_child($skin);
			unset($skin);

			if ( $inc_langs )
			{
				$langs = $xml->create_element('phpgwapi:langs');
				foreach ( $lang_strs as $lkey => $lval )
				{
					$lang = $xml->create_element('phpgwapi:lang');
					$lang->set_attribute('id', $lkey);
					$lang->append_child($xml->create_text_node($lval) );
					$langs->append_child($lang);
				}
				$info->append_child($langs);
			}
			
			$phpgw->append_child($info);

			$attachments = $xml->create_element('communik8r:attachments');
			
			foreach( $this->get_raw_list() as $key => $vals )
			{
				$attachment = $xml->create_element('communik8r:attachment');
				$attachment->set_attribute('id', $key);
				$attachment->set_attribute('icon', $this->mime2icon($vals['type']) );
				$attachment->set_attribute('size', $this->_int2human($vals['size']) );
				$attachment->append_child($xml->create_text_node($vals['name']));
				$attachments->append_child($attachment);
			}

			$phpgw->append_child($attachments);
			
			$xml->append_child($phpgw);
			
			Header('Content-Type: text/xml');
			echo $xml->dump_mem(true);

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
					$_FILES['attachment']['phpgw_file'] = $this->msg_path . SEP . $id;
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
				$phpgw_file = $this->msg_path . SEP . $this->_get_attach_id($file_name);
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
						. SEP . 'c8' 
						. SEP . $this->sess->sessionid 
						. SEP . $msg_id;
			}
			else
			{
				trigger_error(serialize(array(403 => 'invalid message id')), E_USER_WARNING);
			}
		}
	}
?>
