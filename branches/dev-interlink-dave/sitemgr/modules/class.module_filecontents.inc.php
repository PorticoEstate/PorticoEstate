<?php

class module_filecontents extends Module 
{
	function module_filecontents()
	{
		$this->arguments = array(
			'filepath' => array(
				'type' => 'textfield', 
				'label' => lang('The complete path to the file to be included')
			)
		);
		$this->title = lang('File contents');
		$this->description = lang('This module includes the contents of a (world readable) file');
	}

	function get_content(&$arguments,$properties)
	{
		if ($this->validate($arguments))
		{
			return implode('', file($arguments['filepath']));
		}
		else
		{
			return lang('File is not world readable');
		}
	}

	function validate(&$data)
	{
		if (!(fileperms($data['filepath']) & 00004))
		{
			$this->validation_error = lang('File is not world readable');
			return false;
		}
		else
		{
			return true;
		}
	}
}
