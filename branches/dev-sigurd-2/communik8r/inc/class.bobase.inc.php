<?php
/**
 * Communik8r base logic class - all the common stuff
 *
 * @abstract
 * @author Dave Hall skwashd@phpgroupware.org
 * @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
 * @package communik8r
 * @subpackage base
 * @version $Id: class.bobase.inc.php,v 1.1.1.1 2005/08/23 05:03:58 skwashd Exp $
 */

	class bobase
	{
		/**
		 * @var object $prefs preferences object
		 */
		var $prefs;

		/**
		 * @constructor
		 */
		function bobase()
		{
			$this->prefs = createObject('phpgwapi.preferences');
		}

		/**
		 * Get a list of buttons as xml
		 */
		function buttons($uri_parts)
		{
			$buttons = array();
			$buttons['new']		= array
				(
				 'id'		=> 'new',
				 'shortcut'	=> 'N',
				 'label'		=> ucwords(lang('new')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'new-24x24')
				);

			$buttons[]		= array
				(
				 'shortcut'	=> '--',
				 'label'		=> '--'
				);

			$buttons['refresh']	= array
				(
				 'id'		=> 'refresh',
				 'shortcut'	=> 'H',
				 'label'		=> ucwords(lang('refresh')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'refresh-24x24')
				);

			$buttons[]		= array
				(
				 'shortcut'	=> '--',
				 'label'		=> '--'
				);

			$buttons['reply']	= array
				(
				 'id'		=> 'reply',
				 'shortcut'	=> 'R',
				 'label'		=> ucwords(lang('reply')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'reply-24x24')
				);

			$buttons['replyall']	= array
				(
				 'id'		=> 'reply_to_all',
				 'shortcut'	=> 'A',
				 'label'		=> ucwords(lang('reply to all')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'reply-to-all-24x24')
				);

			$buttons['forward']	= array
				(
				 'id'		=> 'forward',
				 'shortcut'	=> 'F',
				 'label'		=> ucwords(lang('forward')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'forward-24x24')
				);
			$buttons[]		= array
				(
				 'shortcut'	=> '--',
				 'label'		=> '--'
				);

			$buttons['copy']	= array
				(
				 'id'		=> 'copy',
				 'shortcut'	=> 'C',
				 'label'		=> ucwords(lang('copy')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'copy-24x24')
				);

			$buttons['move']	= array
				(
				 'id'		=> 'move',
				 'shortcut'	=> 'M',
				 'label'		=> ucwords(lang('move')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'move-24x24')
				);

			$buttons[]		= array
				(
				 'shortcut'	=> '--',
				 'label'		=> '--'
				);

			$buttons['print']	= array
				(
				 'id'		=> 'print',
				 'shortcut'	=> 'P',
				 'label'		=> ucwords(lang('print')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'print-24x24')
				);

			$buttons['delete']	= array
				(
				 'id'		=> 'delete',
				 'shortcut'	=> 'D',
				 'label'		=> ucwords(lang('delete')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'delete-24x24')
				);

			/*
			   $buttons['spam']	= array
			   (
			   'id'		=> 'spam',
			   'shortcut'	=> 'S',
			   'label'		=> ucwords(lang('spam')),
			   'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'spam-24x24')
			   );

			   $buttons['not_spam']	= array
			   (
			   'id'		=> 'not_spam',
			   'shortcut'	=> 'S',
			   'label'		=> ucwords(lang('not spam')),
			   'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'not-spam-24x24')
			   );
			 */

			/* Stop confusion for now
			   $buttons['cancel']	= array
			   (
			   'id'		=> 'cancel',
			   'shortcut'	=> '',
			   'label'		=> ucwords(lang('cancel')),
			   'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'stop-24x24')
			   );
			 */


			Header('Content-Type: text/xml');
			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;

			$xsl = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . "{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/templates/base/buttons.xsl" . '"');
			$xml->appendChild($xsl);

			$phpgw = $xml->createElement('phpgw:response', 'phpgw');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgw', 'http://dtds.phpgroupware.org/phpgw.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgwapi', 'http://dtds.phpgroupware.org/phpgwapi.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:communik8r', 'http://dtds.phpgroupware.org/communik8r.dtd');


			$elm = $xml->createElement('communik8r:response');

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
			$phpgw->appendChild($elm);

			$xml->appendChild($phpgw);

			echo $xml->saveXML();;
			//echo '<pre>' . htmlentities($xml->dump_mem(true)) . '</pre>';
		}

		/**
		 * Get button definitions for compose window
		 *
		 * @param string $msg_type the type of message being composed (required but, currently ignored)
		 * @return array buttons
		 */
		function get_compose_buttons($msg_type)
		{
			$buttons = array();
			$buttons['send']	= array
				(
				 'id'		=> 'send',
				 'shortcut'	=> 'S',
				 'label'		=> ucwords(lang('send')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'send-24x24.png')
				);

			$buttons['save_draft']	= array
				(
				 'id'		=> 'save_draft',
				 'shortcut'	=> 'D',
				 'label'		=> ucwords(lang('save draft')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'save-24x24')
				);

			$buttons['attach']	= array
				(
				 'id'		=> 'attach',
				 'shortcut'	=> 'A',
				 'label'		=> ucwords(lang('attach')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'attach-24x24')
				);

			$buttons[]		= array
				(
				 'shortcut'	=> '--',
				 'label'		=> '--'
				);

			$buttons['undo']	= array
				(
				 'id'		=> 'undo',
				 'shortcut'	=> 'U',
				 'label'		=> ucwords(lang('undo')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'undo-24x24')
				);

			$buttons['redo']	= array
				(
				 'id'		=> 'redo',
				 'shortcut'	=> 'R',
				 'label'		=> ucwords(lang('redo')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'redo-24x24')
				);

			$buttons[]		= array
				(
				 'shortcut'	=> '--',
				 'label'		=> '--'
				);

			$buttons['cut']		= array
				(
				 'id'		=> 'cut',
				 'shortcut'	=> 'X',
				 'label'		=> ucwords(lang('cut')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'cut-24x24')
				);

			$buttons['copy']	= array
				(
				 'id'		=> 'copy',
				 'shortcut'	=> 'C',
				 'label'		=> ucwords(lang('copy')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'refresh-24x24')
				);


			$buttons['paste']	= array
				(
				 'id'		=> 'paste',
				 'shortcut'	=> 'P',
				 'label'		=> ucwords(lang('paste')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'paste-24x24')
				);

			$buttons[]		= array
				(
				 'shortcut'	=> '--',
				 'label'		=> '--'
				);

			$buttons['find']	= array
				(
				 'id'		=> 'find',
				 'shortcut'	=> 'F',
				 'label'		=> ucwords(lang('find')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'search-24x24')
				);

			$buttons['replace']	= array
				(
				 'id'		=> 'replace',
				 'shortcut'	=> 'R',
				 'label'		=> ucwords(lang('replace')),
				 'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'search-and-replace-24x24')
				);

			/* Not sure if this will be included or not - skwashd
			   $buttons[]		= array
			   (
			   'shortcut'	=> '--',
			   'label'		=> '--'
			   );

			   $buttons['cancel']	= array
			   (
			   'id'		=> 'cancel',
			   'shortcut'	=> '',
			   'label'		=> ucwords(lang('cancel')),
			   'icon'		=> $GLOBALS['phpgw']->common->image('communik8r', 'stop-24x24')
			   );
			 */
			return $buttons;

		}

		/**
		 * Compose a new messages
		 * @abstract
		 */
		function compose($uri_parts)
		{}

		/**
		 * Recursively create a path
		 *
		 * @internal taken from http://php.net/mkdir 
		 * @author Aleks Peshkov - no contact details available
		 * @param string $path the path to create
		 * @returns bool was the path created?
		 */
		function make_path($path)
		{
			//error_log("bobase::make_path({$path});");
			return is_dir($path) || ( $this->make_path(dirname($path)) && mkdir($path) );
		}

		function menu($uri_parts)
		{
			if ( count($uri_parts) == 2)
			{
				Header('Content-Type: text/xml');
				$xml = new DOMDocument('1.0', 'utf-8');
				$xml->formatOutput = true;

				$menu = $xml->createElement('menu');
				//$menu->setAttribute('name', 'Communik8r App Menu');

				//File
				$menu_group = $xml->createElement('MenuItem');
				$menu_group->setAttribute('id', 'menu_file');
				$menu_group->setAttribute('name', lang('file'));

				$menu_item = $xml->createElement('MenuItem');
				$menu_item->setAttribute('id', 'menu_new');
				$menu_item->setAttribute('name', lang('new'));
				$menu_item->setAttribute('src', 'new-16x16.png');
				$menu_group->appendChild($menu_item);

				$menu_item = $xml->createElement('MenuItem');
				$menu_item->setAttribute('id', 'menu_print');
				$menu_item->setAttribute('name', lang('print'));
				$menu_item->setAttribute('src', 'print-16x16.png');
				$menu_group->appendChild($menu_item);

				$menu_item = $xml->createElement('MenuItem');
				$menu_item->setAttribute('id', 'menu_exit');
				$menu_item->setAttribute('name', lang('exit'));
				$menu_item->setAttribute('src', 'exit-16x16.png');
				$menu_group->appendChild($menu_item);

				$menu->appendChild($menu_group);

				//Edit
				$menu_group = $xml->createElement('MenuItem');
				$menu_group->setAttribute('id', 'menu_edit');
				$menu_group->setAttribute('name', lang('edit'));

				$menu_item = $xml->createElement('MenuItem');
				$menu_item->setAttribute('id', 'menu_delete');
				$menu_item->setAttribute('name', lang('delete'));
				$menu_item->setAttribute('src', 'delete-16x16.png');
				$menu_group->appendChild($menu_item);

				$menu_item = $xml->createElement('MenuItem');
				$menu_item->setAttribute('id', 'menu_undelete');
				$menu_item->setAttribute('name', lang('undelete'));
				$menu_item->setAttribute('src', 'undelete-16x16.png');
				$menu_group->appendChild($menu_item);

				$menu_item = $xml->createElement('MenuItem');
				$menu_item->setAttribute('id', 'menu_mark_as_read');
				$menu_item->setAttribute('name', lang('mark as read'));
				$menu_item->setAttribute('src', 'mail-open-16x16.png');
				$menu_group->appendChild($menu_item);

				$menu_item = $xml->createElement('MenuItem');
				$menu_item->setAttribute('id', 'menu_mark_as_unread');
				$menu_item->setAttribute('name', lang('mark as unread'));
				$menu_item->setAttribute('src', 'mail-16x16.png');
				$menu_group->appendChild($menu_item);

				$menu_item = $xml->createElement('MenuItem');
				$menu_item->setAttribute('id', 'menu_prefs');
				$menu_item->setAttribute('name', lang('preferences'));
				$menu_item->setAttribute('src', 'preferences-16x16.png');
				$menu_group->appendChild($menu_item);

				$menu->appendChild($menu_group);

				//Help
				$menu_group = $xml->createElement('MenuItem');
				$menu_group->setAttribute('id', 'menu_help');
				$menu_group->setAttribute('name', lang('help'));

				$menu_item = $xml->createElement('MenuItem');
				$menu_item->setAttribute('id', 'menu_help');
				$menu_item->setAttribute('name', lang('contents'));
				$menu_item->setAttribute('src', 'help-16x16.png');
				$menu_group->appendChild($menu_item);
				
				$menu_item = $xml->createElement('MenuItem');
				$menu_item->setAttribute('id', 'menu_about');
				$menu_item->setAttribute('name', lang('about'));
				$menu_item->setAttribute('src', 'about-16x16.png');
				$menu_group->appendChild($menu_item);

				$menu->appendChild($menu_group);

				$xml->appendChild($menu);
				echo $xml->saveXML();;
			}
		}

		function mime2icon($mimetype)
		{
			$mimetype = str_replace('/', '-', $mimetype);
			$icon = $GLOBALS['phpgw_info']['server']['webserver_url']
				. '/communik8r/templates/default/images/mimetypes/';
			if ( is_file(PHPGW_SERVER_ROOT . "/communik8r/templates/default/images/mimetypes/mime-{$mimetype}.png") )
			{
				$icon .= "mime-{$mimetype}.png";
			}
			else
			{
				$icon .= 'unknown.png';
			}
			return $icon;
		}

		/**
		* Provide the xml for rendering the preferences window
		*/
		function settings()
		{
			$lang_strs = array
					(
						'settings_title'	=> lang('settings'),
						'add'			=> lang('add'),
						'edit'			=> lang('edit'),
						'remove'		=> lang('remove'),
						'default'		=> lang('default'),
						'enable'		=> lang('enable'),
						'disbale'		=> lang('disable'),
						'accounts'		=> lang('accounts'),
						'settings'		=> lang('settings'),
						'enabled'		=> lang('enabled'),
						'account_name'		=> lang('account name'),
						'type'			=> lang('type'),
						'help'			=> lang('help'),
						'close'			=> lang('close')
					);

			Header('Content-Type: text/xml');

			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;

			$xsl = $xml->createProcessingInstruction('xml-stylesheet', "type=\"text/xsl\" href=\"{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/templates/base/settings.xsl\"");
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

			$c8 = $xml->createElement('communik8r:response');
			$accounts = $xml->createElement('communik8r:accounts');

			$accts = ExecMethod('communik8r.boaccounts.get_list');
			foreach ( $accts as $acct )
			{
				
				$account = $xml->createElement('communik8r:account');
				$account->setAttribute('id', $acct['acct_id']);
				$account->setAttribute('title', $acct['acct_name']);
				$account->setAttribute('handler', lang($acct['type_name']));
				$account->setAttribute('enabled', 1);
				$accounts->appendChild($account);
			}
			$c8->appendChild($accounts);
			$phpgw->appendChild($c8);

			$xml->appendChild($phpgw);

			echo $xml->saveXML();;
		}

		/**
		 * Start Communik8r Running
		 */
		function start()
		{
			$stylesheet = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/templates/base/layout.xsl";
			Header('Content-Type: text/xml');
			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;
	//		$xml->validateOnParse = true;

			$xsl = $xml->createProcessingInstruction('xml-stylesheet', "type='text/xsl' href = '{$stylesheet}'");
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

			$skin = $xml->createElement('phpgwapi:skin');
			$skin->appendChild( $xml->createTextNode('base') );
			$info->appendChild($skin);
			unset($skin);

			$phpgw->appendChild($info);

			$c8 = $xml->createElement('communik8r:info');

			$prefs = $this->_get_pref('');
			if ( is_array($prefs) && count($prefs) )
			{
				foreach ( $prefs as $pname => $pvalue )
				{
					if ( is_array($pvalue) )
					{
						foreach ( $pvalue as $pid => $pval )
						{
							$c8_pref = $xml->createElement("communik8r:{$pname}");
							$c8_pref->setAttribute('id', $pid);
							$c8_pref->appendChild($xml->createTextNode($pval));
							$c8->appendChild($c8_pref);
						}
					}
					else
					{
						$c8_pref = $xml->createElement("communik8r:{$pname}");
						$c8_pref->appendChild($xml->createTextNode($pvalue));
						$c8->appendChild($c8_pref);
					}
				}
			}
			$phpgw->appendChild($c8);

			$xml->appendChild($phpgw);
			echo $xml->saveXML();
		}

		/**
		 * Output xsl template to user
		 * @internal TODO make n-tier ???
		 */
		function xsl($uri_parts)
		{
			if ( (count($uri_parts) != 3) || in_array( array('.', '..'), $uri_parts)  
					|| eregi('[a-z0-9\-_]+', $uri_parts[2]) != 1 )
			{
				trigger_error("Invalid XSL style sheet request - {$uri_parts[2]} ", E_USER_ERROR);
			}

			$user_file = PHPGW_SERVER_ROOT . SEP 
				. 'communik8r' . SEP 
				. 'templates' . SEP
				. $GLOBALS['phpgw_info']['user']['common']['preferences']['template_set'] . SEP
				. $uri_parts[2] . '.xsl';

			$default_file = PHPGW_SERVER_ROOT . SEP 
				. 'communik8r' . SEP 
				. 'templates' . SEP
				. 'default' . SEP
				. $uri_parts[2] . '.xsl';

			if ( is_file($user_file) )
			{
				header('Content-type: text/xml');
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($user_file) ) . ' GMT');
				echo file_get_contents($user_file);
				exit;
			}
			else if( is_file($default_file) )
			{
				header('Content-type: text/xml');
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($default_file) ) . ' GMT');
				echo file_get_contents($default_file);
				exit;
			}
			else
			{
				trigger_error("Invalid XSL style sheet request - {$uri_parts[2]} ", E_USER_ERROR);
			}
		}

		/**
		 * Convert an epoch date to a human friendly date
		 *
		 * @access private
		 * @param int $epoch date in seconds from Jan 1970 (the epoch)
		 * @returns string human friendly date
		 */
		function _date2human($epoch)
		{
			$date_fmt = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$time_fmt = ( $GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == 12
					? 'g:i a'
					: 'H:i' );

			if ( $epoch > mktime(0, 0, 0) && $epoch < strtotime('tomorrow') ) //some time today
			{
				return lang('today %1', date($time_fmt, $epoch) );
			}

			if ( (time() - $epoch) < 604800 ) //some time in the last week
			{
				return date('D ' . $time_fmt, $epoch);
			}

			return date($date_fmt, $epoch);
		}

		/**
		 * Get a preference (or array of preferences) for user
		 *
		 * @param string $pname preference name - empty sting == all
		 * @returns mixed preference/s
		 */
		function _get_pref($pname = ' ')//the space is intentional
		{
			$prefs = $this->prefs->read();
			if ( $pname === '' )
			{
				return $prefs['communik8r'];
			}
			else
			{
				if ( isset($prefs['communik8r'][$pname]) )
				{
					return $prefs['communik8r'][$pname];
				}
			}
			return ''; //better than nothing
		}

		/**
		 * Convert an integer size to human readable measurement - <1K, xK, xM etc
		 *
		 * @access private
		 * @param int $raw_size raw size value
		 * @returns string human readable form of measurement
		 */
		function _int2human($raw_size)
		{
			if ( $raw_size < 1024 ) // less than 1K
			{
				return '< 1K';
			}

			if ( $raw_size < 1048576 ) //less than a 1M
			{
				return intval($raw_size / 1024) . 'K';
			}

			return round( ($raw_size / 1048576), 1) . 'M'; //measure everything else in M
		}

		/**
		* Determine if a mime type can be shown inline
		*/
		function _is_inline($mimetype)
		{
			$inline_types = array
					(
						'message/rfc822'	=> True,
						'text/plain'		=> True,
					);

			return (isset($inline_types[$mimetype]) ? $inline_types[$mimetype] : false);
		}

		/**
		 * Store a preference for a user
		 *
		 * @param string $pname preference name
		 * @param mixed $pvalue preference value
		 */
		function _store_pref($pname, $pvalue)
		{	
			$this->prefs->read_repository();
			$this->prefs->add('communik8r', $pname, $pvalue);
			$this->prefs->save_repository();
			//error_log(print_r($this->prefs->data['ccomunik8r'], true));
		}
	}
?>
