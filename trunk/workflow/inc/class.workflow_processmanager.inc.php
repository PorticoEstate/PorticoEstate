<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessManager' . '/' . 'ProcessManager.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessManager' . '/' . 'RoleManager.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessManager' . '/' . 'ActivityManager.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'API' . '/' . 'Process.php');

	class workflow_processmanager extends ProcessManager
	{
		function workflow_processmanager()
		{
			parent::ProcessManager($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
