<?php
	class Block_SO
	{
		var $id;
		var $cat_id;
		var $page_id;
		var $area;
		var $module_id;
		var $module_name;
		var $arguments;
		var $sort_order;
		var $title;
		var $view;
		var $state;
		var $version;
		
		function Block_SO()
		{
		}

		function set_version($version)
		{
			$this->arguments = $version['arguments'];
			$this->state = $version['state'];
			$this->version = $version['id'];
		}
	}
?>
