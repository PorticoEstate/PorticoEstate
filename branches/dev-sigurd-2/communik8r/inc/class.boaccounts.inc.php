<?php
	/**
	* Communik8r accounts logic class
	*
	* @author Dave Hall skwashd@phpgroupware.org
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package communik8r
	* @subpackage accounts
	* @version $Id: class.boaccounts.inc.php,v 1.1.1.1 2005/08/23 05:03:51 skwashd Exp $
	*/

	/**
	* @see bobase
	*/

	phpgw::import_class('communik8r.bobase');	
	/**
	* Communik8r accounts logic class
	*/
	class boaccounts extends bobase
	{
		/**
		* @var object $so storage logic
		*/
		var $so;

		/**
		* @constructor
		*/
		function boaccounts()
		{
			$this->so = createObject('communik8r.soaccounts');
		}
		
		/**
		* REST dispatcher for accounts
		*/
		function rest($data)
		{
			switch ( $_SERVER['REQUEST_METHOD'] )
			{
				case 'GET':
					switch ( $data['action'] )
					{
						case '': //get list of accounts
						case 'new': //get list of accounts
							$this->get_accounts();
							break;
						case 'edit': // edit an account
							$this->edit_account($data);
							break;
					default:
							die('<phpgw:response><phpgw:error>invalid request - received ' . count($uri_parts) . ' elements</phpgw:error></phpgw:response>');
							//invalid request
					}
					break;
				
				case 'PUT':
					
					//FIXME
					if ( count($uri_parts) == 3 )
					{
						$this->_process_edit($uri_parts);
					}
			}
		}

		/**
		* Edit the details for an account
		*/
		function edit_account($uri_parts)
		{
			trigger_error('boaccounts::edit_account(' . print_r($uri_parts, true) . ')');

			$acct = array();
			if ( $uri_parts[2] != 'new' )
			{
				$acct = $this->id2array($uri_parts[2]);
				$type = $acct['handler'];
			}
			else
			{
				switch( $_GET['type'] )
				{
					case 'email':
						$type = 'email';
						break;

					default:
						die('<html><body>' . lang('invalid account') . '<br>'
							. '<a href="javascript:window.close();">' . lang('close window') . '</a>');
				}
			}

			trigger_error( print_r($acct, true) );

			$lang_strs = array
					(
						'accounts_title'	=> ($uri_parts[2] == 'new' ? lang('create account') : lang('edit account %1', $acct['acct_name']) ),
						'identity'		=> lang('identity'),
						'acct_info'		=> lang('account information'),
						'acct_name'		=> lang('account name'),
						'required_info'		=> lang('required information'),
						'full_name'		=> lang('full name'),
						'email_address'		=> lang('email address'),
						'org'			=> lang('organisation'),
						'receiving'		=> lang('receiving options'),
						'server_type'		=> lang('server type'),
						'hostname'		=> lang('hostname'),
						'username'		=> lang('username'),
						'password'		=> lang('password'),
						'port'			=> lang('port'),
						'server_prefix'		=> lang('namespace prefix'),
						'sending'		=> lang('sending'),
						'help'			=> lang('help'),
						'confirm_cancel_msg'	=> lang('are you sure you want to exit editting this account? all changes will be lost'),
						'cancel'		=> lang('cancel'),
						'ok'			=> lang('ok')
					);

			Header('Content-Type: text/xml');

			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;

			$xsl = $xml->createProcessingInstruction('xml-stylesheet', "type=\"text/xsl\" href=\"{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/templates/base/accounts_{$type}.xsl\"");

			$xml->appendChild($xsl);

			$phpgw = $xml->createElement('phpgw:response', 'phpgw');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgw', 'http://dtds.phpgroupware.org/phpgw.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:phpgwapi', 'http://dtds.phpgroupware.org/phpgwapi.dtd');
			$phpgw->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:communik8r', 'http://dtds.phpgroupware.org/communik8r.dtd');

			$info = $xml->createElement('phpgwapi:info');

			$base_url = $xml->createElement('phpgwapi:base_url');
			$base_url->appendChild( $xml->createTextNode("{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r") );
			$info->appendChild($base_url);
			unset($base_url);

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
			
			$account = $xml->createElement('communik8r:account');

			$account->setAttribute('id', $acct['acct_id']);
			foreach ( $acct as $key => $val )
			{
				if ( $key == 'password' || $key == 'acct_id' )
				{
					continue;
				}

				if ( $key == 'acct_options' )
				{
					foreach ( $val as $skey => $sval )
					{
						if ( strpos($skey, 'passw') )
						{
							continue;
						}

						$elm = $xml->createElement("communik8r:{$skey}");
						$elm->appendChild($xml->createTextNode($sval));
						$account->appendChild($elm);
					}
					continue;
				}

				$elm = $xml->createElement("communik8r:{$key}");
				$elm->appendChild($xml->createTextNode($val));
				$account->appendChild($elm);
			}

			$c8->appendChild($account);

			$phpgw->appendChild($c8);

			$xml->appendChild($phpgw);

			echo $xml->saveXML();
		}

		/**
		* Get a list of accounts for the current user as a xml document
		*
		* @param bool $inc_mailboxes include mailbox listings
		*/
		function get_accounts($inc_mailboxes = True)
		{
			$accounts = $this->get_list();
			trigger_error('boaccounts::get_accounts() ' . print_r($accounts, true));

			Header('Content-Type: text/xml');
			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->formatOutput = true;
			
			/*
			$xsl = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' . "{$GLOBALS['phpgw_info']['server']['webserver_url']}/communik8r/templates/base/accounts.xsl" . '"');
			$xml->appendChild($xsl);
			*/

			$tree = $xml->createElement('tree');
			$tree->setAttribute('id', 0);

			foreach($accounts as $account)
			{
				$acct_icon = $account['handler'] . '-16x16.png'; //$GLOBALS['phpgw']->common->image('communik8r', $account['handler'] . '-16x16');
				
				$acct = $xml->createElement('item');
				$acct->setAttribute('id', $account['acct_id']);
				$acct->setAttribute('text', $account['acct_name']);
				$acct->setAttribute('im0', $acct_icon);
				$acct->setAttribute('im1', $acct_icon);
				$acct->setAttribute('im2', $acct_icon);
				$acct->setAttribute('open', 1);

				if ( $inc_mailboxes && $account['handler'] == 'email' )
				{
				//	$acct->appendChild(ExecMethod('communik8r.boemail.get_mailboxes', $account));	
				//	$acct->appendChild($node);
//----------------
					if ( is_string($account) )
					{
				//		$account = execMethod('communik8r.boaccounts.name2array', $account);
						$account = $this->name2array($account);
					}
					$socache = createObject('communik8r.socache_email', $account);

					$elm = $xml->createElement('dummy');

					$mboxs = $socache->get_mailboxes();
					if($mboxs)
					{
						$boemail = createObject('communik8r.boemail');

						foreach( $mboxs as $mbox => $info)
						{
							$elm = $boemail->_mbox2xml($xml, $mbox, $info );
							$acct->appendChild($elm);
						}
					}
					else
					{
						$acct->appendChild($elm);					
					}
//-------------------
				}
				$tree->appendChild($acct);
			}
			$xml->appendChild($tree);

			echo $xml->saveXML();
		}

		/**
		* Get the list of accounts for the current user as a php array
		*
		* @param string $acct_handler the handler for the type of accout being sought
		* @returns array list of accounts
		*/
		function get_list($acct_handler = null)
		{
			return $this->so->get_list($acct_handler);
		}

		/**
		* You work it out
		*/
		function name2array($name)
		{
			return $this->so->get_account(array('name' => $name) );
		}

		/**
		* You work it out :P
		*/
		function id2array($id)
		{
			return $this->so->get_account(array('id' => (int)$id) );
		}

		function _process_edit($uri_parts)
		{
			$xmldata = '';
			$putdata = fopen('php://input', 'r');//the TFphpM is fsckd stdin doesn't work!!

			while ( $data = fread($putdata, 1024) )
			{
				$xmldata .= "{$data}\n";
			}

			if( !strlen($xmldata) )
			{
				Header('HTTP/1.0 400 ' . lang('invalid account data') );
				echo lang('invalid account data');
				exit;
			}

			$smtp = createObject('communik8r.comm_smtp');

			trigger_error($xmldata);

			$xml = new DOMDocument;
			$xml->loadXML($xmldata);

			trigger_error($xml->saveXML());

			error_log(print_r($xml->getElementsByTagName('account'), true));
			
			$tmp_array = $xml->getElementsByTagName('account');

			if ( count($tmp_array) != 1 )
			{
				Header('HTTP/1.0 400 ' . lang('invalid account data') );
				echo lang('invalid account data');
				exit;
			}
			
			$account_xml = $tmp_array[0];
			unset($tmp_array);
			
			$account_data = array();
			$account_data['extra'] = array();
			foreach ( $account_xml->child_nodes() as $node )
			{
				/* Initialize elements */
				$account_data[$node->tagname()] = '';
				if ( strpos($node->tagname(), 'extra_') === 0 )
				{
					$account_data['extra'][substr($node->tagname(), 6)] = '';
				}

				if ( $node->has_child_nodes() )
				{
					$child_nodes = $node->child_nodes();

					$i = 0;
					while ( $child_nodes[$i]->node_type() != XML_TEXT_NODE )
					{
						++$i;
					}
					
					if ( strpos($node->tagname(), 'extra_') === 0 )
					{
						$account_data['extra'][substr($node->tagname(), 6)] = $child_nodes[$i]->node_value();
						continue;
					}
					$account_data[$node->tagname()] = $child_nodes[$i]->node_value();
				}
				unset($child_nodes);
			}
			trigger_error('$account_data = ' . print_r($account_data, true));

			//FIXME Need better error handling
			if ( $this->so->save_account(intval($uri_parts[2]), $account_data) )
			{
				//Header('HTTP/1.0 200 Done!'); //FIXME
				exit;
			}
			Header('HTTP/1.0 500 Not Saved');
			echo lang('not saved');
			exit;
			
		}
	}
