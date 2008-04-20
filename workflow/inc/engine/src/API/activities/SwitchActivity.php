<?php
require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'BaseActivity.php');
//!! SwitchActivity
//! SwitchActivity class
/*!
This class handles activities of type 'switch'
*/
class SwitchActivity extends BaseActivity
{
	function SwitchActivity(&$db)
	{
	   parent::Base($db);
	   $this->child_name = 'Switch';
	}
}
?>
