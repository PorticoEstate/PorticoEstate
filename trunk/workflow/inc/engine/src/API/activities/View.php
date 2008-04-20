<?php
require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'BaseActivity.php');
//!! View
//! View class
/*!
This class handles activities of type 'view'
*/
class View extends BaseActivity
{
	function View(&$db)
	{
	 	parent::Base($db);
		$this->child_name = 'View';
	}

}
?>
