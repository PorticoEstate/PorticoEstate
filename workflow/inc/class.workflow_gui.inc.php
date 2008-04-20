<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'GUI' . '/' . 'GUI.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'API' . '/' . 'Process.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'API' . '/' . 'Instance.php');
	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'common' . '/' . 'WfSecurity.php');

	class workflow_gui extends GUI
	{
		function workflow_gui()
		{
			parent::GUI($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
