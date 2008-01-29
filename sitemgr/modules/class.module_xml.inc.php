<?php 

class module_xml extends Module
{

	var $filenumber;

	function module_xml()
	{
		$this->arguments = array(
			'dirpath' => array('type' => 'textfield', 'label' => lang('Filesystem path of the directory where XML files are stored')),
			'filename' => array('type' => 'textfield', 'label' => lang('the XML files\' common name')),
			'xsltfile' => array('type' => 'textfield', 'label' => lang('Full path of the XSLT file that should be applied to the XML files'))
		);
		$this->post = array(
			'prev' => array(
				'type' => 'submit',
				'value' => lang('Previous')
			),
			'next' => array(
				'type' => 'submit',
				'value' => lang('Next')
			)
		);
		$this->session = array('filenumber');
		$this->title = lang('XML browser');
		$this->description = lang('This module permits browsing through XML files stored in a directory, and transformed by XSLT');
	}

	function set_block(&$block,$produce=False)
	{
		parent::set_block($block,$produce);

		if ($produce)
		{
			if (!$this->block->arguments['filenumber'])
			{
				$this->block->arguments['filenumber'] = 1;
			}
			else
			{
				$this->block->arguments['filenumber'] = (int)$this->block->arguments['filenumber'];
			}
			if ($this->block->arguments['next'])
			{
				$this->block->arguments['filenumber']++;
			}
			elseif ($this->block->arguments['prev'])
			{
				$this->block->arguments['filenumber']--;
			}
			if ($this->block->arguments['filenumber'] < 1 || !file_exists(
					$this->block->arguments['dirpath'] . SEP . $this->block->arguments['filename'] . 
					$this->block->arguments['filenumber'] . '.xml'
				))
			{
				$this->block->arguments['filenumber'] = 1;
			}

			require_once(PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'inc' . SEP . 'class.xslt_transform.inc.php');
			$this->add_transformer(new xslt_transform($this->block->arguments['xsltfile']));

			$prevlink = ($this->block->arguments['filenumber'] > 1) ? $this->build_post_element('prev') : '';
			$nextlink = 
				(file_exists(
					$this->block->arguments['dirpath'] . SEP . $this->block->arguments['filename'] . 
					($this->block->arguments['filenumber'] + 1) . '.xml'
				)) ?
				$this->build_post_element('next') : 
				'';
			require_once(PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'inc' . SEP . 'class.browser_transform.inc.php');
			$this->add_transformer(new browser_transform($prevlink,$nextlink,$this->block->module_name));
		}
	}

	function get_content(&$arguments,$properties)
	{
		return implode('',@file($arguments['dirpath'] . SEP . $arguments['filename'] . $arguments['filenumber'] . '.xml'));
	}
}
