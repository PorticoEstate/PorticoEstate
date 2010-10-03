<?php
	/**
	 * Communik8r email logic class
	 *
	 * @author Dave Hall skwashd@phpgroupware.org
	 * @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package communik8r
	 * @subpackage email
	 * @version $Id: class.boemail.inc.php,v 1.1.1.1 2005/08/23 05:04:02 skwashd Exp $
	 */

	/**
	 * @see bobase
	 */

	phpgw::import_class('communik8r.bobase');

	/**
	 * Communik8r email logic class
	 */
	class boemail extends bobase
	{
		/**
		 * @var bool $smtp_ssl does the SMTP server require STARTTLS?
		 */
		var $smtp_auth;

		/**
		 * @var string $smtp_host the SMTP host for sending mail out
		 */
		var $smtp_host;

		/**
		 * @var int $smtp_port the SMTP port for sending mail out
		 */
		var $smtp_port;

		/**
		 * @var bool $smtp_ssl does the SMTP server require SSL?
		 */
		var $ssl;

		/**
		 * @var bool $smtp_ssl does the SMTP server require STARTTLS?
		 */
		var $tls;

		/**
		 * @constructor
		 */
		function boemail()
		{
			$config = createObject('phpgwapi.config', 'communik8r');
			$config->read_repository();

			$this->smtp_auth  = false; //!!$config->config_data['smtp_auth'];
			$this->smtp_host = $config->config_data['smtp_host'];
			$this->smtp_port = $config->config_data['smtp_port'];
			$this->smtp_ssl  = false; //!!$config->config_data['smtp_ssl'];
			$this->smtp_tls  = false; //!!$config->config_data['smtp_tls'];
			
			$this->bobase();
		}

		/**
		 * REST URL handler
		 */
		function rest($data)
		{
			switch ( strtoupper($_SERVER['REQUEST_METHOD']) )
			{
				case 'GET':
					switch ( $data['action'] )
					{
						//FIXME
						case 'something': //requesting mailboxes for account
							$this->get_mailboxes($data['acct_id'], True);
							break;
						case 'status': //requesting mailboxes for account
							$this->update_mailbox_status($data);
							break;

						case 'summary': //requesting mailbox summary
							if($data['acct_id'] && $data['mbox_name'])
							{
								$this->get_summary($data);
							}
							break;

						case 'get': //requesting message
							$this->get_msg($data);
							break;

						case 'attachment': //requesting attachment
							$this->get_part($data);
							break;

						default:
						//	$this->compose($data);//testing
						//	_debug_array($data);
							die('<error>invalid request</error>');
							//invalid request
					}
					break;

				case 'POST':
			//	case 'GET':
			//		error_log("\nPOST: " . print_r($_POST,true),3,'/tmp/my-errors.log');
					switch ( $data['action'] )
					{
						case 'status':
							$this->update_mailbox_status($data);
							break;
						default:
							$this->compose($data);//no validation for now :P
					}
					break;

				case 'DELETE':
					if ( count($uri_parts) == 5 )
					{
						$this->delete_msg($uri_parts);
					}
					break;
			}
		}

		function compose($data)
		{
			$reply = $forward = false;
			if (  strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' )
			{
				if ( $data['action'] == 'draft' ) 
				{
					//not yet supported
				}
				else //send
				{
					$this->_process_send($data);
				}
			}
			else if (  strtoupper($_SERVER['REQUEST_METHOD']) != 'GET' )
			{
				return false;
			}

			//FIXME Make js::oApplication to obtain and send this id to boemail::compose
			//Each new message must have a unique id
			$ids = $GLOBALS['phpgw']->session->appsession('composing');
			$msg_id = sha1(md5(time()) + $GLOBALS['phpgw_info']['user']['account_lid']);
			$ids[$msg_id] = array
				(
				 'info'		=> array(),
				 'attachments'	=> array()
				);
			$GLOBALS['phpgw']->session->appsession('composing', 'communik8r', $ids);
			unset($ids);

			if ( $data['action'] != 'new' )
			{
				$msg_parts = explode('_', $uri_parts[3] ); //0 type, 1 acctid, 2 folder, 3 msgid
				$acct_info = execMethod('communik8r.boaccounts.id2array', $msg_parts[1]);
				$socache = createObject('communik8r.socache_email', $acct_info);
				$msg = $socache->get_msg($msg_parts[3]);

				$reply = ( $data['action'] == 'reply' || $data['action'] == 'reply');
				$forward = ($data['action'] == 'forward');
				$body = '';

				if ( $reply )
				{
					if( !is_object($msg) ) //FIXME need error handling here
					{
						trigger_error(serialize(array(500 => 'invalid message id specified')), E_USER_ERROR);
					}
					$body = $this->_quote($msg->body);
				}
				else if( $forward )
				{
					if ( count( $msg->entities ) )
					{
						$body = $socache->get_raw_msg($msg_parts[3]);
						$attach = createObject('communik8r.boattachments');
						$attach->msg_id = $msg_id;
						$attach->store_string($body, $msg->rfc822_header['subject']);
						$body = '';
					}
					else // just quote it
					{
						error_log(print_r( $msg->rfc822_header, True));
						$body = "-------- Forwarded Message --------\n"
							. lang('from: %1', "{$msg->rfc822_header['from'][0]} <{$msg->rfc822_header['from'][2]}@{$msg->rfc822_header['from'][3]}>") . "\n"
							//. lang('to: %1', "{$msg->rfc822_header['from'][0]} <{$msg->rfc822_header['from'][2]}@{$msg->rfc822_header['from'][3]}>") . "\n" //FIXME
							. lang('subject: %1', $msg->rfc822_header['subject']) . "\n"
							. lang('date: %1', implode(' ', $msg->rfc822_header['date']) ) . "\n\n" 
							. $msg->body;
					}
				}
			}
			else
			{
				$msg = new stdClass();
				$msg->from = $_GET['to']; //hack
			}

			$lang_strs = array
				(
				 'bcc'                   => lang('bcc'),
				 'cc'                    => lang('cc'),
				 'compose_title'         => lang('compose email'),
				 'date'                  => lang('date'),
				 'from'                  => lang('from'),
				 'reply_to'              => lang('reply to'),
				 'subject'               => lang('subject'),
				 'to'                    => lang('to'),
				 'view_attachments'      => lang('view attachments'),
				 'view_signature'        => lang('view signature'),
				);

			$buttons = $this->get_compose_buttons('email');

			Header('Content-Type: text/xml');

			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;

			$xsl = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . "{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/templates/base/compose.xsl" . '"');
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

			$skin = $xml->createElement('phpgwapi:skin');
			$skin->appendChild( $xml->createTextNode('base') );
			$info->appendChild($skin);
			unset($skin);

			$langs = $xml->createElement('phpgwapi:langs');
			foreach ( $lang_strs as $lkey => $lval )
			{
				$lang = $xml->createElement('phpgwapi:lang');
				$lang->setAttribute('id', $lkey);
				$lang->appendChild($xml->createTextNode($lval) );
				$langs->appendChild($lang);
			}
			$info->appendChild($langs);

			$phpgw->appendChild($info);

			$elm = $xml->createElement('communik8r:response');

			$comm_info = $xml->createElement('communik8r:info');

			$btns = $xml->createElement('communik8r:buttons');
			foreach($buttons as $id => $attribs)
			{
				if ( strpos($attribs['label'], '*') )
				{
					$attribs['label'] = substr($attribs['label'], 0, -1);
				}

				if ( ($attribs['shortcut'] != '') && strpos($attribs['label'], $attribs['shortcut'] ) === false )
				{
					if ( strpos($attribs['label'], strtolower($attribs['shortcut']) ) !== false )
					{
						$attribs['shortcut'] = strtolower($attribs['shortcut']);
					}
					else
					{
						$attribs['label'] .= " [{$attribs['shortcut']}]";
					}
				}

				$btn = $xml->createElement('communik8r:button');

				foreach($attribs as $akey => $aval)
				{
					$btn->setAttribute($akey, $aval);
				}

				$btns->appendChild($btn);
			}
			$elm->appendChild($btns);

			$accts = $xml->createElement('communik8r:accounts');

			$accounts = execMethod('communik8r.boaccounts.get_list', 'email');
			$accts = $xml->createElement('communik8r:accounts');
			foreach($accounts as $account)
			{
				$acct = $xml->createElement('communik8r:account');
				$acct->setAttribute('id', $account['acct_id']);
				$acct->setAttribute('sig_id', $account['sig_id']);
				if( $account['acct_id'] == $msg_parts[1] )
				{
					$acct->setAttribute('current', 1);
				}

				$acct_name = $xml->createElement('communik8r:account_name');
				$acct_name->appendChild( $xml->createCDATASection("{$account['display_name']} <{$account['acct_uri']}>") );
				$acct->appendChild($acct_name);

				$accts->appendChild($acct);
			}
			$elm->appendChild($accts);

			$message = $xml->createElement('communik8r:message');
			$message->setAttribute('id', $msg_id );

			$headers = $xml->createElement('communik8r:headers');

			if ( isset($msg->from) )
			{
				$header = $xml->createElement('communik8r:message_to');
				$header->appendChild( $xml->createTextNode($msg->from) );
				$headers->appendChild($header);
			}

			//ADD REPLY-TO-ALL-LOGIC-HERE

			if( !($reply || $forward) )
			{
				$subject = '';
			}
			else
			{
				$prefix = ($reply ? 're:' : 'fwd:');
				if ( isset($msg->rfc822_header['subject']) )
				{
					$subject = lang("{$prefix} %1", $msg->rfc822_header['subject']);
				}
				else
				{
					$subject = lang("{$prefix} %1", 'no subject');
				}
			}
			$header = $xml->createElement('communik8r:message_subject');
			$header->appendChild( $xml->createTextNode($subject) );
			$headers->appendChild($header);
			$message->appendChild($headers);

			$body = $xml->createElement('communik8r:body');
			$this->_decode_body($msg->body, $msg->entities[0]);
//			$body->appendChild( $xml->createTextNode( $body ) );
			$body->appendChild( $xml->createTextNode( $msg->body ) );
			$message->appendChild($body);

			$elm->appendChild($message);
			$phpgw->appendChild($elm);

			$xml->appendChild($phpgw);

			echo $xml->saveXML();
		}

		/**
		 * Delete a message
		 *
		 * @param array $uri_parts the message information acct_id, mbox, msg_id
		 */
		function delete_msg($uri_parts)
		{
			trigger_error('Attempting to delete message: ' . print_r($uri_parts, true));
			$acct_info = execMethod('communik8r.boaccounts.id2array', $uri_parts[2]);
			$socache = createObject('communik8r.socache_email', $acct_info);
			if ( $socache->delete_msg($uri_parts[4]) )
			{
				echo '<done />';
			}
			else
			{
				header('HTTP/1.0 404 Message not found, so not deleted :P');
			}
		}

		/**
		 * Get a summary of all mailboxes for account
		 *
		 * @param array|string $info account info
		 * @param bool $output echo output result drectly as xml?
		 */
		function get_mailboxes($info, $output = False)
		{
			//trigger_error('boemail::get_mailboxes(' . print_r($info, true) . ', ' . intval($output) . ') called');
			if ( is_string($info) )
			{
				$info = execMethod('communik8r.boaccounts.name2array', $info);
			}
			else
			{
				$info = execMethod('communik8r.boaccounts.id2array', $info);
			}

			$socache = createObject('communik8r.socache_email', $info);

			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;
			
			$tree = $xml->createElement('tree');
			$tree->setAttribute('id', $info['acct_id']);
			$tree->setAttribute('text', $info['acct_name']);

			$elm = $xml->createElement('crap');

			$mboxs = $socache->get_mailboxes();
			foreach( $mboxs as $mbox => $info)
			{
				//error_log("fetching data for {$mbox} Line: " . __LINE__ . ' in ' . __FILE__);
				$elm = $this->_mbox2xml($xml, $mbox, $info );
				$tree->appendChild($elm);
			}

			if ( !$output )
			{
				return $tree;
			//	return $elm;
			//	return $elm->cloneNode(True);
			}

	//		$tree->appendChild($elm);
			$xml->appendChild($tree);

			Header('Content-Type: text/xml');
			echo $xml->saveXML();
		}

		function get_msg($data)
		{
			$acct_info = execMethod('communik8r.boaccounts.id2array', $data['acct_id']);
			$socache = createObject('communik8r.socache_email', $acct_info);
			$msg = $socache->get_msg($data['msg_id']);

			//error_log(print_r($msg, True));

			if( !is_object($msg) ) //FIXME need error handling here
			{}

			$langs = array
				(
				 'lang_from'	=> lang('from'),
				 'lang_reply_to'=> lang('reply to'),
				 'lang_to'	=> lang('to'),
				 'lang_cc'	=> lang('cc'),
				 'lang_subject'	=> lang('subject'),
				 'lang_date'	=> lang('date')
				);

			Header('Content-Type: text/xml');

			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;

			$xsl = $xml->createProcessingInstruction('xml-stylesheet', "type=\"text/xsl\" href=\"{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/templates/base/message.xsl\"");
			$xml->appendChild($xsl);

			$phpgw = $xml->createElement('phpgw:response', 'phpgw');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgw', 'http://dtds.phpgroupware.org/phpgw.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgwapi', 'http://dtds.phpgroupware.org/phpgwapi.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:communik8r', 'http://dtds.phpgroupware.org/communik8r.dtd');

			$info = $xml->createElement('phpgwapi:info');

			$base_url = $xml->createElement('phpgwapi:base_url');
			$base_url->appendChild( $xml->createTextNode($GLOBALS['phpgw_info']['server']['webserver_url']) );
			$info->appendChild($base_url);

			foreach ( $langs as $lkey => $lval )
			{
				$lang = $xml->createElement('phpgwapi:lang');
				$lang->setAttribute('id', "phpgwapi_$lkey");
				$lang->appendChild($xml->createTextNode($lval) );
				$info->appendChild($lang);
			}

			$phpgw->appendChild($info);

			$elm = $xml->createElement('communik8r:response');

			$comm_info = $xml->createElement('communik8r:info');

			$message = $xml->createElement('communik8r:message');

			//$message->setAttribute('id', "{$acct_info['acct_id']}_{$uri_parts[3]}_{$uri_parts[4]}");
			$message->setAttribute('id', "{$acct_info['acct_id']}_{$data['mbox_name']}_{$data['msg_id']}");
			$headers = $xml->createElement('communik8r:headers');

			if ( isset($msg->from) )
			{
				$from_str = '';
				$from = $this->_address2parts($msg->from);
				if ( $from['name'] != '' )
				{
					$from_str = $from['name'] . ' ';
				}
				$from_str .= "<{$from['full']}>";

				$header = $xml->createElement('communik8r:message_from');
				$header->appendChild( $xml->createTextNode($from_str) );
				$headers->appendChild($header);
				unset($from);
			}

			if ( isset($msg->reply_to) && $msg->reply_to )
			{
				$reply_tos = split(',', $msg->reply_to);
				$header = $xml->createElement('communik8r:message_reply_to');
				$i = 0;
				foreach ( $reply_tos as $reply_to )
				{
					if ( $reply_to == '' ) //ignore empties
					{
						continue;
					}

					$r2_str = ($i ? ', ' : '');
					$r2 = $this->_address2parts(trim($reply_to));
					if ( $r2['name'] != '' )
					{
						$r2_str = $r2['name'] . ' ';
					}
					$r2_str .= "<{$r2['full']}>";

					$header->appendChild( $xml->createTextNode($r2_str) );
					unset($r2_str);
					++$i;
				}
				$headers->appendChild($header);
			}

			$tos = array();
			if ( !isset($msg->to) || !$msg->to )
			{
				$tos[0] = array
					(
						'name'	=> lang('undisclosed recipients'),
						'mbox'	=> '',
						'host'	=> '',
						'full'	=> ''
					);
			}
			else
			{
				$tmp_tos = explode(',', $msg->to);
				foreach($tmp_tos as $to)
				{
					if ( $to == '' ) //ignore empties
					{
						continue;
					}
					$tos[] = $tmp = $this->_address2parts( trim($to) );
				}
				unset($tmp_tos); unset($to);
			}

			$i = 0;
			$header = $xml->createElement('communik8r:message_to');
			foreach ( $tos as $hdr )
			{
				if( $i == 0 && $hdr['name'] == lang('undisclosed recipients'))
				{
					$header = $xml->createElement('communik8r:message_to');
					$header->appendChild( $xml->createTextNode($hdr[3]) );
					$headers->appendChild($header);
					$header->appendChild( $xml->createTextNode($hdr) );
					$headers->appendChild($header);
					break;
				}

				$to_str = ($i ? ', ' : '');
				if ( $hdr['name'] != '' )
				{
					$to_str = $hdr['name'] . ' ';
				}
				$to_str .= "<{$hdr['full']}>";

				$header->appendChild( $xml->createTextNode($to_str) );
				unset($to_str);
				++$i;
			}
			$headers->appendChild($header);


			if ( isset($msg->cc) && $msg->cc )
			{
				$ccs = split(',', $msg->cc);
				$header = $xml->createElement('communik8r:message_cc');
				$i = 0;
				foreach ( $ccs as $cc )
				{
					if ( $cc == '' ) //ignore empties
					{
						continue;
					}

					$cc_str = ($i ? ', ' : '');
					$cc = $this->_address2parts(trim($cc));
					if ( $cc['name'] != '' )
					{
						$cc_str = $cc['name'] . ' ';
					}
					$cc_str .= "<{$cc['full']}>";

					$header->appendChild( $xml->createTextNode($cc_str) );
					unset($cc_str);
					++$i;
				}
				$headers->appendChild($header);
			}

			$header = $xml->createElement('communik8r:message_subject');
			$header->appendChild( $xml->createTextNode($msg->subject ) );
			$headers->appendChild($header);

			$date_fmt = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] . ' '
				. ( $GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == 12
						? 'g:i a'
						: 'H:i' );

			$header = $xml->createElement('communik8r:message_date');
			$header->appendChild( $xml->createTextNode( date($date_fmt, $msg->timestamp) ) );
			$headers->appendChild($header);

			$message->appendChild($headers);

			$body = $xml->createElement('communik8r:body');
			$this->_decode_body($msg->body, $msg->body_meta);
			$body->appendChild( $xml->createCDATASection( $msg->body . "\n\n" ) );

			$parts = $xml->createElement('communik8r:parts');
			if ( isset($msg->parts) && count($msg->parts) != 1 ) // we don't need it if it is only the body
			{
				//$body->appendChild( $xml->createCDATASection( print_r($msg->parts, True) ) );
				foreach ( $msg->parts as $part_no => $part )
				{
					if ( $part_no == 1 )
					{
						continue; //skip the body
					}
					$prt = $xml->createElement('communik8r:part');
					$prt->setAttribute('id', $part_no);
					$prt->setAttribute('mimetype', $part['typestring']);
					$prt->setAttribute('inline', ($this->_is_inline($part['typestring']) ? 'true' : 'false') );
					$prt->setAttribute('size', $this->_int2human($part['size']) );
					$prt->appendChild( $xml->createCDATASection($part['name']) );
					$prt->setAttribute('icon', $this->mime2icon($part['typestring']) );
					$parts->appendChild($prt);
				}
			}
			$message->appendChild($parts);

			$message->appendChild($body);

			$elm->appendChild($message);
			$phpgw->appendChild($elm);

			$xml->appendChild($phpgw);

			echo $xml->saveXML();

			$msg_pref = $this->_get_pref('current_message');
			//$uri_parts[3] = str_replace('.', '___', $uri_parts[3]);
			//$msg_pref["email_{$uri_parts[2]}_{$uri_parts[3]}"] = $uri_parts[4];
			$msg_pref["email_{$data['acct_id']}_{$data['mbox_name']}"] = $data['msg_id'];
			
			$this->_store_pref('current_message', $msg_pref);
		}

		function get_part($data)
		{
			$acct_info = execMethod('communik8r.boaccounts.id2array', $data['acct_id']);
			$socache = createObject('communik8r.socache_email', $acct_info);
			$msg = $socache->get_msg($data['msg_id']);
			$part = $socache->get_msg($data['msg_id'], False,  $data['part']);

			$info =& $part['structure']->structure;//convience
			//echo '<pre>' . print_r($info, True) . '</pre>';

			$mime = createObject('communik8r.mail_mime', '');

			$part_type = $mime->get_part_type_code($part_no, $info );

			$part_info = $msg->parts[$data['part']];
			$attach_name = $part_info['name'];
//_debug_array($part_info);die();
			$mime_type = '';
			if(strtoupper($info[((int)$data['part'])-1][0]) == 'APPLICATION' || strtoupper($info[((int)$data['part'])-1][0]) == 'IMAGE')
			{
				$part['content'] = base64_decode($part['content']);			
			}
			elseif (strtoupper($part_info['typestring']) == 'TEXT/HTML' ) 
			{
				$mime_type = 'text/html';
				$part['content'] = strip_tags($part['content'], '<a><b><blockquote><body><br><div><em><h1><h2><h3><hr><i><li><p><pre><blockquote><img><span><strong><ul>');
				$part['content'] = quoted_printable_decode($part['content']);
				if(strtoupper($part_info['charset']) != 'UTF-8')
				{
					$part['content'] = utf8_encode($part['content']);
				}
			}
			elseif (strtoupper($part_info['typestring']) == 'MESSAGE/RFC822' ) 
			{
				$mime_type = 'text/plain';
				$part['content'] = quoted_printable_decode($part['content']);
				if(strtoupper($part_info['charset']) != 'UTF-8')
				{
					$part['content'] = utf8_encode($part['content']);
				}
			}
			else
			{
				$attach_name = 'attachment';
			}

//_debug_array($info);
//_debug_array($attach_name);
//die();
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($attach_name, $mime_type, $part_info['size']);
			echo $part['content'];
			exit;

/*
			if ( isset($info->header->parameters['name']) && $info->header->parameters['name'] != '')
			{
				$attach_name = $info->header->parameters['name'];
			}
			elseif(isset($info->header->properties) && isset($info->header->disposition->properties)
					&& isset($info->header->disposition->properties['name']) 
					&& $info->header->disposition->properties['name'] != '')
			{
				$attach_name = $info->header->disposition->properties['name'];
			}
*/
/*
			if ( strtolower($info->header->encoding) == 'base64' )
			{
				$part['content'] = base64_decode($part['content']);
			}
			else if ( strtolower($info->header->encoding) == 'quoted-printable' )
			{
				$part['content'] = quoted_printable_decode($part['content']);
			}
*/
			$browser = CreateObject('phpgwapi.browser');
			if ( ( $info->type0 == 'text' && $info->type1 == 'plain' ) 
					|| ( $info->type0 == 'message' && $info->type1 == 'rfc822' ) )
			{
				$browser->content_header('attachment', 'text/plain');
				echo $part['content'];
				exit;
			}
			elseif ( $info->type0 == 'text' && $info->type1 == 'html' ) 
			{
				$part['content'] = strip_tags($part['content'], '<a><b><blockquote><body><br><div><em><h1><h2><h3><hr><i><li><p><pre><blockquote><img><span><strong><ul>');
			}

			$browser->content_header($attach_name, "{$info->type0}/{$info->type1}", strlen($part['content']));

			echo $part['content'];
			exit;
		}

		/**
		 * Get the summary of the contents of the specified mailbox
		 *
		 * @param array $uri_parts the exploded request uri
		 */
		function get_summary($data)
		{
		//	trigger_error('boemail::get_summary(' . print_r($data, true) . ') called');
			Header('Content-Type: text/xml');
			$acct_info = execMethod('communik8r.boaccounts.id2array', $data['acct_id']);
			$socache = createObject('communik8r.socache_email', $acct_info);

			$msgs = $socache->get_msg_list($data['mbox_name']);

			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;

			$xsl = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' 
					. "{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/templates/base/summary.xsl" . '"');
			$xml->appendChild($xsl);

			$phpgw = $xml->createElement('phpgw:response', 'phpgw');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgw', 'http://dtds.phpgroupware.org/phpgw.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgwapi', 'http://dtds.phpgroupware.org/phpgwapi.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:communik8r', 'http://dtds.phpgroupware.org/communik8r.dtd');

			$info = $xml->createElement('phpgwapi:info');
			$base_url = $xml->createElement('phpgwapi:base_url');
			$base_url->appendChild( $xml->createTextNode($GLOBALS['phpgw_info']['server']['webserver_url']) );
			$info->appendChild($base_url);
			$phpgw->appendChild($info);

			$elm = $xml->createElement('communik8r:response');

			$comm_info = $xml->createElement('communik8r:info');
			$msg_sums = $xml->createElement('communik8r:msg_sums');

			$acct = $xml->createElement('communik8r:account');
			$acct->setAttribute('id', $acct_info['acct_id']);
			$acct->appendChild( $xml->createTextNode($data['acct_id']) );
			$comm_info->appendChild($acct);

			$mailbox = $xml->createElement('communik8r:mailbox');
			$mailbox->appendChild( $xml->createTextNode($data['mbox_name']) );
			$comm_info->appendChild($mailbox);

			$mailbox = $xml->createElement('communik8r:hide_deleted');
			$mailbox->appendChild( $xml->createTextNode('true') );
			$comm_info->appendChild($mailbox);

			$elm->appendChild($comm_info);

			foreach ( $msgs as $id => $msg)
			{
				$msg_sum = $xml->createElement('communik8r:msg_sum');
				$msg_sum->setAttribute('id', "email_{$acct_info['acct_id']}_{$msg['mbox_name']}_{$id}");

				$msg_flags = $xml->createElement('communik8r:msg_flags');
				$msg_flags->setAttribute('flag_seen', ($msg['flag_seen'] ? 'true' : 'false') );
				$msg_flags->setAttribute('flag_answered', ($msg['flag_answered'] ? 'true' : 'false') );
				$msg_flags->setAttribute('flag_deleted', ($msg['flag_deleted'] ? 'true' : 'false') );
				$msg_flags->setAttribute('flag_flagged', ($msg['flag_flagged'] ? 'true' : 'false') );
				$msg_flags->setAttribute('flag_draft', ($msg['flag_draft'] ? 'true' : 'false') );
				$msg_flags->setAttribute('attachments', ($msg['attachments'] ? 'true' : 'false') );
				$msg_sum->appendChild($msg_flags);

				$msg_sender = $xml->createElement('communik8r:msg_sender');
				$msg_sender->appendChild( $xml->createTextNode( $msg['sender']) );
				$msg_sum->appendChild($msg_sender);

				$subject = $xml->createElement('communik8r:msg_subject');
				$subject->appendChild( $xml->createTextNode($msg['subject']) );

				$msg_sum->appendChild($subject);

				$msg_size = $xml->createElement('communik8r:msg_size');
				$msg_size->setAttribute('intval', $msg['msg_size']);
				$msg_size->appendChild( $xml->createTextNode( $this->_int2human($msg['msg_size']) ) );
				$msg_sum->appendChild($msg_size);

				$msg_date = $xml->createElement('communik8r:msg_date');
				$msg_date->setAttribute('intval', $msg['date_sent']);
				$msg_date->appendChild( $xml->createTextNode( $this->_date2human($msg['date_sent']) ) );
				$msg_sum->appendChild($msg_date);

				$msg_sums->appendChild($msg_sum);
			}
			$elm->appendChild($msg_sums);
			$phpgw->appendChild($elm);

			$xml->appendChild($phpgw);

			echo $xml->saveXML();
			$this->_store_pref('current_selection', "email_{$data['acct_id']}_{$data['mbox_name']}");
			//echo '<pre>' . print_r(domxml_xmltree($xml->dump_mem(true)), True) . '</pre>';
		}

		/**
		* Used for updating the status of a mailbox
		*/
		function update_mailbox_status($data)
		{
			$acct_info = execMethod('communik8r.boaccounts.id2array', $data['acct_id']);
			$socache = createObject('communik8r.socache_email', $acct_info);

			$socache->set_open($data['mbox_name'], $data['status'] );
			return;

			$xmldata = '';
			$putdata = fopen('php://input', 'r');//the TFphpM is fsckd stdin doesn't work!!

			while ( $_data = fread($putdata, 1024) )
			{
				$xmldata .= "{$_data}\n";
			}
			fclose($putdata);

			$doc = new DOMDocument;

			if ( !$doc->loadXML($xmldata) )
			{
			//	trigger_error('Invalid XML: ' . print_r($xmldata), E_USER_ERROR);
			}
			unset($xmldata);

			$elms = $doc->getElementsByTagName('status');
			
			foreach ( $elms as $elm )
			{
				switch ( trim( $elm->getAttribute('id') ) )
				{
					case 'open' :
//						$socache->set_open($data['mbox_name'], $elm->get_content() );
						break;
					default:
	//					trigger_error("Invalid status request: Mailbox: {$data['action']} ID: " . $elm->getAttribute('id')  
	//					. ' Value: ' . $elm->get_content(), E_USER_ERROR );
				}
			}
		}

		/**
		 * Convert an email address to a multidimensional string
		 *
		 * @internal lifted from phpgw's email app class.mail_dcom_base_sock.inc.php
		 * @param string $addr email address
		 * @returns array address parts
		 */
		function _address2parts($addr)
		{
			$name = $email = '';

			/* 
			 *	Extract real name and e-mail address
			 *	According to RFC1036 the From field can have one of three formats:
			 *		1. Real Name <name@domain.name>
			 *		2. name@domain.name (Real Name)
			 *		3. name@domain.name
			 */

			if ( eregi("(.*) <([-a-z0-9_$+.]+@[-a-z0-9_.]+[-a-z0-9_]+)>", $addr, $regs) ) //option 1
			{
				$email = $regs[2];
				$name = $regs[1];
			}
			elseif ( eregi("([-a-z0-9_$+.]+@[-a-z0-9_.]+[-a-z0-9_]+) ((.*))", $addr, $regs) ) //option 2
			{
				$email = $regs[1];
				$name = $regs[2];
			}
			else //all else fails
			{
				$email = $addr;
			}

			$name = eregi_replace("^\"(.*)\"$", "\\1", $name);
			$name = eregi_replace("^\((.*)\)$", "\\1", $name);

			$parts = array
				(
				 'name'	=> '',
				 'mbox'	=> '',
				 'host'	=> '',
				 'full'	=> $email
				);
			$parts['name'] = $name;

			$temp = explode('@', $email);
			$parts['mbox'] = $temp[0];

			if ( count($temp) == 2 )
			{
				$parts['host'] = $temp[1];
			}
			return $parts;
		}

		/**
		 * Decode a body part
		 */
		function _decode_body(&$body, &$headers)
		{
			switch ( strtolower($headers[5]) )
			{
				case 'base64':
					$body = base64_decode($body);
					break;

				case 'quoted-printable':
					$body = quoted_printable_decode($body);
					break;
			}

			$charset = 'us-ascii';
			for($i = 0; $i < count($headers[2]); $i +=2 )
			{
				if ( strtolower($headers[2][$i]) == 'charset' )
				{
					$charset = $headers[2][$i+1];
					break;
				}
			}

			if ( $charset != 'us-ascii' && $charset != 'utf-8')
			{
				$body = mb_convert_encoding($body, 'utf-8', $charset);
		//		$body = utf8_encode($body);
			}

			if( $headers[1]=='HTML')
			{
				$body = $this->_html2plain($body);
			}
			else
			{
				$body = wordwrap($body, 80); //make it look nice
			}
		}

		/**
		 * Convert HTML to formatted plain text
		 *
		 * @param string $html the raw html
		 * @returns string formatted plain text
		 */
		function _html2plain($html)
		{
			$cnvtr = createObject('communik8r.html2text', $html);
			return $cnvtr->get_text();
		}

		/**
		 * "XMLify" a multidimensional mailbox array
		 *
		 * @access private
		 * @internal is normalled called recursively by itself
		 * @param object $xml reference to DOMXML object
		 * @param string $mbox mailbox name
		 * @param array $info mailbox information
		 */
		function _mbox2xml(&$xml, $mbox, $info, $parent = '')
		{
			$pref = $this->_get_pref('current_selection');
			$parent .= ($parent ? $info['sep'] : '');
			$elm = $xml->createElement('item');

			$elm->setAttribute('id', "{$info['acct_handler']}_{$info['acct_id']}_{$parent}{$mbox}");
			$elm->setAttribute('text', $mbox);
			$elm->setAttribute('open', intval($info['open'] || strtolower($mbox) == 'inbox') );
			
			if ( "{$info['acct_handler']}_{$info['acct_id']}_{$parent}{$mbox}" == $pref )
			{
				$elm->setAttribute('select', 1);
				$elm->setAttribute('call', 1);
			}

			if ( count($info['children']) )
			{
				foreach( $info['children'] as $smbox => $sinfo )
				{
					$elm->appendChild($this->_mbox2xml($xml, $smbox, $sinfo, $parent . $mbox));
				}
			}

			return $elm;
		}

		/**
		 * Process the parts of a message into a simple array
		 *
		 * @param object $entities the message parts
		 * @returns array message parts
		 */
		function _process_parts(&$entities)
		{
			$parts = array();

			if ( !( is_array($entities) && count($entities) ) )
			{
				return $parts;
			}

			foreach ( $entities as $ent_id => $entity )
			{
				$filename =  lang('attachment (%1)', "{$entity->type0}/{$entity->type1}");
				if ( isset($entity->header->parameters) && isset($entity->header->parameters['name']) )
				{
					$filename = $entity->header->parameters['name'];
				}
				elseif ( isset($entity->header->parameters) && isset($entity->header->parameters['filename']) )
				{
					$filename = $entity->header->parameters['filename'];
				}

				$icon = $this->mime2icon("{$entity->type0}/{$entity->type1}");

				$parts[] = array
					(
					 'id'		=> $ent_id,
					 'mime_major'	=> $part->type0,
					 'mime_minor'	=> $part->type1,
					 'filename'	=> $filename,
					 'icon'		=> $icon
					);

			}
			return $parts;
		}

		/**
		 * Process and send a message
		 *
		 * @access private
		 * @param string $msg_id unique message id
		 */
		function _process_send($data)
		{
			
			$msg_id = $data['msg_id'];

			$ids = $GLOBALS['phpgw']->session->appsession('composing');

			if ( !isset($ids[$msg_id]) )
			{
				trigger_error(array('400' => 'invalid message number, please contact your system administrator'), E_USER_ERROR );
				exit;
			}

			$xmldata = $data['msg_data'];

			if( !strlen($xmldata) )
			{
				Header('HTTP/1.0 400 ' . lang('invalid message content') );
				exit;
			}

			$xml = new DOMDocument;
			$xml->loadXML($xmldata);

			$acct_id = $xml->getElementsByTagName('message_account_id');
			$acct_info = execMethod('communik8r.boaccounts.id2array', $acct_id->item(0)->nodeValue );
			unset($acct_id);

			$from_name = '';
			if($acct_info['display_name'])
			{
				$from_email = "{{$acct_info['display_name']}}<{$acct_info['acct_uri']}>";
				$from_name = $acct_info['display_name'];
			}
			else
			{
				$from_email = $acct_info['acct_uri'];
			}	

			unset($acct_info);

			$tos = $xml->getElementsByTagName('message_to');
			$_to = array();
			foreach ( $tos as $to )
			{
				$rcpt = $this->_address2parts( $to->nodeValue );

				if($rcpt['name'])
				{
					$_to[] = "{$rcpt['name']}<{$rcpt['full']}>";
				}
				else
				{
					$_to[] = $rcpt['full'];
				}
			}

			unset($tos);
			$to = implode(';', $_to);
			unset($_to);

			$ccs = $xml->getElementsByTagName('message_cc');
			$_cc = array();
			foreach ( $ccs as $cc )
			{
				$rcpt = $this->_address2parts( $cc->nodeValue );
				if($rcpt['name'])
				{
					$_cc[] = "{$rcpt['name']}<{$rcpt['full']}>";
				}
				else
				{
					$_cc[] = $rcpt['full'];
				}
			}
			unset($ccs);
			$cc = implode(';', $_cc);
			unset($_cc);

			$bccs = $xml->getElementsByTagName('message_bcc');
			$_bcc = array();
			foreach ( $bccs as $bcc )
			{
				$rcpt = $this->_address2parts( $bcc->nodeValue );
				if($rcpt['name'])
				{
					$_bcc[] = "{$rcpt['name']}<{$rcpt['full']}>";
				}
				else
				{
					$_bcc[] = $rcpt['full'];
				}
			}
			unset($bccs);
			$bcc = implode(';', $_bcc);
			unset($_bcc);

			$_subject = $xml->getElementsByTagName('message_subject');
			$subject = $_subject->item(0)->nodeValue;

			$mime_magic = createObject('phpgwapi.mime_magic');
			$boattach = createObject('communik8r.boattachments');
			$boattach->msg_id = $msg_id;
			$_attachments = $boattach->get_raw_list();
			if ( is_array($_attachments) && count($_attachments) )
			{
				foreach( $_attachments as $_attachment)
				{
					$attachments[] = array
					(
						'file' => $_attachment['phpgw_file'],
						'name' => $_attachment['name'],
			//			'type' => $_attachment['type']
						'type' => $mime_magic->filename2mime($_attachment['name'])
					);
				}
			}
			unset($_attachments);

			$msgbody = $xml->getElementsByTagName('msgbody');
			$body = $msgbody->item(0)->nodeValue;
			unset($msgbody);

			if ( $this->_html_ok() )
			{
				$type = 'html';
//				$smtp->AltBody = $this->_html2plain($body);
			}
			else
			{
				$type = 'text';
				$body = $this->_html2plain($body);
			}

			if (!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}
			$rcpt = $GLOBALS['phpgw']->send->msg('email', $to, $subject, $body, '', $cc, $bcc, $from_email, $from_name, $type, '', $attachments);
			//error_log( "Message {$msg_id}:{$rcpt} Sent ",3, "/tmp/my-errors.log");
			//$boattach->remove_path();

			exit;
		}

		/**
		 * Check to see if HTML Mail is permitted
		 *
		 * @returns bool can html mail be sent
		 */
		function _html_ok()
		{
			return false;
		}

		/**
		 * Quote a string for replying
		 */
		function _quote( $str )
		{
			$str =  preg_replace("/--\s{0,1}\r\n.{1,}\r\n\r\n/smx", '', $str); //strip sig - regex lifted from phpgw-email
			$new_body = array();
			$old_body = explode("\n", $str);
			foreach ( $old_body as $line)
			{
				$new_body[] = '> ' . rtrim($line);
			}
			return implode("\n", $new_body);

		}
	}
