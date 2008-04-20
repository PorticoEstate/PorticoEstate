<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessManager' . '/' . 'RoleManager.php');

	class workflow_rolemanager extends RoleManager
	{
		function workflow_rolemanager()
		{
			parent::RoleManager($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
