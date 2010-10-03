<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_socommon
	{
		function hrm_socommon()
		{
			//$GLOBALS['phpgw_info']['flags']['currentapp']	=	'hrm';
		//	$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
		//	$this->db		= $GLOBALS['phpgw']->db;
			$this->db = CreateObject('phpgwapi.db');
			$this->db->Host = $GLOBALS['phpgw_info']['server']['db_host'];
			$this->db->Type = $GLOBALS['phpgw_info']['server']['db_type'];
			$this->db->Database = $GLOBALS['phpgw_info']['server']['db_name'];
			$this->db->User = $GLOBALS['phpgw_info']['server']['db_user'];
			$this->db->Password = $GLOBALS['phpgw_info']['server']['db_pass'];

			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];

			if ($GLOBALS['phpgw_info']['server']['db_type']=='pgsql' || $GLOBALS['phpgw_info']['server']['db_type']=='postgres')
			{
				$this->join = " JOIN ";
				$this->like = "ILIKE";
			}
			else
			{
				$this->join = " INNER JOIN ";
				$this->like = "LIKE";
			}

			$this->left_join = " LEFT JOIN ";
		}

		function fm_cache($name='',$value='')
		{
			if($value)
			{
				$value = serialize($value);
				$this->db->query("INSERT INTO fm_cache (name,value)VALUES ('$name','$value')",__LINE__,__FILE__);
			}
			else
			{
				$this->db->query("SELECT value FROM fm_cache where name='$name'");
				$this->db->next_record();
				$value= unserialize($this->db->f('value'));
				return $value;
			}
		}

		function create_preferences($app='',$user_id='')
		{
				$this->db->query("SELECT preference_value FROM phpgw_preferences where preference_app = '$app' AND preference_owner=".(int)$user_id );
				$this->db->next_record();
				$value= unserialize($this->db->f('preference_value'));
				return $value;
		}


		function next_id($table='',$key='')
		{
			if(is_array($key))
			{
				while (is_array($key) && list($column,$value) = each($key))
				{
					if($value)
					{
						$condition[] = $column . '=' . $value;
					}
				}

				$where=' WHERE ' . implode(" AND ", $condition);
			}

			$this->db->query("SELECT max(id) as maximum FROM $table $where",__LINE__,__FILE__);
			$this->db->next_record();
			$next_id = $this->db->f('maximum')+1;
			return "$next_id";
		}

		function new_db()
		{
			$db = CreateObject('phpgwapi.db');
			$db->Host = $GLOBALS['phpgw_info']['server']['db_host'];
			$db->Type = $GLOBALS['phpgw_info']['server']['db_type'];
			$db->Database = $GLOBALS['phpgw_info']['server']['db_name'];
			$db->User = $GLOBALS['phpgw_info']['server']['db_user'];
			$db->Password = $GLOBALS['phpgw_info']['server']['db_pass'];
			return $db;
		}

	}
