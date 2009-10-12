<?php

class module_download extends Module 
{
	function module_download()
	{
		$this->arguments = array(
			'path' => array(
				'type' => 'textfield', 
				'label' => lang('The path to the file to be downloaded')
			),
			'file' => array(
				'type' => 'textfield', 
				'label' => lang('The file to be downloaded')
			),
			'text' => array(
				'type' => 'textfield',
				'label' => lang('The text for the link, if empty the module returns the raw URL (without the HTML A element)')
			),
			'op' => array(
				'type' => 'select',
				'label' => lang('Should the file be viewed in the browser or downloaded'),
				'options' => array(1 => lang('viewed'), 2 => lang('downloaded'))
			)
		);
		$this->post = array('name' => array('type' => 'textfield'));
		$this->session = array('name');
		$this->title = lang('File download');
		$this->description = lang('This module create a link for downloading a file from the VFS');
	}

	function get_content(&$arguments,$properties) 
	{
		$linkdata['path'] = rawurlencode(base64_encode($arguments['path']));
		if ($arguments['op'] == 2)
		{
			$linkdata['download'] = 1;
			$linkdata['fileman[0]'] = rawurlencode(base64_encode($arguments['file']));
		}
		else
		{
			$linkdata['op'] = rawurlencode(base64_encode('view'));
			$linkdata['file'] = rawurlencode(base64_encode($arguments['file']));
		}
		return $arguments['text'] ? 
			('<a href="' . phpgw_link('/phpwebhosting/index.php',$linkdata) . '">' . $arguments['text'] . '</a>') :
			phpgw_link('/phpwebhosting/index.php',$linkdata);
	}
}
