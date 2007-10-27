<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage admin
 	* @version $Id: class.soadmin.inc.php,v 1.8 2006/12/27 10:38:35 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_soadmin
	{
		function hrm_soadmin()
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('hrm.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();
			$this->join		= $this->bocommon->join;
			$this->like		= $this->bocommon->like;
		}

		function select_location($grant='', $appname = '')
		{
			if(!$appname)
			{
				$appname = $this->currentapp;
			}
			
			$filter = " WHERE appname='$appname'";
			
			if($grant)
			{
				$filter .= ' AND allow_grant=1';
			}
			$this->db->query("SELECT * FROM phpgw_hrm_acl_location $filter ORDER BY id ");

				$i = 0;
				while ($this->db->next_record())
				{
						$location[$i]['id']		= $this->db->f('id');
						$location[$i]['descr']		= stripslashes($this->db->f('descr'));
				$i++;
				}

				return $location;
		}

	}
