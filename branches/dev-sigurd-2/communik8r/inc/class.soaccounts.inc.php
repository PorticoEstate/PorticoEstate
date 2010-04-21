<?php
	/**
	* Communik8r accounts storage class
	*
	* @author Dave Hall skwashd@phpgroupware.org
	* @copyright Copyright (C) 2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package communik8r
	* @subpackage accounts
	* @version $Id: class.soaccounts.inc.php,v 1.1.1.1 2005/08/23 05:03:53 skwashd Exp $
	*/

	/**
	* Communik8r accounts logic class
	*/
	class soaccounts
	{
		/**
		* @var object $db database abstraction layer
		*/
		var $db;

		function soaccounts()
		{
			$this->db = &$GLOBALS['phpgw']->db;
		}
		
		/**
		* Get account/s for the current user
		*
		* @param array $options paramaters for filtering by
		* @returns array account/s data
		*/
		function get_account($options = array() )
		{
			$account_filter = '';
			if ( isset($options['name']) && $options['name'] )
			{
				$account_filter .= " AND phpgw_communik8r_accts.acct_name = '" 
							. $this->db->db_addslashes($options['name']) . "'";
			}
			
			if ( isset($options['id']) )
			{
				$account_filter .= ' AND phpgw_communik8r_accts.acct_id = ' . (int)$options['id'];
			}

			if ( isset($options['acct_handler']) && $options['acct_handler'] )
			{
				$account_filter .= " AND phpgw_communik8r_acct_types.handler = '" 
							. $this->db->db_addslashes($options['acct_handler']) . "'";
				unset($options['acct_handler']);//this is a needed hack
			}
			
			$sql = 'SELECT phpgw_communik8r_accts.acct_id, phpgw_communik8r_accts.acct_name,'
				. ' phpgw_communik8r_accts.display_name, phpgw_communik8r_accts.acct_uri,'
				. ' phpgw_communik8r_accts.username, phpgw_communik8r_accts.password,'
				. ' phpgw_communik8r_accts.server, phpgw_communik8r_accts.port,'
				. ' phpgw_communik8r_accts.is_ssl, phpgw_communik8r_accts.is_tls,'
				. ' phpgw_communik8r_accts.acct_options, phpgw_communik8r_accts.signature_id, '
				. ' phpgw_communik8r_accts.org, phpgw_communik8r_acct_types.acct_type_id,'
				. ' phpgw_communik8r_acct_types.type_name, phpgw_communik8r_acct_types.type_descr,'
				. ' phpgw_communik8r_acct_types.handler'
				. ' FROM phpgw_communik8r_accts, phpgw_communik8r_acct_types'
				. ' WHERE phpgw_communik8r_accts.acct_type_id = phpgw_communik8r_acct_types.acct_type_id'
				. " AND phpgw_communik8r_accts.owner_id = {$GLOBALS['phpgw_info']['user']['account_id']}"
				. ' ' . $account_filter;

			$this->db->query($sql, __LINE__, __FILE__);

			while ( $this->db->next_record() )
			{
				$accts[] = array
						(
							'acct_id'	=> $this->db->f('acct_id'),
							'acct_name'	=> $this->db->f('acct_name', True),
							'display_name'	=> $this->db->f('display_name', True),
							'acct_uri'	=> $this->db->f('acct_uri', True),
							'username'	=> $this->db->f('username', True),
							'password'	=> $GLOBALS['phpgw']->crypto->decrypt($this->db->f('password', True)),
							'hostname'	=> $this->db->f('server', True),
							'port'		=> $this->db->f('port'),
							'ssl'		=> !!$this->db->f('is_ssl'),
							'tls'		=> !!$this->db->f('is_tls'),
							'acct_options'	=> unserialize($this->db->f('acct_options', True)),
							'signature_id'	=> intval($this->db->f('signature_id')),
							'org'		=> $this->db->f('org', true),
							'acct_type_id'	=> $this->db->f('acct_type_id'),
							'type_name'	=> $this->db->f('type_name', True),
							'type_descr'	=> $this->db->f('type_descr', True),
							'handler'	=> $this->db->f('handler', True)
						);
			}
			if( count($options) && count($accts) )
			{
				return $accts[0];
			}
			return $accts;
		}

		/**
		* Get a summary list of accounts
		*
		* @param string $acct_handler the handler type for this list being sought
		* @returns array list of accounts
		*/
		function get_list($acct_handler = null)
		{
			$params = array();
			if ( $acct_handler )
			{
				$params = array('acct_handler' => $acct_handler);
			}
			return $this->get_account($params);
		}

		/**
		* Save Account
		*
		* @param int $acct_id account id (0 == new account)
		* @param array $data account data
		*/
		function save_account($acct_id, $data)
		{
			trigger_error("soaccounts::save_account({$acct_id}," . print_r($data, true) . ')');
			$passwd = $GLOBALS['phpgw']->crypto->encrypt($data['password']);
			if ( $acct_id == 0 ) //new
			{
				$sql = 'INSERT INTO phpgw_communik8r_accts(owner_id, acct_name, display_name, acct_uri, username, password, '
						. ' server, port, is_ssl, is_tls, acct_type_id, acct_options, signature_id, org)'
					. ' VALUES( ' . intval($GLOBALS['phpgw_info']['user']['account_id']) . ','
						. " '" . $this->db->db_addslashes($data['acct_name']) . "',"
						. " '" . $this->db->db_addslashes($data['display_name']) . "',"
						. " '" . $this->db->db_addslashes($data['acct_uri']) . "',"
						. " '" . $this->db->db_addslashes($data['username']) . "',"
						. " '" . $this->db->db_addslashes($passwd) . "',"
						. " '" . $this->db->db_addslashes($data['hostname']) . "',"
						. ' ' . intval($data['port']) . ','
						. ' ' . intval($data['is_ssl']) . ','
						. ' ' . intval($data['is_tls']) . ','
						. ' ' . intval($data['acct_type_id']) . ','
						. " '" . serialize($data['extra']) . "',"
						. ' ' . intval($data['signature_id']) . ','
						. " '" . $this->db->db_addslashes($data['org']) 
					. "')";
				
				$this->db->query($sql, __LINE__, __FILE__);
				return $this->db->get_last_insert_id('phpgw_communik8r_accts', 'acct_id');
			}
			else //exisiting
			{
				$sql = 'UPDATE phpgw_communik8r_accts'
					. " SET acct_name = '" . $this->db->db_addslashes($data['acct_name']) . "',"
						. " display_name = '" . $this->db->db_addslashes($data['display_name']) . "',"
						. " acct_uri = '" . $this->db->db_addslashes($data['acct_uri']) . "'," 
						. " username = '" . $this->db->db_addslashes($data['username']) . "',";
				if (isset($data['password']) && strlen($data['password']) )
				{
					$sql .= " password = '" . $this->db->db_addslashes($passwd) . "',";
				}

				$sql .= " server = '" . $this->db->db_addslashes($data['hostname']) . "',"
						. ' port = ' . intval($data['port']) . ','
						. ' is_ssl = ' . intval($data['is_ssl']) . ','
						. ' is_tls = ' . intval($data['is_tls']) . ','
						. ' acct_type_id = ' . intval($data['acct_type_id']) . ','
						. " acct_options = '" . $this->db->db_addslashes(serialize($data['extra'])) . "',"
						. ' signature_id = ' . intval($data['signature_id']) . ','
						. " org = '" . $data['org'] . "' "
					. 'WHERE acct_id = ' . intval($acct_id)
						. ' AND owner_id = ' . intval($GLOBALS['phpgw_info']['user']['account_id']);

				$this->db->query($sql, __LINE__, __FILE__);
				//return $this->db->affected_rows(); // FIXME
				return 1;
			}
		}

	}
