<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessManager' . '/' . 'ActivityManager.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessManager' . '/' . 'GraphViz.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessManager' . '/' . 'ProcessManager.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'common' . '/' . 'WfSecurity.php');

	class workflow_activitymanager extends ActivityManager
	{
		function workflow_activitymanager()
		{
			parent::ActivityManager($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
