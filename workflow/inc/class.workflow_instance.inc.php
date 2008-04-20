<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'API' . '/' . 'Instance.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'common' . '/' . 'WfSecurity.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessManager' . '/' . 'ActivityManager.php');

	class workflow_instance extends Instance
	{
		function workflow_Instance()
		{
			parent::Instance($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
