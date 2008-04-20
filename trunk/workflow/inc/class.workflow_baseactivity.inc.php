<?php
	// include galaxia's configuration tailored to phpgroupware
	require_once('engine/config.phpgw.inc.php');

	require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'API' . '/' . 'BaseActivity.php');

	class workflow_baseactivity extends BaseActivity
	{
		function workflow_baseactivity()
		{
			parent::BaseActivity($GLOBALS['phpgw']->db->link_id());
		}
	}
?>
