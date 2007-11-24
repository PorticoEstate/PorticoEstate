<?php
	/**
	* Session management - Database storage
	* @author NetUSE AG Boris Erdmann, Kristian Koehntopp
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Ralf Becker <ralfbecker@outdoor-training.de>
	* @copyright Copyright (C) 1998-2000 NetUSE AG Boris Erdmann, Kristian Koehntopp
	* @copyright Portions Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.sessions_db.inc.php,v 1.3 2007/10/11 13:30:47 skwashd Exp $
	* @link http://www.sanisoft.com/phplib/manual/session.php
	*/

	/**
	* Session management based on database
	* 
	* @package phpgwapi
	* @subpackage sessions
	*/
	class sessions_db extends sessions
	{
		function sessions_db()
		{
			parent::sessions();
		}
		
		function read_session($sessionid)
		{
			$this->db->query("SELECT * FROM phpgw_sessions WHERE session_id='" . $this->sessionid . "'",__LINE__,__FILE__);
			$this->db->next_record();
			
			return $this->db->resultSet->fetchRow();
		}

		// This will remove stale sessions out of the database
		function clean_sessions()
		{
			// If you plan on using the cron apps, please remove the following lines.
			// I am going to make this a config option durring 0.9.11, instead of an application (jengo)

			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_sessions WHERE session_dla <= '" . (time() - $GLOBALS['phpgw_info']['server']['sessions_timeout'])
				. "' AND session_flags !='A'",__LINE__,__FILE__);

			// This is set a little higher, we don't want to kill session data for anonymous sessions.
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_app_sessions WHERE session_dla <= '" . (time() - $GLOBALS['phpgw_info']['server']['sessions_timeout'])
				. "'",__LINE__,__FILE__);
		}

		function register_session($login,$user_ip,$now,$session_flags)
		{
			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_sessions VALUES ('" . $this->sessionid
				. "','".$login."','" . $user_ip . "','"
				. $now . "','" . $now . "','" . $_SERVER['PHP_SELF'] . "','" . $session_flags
				. "')",__LINE__,__FILE__);
		}

		// This will update the DateLastActive column, so the login does not expire
		function update_dla()
		{
			$menu_action = phpgw::get_var('menuaction');
			$action = $menu_action ? $menu_action : $_SERVER['PHP_SELF'];  

			// This way XML-RPC users aren't always listed as
			// xmlrpc.php
			if ($this->xmlrpc_method_called)
			{
				$action = $this->xmlrpc_method_called;
			}

			$GLOBALS['phpgw']->db->query("UPDATE phpgw_sessions SET session_dla='" . time() . "', session_action='$action' "
				. "WHERE session_id='" . $this->sessionid."'",__LINE__,__FILE__);

			$GLOBALS['phpgw']->db->query("UPDATE phpgw_app_sessions SET session_dla='" . time() . "' "
				. "WHERE sessionid='" . $this->sessionid."'",__LINE__,__FILE__);
			return True;
		}

		function destroy($sessionid, $kp3)
		{
			if (! $sessionid && $kp3)
			{
				return False;
			}

			$GLOBALS['phpgw']->db->transaction_begin();
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_sessions WHERE session_id='"
				. $sessionid . "'",__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_app_sessions WHERE sessionid='"
				. $sessionid . "'",__LINE__,__FILE__);
			$this->log_access($this->sessionid);	// log logout-time

			// Only do the following, if where working with the current user
			if ( isset($GLOBALS['phpgw_info']['user']['sessionid'])
				&& $sessionid == $GLOBALS['phpgw_info']['user']['sessionid'])
			{
				$this->clean_sessions();
			}
			$GLOBALS['phpgw']->db->transaction_commit();

			return true;
		}

		/*************************************************************************\
		* Functions for appsession data and session cache                         *
		\*************************************************************************/

		function delete_cache($accountid='')
		{
			$account_id = get_account_id($accountid,$this->account_id);

			$query = "DELETE FROM phpgw_app_sessions WHERE loginid = '".$account_id."'"
				." AND app = 'phpgwapi' AND location = 'phpgw_info_cache'";

			$GLOBALS['phpgw']->db->query($query);
		}

		function appsession($location = 'default', $appname = '', $data = '##NOTHING##')
		{
			if (! $appname)
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			
			/* This allows the user to put '' as the value. */
			if ($data == '##NOTHING##')
			{
				$query = "SELECT content FROM phpgw_app_sessions WHERE"
					." sessionid='".$this->sessionid."' AND loginid='".$this->account_id."'"
					." AND app = '".$appname."' AND location='".$location."'";
	
				$GLOBALS['phpgw']->db->query($query,__LINE__,__FILE__);
				$GLOBALS['phpgw']->db->next_record();

				// I added these into seperate steps for easier debugging
				$data = $GLOBALS['phpgw']->db->f('content', true);
				if($data)
				{
					$data = $GLOBALS['phpgw']->crypto->decrypt($data);
					// echo 'appsession returning: '; _debug_array($data);
					return $data;
				}
			}
			else
			{
				$GLOBALS['phpgw']->db->query("SELECT content FROM phpgw_app_sessions WHERE "
					. "sessionid = '".$this->sessionid."' AND loginid = '".$this->account_id."'"
					. " AND app = '".$appname."' AND location = '".$location."'",__LINE__,__FILE__);

				$encrypteddata = $GLOBALS['phpgw']->db->db_addslashes($GLOBALS['phpgw']->crypto->encrypt($data));

				if ($GLOBALS['phpgw']->db->num_rows()==0)
				{
					$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_app_sessions (sessionid,loginid,app,location,content,session_dla) "
						. "VALUES ('".$this->sessionid."','".$this->account_id."','".$appname
						. "','".$location."','".$encrypteddata."','" . time() . "')",__LINE__,__FILE__);
				}
				else
				{
					$GLOBALS['phpgw']->db->query("UPDATE phpgw_app_sessions SET content='".$encrypteddata."'"
						. "WHERE sessionid = '".$this->sessionid."'"
						. "AND loginid = '".$this->account_id."' AND app = '".$appname."'"
						. "AND location = '".$location."'",__LINE__,__FILE__);
				}
				return $data;
			}
		}

		function list_sessions($start, $order = 'ASC', $sort = 'session_lid', $all_no_sort = false)
		{
			$SQL_sort  =  strlen($sort) ? 'ORDER BY '.$sort : '';
			$SQL_sort .= (strlen($sort) && strlen($order)) ? ' '.$order : '' ;
			
			$this->db->limit_query('SELECT * FROM phpgw_sessions'
					. " WHERE session_flags != 'A' $SQL_sort", $start, __LINE__, __FILE__);

			$values = array();
				
			while ($this->db->next_record())
			{
				$values[] = array(
					'session_id'        => $this->db->f('session_id'),
					'session_lid'       => $this->db->f('session_lid'),
					'session_ip'        => $this->db->f('session_ip'),
					'session_logintime' => $this->db->f('session_logintime'),
					'session_action'    => $this->db->f('session_action'),
					'session_dla'       => $this->db->f('session_dla')
				);
			}
			return $values;
		}
		
		/**
		 * get number of normal / non-anonymous sessions
		*
		 */
		function total()
		{
			$this->db->query("select count(*) from phpgw_sessions where session_flags != 'A'",__LINE__,__FILE__);
			$this->db->next_record();

			return $this->db->f(0);
		}

		/**
		* Get the list of session variables used for non cookie based sessions
		*
		* @access private
		* @return array the variables which are specific to this session type
		*/
		function _get_session_vars()
		{
			return array
			(
				'sessionid'	=> $this->sessionid,
				'kp3'		=> $this->kp3,
				'domain'	=> $this->account_domain
			);
		}
	}
?>
