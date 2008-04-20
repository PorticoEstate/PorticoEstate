<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessManager' . '/' . 'InstanceManager.php');

	class workflow_instancemanager extends InstanceManager
	{
		function workflow_instancemanager()
		{
			parent::InstanceManager($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
