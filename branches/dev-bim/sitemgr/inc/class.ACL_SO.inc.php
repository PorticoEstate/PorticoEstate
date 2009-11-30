<?php
class ACL_SO
{
	var $db;
	var $acl;
	var $acct;

	function ACL_SO()
	{
		$this->db = $GLOBALS['phpgw']->db;
		$this->acl = CreateObject('phpgwapi.acl');
		$this->acct = CreateObject('phpgwapi.accounts');
	}

	function get_permission($location)
	{
		$memberships = $this->acct->membership($this->acl->logged_in_user);
		$sql = 'SELECT acl_rights FROM phpgw_acl'
			. " WHERE acl_location='" . $GLOBALS['phpgw']->db->db_addslashes($location) . "'"
			. ' AND acl_account IN (' . intval($GLOBALS['phpgw_info']['user']['account_id']);
		if (is_array($memberships))
		{
			foreach($memberships as $group)
			{
				$sql .= ',' . intval($group['account_id']);
			}
		}
		$sql .= ')';
		$this->db->query($sql, __LINE__, __FILE__);
		$permission = 0;
		while ($this->db->next_record())
		{
			$permission = $permission | $this->db->f('acl_rights');
		}
		return $permission;
	}

	function get_rights($account_id, $location)
	{
		$this->db->query('SELECT acl_rights FROM phpgw_acl'
				. " WHERE acl_appname='sitemgr' "
				. " AND acl_location='" . $GLOBALS['phpgw']->db->db_addslashes($location) . "'"
				. ' AND acl_account = ' . intval($account_id), __LINE__, __FILE__);

		if ($this->db->next_record())
		{
			return $this->db->f('acl_rights');
		}
		else
		{
			return 0;
		}
	}

	function copy_rights($fromlocation,$tolocation)
	{
		$this->db->query('SELECT acl_account,acl_rights FROM phpgw_acl'
				. " WHERE acl_appname='sitemgr'"
				. " AND acl_location='" 
					. $this->db->db_addslashes($fromlocation) . "'", __LINE__, __FILE__);
		while ($this->db->next_record())
		{
			$this->acl->add_repository('sitemgr',$tolocation,$this->db->f('acl_account'),$this->db->f('acl_rights'));
		}
	}

	function remove_location($location)
	{
		$this->db->query('DELETE FROM phpgw_acl'
				. " WHERE acl_appname='sitemgr' "
				. " AND acl_location='" . $this->db->db_addslashes($location) . "'", __LINE__, __FILE__);
	}
}
?>
