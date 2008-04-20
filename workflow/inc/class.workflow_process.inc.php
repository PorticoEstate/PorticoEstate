<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'API' . '/' . 'Process.php');

	class workflow_process extends Process
	{
		function workflow_process()
		{
			parent::Process($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
