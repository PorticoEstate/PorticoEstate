<?php
require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'BaseActivity.php');
//!! Activity
//!
/*!
This class handles activities of type 'activity'
*/
class Activity extends BaseActivity
{
	function Activity(&$db)
	{
		parent::Base($db);
		$this->child_name = 'Activity';
	}

}
?>
