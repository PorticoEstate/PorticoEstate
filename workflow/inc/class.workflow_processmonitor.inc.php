<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessMonitor' . '/' . 'ProcessMonitor.php');

	class workflow_processmonitor extends ProcessMonitor
	{
		function workflow_processmonitor()
		{
			parent::ProcessMonitor($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
