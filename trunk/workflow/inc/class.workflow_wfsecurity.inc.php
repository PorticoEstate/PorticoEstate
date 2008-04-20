<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'common' . '/' . 'WfSecurity.php');

	class workflow_wfsecurity extends WfSecurity
	{
		function workflow_wfsecurity()
		{
			parent::WfSecurity($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
