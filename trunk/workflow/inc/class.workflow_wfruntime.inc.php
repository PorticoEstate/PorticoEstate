<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'common' . '/' . 'WfRuntime.php');

	class workflow_wfruntime extends WfRuntime
	{
		function workflow_wfruntime()
		{
			parent::WfRuntime($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
