<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage core
 	* @version $Id: class.socommon.inc.php 17785 2006-12-27 10:39:15Z sigurdne $
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_socommon
	{
		function sms_socommon()
		{

			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->db		= clone($GLOBALS['phpgw']->db);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

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

	}
