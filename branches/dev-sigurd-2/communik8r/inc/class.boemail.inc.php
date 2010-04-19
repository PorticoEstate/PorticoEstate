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
	include_once(PHPGW_INCLUDE_ROOT . SEP . 'communik8r' . SEP . 'inc' . SEP . 'class.bobase.inc.php');

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
		function rest($uri_parts)
		{
			switch ( strtoupper($_SERVER['REQUEST_METHOD']) )
			{
				case 'GET':
					switch ( count($uri_parts) )
					{
						case 3: //requesting mailboxes for account
							$this->get_mailboxes($uri_parts[2], True);
							break;

						case 4: //requesting mailbox summary
							$this->get_summary($uri_parts);
							break;

						case 5: //requesting message
							$this->get_msg($uri_parts);
							break;

						case 6: //requesting attachment
							$this->get_part($uri_parts);
							break;

						default:
							die('<error>invalid request</error>');
							//invalid request
					}
					break;

				case 'PUT':
					error_log('PUT: ' . print_r($uri_parts, true));
					switch ( count($uri_parts) )
					{
						case 5:
							$this->update_mailbox_status($uri_parts);
							break;
						default:
							$this->compose($uri_parts);//no validation for now :P
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

		function compose($uri_parts)
		{
			$reply = $forward = false;
			if (  strtoupper($_SERVER['REQUEST_METHOD']) == 'PUT' )
			{
				if ( $uri_parts[2] == 'draft' ) 
				{
					//not yet supported
				}
				else //send
				{
					$this->_process_send($uri_parts[3]);//3 is the unique message id
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

			if ( $uri_parts[1] != 'new' )
			{
				$msg_parts = explode('_', $uri_parts[3] ); //0 type, 1 acctid, 2 folder, 3 msgid
				$acct_info = execMethod('communik8r.boaccounts.id2array', $msg_parts[1]);
				$socache = createObject('communik8r.socache_email', $acct_info);
				$msg = $socache->get_msg($msg_parts[3]);

				$reply = ( $uri_parts[1] == 'reply' || $uri_parts[1] == 'reply');
				$forward = ($uri_parts[1] == 'forward');
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

			$xml = domxml_new_doc('1.0');

			$xsl = $xml->create_processing_instruction('xml-stylesheet', 'type="text/xsl" href="' . "{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/xsl/compose" . '"');
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
			$skin->append_child( $xml->create_text_node('default') );
			$info->append_child($skin);
			unset($skin);

			$langs = $xml->create_element('phpgwapi:langs');
			foreach ( $lang_strs as $lkey => $lval )
			{
				$lang = $xml->create_element('phpgwapi:lang');
				$lang->set_attribute('id', $lkey);
				$lang->append_child($xml->create_text_node($lval) );
				$langs->append_child($lang);
			}
			$info->append_child($langs);

			$phpgw->append_child($info);

			$elm = $xml->create_element('communik8r:response');

			$comm_info = $xml->create_element('communik8r:info');

			$btns = $xml->create_element('communik8r:buttons');
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

				$btn = $xml->create_element('communik8r:button');

				foreach($attribs as $akey => $aval)
				{
					$btn->set_attribute($akey, $aval);
				}

				$btns->append_child($btn);
			}
			$elm->append_child($btns);

			$accts = $xml->create_element('communik8r:accounts');

			$accounts = execMethod('communik8r.boaccounts.get_list', 'email');
			$accts = $xml->create_element('communik8r:accounts');
			foreach($accounts as $account)
			{
				$acct = $xml->create_element('communik8r:account');
				$acct->set_attribute('id', $account['acct_id']);
				$acct->set_attribute('sig_id', $account['sig_id']);
				if( $account['acct_id'] == $msg_parts[1] )
				{
					$acct->set_attribute('current', 1);
				}

				$acct_name = $xml->create_element('communik8r:account_name');
				$acct_name->append_child( $xml->create_cdata_section("{$account['display_name']} <{$account['acct_uri']}>") );
				$acct->append_child($acct_name);

				$accts->append_child($acct);
			}
			$elm->append_child($accts);

			$message = $xml->create_element('communik8r:message');
			$message->set_attribute('id', $msg_id );

			$headers = $xml->create_element('communik8r:headers');

			if ( isset($msg->from) )
			{
				$header = $xml->create_element('communik8r:message_to');
				$header->append_child( $xml->create_text_node($msg->from) );
				$headers->append_child($header);
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
			$header = $xml->create_element('communik8r:message_subject');
			$header->append_child( $xml->create_text_node($subject) );
			$headers->append_child($header);
			$message->append_child($headers);

			$body = $xml->create_element('communik8r:body');
			$this->_decode_body($msg->body, $msg->entities[0]);
			$body->append_child( $xml->create_text_node( $body ) );
			$message->append_child($body);

			$elm->append_child($message);
			$phpgw->append_child($elm);

			$xml->append_child($phpgw);

			echo $xml->dump_mem(true);
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
			trigger_error('boemail::get_mailboxes(' . print_r($info, true) . ', ' . intval($output) . ') called');
			if ( is_string($info) )
			{
				$info = execMethod('communik8r.boaccounts.name2array', $info);
			}
			$socache = createObject('communik8r.socache_email', $info);

			$xml = domxml_new_doc('1.0');
			
			$tree = $xml->create_element('tree');
			$tree->set_attribute('id', $info['acct_id']);
			$tree->set_attribute('text', $info['acct_name']);

			$elm = $xml->create_element('crap');

			$mboxs = $socache->get_mailboxes();
			foreach( $mboxs as $mbox => $info)
			{
				//error_log("fetching data for {$mbox} Line: " . __LINE__ . ' in ' . __FILE__);
				$elm = $this->_mbox2xml($xml, $mbox, $info );
			}

			if ( !$output )
			{
				return $elm->clone_node(True);
			}

			$tree->append_child($elm);
			$xml->append_child($tree);

			Header('Content-Type: text/xml');
			echo $xml->dump_mem(true);
		}

		function get_msg($uri_parts)
		{
			$acct_info = execMethod('communik8r.boaccounts.id2array', $uri_parts[2]);
			$socache = createObject('communik8r.socache_email', $acct_info);
			$msg = $socache->get_msg($uri_parts[4]);

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

			$xml = domxml_new_doc('1.0');

			$xsl = $xml->create_processing_instruction('xml-stylesheet', "type=\"text/xsl\" href=\"{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/xsl/message\"");
			$xml->append_child($xsl);

			$phpgw = $xml->create_element_ns('http://dtds.phpgroupware.org/phpgw.dtd', 'response', 'phpgw');
			$phpgw->add_namespace('http://dtds.phpgroupware.org/phpgwapi.dtd', 'phpgwapi');
			$phpgw->add_namespace('http://dtds.phpgroupware.org/communik8r.dtd', 'communik8r');

			$info = $xml->create_element('phpgwapi:info');

			$base_url = $xml->create_element('phpgwapi:base_url');
			$base_url->append_child( $xml->create_text_node($GLOBALS['phpgw_info']['server']['webserver_url']) );
			$info->append_child($base_url);

			foreach ( $langs as $lkey => $lval )
			{
				$lang = $xml->create_element('phpgwapi:lang');
				$lang->set_attribute('id', "phpgwapi_$lkey");
				$lang->append_child($xml->create_text_node($lval) );
				$info->append_child($lang);
			}

			$phpgw->append_child($info);

			$elm = $xml->create_element('communik8r:response');

			$comm_info = $xml->create_element('communik8r:info');

			$message = $xml->create_element('communik8r:message');

			$message->set_attribute('id', "{$acct_info['acct_id']}_{$uri_parts[3]}_{$uri_parts[4]}");

			$headers = $xml->create_element('communik8r:headers');

			if ( isset($msg->from) )
			{
				$from_str = '';
				$from = $this->_address2parts($msg->from);
				if ( $from['name'] != '' )
				{
					$from_str = $from['name'] . ' ';
				}
				$from_str .= "<{$from['full']}>";

				$header = $xml->create_element('communik8r:message_from');
				$header->append_child( $xml->create_text_node($from_str) );
				$headers->append_child($header);
				unset($from);
			}

			if ( isset($msg->reply_to) && $msg->reply_to )
			{
				$reply_tos = split(',', $msg->reply_to);
				$header = $xml->create_element('communik8r:message_reply_to');
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

					$header->append_child( $xml->create_text_node($r2_str) );
					unset($r2_str);
					++$i;
				}
				$headers->append_child($header);
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
			$header = $xml->create_element('communik8r:message_to');
			foreach ( $tos as $hdr )
			{
				if( $i == 0 && $hdr['name'] == lang('undisclosed recipients'))
				{
					$header = $xml->create_element('communik8r:message_to');
					$header->append_child( $xml->create_text_node($hdr[3]) );
					$headers->append_child($header);
					$header->append_child( $xml->create_text_node($hdr) );
					$headers->append_child($header);
					break;
				}

				$to_str = ($i ? ', ' : '');
				if ( $hdr['name'] != '' )
				{
					$to_str = $hdr['name'] . ' ';
				}
				$to_str .= "<{$hdr['full']}>";

				$header->append_child( $xml->create_text_node($to_str) );
				unset($to_str);
				++$i;
			}
			$headers->append_child($header);


			if ( isset($msg->cc) && $msg->cc )
			{
				$ccs = split(',', $msg->cc);
				$header = $xml->create_element('communik8r:message_cc');
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

					$header->append_child( $xml->create_text_node($cc_str) );
					unset($cc_str);
					++$i;
				}
				$headers->append_child($header);
			}

			$header = $xml->create_element('communik8r:message_subject');
			$header->append_child( $xml->create_text_node($msg->subject ) );
			$headers->append_child($header);

			$date_fmt = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] . ' '
				. ( $GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == 12
						? 'g:i a'
						: 'H:i' );

			$header = $xml->create_element('communik8r:message_date');
			$header->append_child( $xml->create_text_node( date($date_fmt, $msg->timestamp) ) );
			$headers->append_child($header);

			$message->append_child($headers);

			$body = $xml->create_element('communik8r:body');
			$this->_decode_body($msg->body, $msg->body_meta);
			$body->append_child( $xml->create_cdata_section( $msg->body . "\n\n" ) );

			$parts = $xml->create_element('communik8r:parts');
			if ( isset($msg->parts) && count($msg->parts) != 1 ) // we don't need it if it is only the body
			{
				//$body->append_child( $xml->create_cdata_section( print_r($msg->parts, True) ) );
				foreach ( $msg->parts as $part_no => $part )
				{
					if ( $part_no == 1 )
					{
						continue; //skip the body
					}
					$prt = $xml->create_element('communik8r:part');
					$prt->set_attribute('id', $part_no);
					$prt->set_attribute('mimetype', $part['typestring']);
					$prt->set_attribute('inline', ($this->_is_inline($part['typestring']) ? 'true' : 'false') );
					$prt->set_attribute('size', $this->_int2human($part['size']) );
					$prt->append_child( $xml->create_cdata_section($part['name']) );
					$prt->set_attribute('icon', $this->mime2icon($part['typestring']) );
					$parts->append_child($prt);
				}
			}
			$message->append_child($parts);

			$message->append_child($body);

			$elm->append_child($message);
			$phpgw->append_child($elm);

			$xml->append_child($phpgw);

			echo $xml->dump_mem(true);

			$msg_pref = $this->_get_pref('current_message');
			//$uri_parts[3] = str_replace('.', '___', $uri_parts[3]);
			$msg_pref["email_{$uri_parts[2]}_{$uri_parts[3]}"] = $uri_parts[4];
			$this->_store_pref('current_message', $msg_pref);
		}

		function get_part($uri_parts)
		{
			$acct_info = execMethod('communik8r.boaccounts.id2array', $uri_parts[2]);
			$socache = createObject('communik8r.socache_email', $acct_info);

			$part = $socache->get_msg($uri_parts[4], False, $uri_parts[5]);
			$info =& $part['structure']->structure;//convience
			//echo '<pre>' . print_r($info, True) . '</pre>';

			$attach_name = '';
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


			if ( strtolower($info->header->encoding) == 'base64' )
			{
				$part['content'] = base64_decode($part['content']);
			}
			else if ( strtolower($info->header->encoding) == 'quoted-printable' )
			{
				$part['content'] = quoted_printable_decode($part['content']);
			}

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
		function get_summary($uri_parts)
		{
			trigger_error('boemail::get_summary(' . print_r($uri_parts, true) . ') called');
			Header('Content-Type: text/xml');
			$acct_info = execMethod('communik8r.boaccounts.id2array', $uri_parts[2]);
			$socache = createObject('communik8r.socache_email', $acct_info);
			$msgs = $socache->get_msg_list($uri_parts[3]);

			$xml = domxml_new_doc('1.0');

			$xsl = $xml->create_processing_instruction('xml-stylesheet', 'type="text/xsl" href="' 
					. "{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/xsl/summary" . '"');
			$xml->append_child($xsl);

			$phpgw = $xml->create_element_ns('http://dtds.phpgroupware.org/phpgw.dtd', 'response', 'phpgw');
			$phpgw->add_namespace('http://dtds.phpgroupware.org/phpgwapi.dtd', 'phpgwapi');
			$phpgw->add_namespace('http://dtds.phpgroupware.org/communik8r.dtd', 'communik8r');

			$info = $xml->create_element('phpgwapi:info');
			$base_url = $xml->create_element('phpgwapi:base_url');
			$base_url->append_child( $xml->create_text_node($GLOBALS['phpgw_info']['server']['webserver_url']) );
			$info->append_child($base_url);
			$phpgw->append_child($info);

			$elm = $xml->create_element('communik8r:response');

			$comm_info = $xml->create_element('communik8r:info');
			$msg_sums = $xml->create_element('communik8r:msg_sums');

			$acct = $xml->create_element('communik8r:account');
			$acct->set_attribute('id', $acct_info['acct_id']);
			$acct->append_child( $xml->create_text_node($uri_parts[2]) );
			$comm_info->append_child($acct);

			$mailbox = $xml->create_element('communik8r:mailbox');
			$mailbox->append_child( $xml->create_text_node($uri_parts[3]) );
			$comm_info->append_child($mailbox);

			$mailbox = $xml->create_element('communik8r:hide_deleted');
			$mailbox->append_child( $xml->create_text_node('true') );
			$comm_info->append_child($mailbox);

			$elm->append_child($comm_info);

			foreach ( $msgs as $id => $msg)
			{
				$msg_sum = $xml->create_element('communik8r:msg_sum');
				$msg_sum->set_attribute('id', "email_{$acct_info['acct_id']}_{$msg['mbox_name']}_{$id}");

				$msg_flags = $xml->create_element('communik8r:msg_flags');
				$msg_flags->set_attribute('flag_seen', ($msg['flag_seen'] ? 'true' : 'false') );
				$msg_flags->set_attribute('flag_answered', ($msg['flag_answered'] ? 'true' : 'false') );
				$msg_flags->set_attribute('flag_deleted', ($msg['flag_deleted'] ? 'true' : 'false') );
				$msg_flags->set_attribute('flag_flagged', ($msg['flag_flagged'] ? 'true' : 'false') );
				$msg_flags->set_attribute('flag_draft', ($msg['flag_draft'] ? 'true' : 'false') );
				$msg_flags->set_attribute('attachments', ($msg['attachments'] ? 'true' : 'false') );
				$msg_sum->append_child($msg_flags);

				$msg_sender = $xml->create_element('communik8r:msg_sender');
				$msg_sender->append_child( $xml->create_text_node( $msg['sender']) );
				$msg_sum->append_child($msg_sender);

				$subject = $xml->create_element('communik8r:msg_subject');
				$subject->append_child( $xml->create_text_node($msg['subject']) );
				$msg_sum->append_child($subject);

				$msg_size = $xml->create_element('communik8r:msg_size');
				$msg_size->set_attribute('intval', $msg['msg_size']);
				$msg_size->append_child( $xml->create_text_node( $this->_int2human($msg['msg_size']) ) );
				$msg_sum->append_child($msg_size);

				$msg_date = $xml->create_element('communik8r:msg_date');
				$msg_date->set_attribute('intval', $msg['date_sent']);
				$msg_date->append_child( $xml->create_text_node( $this->_date2human($msg['date_sent']) ) );
				$msg_sum->append_child($msg_date);

				$msg_sums->append_child($msg_sum);
			}
			$elm->append_child($msg_sums);
			$phpgw->append_child($elm);

			$xml->append_child($phpgw);

			echo $xml->dump_mem(true);
			$this->_store_pref('current_selection', "email_{$uri_parts[2]}_{$uri_parts[3]}");
			//echo '<pre>' . print_r(domxml_xmltree($xml->dump_mem(true)), True) . '</pre>';
		}

		/**
		* Used for updating the status of a mailbox
		*/
		function update_mailbox_status($uri_parts)
		{
			$xmldata = '';
			$putdata = fopen('php://input', 'r');//the TFphpM is fsckd stdin doesn't work!!

			while ( $data = fread($putdata, 1024) )
			{
				$xmldata .= "{$data}\n";
			}
			fclose($putdata);
			
			if ( !$doc = domxml_open_mem($xmldata) )
			{
				trigger_error('Invalid XML: ' . print_r($xmldata), E_USER_ERROR);
			}
			unset($xmldata);

			$acct_info = execMethod('communik8r.boaccounts.id2array', $uri_parts[2]);
			$socache = createObject('communik8r.socache_email', $acct_info);

			$elms = $doc->get_elements_by_tagname('status');
			
			foreach ( $elms as $elm )
			{
				switch ( trim( $elm->get_attribute('id') ) )
				{
					case 'open' :
						$socache->set_open($uri_parts[3], $elm->get_content() );
						break;
					default:
						trigger_error("Invalid status request: Mailbox: {$uri_parts[4]} ID: " . $elm->get_attribute('id')  
						. ' Value: ' . $elm->get_content(), E_USER_ERROR );
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
			for($i = 0; $i > count($headers[2]); $i +=2 )
			{
				if ( strtolower($headers[2][$i]) == 'charset' )
				{
					$charset = $headers[2][$i+1];
					break;
				}
			}
			if ( $charset != 'us-ascii' || $charset != 'utf-8')
			{
				$body = mb_convert_encoding($body, 'utf-8', $charset);
			}
			$body = wordwrap($body, 80); //make it look nice
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
			$elm = $xml->create_element('item');

			$elm->set_attribute('id', "{$info['acct_handler']}_{$info['acct_id']}_{$parent}{$mbox}");
			$elm->set_attribute('text', $mbox);
			$elm->set_attribute('open', intval($info['open'] || strtolower($mbox) == 'inbox') );
			
			if ( "{$info['acct_handler']}_{$info['acct_id']}_{$parent}{$mbox}" == $pref )
			{
				$elm->set_attribute('select', 1);
				$elm->set_attribute('call', 1);
			}

			if ( count($info['children']) )
			{
				foreach( $info['children'] as $smbox => $sinfo )
				{
					$elm->append_child($this->_mbox2xml($xml, $smbox, $sinfo, $parent . $mbox));
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
		function _process_send($msg_id)
		{
			$ids = $GLOBALS['phpgw']->session->appsession('composing');

			if ( !isset($ids[$msg_id]) )
			{
				trigger_error(array('400' => 'invalid message number, please contact your system administrator'), E_USER_ERROR );
				exit;
			}

			$xmldata = '';
			$putdata = fopen('php://input', 'r');//the TFphpM is fsckd stdin doesn't work!!

			while ( $data = fread($putdata, 1024) )
			{
				$xmldata .= "{$data}\n";
			}

			if( !strlen($xmldata) )
			{
				Header('HTTP/1.0 400 ' . lang('invalid message content') );
				exit;
			}

			$smtp = createObject('communik8r.comm_smtp');

			$xml = domxml_open_mem($xmldata);

			$smtp->Host = $this->smtp_host;
			$smtp->Port = $this->smtp_port;

			if ( $this->smtp_auth )
			{
				$smtp->SMTPAuth = true;
				$smtp->Username = $GLOBALS['phpgw_info']['user']['userid'];
				$smtp->Password = $GLOBALS['phpgw_info']['user']['password'];
			}

			$acct_id = $xml->get_elements_by_tagname('message_account_id');
			$acct_info = execMethod('communik8r.boaccounts.id2array', $acct_id[0]->get_content() );
			unset($acct_id);
			$smtp->Sender = $acct_info['acct_uri'];
			$smtp->From =& $smtp->Sender;
			$smtp->FromName = $acct_info['display_name'];
			unset($acct_info);


			$tos = $xml->get_elements_by_tagname('message_to');
			foreach ( $tos as $to )
			{
				$rcpt = $this->_address2parts( $to->get_content() );
				$smtp->AddAddress($rcpt['full'], $rcpt['name']);
			}
			unset($tos);

			$ccs = $xml->get_elements_by_tagname('message_cc');
			foreach ( $ccs as $cc )
			{
				$rcpt = $this->_address2parts( $cc->get_content() );
				$smtp->AddCC($rcpt['full'], $rcpt['name']);
			}
			unset($ccs);

			$bccs = $xml->get_elements_by_tagname('message_cc');
			foreach ( $bccs as $bcc )
			{
				$rcpt = $this->_address2parts( $bcc->get_content() );
				$smtp->AddBCC($rcpt['full'], $rcpt['name']);
			}
			unset($bccs);

			$subject = $xml->get_elements_by_tagname('message_subject');
			$smtp->Subject = $subject[0]->get_content();
			unset($subject);

			$boattach = createObject('communik8r.boattachments');
			$boattach->msg_id = $msg_id;
			$attachments = $boattach->get_raw_list();
			if ( is_array($attachments) && count($attachments) )
			{
				foreach( $attachments as $attachment)
				{
					$smtp->AddAttachment($attachment['phpgw_file'], $attachment['name'], 'base64', $attachment['type']);
				}
			}
			unset($attachments);

			$msgbody = $xml->get_elements_by_tagname('msgbody');
			$body = $msgbody[0]->get_content();
			unset($msgbody);
			error_log(print_r($smtp, true));
			if ( $this->_html_ok() )
			{
				$smtp->IsHTML(True);
				$smtp->Body = $body;
				$smtp->AltBody = $this->_html2plain($body);
			}
			else
			{
				$body = $this->_html2plain($body);
				$smtp->Body = $body;
			}
			unset($body);

			error_log( "Message {$msg_id}: " .( $smtp->Send() . ' : ' ? 'Sent ' . $smtp->getHeader() . $smtp->getBody() : "FAILED: {$smtp->ErrorInfo}") );
			//$boattach->remove_path();
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
?>
