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
 	* @version $Id: class.soadmin_acl.inc.php 17161 2006-09-16 16:55:39Z sigurdne $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class soadmin_acl
	{
		function soadmin_acl()
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->db =& $GLOBALS['phpgw']->db;
		}

		function select_location($grant = false, $appname = '', $allow_c_attrib = false)
		{
			$location = array();
			
			if ( !$appname )
			{
				$appname = $this->currentapp;
			}
			$appname = $this->db->db_addslashes($appname);
			
			$filter = " WHERE appname='{$appname}'";
			
			if($allow_c_attrib)
			{
				$filter .= ' AND allow_c_attrib = 1';
			}

			if($grant)
			{
				$filter .= ' AND allow_grant = 1';
			}
			$this->db->query("SELECT id, descr FROM phpgw_acl_location $filter ORDER BY id");
			
			while ($this->db->next_record())
			{
				$location[$this->db->f('id')] = $this->db->f('descr', true);
			}
			return $location;
		}
	}
?>
