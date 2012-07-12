<?php
	/**
	* EMail - Login hook
	*
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @subpackage hooks
	* @version $Id$
	*/

	$sql  = 'DELETE FROM phpgw_anglemail ';
	$sql .= "WHERE account_id='" . intval($GLOBALS['phpgw_info']["user"]["account_id"]) . "'";
    $GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
?>
