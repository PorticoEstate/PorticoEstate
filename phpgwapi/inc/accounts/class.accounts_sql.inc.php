<?php
	/**
	* View and manipulate account records using SQL
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.accounts_sql.inc.php 18237 2007-08-22 07:40:55Z sigurdne $
	*/

	/**
	* View and manipulate handling user and group account records using SQL
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class accounts_sql extends accounts_
	{
		function accounts_sql($account_id = null, $account_type = null)
		{
			parent::accounts($account_id, $account_type);
		}

		function list_methods($_type='xmlrpc')
		{
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}

			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'get_list' => array(
							'function'  => 'get_list',
							'signature' => array(array(xmlrpcStruct)),
							'docstring' => lang('Returns a full list of accounts on the system.  Warning: This is return can be quite large')
						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
						)
					);
					return $xml_functions;
					break;
 				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
					break;
			}
		}

		/**
		* Read account information from database
		*
		* @return array Array with the following information: userid, account_id, account_lid, firstname, lastname, account_firstname, account_lastname, fullname, lastlogin, lastloginfrom, lastpasswd_change, status, expires, person_id
		*/
		function read_repository()
		{
			$this->db->query('SELECT * FROM phpgw_accounts WHERE account_id=' . intval($this->account_id),__LINE__,__FILE__);
			$this->db->next_record();

			$this->account_id 		= $this->data['account_id']	= $this->db->f('account_id');
			$this->lid			= $this->data['userid']		= $this->data['account_lid'] = $this->db->f('account_lid');
			$this->firstname		= $this->data['firstname']	= $this->data['account_firstname'] = $this->db->f('account_firstname');
			$this->lastname			= $this->data['lastname']	= $this->data['account_lastname'] = $this->db->f('account_lastname');
			$this->data['fullname']		= "{$this->firstname} {$this->lastname}";
			$this->data['lastlogin']			= $this->db->f('account_lastlogin');
			$this->data['lastloginfrom']		= $this->db->f('account_lastloginfrom');
			$this->data['lastpasswd_change']	= $this->db->f('account_lastpwd_change');
			$this->status			= $this->data['status']		= $this->db->f('account_status');
			$this->expired			= $this->data['expires']	= $this->db->f('account_expires');
			$this->person_id		= $this->data['person_id']	= $this->db->f('person_id');
			$this->quota 			= $this->data['quota']		= $this->db->f('account_quota');
			return $this->data;
		}

		/**
		* Save/update account information to/in database
		*/
		function save_repository()
		{
			$this->db->query("UPDATE phpgw_accounts SET account_firstname='" . $this->data['account_firstname']
							. "', account_lastname='" . $this->data['account_lastname'] . "', account_status='"
							. $this->data['status'] . "', account_expires=" . $this->data['expires']
							. ($this->data['account_lid']?", account_lid='".$this->data['account_lid']."'":'')
						//	. (isset($this->data['person_id'])?', person_id=' . $this->data['person_id']:'')
							. ', account_quota=' . intval($this->data['quota'])
							. ' WHERE account_id=' . intval($this->data['account_id']),__LINE__,__FILE__);
		}

		function delete($accountid = '')
		{
			$account_id = get_account_id($accountid);

			/* Do this last since we are depending upon this record to get the account_lid above */
			$tables_array = Array('phpgw_accounts');
			$this->db->lock($tables_array);
			$this->db->query('DELETE FROM phpgw_accounts WHERE account_id=' . $account_id);
			$this->db->unlock();
			return true;
		}

		function get_list($_type='both',$start = -1,$sort = '', $order = '', $query = '', $offset = -1)
		{
			// For XML-RPC
/*			if (is_array($_type))
			{
				$p      = $_type;
				$_type  = $p[0]['type'];
				$start  = $p[0]['start'];
				$order  = $p[0]['order'];
				$query  = $p[0]['query'];
				$offset = $p[0]['offset'];
			}
*/
			$start = (isset($start) ? (int) $start : 0 );
			$offset = (isset($offset) ? (int) $offset : 0 );

			if (! $sort)
			{
				$sort = "DESC";
			}

			if ($order)
			{
				$orderclause = "ORDER BY $order $sort";
			}
			else
			{
				$orderclause = "ORDER BY account_lid ASC";
			}

			switch($_type)
			{
				case 'accounts':
					$whereclause = "WHERE account_type = 'u'";
					break;
				case 'groups':
					$whereclause = "WHERE account_type = 'g'";
					break;
				default:
					$whereclause = '';
			}

			if ($query)
			{
				$query = $this->db->db_addslashes($query);
				if ($whereclause)
				{
					$whereclause .= ' AND ( ';
				}
				else
				{
					$whereclause = ' WHERE ( ';
				}

				$whereclause .= " account_firstname $this->like '%$query%' OR account_lastname $this->like "
					. "'%$query%' OR account_lid $this->like '%$query%' OR person_id $this->like '%$query%')";
			}

			$sql = "SELECT * FROM phpgw_accounts $whereclause $orderclause";
			if ($offset == -1 && $start == -1)
			{
				$this->db->query($sql,__LINE__,__FILE__);
			} 
			elseif ($start != -1)
			{
				$this->db->limit_query($sql, $start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->limit_query($sql, $start,__LINE__,__FILE__, $offset);
			}

			$accounts = array();
			while ($this->db->next_record())
			{
				//echo '<pre>' . print_r($this->db->Record, true) . '</pre>';
				$accounts[] = array
						(
							'account_id'		=> $this->db->f('account_id'),
							'account_lid'		=> $this->db->f('account_lid'),
							'account_type'		=> $this->db->f('account_type'),
							'account_firstname'	=> $this->db->f('account_firstname'),
							'account_lastname'	=> $this->db->f('account_lastname'),
							'account_status'	=> $this->db->f('account_status'),
							'account_expires'	=> $this->db->f('account_expires'),
							'person_id'		=> $this->db->f('person_id')
						);
			}
			$this->db->query("SELECT count(account_id) FROM phpgw_accounts $whereclause");
			$this->db->next_record();
			$this->total = $this->db->f(0);

			return $accounts;
		}
		
		function name2id($account_lid)
		{
			static $name_list;

			if (! $account_lid)
			{
				return False;
			}

			if ( isset($name_list[$account_lid])
				&& $name_list[$account_lid] != '')
			{
				return $name_list[$account_lid];
			}

			$account_lid = $this->db->db_addslashes($account_lid);

			$this->db->query('SELECT account_id FROM phpgw_accounts '
				. " WHERE account_lid='" . $account_lid . "'",__LINE__,__FILE__);
			if($this->db->num_rows())
			{
				$this->db->next_record();
				$name_list[$account_lid] = intval($this->db->f('account_id'));
			}
			else
			{
				$name_list[$account_lid] = False;
			}
			return $name_list[$account_lid];
		}

		/**
		* Convert an id into its corresponding account login or group name
		*
		* @param integer $id Account or group id
		* @return string account login id or the group - empty string means not found
		*/
		function id2lid($account_id)
		{
			static $lid_list;

			(int)$account_id;

			if (! $account_id)
			{
				return '';
			}

			if( isset($lid_list[$account_id]) && $id_list[$account_id] ) 
			{
				return $lid_list[$account_id];
			}

			$this->db->query("SELECT account_lid FROM phpgw_accounts WHERE account_id={$account_id}",__LINE__,__FILE__);
			if($this->db->num_rows())
			{
				$this->db->next_record();
				$lid_list[$account_id] = $this->db->f('account_lid');
			}
			else
			{
				$lid_list[$account_id] = '';
			}
			return $lid_list[$account_id];
		}

		function id2name($account_id)
		{
			static $id_list;

			if (! $account_id)
			{
				return False;
			}

			if( isset($id_list[$account_id]) && $id_list[$account_id] ) 
			{
				return $id_list[$account_id];
			}

			$this->db->query('SELECT account_lid, account_firstname, account_lastname FROM phpgw_accounts WHERE account_id=' . intval($account_id),__LINE__,__FILE__);
			if($this->db->num_rows())
			{
				$this->db->next_record();
				$id_list[$account_id] = $GLOBALS['phpgw']->common->display_fullname($this->db->f('account_lid'), $this->db->f('account_firstname'), $this->db->f('account_lastname') );
			}
			else
			{
				$id_list[$account_id] = False;
			}
			return $id_list[$account_id];
		}

		/**
		* Match a contact ID with an account id
		*
		* @param int $person_id the contact person ID
		* @param int account id - 0 if not found
		*/
		function search_person($person_id)
		{
			static $person_list;

			if (! $person_id)
			{
				return 0;
			}

			if ( isset($person_list[$person_id]) )
			{
				return $person_list[$person_id];
			}

			$this->db->query('SELECT account_id FROM phpgw_accounts WHERE person_id=' . (int) $person_id ,__LINE__,__FILE__);
			if($this->db->num_rows())
			{
				$this->db->next_record();
				$person_list[$person_id] = $this->db->f('account_id');
			}
			else
			{
				$person_list[$person_id] = 0;
			}
			return $person_list[$person_id];
		}

		function get_type($accountid)
		{
			static $account_type;
			$account_id = get_account_id($accountid);
			
			if (isset($this->account_type) && $account_id == $this->account_id)
			{
				return $this->account_type;
			}

			if(@isset($account_type[$account_id]) && @$account_type[$account_id])
			{
				return $account_type[$account_id];
			}
			elseif($account_id == '')
			{
				return False;
			}
			$this->db->Halt_On_Error = 'no';
			$this->db->query('SELECT account_type FROM phpgw_accounts WHERE account_id=' .intval($account_id), __LINE__,__FILE__);
			if ($this->db->num_rows())
			{
				$this->db->next_record();
				$account_type[$account_id] = $this->db->f('account_type');
			}
			else
			{
				$account_type[$account_id] = False;
			}
			$this->db->Halt_On_Error = 'yes';
			return $account_type[$account_id];
		}

		function exists($account_lid) // imho this should take $id, $lid as args
		{
			static $by_id, $by_lid;

			$sql = 'SELECT count(account_id) FROM phpgw_accounts WHERE ';
			if(is_integer($account_lid))
			{
				if(@isset($by_id[$account_lid]) && $by_id[$account_lid] != '')
				{
					return $by_id[$account_lid];
				}
				$sql .= 'account_id=' . intval($account_lid);
			}
			else
			{
				if(@isset($by_lid[$account_lid]) && $by_lid[$account_lid] != '')
				{
					return $by_lid[$account_lid];
				}
				$sql .= "account_lid = '" . $this->db->db_addslashes($account_lid) . "'";
			}

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$ret_val = $this->db->f(0) > 0;
			if(is_integer($account_lid))
			{
				$by_id[$account_lid] = $ret_val;
				$by_lid[$this->id2name($account_lid)] = $ret_val;
			}
			else
			{
				$by_lid[$account_lid] = $ret_val;
				$by_id[$this->name2id($account_lid)] = $ret_val;
			}
			return $ret_val;
		}

		function set_data($data)
		{
			parent::set_data($data);
			if(!isset($this->person_id) || $this->person_id == '' )
			{
				// is there a reason to write 'NULL' into database?
				// this could make trouble in different database systems
				$this->person_id = 'NULL';
			}
			return true;
		}
			
		function create($account_info, $default_prefs = true)
		{
			$this->set_data($account_info, $default_prefs);
			$this->db->transaction_begin();
			
			$values= array
			(
				-3,
				'public',
				1,
			);

			$values	= $this->db->validate_insert($values);

			$this->db->query("INSERT INTO phpgw_contact (owner,access,contact_type_id) "
				. "VALUES ($values)",__LINE__,__FILE__);
			$person_id = $this->db->get_last_insert_id('phpgw_contact','contact_id');

			$values= array
			(
				$person_id,
				$this->db->db_addslashes($this->firstname),
				$this->db->db_addslashes($this->lastname),
				((isset($this->status) && $this->status == 'A') ? 'Y':'N'),
				time(),
				0,
				time(),
				0	
			);

			$values	= $this->db->validate_insert($values);

			$this->db->query("INSERT INTO phpgw_contact_person (person_id,first_name,last_name,active,created_on,created_by,modified_on,modified_by) "
				. "VALUES ($values)",__LINE__,__FILE__);


			$fields = array('account_lid',
							'account_type',
							'account_pwd',
							'account_firstname',
							'account_lastname',
							'account_status',
							'account_expires',
							'person_id',
							'account_quota'
						   );
			$values = array("'".$this->db->db_addslashes($this->lid)."'",
							"'".$this->db->db_addslashes($account_info['account_type'])."'",
							"'".md5($this->password)."'",
							"'".$this->db->db_addslashes($this->firstname)."'",
							"'".$this->db->db_addslashes($this->lastname)."'",
							"'".$this->db->db_addslashes($this->status)."'",
							intval($this->expires),
							intval($person_id),
							intval($this->quota)
						   );
			if((int)$this->account_id && !$this->exists((int)$this->account_id))
			{
				$fields[] = 'account_id';
				$values[] = (int)$this->account_id;
			}
			$this->db->query('INSERT INTO phpgw_accounts ('.implode($fields, ',').') '.
												 'VALUES ('.implode($values, ',').')',
							 __LINE__,__FILE__);

			$account_info['account_id'] = $this->db->get_last_insert_id('phpgw_accounts','account_id');
			$this->db->transaction_commit();
			return parent::create($account_info, $default_prefs);
		}

		function get_account_name($accountid,&$lid,&$fname,&$lname)
		{
			static $account_name;
			
			$account_id = get_account_id($accountid);
			if(isset($account_name[$account_id]))
			{
				$lid = $account_name[$account_id]['lid'];
				$fname = $account_name[$account_id]['fname'];
				$lname = $account_name[$account_id]['lname'];
				return;
			}
			$db =& $GLOBALS['phpgw']->db;
			$db->query('select account_lid,account_firstname,account_lastname from phpgw_accounts where account_id=' . intval($account_id),__LINE__,__FILE__);
			$db->next_record();
			$account_name[$account_id]['lid']   = $db->f('account_lid');
			$account_name[$account_id]['fname'] = $db->f('account_firstname');
			$account_name[$account_id]['lname'] = $db->f('account_lastname');
			$lid   = $account_name[$account_id]['lid'];
			$fname = $account_name[$account_id]['fname'];
			$lname = $account_name[$account_id]['lname'];
			return;
		}

		function get_account_with_contact()
		{
			$accounts = array();
			
			$sql = 'SELECT account_id, person_id FROM phpgw_accounts '
				. 'WHERE person_id IS NOT NULL OR person_id != 0';
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$accounts[$this->db->f('account_id')] = $this->db->f('person_id');
			}
			return $accounts;
		}

		function get_account_without_contact()
		{
			$sql = 'SELECT account_id FROM phpgw_accounts '
				. 'WHERE person_id IS NULL OR person_id = 0';
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$accounts[] = $this->db->f('account_id');
			}
			return $accounts;
		}
	}
?>
