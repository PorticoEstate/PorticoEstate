<?php
require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'BaseActivity.php');
//!! End
//! End class
/*!
This class handles activities of type 'end'
*/
class End extends BaseActivity {
	function End(&$db)
	{
	 	parent::Base($db);
		$this->child_name = 'End';
	}
}
?>
