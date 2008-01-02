<?php 

class module_galerie extends Module
{
	function module_galerie()
	{
		$this->i18n = True;
		$this->arguments = array(
			'imagedirurl' => array(
				'type' => 'textfield', 
				'label' => lang('URL pointing to the directory where the images are found (no trailing slash)')
			),
			'imagedirpath' => array(
				'type' => 'textfield', 
				'label' => lang('Filesystem path of the directory where the images are found (no trailing slash)')
			),
			'imagename' => array(
				'type' => 'textfield', 
				'label' => lang('the images\' common name')
			),
			'imagetype' => array(
				'type' => 'select', 
				'label' => lang('image type'), 
				'options' => array(
					'jpeg' => 'jpeg',
					'gif' => 'gif',
					'png' => 'png'
				)
			),
		);
		$this->title = lang('Galerie');
		$this->post = array(
			'prev' => array(
				'type' => 'submit',
				'value' => "&lt;---"
			),
			'next' => array(
				'type' => 'submit',
				'value' => "---&gt;"
			)
		);
		$this->session = array('filenumber');
		$this->description = lang('A simple picture galery');
	}

	function get_user_interface()
	{
		$this->set_subtext_args();
		return parent::get_user_interface();
	}

	function get_translation_interface($fromblock,$toblock)
	{
		$this->set_subtext_args();
		return parent::get_translation_interface($fromblock,$toblock);
	}
	
	function set_subtext_args()
	{
		$defaults = $this->block->arguments;
		if ($defaults['imagedirpath'] && is_dir($defaults['imagedirpath']))
		{
			$i = 1;
			$this->arguments['subtext'] = array(
				'type' => "array",
				'i18n' => True
			);
			while (file_exists($defaults['imagedirpath'] . SEP . $defaults['imagename'] . $i . '.' . $defaults['imagetype']))
			{
				$this->arguments['subtext'][$i-1] = array(
					'type' => 'textfield',
					'label' => 'Subtext for image ' . $i . '<br /><img src="' . 
						$defaults['imagedirurl'] . SEP . $defaults['imagename'] . $i . '.' . $defaults['imagetype'] . '" />',
					'i18n' => True
				);
				$i++;
			}
		}
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
					$this->block->arguments['imagedirpath'] . SEP . $this->block->arguments['imagename'] . 
					$this->block->arguments['filenumber'] . '.' . $this->block->arguments['imagetype']
				))
			{
				$this->block->arguments['filenumber'] = 1;
			}
			$prevlink = ($this->block->arguments['filenumber'] > 1) ? $this->build_post_element('prev') : '';
			$nextlink = 
				(file_exists(
					$this->block->arguments['imagedirpath'] . SEP . $this->block->arguments['imagename'] . 
					($this->block->arguments['filenumber'] + 1) . '.' . $this->block->arguments['imagetype']
				)) ?
				$this->build_post_element('next') : 
				'';
			require_once(PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'inc' . SEP . 'class.browser_transform.inc.php');
			$this->add_transformer(new browser_transform($prevlink,$nextlink));
		}
	}


	
	function get_content(&$arguments,$properties)
	{
		$content .= '<div align="center"><img  hspace="20" align="absmiddle" src="'. $arguments['imagedirurl'] . SEP . $arguments['imagename'] . $arguments['filenumber'] . '.' . $arguments['imagetype'] . '" /></div>';
		$content .= '<div align="center" style="margin:5mm">' . $arguments['subtext'][$arguments['filenumber']-1] . '</div>';
		return $content;
	}
}
