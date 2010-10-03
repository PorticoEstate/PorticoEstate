<?php

class module_redirect extends Module 
{
	function module_redirect()
	{
		$this->arguments = array(
			'URL' => array(
				'type' => 'textfield', 
				'label' => lang('The URL to redirect to')
			)
		);
		$this->title = lang('Redirection');
		$this->description = lang('This module lets you define pages that redirect to another URL, if you use it, there should be no other block defined for the page');
	}

	function get_content(&$arguments,$properties) 
	{
		Header('Location: ' . $arguments['URL']);
		exit;
	}
}
