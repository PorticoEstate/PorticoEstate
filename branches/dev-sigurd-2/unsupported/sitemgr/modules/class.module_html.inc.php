<?php 

	class module_html extends Module
	{
		function module_html()
		{
			$this->i18n = true;
			$this->arguments = array(
				'htmlcontent' => array(
					'type' => 'textarea', 
					'label' => lang('Enter the block content here'), 
					'i18n' => True,
					'params' => Array('cols' => 50, 'rows' => 15)
				)
			);
			$this->properties = array('striphtml' => array('type' => 'checkbox', 'label' => lang('Strip HTML from block content?')));
			$this->title = lang('HTML module');
			$this->description = lang('This module is a simple HTML editor');
		}

	
		function get_content(&$arguments,$properties)
		{
			return $properties['striphtml'] ? $GLOBALS['phpgw']->strip_html($arguments['htmlcontent']) : $arguments['htmlcontent'];
		}
	}
